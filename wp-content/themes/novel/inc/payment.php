<?php
function load_razorpay_scripts() {

    wp_enqueue_script(
        'razorpay-checkout',
        'https://checkout.razorpay.com/v1/checkout.js',
        [],
        null,
        true
    );

    wp_localize_script('razorpay-checkout', 'RazorpayConfig', [
        'key'      => RAZORPAY_KEY_ID,
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('razorpay_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'load_razorpay_scripts');

add_action('wp_ajax_create_razorpay_order', 'create_razorpay_order');
add_action('wp_ajax_nopriv_create_razorpay_order', 'create_razorpay_order');

function create_razorpay_order() {

    require_once get_template_directory() . '/inc/razorpay-php/Razorpay.php';

    $keyId     = RAZORPAY_KEY_ID;
    $keySecret = RAZORPAY_KEY_SECRET;

    $api = new Razorpay\Api\Api($keyId, $keySecret);

    $amount = intval($_POST['amount']); // in paise

    $order = $api->order->create([
        'receipt'         => 'wallet_' . time(),
        'amount'          => $amount,
        'currency'        => 'INR',
        'payment_capture' => 1
    ]);

    wp_send_json_success([
        'order_id' => $order['id']
    ]);
}

add_action('wp_ajax_verify_razorpay_payment', 'verify_razorpay_payment');
add_action('wp_ajax_nopriv_verify_razorpay_payment', 'verify_razorpay_payment');

function verify_razorpay_payment() {

    require_once get_template_directory() . '/inc/razorpay-php/Razorpay.php';

    $keyId     = RAZORPAY_KEY_ID;
    $keySecret = RAZORPAY_KEY_SECRET;

    $api = new \Razorpay\Api\Api($keyId, $keySecret);

    $attributes = [
        'razorpay_order_id'   => $_POST['razorpay_order_id'],
        'razorpay_payment_id' => $_POST['razorpay_payment_id'],
        'razorpay_signature'  => $_POST['razorpay_signature'],
    ];

    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    $razorpay_order_id = $_POST['razorpay_order_id'];
    $amount  = intval($_POST['amount']);
    $coins  = $_POST['coins'];

    $user_id = get_current_user_id();

    try {
        $api->utility->verifyPaymentSignature($attributes);

        global $wpdb;
        $table = $wpdb->prefix . 'coin_transactions';
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} 
                WHERE transaction_id = %s AND payment_status = 'success'",
                $razorpay_order_id
            )
        );

        if ($exists) {
            wp_send_json_success(['message' => 'Already processed']);
        }

        if (isset($_POST['api']) && $_POST['api'] == 'subscription') {
            activate_subscription($amount, $_POST['period'], $_POST['name'], $razorpay_payment_id, $razorpay_order_id, $user_id);
        }

        if (isset($_POST['api']) && $_POST['api'] == 'coin') {
            credit_coins_after_payment($amount, $razorpay_payment_id, $razorpay_order_id, $coins, $user_id);            
        }

        wp_send_json_success();

    } catch (Exception $e) {
        global $wpdb;
        $table = $wpdb->prefix . 'coin_transactions';

        $wpdb->insert(
            $table,
            [
                'user_id'        => get_current_user_id(),
                'payment_id'     => $razorpay_payment_id,
                'transaction_id' => $razorpay_order_id,
                'amount'         => $amount,
                'pay_for'        => 'coin',
                'purchased_coins' => $coins,
                'created_at'     => current_time('mysql'),
                'payment_status' => 'failed'
            ],
            [
                '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s'
            ]
        );

        wp_send_json_error(['message' => 'Verification failed']);
    }
}

function credit_coins_after_payment($amount, $payment_id, $order_id, $coins, $user_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'coin_transactions';

    $current = (int) get_user_meta($user_id, 'user_coin_balance', true) ?? 0;
    $new_total = $current + $coins;

    update_user_meta($user_id, 'user_coin_balance', $new_total);

    $wpdb->insert(
        $table,
        [
            'user_id'        => $user_id,
            'payment_id'     => $payment_id,
            'transaction_id' => $order_id,
            'amount'         => $amount,
            'pay_for'        => 'coin',
            'purchased_coins' => $coins,
            'created_at'     => current_time('mysql'),
            'payment_status' => 'success'
        ],
        [
            '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s'
        ]
    );
}

function activate_subscription($amount, $period, $name, $payment_id, $order_id, $user_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'coin_transactions';

    $planName = $name;
    $amount = $amount ?? 0;
    $originalPeriod = $period ?? '';
    $period_raw = $period ?? '';

    if (empty($period_raw)) {
        wp_send_json_error(['msg' => 'No period provided']);
    }

    // Normalize the string (e.g. "1month" -> "1 month")
    $period_raw = preg_replace('/\s+/', ' ', strtolower($period_raw));
    $expiry = null;

    add_subscription_plan($user_id, $planName, $period_raw, $originalPeriod);

    $wpdb->insert(
        $table,
        [
            'user_id'     => $user_id,
            'payment_id'  => $payment_id,
            'transaction_id' => $order_id,
            'amount'      => $amount,
            'pay_for' => 'subscription',
            'created_at'  => current_time('mysql'),
            'payment_status' => 'success',
            'subscription_plan' => $planName,
        ],
        [
            '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s'
        ]
    );
}

    function add_subscription_plan($user_id, $planName, $period_raw, $originalPeriod) {
        global $wpdb;

        $now_ts = current_time('timestamp');
        $wp_tz  = new DateTimeZone(wp_timezone_string());

        $active_expiry = get_user_meta($user_id, 'subscription_active_expiry', true);
        $queue = get_user_meta($user_id, 'subscription_queue', true);
        $queue = is_array($queue) ? $queue : [];

        // Determine start datetime
        if (!empty($queue)) {
            $last = end($queue);
            $start_dt = new DateTime($last['expiry'], $wp_tz);
        } elseif ($active_expiry && strtotime($active_expiry) > $now_ts) {
            // queue empty but active plan running → அதன் expiry தான் queue 1 start
            $start_dt = new DateTime($active_expiry, $wp_tz);
        } else {
            // no active, no queue → now start
            $start_dt = new DateTime('@' . $now_ts);
            $start_dt->setTimezone($wp_tz);
        }

        // Calculate expiry
        $expiry_dt = clone $start_dt;
        $expiry_dt->modify('+' . $period_raw);

        // If no active plan → activate immediately
        if (!$active_expiry || strtotime($active_expiry) <= $now_ts) {

            update_user_meta($user_id, 'subscription_active_plan', $planName);
            update_user_meta($user_id, 'subscription_active_period', $originalPeriod);
            update_user_meta($user_id, 'subscription_active_from', $start_dt->format('Y-m-d H:i:s'));
            update_user_meta($user_id, 'subscription_active_expiry', $expiry_dt->format('Y-m-d H:i:s'));

            $wpdb->insert(
                "{$wpdb->prefix}author_notifications",
                array(
                    'user_id' => $user_id,
                    'type' => 'subscription_activated',
                    'seen' => 0,
                    'created_at' => current_time('mysql'),
                    'message'    => 'Your subscription plan ' . $planName . ' activated.',
                ),
                array('%d', '%s', '%d', '%s', '%s')
            );

        } else {
            // Queue it
            $queue[] = [
                'plan'   => $planName,
                'originalPeriod' => $originalPeriod,
                'period' => $period_raw,
                'from'   => $start_dt->format('Y-m-d H:i:s'),
                'expiry' => $expiry_dt->format('Y-m-d H:i:s'),
            ];

            update_user_meta($user_id, 'subscription_queue', $queue);

            $wpdb->insert(
                "{$wpdb->prefix}author_notifications",
                [
                    'user_id'    => $user_id,
                    'type'       => 'subscription_queued',
                    'seen'       => 0,
                    'message'    => 'Your subscription plan ' . $planName . ' added to queue.',
                    'created_at' => current_time('mysql')
                ],
                ['%d','%s','%d','%s','%s']
            );
        }
    }

    function process_subscription_queue($user_id) {

        $now_ts = current_time('timestamp');

        $active_expiry = get_user_meta($user_id, 'subscription_active_expiry', true);
        if ($active_expiry && strtotime($active_expiry) > $now_ts) {
            return;
        }

        $queue = get_user_meta($user_id, 'subscription_queue', true);
        
        if (empty($queue) || !is_array($queue)) {
            delete_user_meta($user_id, 'subscription_active_plan');
            delete_user_meta($user_id, 'subscription_active_period');
            delete_user_meta($user_id, 'subscription_active_from');
            delete_user_meta($user_id, 'subscription_active_expiry');
            return;
        }

        $next = array_shift($queue);

        update_user_meta($user_id, 'subscription_active_plan', $next['plan']);
        update_user_meta($user_id, 'subscription_active_period', $next['originalPeriod']);
        update_user_meta($user_id, 'subscription_active_from', $next['from']);
        update_user_meta($user_id, 'subscription_active_expiry', $next['expiry']);
        update_user_meta($user_id, 'subscription_queue', $queue);
    }

    add_action('init', function () {
        $users = get_users([
            'meta_key'     => 'subscription_active_from',
            'meta_compare' => 'EXISTS',
        ]);
        foreach ($users as $user) {
            process_subscription_queue($user->ID);
        }
    });

    // Schedule cron if not already scheduled
    // add_action('init', function () {

    //     if (!wp_next_scheduled('process_subscription_queue_cron')) {
    //         wp_schedule_event(time(), 'hourly', 'process_subscription_queue_cron');
    //     }

    // });

    // add_action('init', function () {

    //     if (!wp_next_scheduled('process_subscription_queue_cron')) {
    //         wp_schedule_event(time(), 'every_minute', 'process_subscription_queue_cron');
    //     }

    // });

    // add_filter('cron_schedules', function ($schedules) {
    //     if (!isset($schedules['every_minute'])) {
    //         $schedules['every_minute'] = [
    //             'interval' => 60,
    //             'display'  => 'Every Minute',
    //         ];
    //     }
    //     return $schedules;
    // });

    // add_action('init', function () {
    //     if (isset($_GET['run_cron'])) {
    //         do_action('process_subscription_queue_cron');
    //         exit('Cron executed manually');
    //     }
    // });

    // add_action('process_subscription_queue_cron', 'run_subscription_queue_cron');

    // function run_subscription_queue_cron() {

    //     $users = get_users([
    //         'meta_key'     => 'subscription_queue',
    //         'meta_compare' => 'EXISTS',
    //     ]);

    //     if (empty($users)) {
    //         return;
    //     }

    //     foreach ($users as $user) {
    //         process_subscription_queue($user->ID);
    //     }
    // }

    // add_action('switch_theme', function () {
    //     wp_clear_scheduled_hook('process_subscription_queue_cron');
    // });

add_action('wp_ajax_log_razorpay_failure', 'log_razorpay_failure');
add_action('wp_ajax_nopriv_log_razorpay_failure', 'log_razorpay_failure');

function log_razorpay_failure() {
    global $wpdb;
    $table = $wpdb->prefix . 'coin_transactions';

    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    $razorpay_order_id = $_POST['razorpay_order_id'];
    $amount = $_POST['amount'];
    $api = $_POST['api'];

    $wpdb->insert(
        $table,
        [
            'user_id'        => get_current_user_id(),
            'payment_id'     => $razorpay_payment_id,
            'amount'         => $amount,
            'pay_for'        => $api,
            'created_at'     => current_time('mysql'),
            'payment_status' => 'failed',
            'transaction_id' => $razorpay_order_id,
            'subscription_plan' => $api === 'subscription' ? $_POST['name'] : null,
        ],
        [
            '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s'
        ]
    );
}


