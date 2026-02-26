<?php

// Register Admin Menu
add_action('admin_menu', 'register_subscription_admin_menu');
function register_subscription_admin_menu() {
    add_menu_page(
        'User Subscriptions',
        'User Subscriptions',
        'manage_options',
        'subscription-user-list',
        'render_subscription_user_list_admin',
        'dashicons-tickets',
        26
    );
}

// Render Admin Page
function render_subscription_user_list_admin() {
    global $wpdb;
    $users = get_users(['fields' => ['ID', 'user_login', 'display_name', 'user_email']]);

    echo '<div class="wrap"><h1>User Subscription & Payment Details</h1><hr></div>';

    echo '<input type="text" id="tableSearch" placeholder="Search..." style="width:300px; padding:5px; border:1px solid #ccc; border-radius:6px; margin-bottom:10px; float:right">';

    echo '<table id="userSubscriptionTable" class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Payment Status</th>
                <th>Amount</th>
                <th>Transaction ID</th>
                <th>Paid On</th>
                <th>Subscription Plan</th>
                <th>Active Plan (From → To)</th>
                <th>Upcoming Queue</th>
            </tr>
          </thead><tbody>';

    foreach ($users as $u) {
        $user_id = $u->ID;

        // Fetch all subscription payments for this user
        $sub_txs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT payment_status, subscription_plan, amount, transaction_id, created_at 
                 FROM {$wpdb->prefix}coin_transactions
                 WHERE user_id = %d AND pay_for = 'subscription'
                 ORDER BY created_at DESC",
                $user_id
            )
        );

        // If no subscription payments → still show user, but empty payment columns
        if (empty($sub_txs)) {
            continue;
        }

        // Subscription active plan details
        $from = get_user_meta($user_id, 'subscription_active_from', true);
        $to   = get_user_meta($user_id, 'subscription_active_expiry', true);
        $plan = get_user_meta($user_id, 'subscription_active_plan', true);

        $plan_label = ($plan && $from && $to)
            ? '<strong>' . esc_html($plan) . '</strong><br>' . esc_html($from) . ' → <br>' . esc_html($to)
            : 'No active plan';

        // Upcoming queue details
        $queue = get_user_meta($user_id, 'subscription_queue', true);
        $queue_label = '';
        if (is_array($queue) && !empty($queue)) {
            foreach ($queue as $q) {
                $queue_label .= '• <strong>' . esc_html($q['plan']) . '</strong> (' . esc_html($q['originalPeriod']) . ')<br>';
            }
        } else {
            $queue_label = 'No queued plans';
        }

        // Print a row for each payment
        foreach ($sub_txs as $tx) {
            echo '<tr>
                    <td>' . esc_html($u->user_login) . ' (' . esc_html($u->display_name) . ')</td>
                    <td>' . esc_html($u->user_email) . '</td>
                    <td>
                        <span style="
                            display:inline-block;
                            padding:4px 10px;
                            font-size:12px;
                            font-weight:600;
                            border-radius:6px;
                            color:#fff;
                            background: '. ($tx->payment_status === "success" ? "#2b8d42ff" : "#aa1726ff") . ';
                        ">
                            ' . esc_html(strtoupper($tx->payment_status)) . '
                        </span>
                    </td>
                    <td>₹' . esc_html($tx->amount) . '</td>
                    <td>' . esc_html($tx->transaction_id) . '</td>
                    <td>' . esc_html($tx->created_at) . '</td>
                    <td>' . esc_html($tx->subscription_plan) . '</td>
                    <td>' . $plan_label . '</td>
                    <td>' . $queue_label . '</td>
                  </tr>';
        }
    }

    echo '</tbody></table>';

    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("tableSearch").addEventListener("keyup", function() {
                let filter = this.value.toLowerCase();
                let rows = document.querySelectorAll("#userSubscriptionTable tbody tr");

                rows.forEach(row => {
                    let text = row.innerText.toLowerCase();
                    row.style.display = text.includes(filter) ? "" : "none";
                });
            });
        });
    </script>
    <?php
}

// User Coin Transaction
add_action('admin_menu', 'register_user_coin_admin_menu');
function register_user_coin_admin_menu() {
    add_menu_page(
        'User Coin Transactions',
        'User Coin Transactions',
        'manage_options',
        'user-coin-transaction-list',
        'render_coin_user_list_admin',
        'dashicons-tickets',
        26
    );
}

// Render Admin Page
function render_coin_user_list_admin() {
    global $wpdb;
    $users = get_users(['fields' => ['ID', 'user_login', 'display_name', 'user_email']]);

    echo '<div class="wrap"><h1>User Coin Transactions & Payment Details</h1><hr></div>';
    echo '<input type="text" id="userCoinTransactionSearch" placeholder="Search..." style="width:300px; padding:5px; border:1px solid #ccc; border-radius:6px; margin-bottom:10px; float:right">';

    echo '<table id="userCoinTransactionTable" class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Payment Status</th>
                <th>Amount</th>
                <th>Coin</th>
                <th>Transaction ID</th>
                <th>Paid On</th>
            </tr>
          </thead><tbody>';

    foreach ($users as $u) {
        $user_id = $u->ID;

        // Fetch all subscription payments for this user
        $sub_txs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT payment_status, amount, transaction_id, created_at, purchased_coins 
                 FROM {$wpdb->prefix}coin_transactions
                 WHERE user_id = %d AND pay_for = 'coin'
                 ORDER BY created_at DESC",
                $user_id
            )
        );

        // If no subscription payments → still show user, but empty payment columns
        if (empty($sub_txs)) {
            continue;
        }

        // Print a row for each payment
        foreach ($sub_txs as $tx) {
            echo '<tr>
                    <td>' . esc_html($u->user_login) . ' (' . esc_html($u->display_name) . ')</td>
                    <td>' . esc_html($u->user_email) . '</td>
                    <td>
                        <span style="
                            display:inline-block;
                            padding:4px 10px;
                            font-size:12px;
                            font-weight:600;
                            border-radius:6px;
                            color:#fff;
                            background: '. ($tx->payment_status === "success" ? "#2b8d42ff" : "#aa1726ff") . ';
                        ">
                            ' . esc_html(strtoupper($tx->payment_status)) . '
                        </span>
                    </td>
                    <td>₹' . esc_html($tx->amount) . '</td>
                    <td>' . esc_html($tx->purchased_coins) . '</td>
                    <td>' . esc_html($tx->transaction_id) . '</td>
                    <td>' . esc_html($tx->created_at) . '</td>
                  </tr>';
        }
    }

    echo '</tbody></table>';

    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("userCoinTransactionSearch").addEventListener("keyup", function() {
                let filter = this.value.toLowerCase();
                let rows = document.querySelectorAll("#userCoinTransactionTable tbody tr");

                rows.forEach(row => {
                    let text = row.innerText.toLowerCase();
                    row.style.display = text.includes(filter) ? "" : "none";
                });
            });
        });
    </script>
    <?php
}