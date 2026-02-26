<?php
// 1. Register menu
add_action('admin_menu', function () {
    add_menu_page(
        'Add Coin Transaction',
        'Add Coin Transaction',
        'manage_options',
        'add-missing-tx',
        'render_missing_tx_page',
        'dashicons-update',
        27
    );
});

// 2. Render page
function render_missing_tx_page() {
    ?>
    <div class="wrap">
        <h1>Add Missing Transaction Details</h1>
        <form id="missing-tx-form">
            <table class="form-table">
                <tr>
                    <th><label for="user_id">Select User</label></th>
                    <td>
                        <select id="user_id" name="user_id" style="width:300px">
                            <option value="">-- Select User --</option>
                        </select>
                        <p class="description">Search and select active user</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="amount">Amount</label></th>
                    <td><input type="number" id="amount" name="amount" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th><label for="coin">Coins</label></th>
                    <td><input type="number" id="coin" name="coin" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th><label for="payment_id">Payment ID</label></th>
                    <td><input type="text" id="payment_id" name="payment_id" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th><label for="order_id">Order ID</label></th>
                    <td><input type="text" id="order_id" name="order_id" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th><label for="date">Date</label></th>
                    <td><input type="datetime-local" id="date" name="date" class="regular-text" required /></td>
                </tr>
            </table>

            <button type="submit" class="button button-primary">Submit</button>
        </form>
        <div id="response-msg" style="margin-top:15px"></div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    jQuery(function($) {
        // Load active users into dropdown using AJAX
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: { action: 'fetch_active_users' },
            success: function(res) {
                if (res.success) {
                    let options = '<option value="">-- Select User --</option>';
                    res.data.users.forEach(u => {
                        options += `<option value="${u.id}">${u.name} (${u.email})</option>`;
                    });
                    $('#user_id').html(options);
                    $('#user_id').select2({ placeholder: 'Search user...', allowClear: true });
                }
            }
        });

        // Form submit
        $('#missing-tx-form').on('submit', function(e) {
            e.preventDefault();
            $('#response-msg').html('Processing...');

            const formData = $(this).serialize();
            $.post(ajaxurl, formData + '&action=submit_missing_tx', function(res) {
                if (res.success) {
                    $('#response-msg').html('<div style="color:green">'+res.data.message+'</div>');
                    $('#missing-tx-form')[0].reset();   // Clear form
                    $('#user_id').val(null).trigger('change');
                } else {
                    $('#response-msg').html('<div style="color:red">'+res.data.message+'</div>');
                }
            });
        });
    });
    </script>
    <?php
}

// 3. Fetch active users AJAX
add_action('wp_ajax_fetch_active_users', function () {
    $users = get_users([
        'fields' => ['ID','user_login','user_email']
    ]);

    $list = [];
    foreach ($users as $u) {
        $list[] = [
            'id' => $u->ID,
            'name' => $u->user_login,
            'email' => $u->user_email
        ];
    }

    wp_send_json_success(['users' => $list]);
});

// 4. Handle form submit AJAX
add_action('wp_ajax_submit_missing_tx', function () {
    global $wpdb;
    $table = $wpdb->prefix . 'coin_transactions';

    $user_id = (int) $_POST['user_id'];
    $amount = (int) $_POST['amount'];
    $coins  = (int) $_POST['coin'];
    $payment_id = sanitize_text_field($_POST['payment_id']);
    $order_id   = sanitize_text_field($_POST['order_id']);
    $date_input = sanitize_text_field($_POST['date']);

    if (!$user_id) {
        wp_send_json_error(['message' => 'Please select a user']);
    }

    // Update coin balance for selected user
    $current = (int) get_user_meta($user_id, 'user_coin_balance', true) ?? 0;
    $new_total = $current + $coins;
    update_user_meta($user_id, 'user_coin_balance', $new_total);

    // Insert transaction
    $wpdb->insert(
        $table,
        [
            'user_id'         => $user_id,
            'payment_id'      => $payment_id,
            'transaction_id'  => $order_id,
            'amount'          => $amount,
            'pay_for'         => 'coin',
            'purchased_coins' => $coins,
            'created_at'      => date('Y-m-d H:i:s', strtotime($date_input)),
            'payment_status'  => 'success'
        ],
        ['%d','%s','%s','%d','%s','%d','%s','%s']
    );

    if ($wpdb->last_error) {
        wp_send_json_error(['message' => 'DB Insert failed: '.$wpdb->last_error]);
    }

    wp_send_json_success(['message' => 'Transaction added and coin balance updated']);
});


