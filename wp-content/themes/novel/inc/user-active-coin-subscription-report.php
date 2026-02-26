<?php

// Register Active Subscription Admin Menu
add_action('admin_menu', 'register_active_subscription_admin_menu');
function register_active_subscription_admin_menu() {
    add_menu_page(
        'User Active Subscriptions',
        'Active Subscriptions',
        'manage_options',
        'active-subscription-list',
        'render_active_subscription_admin',
        'dashicons-yes-alt',
        25
    );
}

function render_active_subscription_admin() {
    global $wpdb;

    $per_page = 25;
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($paged - 1) * $per_page;

    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    $where_search = '';

    if ($search) {
        $like = '%' . $wpdb->esc_like($search) . '%';

        $where_search = $wpdb->prepare("
            AND (
                u.user_login LIKE %s
                OR u.display_name LIKE %s
                OR u.user_email LIKE %s
                OR (um.meta_key = 'subscription_active_plan' AND um.meta_value LIKE %s)
                OR (um.meta_key = 'subscription_active_from' AND um.meta_value LIKE %s)
                OR (um.meta_key = 'subscription_active_expiry' AND um.meta_value LIKE %s)
            )
        ", $like, $like, $like, $like, $like, $like);
    }

    $today = current_time('Y-m-d');

    $total_items = $wpdb->get_var(
        $wpdb->prepare("
            SELECT COUNT(DISTINCT u.ID)
            FROM {$wpdb->users} u
            INNER JOIN {$wpdb->usermeta} plan_um ON plan_um.user_id = u.ID AND plan_um.meta_key = 'subscription_active_plan'
            INNER JOIN {$wpdb->usermeta} from_um ON from_um.user_id = u.ID AND from_um.meta_key = 'subscription_active_from'
            INNER JOIN {$wpdb->usermeta} to_um ON to_um.user_id = u.ID AND to_um.meta_key = 'subscription_active_expiry'
            INNER JOIN {$wpdb->prefix}coin_transactions ct 
                ON ct.user_id = u.ID
                AND ct.pay_for = 'subscription'
                AND ct.payment_status = 'success'
            WHERE to_um.meta_value >= %s
            " . ($search ? "AND (
                u.user_login LIKE %s
                OR u.display_name LIKE %s
                OR u.user_email LIKE %s
                OR plan_um.meta_value LIKE %s
                OR from_um.meta_value LIKE %s
                OR to_um.meta_value LIKE %s
            )" : "") . "
        ",
        $today,
        ...($search ? array_fill(0, 6, $like) : [])
        )
    );

    $total_pages = ceil($total_items / $per_page);

    $args = [$today];
    if ($search) {
        $args = array_merge($args, array_fill(0, 6, $like));
    }
    $args[] = $per_page;
    $args[] = $offset;

    $users = $wpdb->get_results(
        $wpdb->prepare("
            SELECT 
                u.ID,
                u.user_login,
                u.display_name,
                u.user_email,
                plan_um.meta_value AS plan,
                from_um.meta_value AS active_from,
                to_um.meta_value AS active_to,
                MAX(ct.created_at) AS latest_subscription_date
            FROM {$wpdb->users} u
            INNER JOIN {$wpdb->usermeta} plan_um ON plan_um.user_id = u.ID AND plan_um.meta_key = 'subscription_active_plan'
            INNER JOIN {$wpdb->usermeta} from_um ON from_um.user_id = u.ID AND from_um.meta_key = 'subscription_active_from'
            INNER JOIN {$wpdb->usermeta} to_um ON to_um.user_id = u.ID AND to_um.meta_key = 'subscription_active_expiry'
            INNER JOIN {$wpdb->prefix}coin_transactions ct 
                ON ct.user_id = u.ID
                AND ct.pay_for = 'subscription'
                AND ct.payment_status = 'success'
            WHERE to_um.meta_value >= %s
            " . ($search ? "AND (
                u.user_login LIKE %s
                OR u.display_name LIKE %s
                OR u.user_email LIKE %s
                OR plan_um.meta_value LIKE %s
                OR from_um.meta_value LIKE %s
                OR to_um.meta_value LIKE %s
            )" : "") . "
            GROUP BY u.ID
            ORDER BY latest_subscription_date DESC
            LIMIT %d OFFSET %d
        ", ...$args)
    );

    echo '<div class="wrap">
            <h1>User Active Subscriptions (' . esc_html($total_items) . ')</h1>
            <hr>
          </div>';

    echo '<form method="get" style="float:right; margin-bottom:10px;">
            <input type="hidden" name="page" value="active-subscription-list">

            <input type="text"
                name="s"
                value="'. esc_attr($_GET["s"] ?? "").'"
                placeholder="Search user...">

            <button class="button">Search</button>
        </form>';

    echo '<table class="wp-list-table widefat fixed striped" id="activeSubscriptionTable">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Subscription Plan</th>
                    <th>Active From</th>
                    <th>Active To</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';

    if (!empty($users)) {
        foreach ($users as $u) {

            $now = current_time('Y-m-d');
            $is_active = ($u->active_to >= $now);

            echo '<tr>
                    <td>
                        <strong>' . esc_html($u->user_login) . '</strong><br>
                        <small>' . esc_html($u->display_name) . '</small>
                    </td>
                    <td>' . esc_html($u->user_email) . '</td>
                    <td><strong>' . esc_html($u->plan) . '</strong></td>
                    <td>' . esc_html($u->active_from) . '</td>
                    <td>' . esc_html($u->active_to) . '</td>
                    <td>
                        <span style="
                            padding:4px 10px;
                            border-radius:6px;
                            font-size:12px;
                            font-weight:600;
                            color:#fff;
                            background:' . ($is_active ? '#2b8d42ff' : '#aa1726ff') . ';
                        ">
                            ' . ($is_active ? 'ACTIVE' : 'EXPIRED') . '
                        </span>
                    </td>
                  </tr>';
        }
    } else {
        echo '<tr><td colspan="6">No active subscriptions found.</td></tr>';
    }

    echo '</tbody></table>';

    if ($total_pages > 1) {
        echo '<div class="tablenav bottom">';
        echo '  <div class="tablenav-pages">';
        echo '<span class="displaying-num">' . esc_html($total_items) . ' items</span>';

        echo paginate_links([
            'base'      => add_query_arg([
                'paged' => '%#%',
                's'     => $search
            ]),
            'format'    => '',
            'prev_text' => '« Prev',
            'next_text' => 'Next »',
            'total'     => $total_pages,
            'current'   => $paged,
        ]);

        echo '  </div>';
        echo '</div>';
    }
    ?>

    <style>
    .tablenav-pages .page-numbers {
        border: solid 1px #ccc;
        padding: 10px;
    }
    .tablenav-pages .page-numbers.current {
        font-weight: 600;
    }
    </style>

    <?php
}

add_action('admin_menu', 'register_user_coin_active_menu');
function register_user_coin_active_menu() {
    add_menu_page(
        'User Coin Active List',
        'User Coin Active List',
        'manage_options',
        'user-coin-active-list',
        'render_user_coin_active_admin',
        'dashicons-money-alt',
        25
    );
}

function render_user_coin_active_admin() {
    global $wpdb;

    $per_page = 25;
    $paged    = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset   = ($paged - 1) * $per_page;
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    $where_search = '';

    if ($search) {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where_search = $wpdb->prepare(
            " AND (u.user_login LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s)",
            $like, $like, $like
        );
    }

    /* Total count */
    $total_items = $wpdb->get_var("
        SELECT COUNT(*)
        FROM {$wpdb->users} u
        INNER JOIN {$wpdb->usermeta} um 
            ON um.user_id = u.ID
            AND um.meta_key = 'user_coin_balance'
        WHERE CAST(um.meta_value AS UNSIGNED) > 0
        $where_search
    ");

    $total_pages = ceil($total_items / $per_page);

    echo '<div class="wrap">
            <h1>User Coin Active List <span style="color:#666;">(' . esc_html($total_items) . ')</span></h1>
            <hr>
          </div>';

    echo '<form method="get" style="float:right; margin-bottom:10px;">
            <input type="hidden" name="page" value="user-coin-active-list">

            <input type="text"
                name="s"
                value="'. esc_attr($_GET["s"] ?? "").'"
                placeholder="Search user...">

            <button class="button">Search</button>
        </form>';

    $users = $wpdb->get_results(
        $wpdb->prepare("
            SELECT 
                u.ID,
                u.user_login,
                u.display_name,
                u.user_email,
                CAST(um.meta_value AS UNSIGNED) AS coin_balance
            FROM {$wpdb->users} u
            INNER JOIN {$wpdb->usermeta} um 
                ON um.user_id = u.ID
                AND um.meta_key = 'user_coin_balance'
            WHERE CAST(um.meta_value AS UNSIGNED) > 0
            $where_search
            ORDER BY coin_balance DESC
            LIMIT %d OFFSET %d
        ", $per_page, $offset)
    );

    echo '<table class="wp-list-table widefat fixed striped" id="userCoinTable">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Coin Balance</th>
                </tr>
            </thead>
            <tbody>';

    if (!empty($users)) {
        foreach ($users as $u) {
            echo '<tr>
                    <td>
                        <strong>' . esc_html($u->user_login) . '</strong><br>
                        <small>' . esc_html($u->display_name) . '</small>
                    </td>
                    <td>' . esc_html($u->user_email) . '</td>
                    <td>
                        <strong style="color:#2271b1;">' . esc_html($u->coin_balance) . '</strong>
                    </td>
                  </tr>';
        }
    } else {
        echo '<tr><td colspan="3">No users with active coin balance.</td></tr>';
    }

    echo '</tbody></table>';

    if ($total_pages > 1) {
        echo '<div class="tablenav bottom">';
        echo '<div class="tablenav-pages">';
        echo '<span class="displaying-num">' . esc_html($total_items) . ' items</span>';

        echo paginate_links([
            'base'      => add_query_arg([
                'paged' => '%#%',
                's'     => $search
            ]),
            'format'    => '',
            'prev_text' => '« Prev',
            'next_text' => 'Next »',
            'total'     => $total_pages,
            'current'   => $paged,
        ]);

        echo '</div></div>';
    }
    ?>

    <style>
    .tablenav-pages .page-numbers {
        border: solid 1px #ccc;
        padding: 10px;
    }
    .tablenav-pages .page-numbers.current {
        font-weight: 600;
    }
    </style>

    <?php
}
