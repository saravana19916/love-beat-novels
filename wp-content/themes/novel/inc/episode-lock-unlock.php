<?php

// Episode lock settings start
add_action('admin_menu', function () {
    add_menu_page(
        'Episode Lock Settings',
        'Episode Lock',
        'manage_options',
        'episode-lock-settings',
        'episode_lock_settings_page',
        'dashicons-lock',
        80
    );
});

function episode_lock_settings_page() {
    ?>
    <div class="wrap">
        <h1>Episode Lock Settings</h1>

        <form method="post" action="options.php">
            <?php
            settings_fields('episode_lock_group');
            do_settings_sections('episode-lock-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function () {

    register_setting('episode_lock_group', 'global_episode_lock_after');

    add_settings_section(
        'episode_lock_section',
        'Lock Episodes After',
        function () {
            echo '<p>Set the episode number after which all episodes will be locked.</p>';
        },
        'episode-lock-settings'
    );

    add_settings_field(
        'global_episode_lock_after',
        'Episode Number',
        function () {
            $value = get_option('global_episode_lock_after', '');
            echo '<input type="number" name="global_episode_lock_after" value="' . esc_attr($value) . '" min="1" />';
        },
        'episode-lock-settings',
        'episode_lock_section'
    );

    /* ------------------------
        OPTION 2: UNLOCK SPECIFIC SERIES
    ------------------------- */
    register_setting('episode_lock_group', 'global_unlocked_series');

    add_settings_field(
        'global_unlocked_series',
        'Unlocked Series',
        function () {

            // Get selected series
            $selected = (array) get_option('global_unlocked_series', []);

            // Query all series taxonomy terms
            $blog_terms = ['main-blog', 'my-creation-blog', 'competition-blog'];
            $meta_query = array();

            // Add filter for main-blog
            if (in_array('main-blog', $blog_terms)) {
                $meta_query[] = array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'parent_blog_id',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key'     => 'parent_blog_id',
                        'value'   => '0',
                        'compare' => '=',
                    ),
                );
            }

            // Add filter for my-creation-blog
            if (in_array('my-creation-blog', $blog_terms)) {
                $meta_query[] = array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'my_creation_parent_blog_id',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key'     => 'my_creation_parent_blog_id',
                        'value'   => '0',
                        'compare' => '=',
                    ),
                );
            }

            // Add filter for competition-blog
            if (in_array('competition-blog', $blog_terms)) {
                $meta_query[] = array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'competition_parent_id',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key'     => 'competition_parent_id',
                        'value'   => '0',
                        'compare' => '=',
                    ),
                );
            }
            $args = array(
                'post_type'      => 'post',
                'tax_query'      => array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => ['novel', 'novels', 'நாவல்'],
                ),
                'meta_query'     => $meta_query,
                'meta_key'       => 'story_view_count',
                'orderby'        => 'meta_value_num',
                'posts_per_page' => -1,
                'paged'          => $paged,
            );

            $query  = new WP_Query($args);
$series = $query->posts;

echo '<p>Select series that should <strong>IGNORE episode locking</strong>.</p>';

echo '<div style="max-height:200px; overflow-y:auto; width:50%; border:1px solid #ccc; padding:8px;">';

foreach ($series as $post) {

    $checked = in_array($post->ID, $selected) ? 'checked' : '';

    echo '<label style="display:block; margin-bottom:5px;">';
    echo '<input type="checkbox" 
                 name="global_unlocked_series[]" 
                 value="' . esc_attr($post->ID) . '" 
                 ' . $checked . '> ';
    echo esc_html($post->post_title);
    echo '</label>';
}

echo '</div>';
        },
        'episode-lock-settings',
        'episode_lock_section'
    );
});
// Episode lock settings End

// Coin settings start
// Create a separate admin menu
add_action('admin_menu', function () {
    add_menu_page(
        'Coin Settings',                // Page title
        'Coin Settings',                // Menu title
        'manage_options',               // Capability
        'coin-settings-page',           // Menu slug
        'render_coin_settings_page',    // Callback
        'dashicons-money-alt',          // Icon
        81                              // Position
    );
});