// 1. Register Admin Menu
add_action('admin_menu', function () {
    add_menu_page(
        'Add Subscription Transaction',
        'Add Subscription Transaction',
        'manage_options',
        'add-missing-transaction',
        'render_missing_subscription_transaction_page',
        'dashicons-database-add',
        27
    );
});

// 2. Render Admin Page
function render_missing_subscription_transaction_page() {
    $plans = get_option('plan_details');
?>
<div class="wrap">
    <h2>Add Missing Transaction</h2>
    <form method="POST">
        <?php wp_nonce_field('missing_txn_action','missing_txn_nonce'); ?>

        <table class="form-table">
            <tr>
                <th><label for="user_id">Select User</label></th>
                <td>
                    <select id="user_id" name="user_id" style="width:300px">
                        <option value="">-- Select User --</option>
                    </select>
                    <p class="description">Search and select active user</p>
                </td>
            </tr>

            <tr><th>Amount</th><td><input type="number" name="amount" required></td></tr>
            <tr><th>Payment ID</th><td><input type="text" name="payment_id" required></td></tr>
            <tr><th>Order ID</th><td><input type="text" name="order_id" required></td></tr>

            <tr>
                <th>Subscription Plan</th>
                <td>
                    <select name="plan_name" required>
                        <option value="">Select Plan</option>
                        <?php foreach ($plans as $p): ?>
                            <option value="<?= esc_html($p['name']); ?>"><?= esc_html($p['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th>Plan Period</th>
                <td>
                    <select name="plan_period" required>
                        <option value="">Select Period</option>
                        <?php foreach ($plans as $p): ?>
                            <option value="<?= esc_html($p['period']); ?>"><?= esc_html($p['period']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <p><button class="button button-primary">Submit</button></p>
    </form>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    jQuery(function($) {
        // Load active users into dropdown using AJAX
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: { action: 'fetch_active_users' },
            success: function(res) {
                if (res.success) {
                    let options = '<option value="">-- Select User --</option>';
                    res.data.users.forEach(u => {
                        options += `<option value="${u.id}">${u.name} (${u.email})</option>`;
                    });
                    $('#user_id').html(options);
                    $('#user_id').select2({ placeholder: 'Search user...', allowClear: true });
                }
            }
        });
    });
    </script>
<?php

// 3. Fetch active users AJAX
add_action('wp_ajax_fetch_active_users', function () {
    $users = get_users([
        'fields' => ['ID','user_login','user_email']
    ]);

    $list = [];
    foreach ($users as $u) {
        $list[] = [
            'id' => $u->ID,
            'name' => $u->user_login,
            'email' => $u->user_email
        ];
    }

    wp_send_json_success(['users' => $list]);
});

    // 3. Handle Form Submit
    if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['missing_txn_nonce'])) {
        if (!wp_verify_nonce($_POST['missing_txn_nonce'], 'missing_txn_action')) {
            echo "<div class='error'><p>Invalid Request</p></div>";
            return;
        }

        $user_id = intval($_POST['user_id']);
        $amount  = intval($_POST['amount']);
        $payment_id = sanitize_text_field($_POST['payment_id']);
        $order_id   = sanitize_text_field($_POST['order_id']);
        $planName = sanitize_text_field($_POST['plan_name']);
        $period   = sanitize_text_field($_POST['plan_period']);

        // Call your subscription activation
        activate_subscription($amount, $period, $planName, $payment_id, $order_id, $user_id);

        echo "<div class='updated'><p>Transaction added & Subscription updated</p></div>";
        exit;
    }
}

