<?php
add_action('rest_api_init', function () {
    register_rest_route('razorpay/v1', '/webhook', [
        'methods'  => ['GET', 'POST'],
        'callback' => 'handle_razorpay_webhook',
        'permission_callback' => '__return_true'
    ]);
});

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
    error_log('order create start');
    require_once get_template_directory() . '/inc/razorpay-php/Razorpay.php';

    $api = new Razorpay\Api\Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

    $amount  = (int) $_POST['amount']; // paise
    $pay_for = sanitize_text_field($_POST['pay_for'] ?? 'coin');
    $user_id = get_current_user_id();

    $receipt_prefix = ($pay_for === 'subscription') ? 'subscription_' : 'coin_';

    $order = $api->order->create([
        'receipt'         => $receipt_prefix . time(),
        'amount'          => $amount,
        'currency'        => 'INR',
        'payment_capture' => 1, // <-- Make sure this is 1 (auto capture)
        'notes'           => [
            'pay_for'   => $pay_for,
            'user_id'   => $user_id,
            'coins'     => (int) ($_POST['coins'] ?? 0),
            'plan_name' => sanitize_text_field($_POST['plan_name'] ?? ''),
            'period'    => sanitize_text_field($_POST['period'] ?? ''),
        ],
    ]);

    error_log('order created: ' . print_r($order, true));

    wp_send_json_success(['order_id' => $order['id']]);
}

// Code for auto payment start
// add_action('wp_ajax_create_razorpay_subscription', 'create_razorpay_subscription');

// function create_razorpay_subscription() {
//     if (!is_user_logged_in()) {
//         wp_send_json_error(['message' => 'Login required']);
//     }

//     require_once get_template_directory() . '/inc/razorpay-php/Razorpay.php';
//     $api = new Razorpay\Api\Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

//     $user_id   = get_current_user_id();
//     $plan_id   = sanitize_text_field($_POST['plan_id'] ?? '');
//     $plan_name = sanitize_text_field($_POST['plan_name'] ?? '');
//     $period    = sanitize_text_field($_POST['period'] ?? '');

//     if (!$plan_id) {
//         wp_send_json_error(['message' => 'plan_id missing']);
//     }

//     $sub = $api->subscription->create([
//         'plan_id' => $plan_id,
//         'customer_notify' => 1,
//         'total_count' => 120,
//         'notes' => [
//             'user_id' => (string) $user_id,
//             'pay_for' => 'subscription',
//             'plan_name' => $plan_name,
//             'period' => $period,
//         ]
//     ]);

//     wp_send_json_success(['subscription_id' => $sub['id']]);
// }
// Code for auto payment end

// add_action('wp_ajax_verify_razorpay_payment', 'verify_razorpay_payment');
// add_action('wp_ajax_nopriv_verify_razorpay_payment', 'verify_razorpay_payment');

// function verify_razorpay_payment() {

//     require_once get_template_directory() . '/inc/razorpay-php/Razorpay.php';

//     $keyId     = RAZORPAY_KEY_ID;
//     $keySecret = RAZORPAY_KEY_SECRET;

//     $api = new \Razorpay\Api\Api($keyId, $keySecret);

//     $attributes = [
//         'razorpay_order_id'   => $_POST['razorpay_order_id'],
//         'razorpay_payment_id' => $_POST['razorpay_payment_id'],
//         'razorpay_signature'  => $_POST['razorpay_signature'],
//     ];

//     $razorpay_payment_id = $_POST['razorpay_payment_id'];
//     $razorpay_order_id = $_POST['razorpay_order_id'];
//     $amount  = intval($_POST['amount']);
//     $coins  = $_POST['coins'];

//     $user_id = get_current_user_id();

//     error_log('Razorpay payment verification started');
//     error_log(print_r($_POST, true));

//     try {
//         $api->utility->verifyPaymentSignature($attributes);
//         error_log('Signature verified successfully');

//         global $wpdb;
//         $table = $wpdb->prefix . 'coin_transactions';
//         $exists = $wpdb->get_var(
//             $wpdb->prepare(
//                 "SELECT COUNT(*) FROM {$table} 
//                 WHERE transaction_id = %s AND payment_status = 'success'",
//                 $razorpay_order_id
//             )
//         );