// Render the settings page
function render_coin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Coin Settings</h1>

        <form method="post" action="options.php">
            <?php
            settings_fields('coin_settings_group');
            do_settings_sections('coin-settings-page');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function () {

    // register_setting('coin_settings_group', 'coin_price');
    register_setting('coin_settings_group', 'coins_to_unlock');

    add_settings_section(
        'coin_settings_section',
        'Configure Coin System',
        function () {
            echo '<p>Set coin price and how many coins required to unlock an episode.</p>';
        },
        'coin-settings-page'
    );

    // add_settings_field(
    //     'coin_price',
    //     'Price of 1 Coin',
    //     function () {
    //         $value = get_option('coin_price', '');
    //         echo '<input type="number" step="0.01" min="0" name="coin_price" value="' . esc_attr($value) . '" /> 
    //               <p class="description">Example: 1 coin = 5.00</p>';
    //     },
    //     'coin-settings-page',
    //     'coin_settings_section'
    // );

    add_settings_field(
        'coins_to_unlock',
        'Coins Needed to Unlock Episode',
        function () {
            $value = get_option('coins_to_unlock', '');
            echo '<input type="number" min="1" name="coins_to_unlock" value="' . esc_attr($value) . '" /> 
                  <p class="description">Example: Unlock requires 3 coins</p>';
        },
        'coin-settings-page',
        'coin_settings_section'
    );
});

// Coin settings end

// subscription plan settings start
add_action('admin_menu', function () {
    add_menu_page(
        'Subscription Plans',
        'Subscription Plans',
        'manage_options',
        'subscription-plans',
        'render_subscription_plans_page',
        'dashicons-list-view',
        82
    );
});

function render_subscription_plans_page() {
    ?>
    <div class="wrap">
        <h1>Subscription Plan Settings</h1>

        <form method="post" action="options.php">
            <?php
            settings_fields('subscription_plans_group');
            do_settings_sections('subscription-plans');
            submit_button('Save Subscription Plans');
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function () {

    register_setting('subscription_plans_group', 'plan_details');

    add_settings_section(
        'subscription_plans_section',
        'Manage Subscription Plans',
        function () {
            echo "<p>Set the details for each subscription plan.</p>";
        },
        'subscription-plans'
    );

    // PLAN LIST
    $plans = [
        'plan1' => 'Plan 1 (1 Month)',
        'plan2' => 'Plan 2 (3 Months)',
        'plan3' => 'Plan 3 (1 Year)',
        // 'plan4' => 'Plan 4 (Custom)' // You can enable if needed
    ];

    foreach ($plans as $key => $title) {

        add_settings_field(
            $key,
            $title,
            function () use ($key) {
                $plan = get_option('plan_details')[$key] ?? [
                    'name' => '',
                    'period' => '',
                    'price' => '',
                    'description' => ''
                ];
                ?>

                <div style="padding:15px; background:#fff; border:1px solid #ccc; margin-bottom:20px;">

                    <label><strong>Plan Name:</strong></label><br>
                    <input type="text" name="plan_details[<?php echo $key; ?>][name]" 
                        value="<?php echo esc_attr($plan['name']); ?>" 
                        class="regular-text" /><br><br>

                    <label><strong>Plan Period:</strong></label><br>
                    <input type="text" name="plan_details[<?php echo $key; ?>][period]" 
                        value="<?php echo esc_attr($plan['period']); ?>" 
                        class="regular-text" /><br><br>

                    <label><strong>Plan Price:</strong></label><br>
                    <input type="number" step="0.01"
                        name="plan_details[<?php echo $key; ?>][price]" 
                        value="<?php echo esc_attr($plan['price']); ?>" 
                        class="regular-text" /><br><br>
                    
                    <label><strong>Plan Offer Price:</strong></label><br>
                    <input type="number" step="0.01"
                        name="plan_details[<?php echo $key; ?>][offerprice]" 
                        value="<?php echo esc_attr($plan['offerprice']); ?>" 
                        class="regular-text" /><br><br>

                    <label><strong>Plan Description / Features:</strong></label><br>

                    <?php
                    $editor_id = 'description_' . $key;
                    $editor_name = 'plan_details[' . $key . '][description]';

                    wp_editor(
                        $plan['description'],
                        $editor_id,
                        [
                            'textarea_name' => $editor_name,
                            'media_buttons' => false,
                            'textarea_rows' => 6,
                            'teeny'         => false,
                            'quicktags'     => true,
                        ]
                    );
                    ?>

                </div>

                <?php
            },
            'subscription-plans',
            'subscription_plans_section'
        );
    }
});
// subscription plan settings end


