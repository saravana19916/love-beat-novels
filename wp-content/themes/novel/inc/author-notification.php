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
// remove_action('wp_ajax_nopriv_fetch_notifications', 'fetch_notifications');

function fetch_notifications() {
    global $wpdb;
    $user_id = get_current_user_id();
    if (!$user_id) wp_send_json_error(['message' => 'Not logged in'], 401);

    $table = "{$wpdb->prefix}author_notifications";

    // Only needed fields (avoid SELECT *)
    $notifications = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id, user_id, post_id, type, by_user_id, seen, message, created_at
             FROM {$table}
             WHERE user_id = %d
             ORDER BY seen ASC, created_at DESC
             LIMIT 50",
            $user_id
        )
    );

    // unread count via SQL (fast)
    $unread_count = (int) $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE user_id = %d AND seen = 0", $user_id)
    );

    $today     = current_time('Y-m-d');
    $yesterday = wp_date('Y-m-d', strtotime('-1 day'), wp_timezone());

    $groups = ['Today' => [], 'Yesterday' => [], 'Earlier' => []];

    foreach ($notifications as $note) {
        $created_date = wp_date('Y-m-d', strtotime($note->created_at), wp_timezone());

        if ($created_date === $today) $groups['Today'][] = $note;
        elseif ($created_date === $yesterday) $groups['Yesterday'][] = $note;
        else $groups['Earlier'][] = $note;
    }

    ob_start();
    $hasNotifications = false;

    foreach ($groups as $label => $group) {
        if (!$group) continue;

        $hasNotifications = true;
        echo "<li class='dropdown-header text-primary-color'>" . esc_html($label) . "</li>";

        foreach ($group as $note) {
            $diff = human_time_diff(strtotime($note->created_at), current_time('timestamp')) . ' ago';
            $diff_short = str_replace(['minutes', 'minute'], ['min', 'min'], $diff);

            if (in_array($note->type, ['subscription_activated','subscription_queued','subscription_renewal_reminder_post','subscription_renewal_reminder_pre'], true)) {
                echo "<li class='border rounded mx-2 p-2 mb-2'>" . esc_html($note->message) . "</li>";
                continue;
            }

            $post_url   = $note->post_id ? get_permalink((int)$note->post_id) : '';
            $post_title = $note->post_id ? get_the_title((int)$note->post_id) : '';
            $user_name  = $note->by_user_id ? get_the_author_meta('display_name', (int)$note->by_user_id) : 'Someone';

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
                    <a class='no-white me-2 text-primary-color' href='" . esc_url($post_url) . "'>
                        <strong>" . esc_html($user_name) . "</strong> {$type}
                        <strong>" . esc_html($post_title) . "</strong>
                    </a>
                    <small class='text-primary-color'>" . esc_html($diff_short) . "</small>
                  </li>";
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

// Lightweight "wait until changed" endpoint (long-poll)
add_action('wp_ajax_novel_notifications_wait', function () {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in'], 401);
    }

    global $wpdb;
    $user_id = get_current_user_id();
    $table   = "{$wpdb->prefix}author_notifications";

    $last_id = isset($_POST['last_id']) ? (int) $_POST['last_id'] : 0;
    $timeout = 20; // seconds (keep 15-25)
    $start   = time();

    do {
        $latest_id = (int) $wpdb->get_var(
            $wpdb->prepare("SELECT COALESCE(MAX(id), 0) FROM {$table} WHERE user_id = %d", $user_id)
        );

        if ($latest_id > $last_id) break;
        if ((time() - $start) >= $timeout) break;

        sleep(1);
    } while (true);

    $unread_count = (int) $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE user_id = %d AND seen = 0", $user_id)
    );

    wp_send_json_success([
        'changed'      => ($latest_id > $last_id),
        'latest_id'    => $latest_id,
        'unread_count' => $unread_count,
    ]);
});