//         if ($exists) {
//             wp_send_json_success(['message' => 'Already processed']);
//         }

//         error_log('Signature verified successfully1');

//         if (isset($_POST['api']) && $_POST['api'] == 'subscription') {
//             activate_subscription($amount, $_POST['period'], $_POST['name'], $razorpay_payment_id, $razorpay_order_id, $user_id);
//         }

//         if (isset($_POST['api']) && $_POST['api'] == 'coin') {
//             credit_coins_after_payment($amount, $razorpay_payment_id, $razorpay_order_id, $coins, $user_id);            
//         }

//         wp_send_json_success();

//     } catch (Exception $e) {
//         error_log('Signature verification failed: ' . $e->getMessage());

//         global $wpdb;
//         $table = $wpdb->prefix . 'coin_transactions';

//         $wpdb->insert(
//             $table,
//             [
//                 'user_id'        => get_current_user_id(),
//                 'payment_id'     => $razorpay_payment_id,
//                 'transaction_id' => $razorpay_order_id,
//                 'amount'         => $amount,
//                 'pay_for'        => 'coin',
//                 'purchased_coins' => $coins,
//                 'created_at'     => current_time('mysql'),
//                 'payment_status' => 'failed'
//             ],
//             [
//                 '%d', '%s', '%s', '%d', '%s', '%d', '%s', '%s'
//             ]
//         );

//         wp_send_json_error(['message' => 'Verification failed']);
//     }
// }

function credit_coins_after_payment($amount, $payment_id, $order_id, $coins, $user_id) {
    error_log('coin payment success, crediting coins');
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

    // Send coin success email
    $to = novel_get_email_for_user($user_id);
    if ($to) {
        novel_mail_coin_success($to);
    }
}

