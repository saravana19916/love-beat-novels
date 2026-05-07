<?php

add_action('wp_ajax_mark_notifications_seen', 'mark_notifications_seen');
function mark_notifications_seen() {
    if (!is_user_logged_in()) wp_die();

    global $wpdb;
    $user_id = get_current_user_id();
    $wpdb->update(
        "{$wpdb->prefix}author_notifications",
        ['seen' => 1],
        ['user_id' => $user_id, 'seen' => 0]
    );

    wp_die();
}

// Set notification when post store
add_action('save_post', 'send_follow_notifications', 10, 3);
function send_follow_notifications($post_id, $post, $update) {
    // Only for published posts
    if ($post->post_type != 'post' || $post->post_status !== 'publish') return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;

    $author_id = $post->post_author;

    $all_users = get_users();
    foreach ($all_users as $user) {
        $following = get_user_meta($user->ID, 'following_users', true);
        if (is_array($following) && in_array($author_id, $following)) {
            global $wpdb;
            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}author_notifications WHERE user_id = %d AND post_id = %d AND type = 'follow_post'",
                    $user->ID, $post_id
                )
            );
            if (!$exists) {
                $wpdb->insert(
                    "{$wpdb->prefix}author_notifications",
                    array(
                        'user_id' => $user->ID,
                        'post_id' => $post_id,
                        'type' => 'follow_post',
                        'by_user_id' => $author_id,
                        'seen' => 0,
                        'created_at' => current_time('mysql')
                    ),
                    array('%d','%d','%s','%d','%d','%s')
                );
            }
        }
    }
}

add_action('wp_ajax_fetch_notifications', 'fetch_notifications');
add_action('wp_ajax_nopriv_fetch_notifications', 'fetch_notifications');

function fetch_notifications() {
    global $wpdb;
    $user_id = get_current_user_id();
    if (!$user_id) wp_send_json_error(['message' => 'Not logged in']);

    $notifications = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}author_notifications 
            WHERE user_id = %d
            ORDER BY seen ASC, created_at DESC 
            LIMIT 50", 
            $user_id
        )
    );

    $unread_count = count(array_filter($notifications, fn($n) => $n->seen == 0));

    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    $groups = ['Today'=>[], 'Yesterday'=>[], 'Earlier'=>[]];

    foreach ($notifications as $note) {
        $created_date = date('Y-m-d', strtotime($note->created_at));

        if ($created_date === $today) {
            $groups['Today'][] = $note;
        } elseif ($created_date === $yesterday) {
            $groups['Yesterday'][] = $note;
        } else {
            $groups['Earlier'][] = $note;
        }
    }


    ob_start();
    $hasNotifications = false;
    foreach ($groups as $label => $group) {
        if (count($group) === 0) continue;
        $hasNotifications = true;
        echo "<li class='dropdown-header text-primary-color'>" . esc_html($label) . "</li>";
        foreach ($group as $note) {
            $post_url = get_permalink($note->post_id) ?? '';
            $post_title = get_the_title($note->post_id) ?? '';
            $user_name = $note->by_user_id ? get_the_author_meta('display_name', $note->by_user_id) : 'Someone';

            $diff = human_time_diff(strtotime($note->created_at), current_time('timestamp')) . ' ago';

            // Replace long words with short versions
            $diff_short = str_replace(
                ['minutes', 'minute'],
                ['min', 'min'],
                $diff
            );

            $current_plan   = get_user_meta($user_id, 'subscription_active_plan', true);

            if ($note->type === 'subscription_activated' || $note->type === 'subscription_queued' || $note->type === 'subscription_renewal_reminder_post' || $note->type === 'subscription_renewal_reminder_pre') {
                echo "<li class='border rounded mx-2 p-2 mb-2'>
                    " . esc_html($note->message) . "
                  </li>";
            } else {
                $type = match($note->type) {
                    'like' => 'liked your story',
                    'comment' => 'commented on your story',
                    'like comment' => 'liked your comment in the story',
                    'reply comment' => 'replied to your comment in the story',
                    'follow_post' => 'posted a new story',
                    'follow' => 'started to follow you.',
                    default => 'did something'
                };

                echo "<li class='border rounded mx-2 p-2 mb-2'>
                        <a class='no-white me-2 text-primary-color' href='".esc_url($post_url)."'>
                            <strong>".esc_html($user_name)."</strong> {$type} 
                            <strong>".esc_html($post_title)."</strong>
                        </a>
                        <small class='text-primary-color'>
                            " . $diff_short . "
                        </small>
                    </li>";
            }
        }
    }

    if (!$hasNotifications) {
        echo "<li><span class='dropdown-item text-primary-color'>No new notifications</span></li>";
    }
    $html = ob_get_clean();

    wp_send_json_success([
        'unread_count' => $unread_count,
        'notifications_html' => $html
    ]);
}