// Unlock start
add_action('wp_ajax_verify_unlock_episodes', 'verify_unlock_episodes');
function verify_unlock_episodes() {

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Login required']);
        return;
    }

    $user_id = get_current_user_id();
    $episode_id = intval($_POST['episode_id']);
    $parent_id = intval($_POST['parent_id']);
    $episode_number = intval($_POST['episode_number']);

    $unlock_cost = (int) get_option('coins_to_unlock');
    $current_coins = (int) get_user_meta($user_id, 'user_coin_balance', true);

    // Get all previous episode IDs up to the selected one (assuming sequential IDs)
    // If your episode IDs are not sequential, you should fetch them by episode number/order
    $args = array(
        'post_type'      => 'post',
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => 'parent_blog_id',
                'value'   => $parent_id,
                'compare' => '=',
            ),
            array(
                'key'     => 'my_creation_parent_blog_id',
                'value'   => $parent_id,
                'compare' => '=',
            ),
            array(
                'key'     => 'competition_parent_id',
                'value'   => $parent_id,
                'compare' => '=',
            ),
        ),
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);
    $episodes = $query->posts;

    $episodes_to_unlock = [];
    foreach ($episodes as $episode) {
        $ep_number = (int) get_post_meta($episode->ID, 'episode_number', true);
        $is_locked = find_episode_is_locked($parent_id, $episode->ID, $ep_number);
        if ($is_locked) {
            $episodes_to_unlock[] = $episode->ID;
        }

        if ($ep_number == $episode_number) {
            break;
        }
    }

    $unlocked = get_user_meta($user_id, 'unlocked_episodes', true);
    $locked_count = count($episodes_to_unlock);

    if ($locked_count === 0) {
        wp_send_json_success(['message' => 'Already unlocked']);
        return;
    }

    $total_cost = $locked_count * $unlock_cost;

    // Not enough coins
    if ($current_coins < $total_cost) {
        wp_send_json_error(['message' => 'Not enough coins']);
        return;
    }

    wp_send_json_success([
        'message' => 'Unlock available',
        'count'   => $locked_count,
        'cost'    => $total_cost
    ]);
}

add_action('wp_ajax_unlock_episode', 'unlock_episode');
function unlock_episode() {

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Login required']);
        return;
    }

    $user_id = get_current_user_id();
    $episode_id = intval($_POST['episode_id']);
    $parent_id = intval($_POST['parent_id']);
    $episode_number = intval($_POST['episode_number']);

    $unlock_cost = (int) get_option('coins_to_unlock');
    $current_coins = (int) get_user_meta($user_id, 'user_coin_balance', true);

    // Get all previous episode IDs up to the selected one (assuming sequential IDs)
    // If your episode IDs are not sequential, you should fetch them by episode number/order
    $args = array(
        'post_type'      => 'post',
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => 'parent_blog_id',
                'value'   => $parent_id,
                'compare' => '=',
            ),
            array(
                'key'     => 'my_creation_parent_blog_id',
                'value'   => $parent_id,
                'compare' => '=',
            ),
            array(
                'key'     => 'competition_parent_id',
                'value'   => $parent_id,
                'compare' => '=',
            ),
        ),
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);
    $episodes = $query->posts;

    $episodes_to_unlock = [];
    foreach ($episodes as $episode) {
        $ep_number = (int) get_post_meta($episode->ID, 'episode_number', true);
        $is_locked = find_episode_is_locked($parent_id, $episode->ID, $ep_number);
        if ($is_locked) {
            $episodes_to_unlock[] = $episode->ID;
        }

        if ($ep_number == $episode_number) {
            break;
        }
    }

    $unlocked = get_user_meta($user_id, 'unlocked_episodes', true);
    if (!is_array($unlocked)) {
        $unlocked = [];
    }

    $locked_count = count($episodes_to_unlock);

    if ($locked_count === 0) {
        wp_send_json_success(['message' => 'Already unlocked']);
        return;
    }

    $total_cost = $locked_count * $unlock_cost;

    // Not enough coins
    if ($current_coins < $total_cost) {
        wp_send_json_error(['message' => 'Not enough coins']);
        return;
    }

    // Deduct coins
    $new_balance = $current_coins - $total_cost;
    update_user_meta($user_id, 'user_coin_balance', $new_balance);

    // Unlock episodes
    $unlocked = array_merge($unlocked, $episodes_to_unlock);
    $unlocked = array_unique($unlocked);
    sort($unlocked);

    update_user_meta($user_id, 'unlocked_episodes', $unlocked);

    wp_send_json_success([
        'message' => 'Unlocked episodes: ' . implode(', ', $episodes_to_unlock),
        'coins_left' => $new_balance
    ]);
}