function activate_subscription($amount, $period, $name, $payment_id, $order_id, $user_id) {
    error_log('subscription payment success, activating subscription');
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

    // Send subscription success email
    $to = novel_get_email_for_user($user_id);
    if ($to) {
        novel_mail_subscription_success($to);
    }
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
            return; // still active
        }

        $queue = get_user_meta($user_id, 'subscription_queue', true);

        // No next plan => mark expired, keep last expiry for reminders/history
        if (empty($queue) || !is_array($queue)) {

            // Keep last known expiry (for reminders + history)
            if (!empty($active_expiry)) {
                update_user_meta($user_id, 'subscription_last_expiry', $active_expiry);
            }

            update_user_meta($user_id, 'subscription_status', 'expired');

            // Optional: clear plan fields (OK), but DO NOT delete expiry if you rely on it
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
        update_user_meta($user_id, 'subscription_status', 'active');
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

// add_action('wp_ajax_log_razorpay_failure', 'log_razorpay_failure');
// add_action('wp_ajax_nopriv_log_razorpay_failure', 'log_razorpay_failure');

// function log_razorpay_failure() {
//     global $wpdb;
//     $table = $wpdb->prefix . 'coin_transactions';

//     $razorpay_payment_id = $_POST['razorpay_payment_id'];
//     $razorpay_order_id = $_POST['razorpay_order_id'];
//     $amount = $_POST['amount'];
//     $api = $_POST['api'];

//     $wpdb->insert(
//         $table,
//         [
//             'user_id'        => get_current_user_id(),
//             'payment_id'     => $razorpay_payment_id,
//             'amount'         => $amount,
//             'pay_for'        => $api,
//             'created_at'     => current_time('mysql'),
//             'payment_status' => 'failed',
//             'transaction_id' => $razorpay_order_id,
//             'subscription_plan' => $api === 'subscription' ? $_POST['name'] : null,
//         ],
//         [
//             '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s'
//         ]
//     );
// }

function handle_razorpay_webhook(WP_REST_Request $request) {
    error_log('Webhook called');

    if (!defined('RAZORPAY_WEBHOOK_SECRET') || RAZORPAY_WEBHOOK_SECRET === '') {
        error_log('Webhook secret not configured');
        return new WP_REST_Response(['status' => 'error', 'message' => 'Webhook secret not configured'], 500);
    }

    $webhook_secret = RAZORPAY_WEBHOOK_SECRET;
    $payload   = (string) $request->get_body();
    $signature = (string) $request->get_header('x-razorpay-signature');

    // Check if signature or payload is missing
    if ($payload === '' || $signature === '') {
        error_log('Webhook: missing payload or signature');
        return new WP_REST_Response(['status' => 'error', 'message' => 'Missing payload or signature'], 400);
    }

    require_once get_template_directory() . '/inc/razorpay-php/Razorpay.php';
    $api = new Razorpay\Api\Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

    try {
        $api->utility->verifyWebhookSignature($payload, $signature, $webhook_secret);
        error_log('signature verified');
        $data = json_decode($payload, true);

        if (empty($data['event']) || empty($data['payload']['payment']['entity'])) {
            return new WP_REST_Response(['status' => 'ignored'], 200);
        }

        $event   = $data['event'];
        $payment = $data['payload']['payment']['entity'];

        $payment_id = $payment['id'] ?? '';
        $order_id   = $payment['order_id'] ?? '';
        $amount     = isset($payment['amount']) ? ((int) $payment['amount'] / 100) : 0; // rupees
        $email      = $payment['email'] ?? '';

        $order = !empty($order_id) ? $api->order->fetch($order_id) : [];
        $notes = $order['notes'] ?? [];
        $receipt = $order['receipt'] ?? '';

        $pay_for = $notes['pay_for'] ?? (
            strpos($receipt, 'subscription_') === 0 ? 'subscription' :
            (strpos($receipt, 'coin_') === 0 ? 'coin' : 'unknown')
        );

        $user_id = (int) ($notes['user_id'] ?? 0);
        if (!$user_id && !empty($email)) {
            $user = get_user_by('email', $email);
            $user_id = $user ? (int) $user->ID : 0;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'coin_transactions';

        // idempotency
        $already = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE payment_id = %s",
                $payment_id
            )
        );
        if ($already > 0) {
            return new WP_REST_Response(['status' => 'duplicate'], 200);
        }

        if ($event === 'payment.captured') {
            if ($pay_for === 'coin') {
                $coins = (int) ($notes['coins'] ?? 0);
                credit_coins_after_payment($amount, $payment_id, $order_id, $coins, $user_id);
            } elseif ($pay_for === 'subscription') {
                $plan_name = sanitize_text_field($notes['plan_name'] ?? '');
                $period    = sanitize_text_field($notes['period'] ?? '');
                activate_subscription($amount, $period, $plan_name, $payment_id, $order_id, $user_id);
            } else {
                $wpdb->insert($table, [
                    'user_id' => $user_id,
                    'payment_id' => $payment_id,
                    'transaction_id' => $order_id,
                    'amount' => $amount,
                    'pay_for' => 'unknown',
                    'purchased_coins' => 0,
                    'created_at' => current_time('mysql'),
                    'payment_status' => 'success',
                ], ['%d','%s','%s','%d','%s','%d','%s','%s']);
            }
        }

        if ($event === 'payment.failed') {
            $wpdb->insert($table, [
                'user_id' => $user_id,
                'payment_id' => $payment_id,
                'transaction_id' => $order_id,
                'amount' => $amount,
                'pay_for' => $pay_for,
                'purchased_coins' => 0,
                'created_at' => current_time('mysql'),
                'payment_status' => 'failed',
                'subscription_plan' => ($pay_for === 'subscription') ? ($notes['plan_name'] ?? '') : null,
            ], ['%d','%s','%s','%d','%s','%d','%s','%s','%s']);

            // Send subscription failed notifications (only for subscription)
            if ($pay_for === 'subscription') {
                $to = novel_get_email_for_user($user_id, $email);
                if ($to) {
                    novel_mail_subscription_failed($to);
                }

                // WhatsApp (non-template text works only if WhatsApp allows it for this user/session)
                // if (function_exists('novel_send_whatsapp_text')) {
                //     $wa_phone = (string) get_user_meta($user_id, 'user_whatsapp', true);

                //     if ($wa_phone === '' && !empty($payment['contact'])) {
                //         $wa_phone = (string) $payment['contact'];
                //     }

                //     $wa_message = <<<TXT
                //         அன்புள்ள வாசகரே,

                //         நீங்கள் சமீபத்தில் Subscription எடுக்க முயற்சி செய்துள்ளீர்கள். ஆனால், கட்டணம் செலுத்தும் செயல்முறை வெற்றியடையவில்லை.

                //         தயவுசெய்து மீண்டும் ஒரு முறை Subscription எடுக்க முயற்சி செய்யவும்.

                //         ⚠️ முக்கிய அறிவிப்பு:
                //         Facebook அல்லது Instagram app-இல் இருந்து link-ஐ நேரடியாக click செய்து முயற்சி செய்ய வேண்டாம்.

                //         அதற்குப் பதிலாக, அந்த link-ஐ copy செய்து உங்கள் மொபைல் browser (Google Chrome, Safari போன்றவை) மூலம் திறந்து Subscription செய்ய முயற்சி செய்யவும்.

                //         சில நேரங்களில் social media app-இல் திறக்கும் போது payment பிரச்சினைகள் ஏற்படலாம். Browser மூலம் முயற்சி செய்தால் சரியாக செயல்படும்.

                //         🌍 International users-க்கு:
                //         Debit / Credit card payment மட்டுமே வேலை செய்யும்.
                //         அதனால், உங்கள் card-ல் international payment enabled ஆக இருக்க வேண்டும்.

                //         Subscription Link - https://lovebeatnovels.com/subscription/

                //         இன்னும் ஏதேனும் பிரச்சினை இருந்தால், எங்களை தொடர்பு கொள்ள தயங்க வேண்டாம்.

                //         உங்கள் ஆதரவுக்கு நன்றி. ❤️
                //         Thanks & Regards,
                //         Sarmi SS
                //         Author | Content Editor
                //         Whatsapp - +916374401933
                //         Instagram: https://www.instagram.com/sarmi_ss/
                //         Facebook: https://www.facebook.com/Sarmi.SSfan
                //         Website: https://lovebeatnovels.com
                //         TXT;

                //     if ($wa_phone !== '') {
                //         novel_send_whatsapp_text($wa_phone, $wa_message);
                //     }
                // }
            }
        }

        return new WP_REST_Response(['status' => 'ok'], 200);
    } catch (Exception $e) {
        error_log('Webhook verify failed: ' . $e->getMessage());
        return new WP_REST_Response(['status' => 'invalid signature'], 400);
    }
}

/**
 * Daily subscription reminder cron
 * - 3 days before expiry: send once per day
 * - 3 days after expiry: send once per day
 */

/**
 * Run reminder check once on page load (logged-in user).
 * IMPORTANT: keep only ONE init hook for this.
 */
add_action('init', function () {
    if (!is_user_logged_in()) return;
    check_subscription_reminder(get_current_user_id());
});

/**
 * Atomic per-user lock (prevents duplicates on concurrent requests).
 */
if (!function_exists('novel_user_daily_lock')) {
    function novel_user_daily_lock($user_id, $kind, $today) {
        $user_id = (int) $user_id;
        $kind    = preg_replace('/[^a-z0-9_\-]/i', '', (string) $kind);
        $today   = preg_replace('/[^0-9\-]/', '', (string) $today);

        if ($user_id <= 0 || $kind === '' || $today === '') return false;

        // unique meta key per user per day per kind
        $lock_key = "novel_sub_reminder_lock_{$kind}_{$today}";

        // add_user_meta with $unique=true is atomic in DB (prevents races)
        return add_user_meta($user_id, $lock_key, 1, true) ? $lock_key : false;
    }
}

function check_subscription_reminder($user_id) {
    if (!function_exists('novel_mail_subscription_renewal_reminder')) return;

    $user_id = (int) $user_id;
    if ($user_id <= 0) return;

    $today  = current_time('Y-m-d');
    $now_ts = current_time('timestamp');
    $tz     = wp_timezone();

    $expiry_str = (string) get_user_meta($user_id, 'subscription_active_expiry', true);
    if ($expiry_str === '') {
        $expiry_str = (string) get_user_meta($user_id, 'subscription_last_expiry', true);
    }
    if ($expiry_str === '') return;

    $expiry_str = trim($expiry_str);

    // Parse both "Y-m-d H:i:s" and "Y-m-d"
    $expiry_dt = date_create_from_format('Y-m-d H:i:s', $expiry_str, $tz);
    if (!$expiry_dt) {
        $expiry_dt = date_create_from_format('Y-m-d', $expiry_str, $tz);
        if ($expiry_dt) $expiry_dt->setTime(23, 59, 59);
    }
    if (!$expiry_dt) {
        try { $expiry_dt = new DateTime($expiry_str, $tz); }
        catch (Exception $e) { return; }
    }

    $expiry_ts = (int) $expiry_dt->getTimestamp();

    // =========================
    // PRE: 3..1 days left
    // =========================
    if ($expiry_ts > $now_ts) {
        $days_left = (int) ceil(($expiry_ts - $now_ts) / DAY_IN_SECONDS);

        if ($days_left >= 1 && $days_left <= 3) {
            $last = (string) get_user_meta($user_id, 'novel_sub_reminder_pre_last', true);
            if ($last === $today) return;

            // Acquire lock to prevent duplicates
            $lock_key = novel_user_daily_lock($user_id, 'pre', $today);
            if ($lock_key === false) return;

            // Mark sent today immediately (prevents duplicates even under concurrency)
            update_user_meta($user_id, 'novel_sub_reminder_pre_last', $today);

            $to = novel_get_email_for_user($user_id);
            if ($to) {
                novel_mail_subscription_renewal_reminder($to, false);
            }

            if (function_exists('novel_add_author_notification')) {
                novel_add_author_notification(
                    $user_id,
                    'subscription_renewal_reminder_pre',
                    'உங்களின் subscription pack will expire in ' . $days_left . ' day(s) உங்கள் வாசிப்பில் interruption ஏற்படாமல் இருக்க, தயவுசெய்து உடனடியாக renew செய்யவும்.'
                );
            }

            // ❌ do not delete lock; next day key changes automatically
            // delete_user_meta($user_id, $lock_key);
        }

        return;
    }

    // =========================
    // POST: 0..2 days past
    // =========================
    $days_past = (int) floor(($now_ts - $expiry_ts) / DAY_IN_SECONDS);

    if ($days_past >= 0 && $days_past <= 2) {
        $last = (string) get_user_meta($user_id, 'novel_sub_reminder_post_last', true);
        if ($last === $today) return;

        $lock_key = novel_user_daily_lock($user_id, 'post', $today);
        if ($lock_key === false) return;

        update_user_meta($user_id, 'novel_sub_reminder_post_last', $today);

        $to = novel_get_email_for_user($user_id);
        if ($to) {
            novel_mail_subscription_renewal_reminder($to, true);
        }

        if (function_exists('novel_add_author_notification')) {
            novel_add_author_notification(
                $user_id,
                'subscription_renewal_reminder_post',
                'உங்களின் subscription pack expire ஆகிவிட்டது. உங்கள் வாசிப்பில் interruption ஏற்படாமல் இருக்க, தயவுசெய்து உடனடியாக renew செய்யவும்.'
            );
        }

        // ❌ do not delete lock
        // delete_user_meta($user_id, $lock_key);
    }
}

if (!function_exists('novel_add_author_notification')) {
    function novel_add_author_notification($user_id, $type, $message) {
        global $wpdb;

        $user_id = (int) $user_id;
        if ($user_id <= 0) return false;

        return (bool) $wpdb->insert(
            "{$wpdb->prefix}author_notifications",
            [
                'user_id'     => $user_id,
                'type'        => (string) $type,
                'seen'        => 0,
                'created_at'  => current_time('mysql'),
                'message'     => (string) $message,
            ],
            ['%d','%s','%d','%s','%s']
        );
    }
}
