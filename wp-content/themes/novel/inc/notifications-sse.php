<?php
// filepath: /home/saravanan/projects/wordpress-sample/wp-content/themes/novel/inc/notifications-sse.php

add_action('rest_api_init', function () {
    register_rest_route('novel/v1', '/notifications/stream', [
        'methods'             => 'GET',
        'permission_callback' => function () {
            return is_user_logged_in(); // EventSource cannot send custom headers; rely on WP cookies (same-origin)
        },
        'callback'            => 'novel_notifications_sse_stream',
    ]);
});

function novel_notifications_sse_stream(\WP_REST_Request $request) {
    // Prevent PHP/session blocking + keep connection
    if (function_exists('session_write_close')) {
        @session_write_close();
    }
    @ignore_user_abort(true);
    @set_time_limit(0);

    // SSE headers
    nocache_headers();
    header('Content-Type: text/event-stream; charset=UTF-8');
    header('Cache-Control: no-cache, no-transform');
    header('Connection: keep-alive');
    header('X-Accel-Buffering: no'); // nginx

    // Clean buffers
    while (ob_get_level() > 0) { @ob_end_flush(); }
    @ob_implicit_flush(true);

    global $wpdb;
    $user_id = get_current_user_id();
    $table   = "{$wpdb->prefix}author_notifications";

    $last_id  = (int) $request->get_param('last_id');
    $timeout  = 55; // seconds; browser auto-reconnect
    $start    = time();
    $sleepSec = 2;

    $send = function (string $event, array $data) {
        echo "event: {$event}\n";
        echo 'data: ' . wp_json_encode($data) . "\n\n";
        @flush();
    };

    // Initial meta (so UI updates immediately)
    $latest_id = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COALESCE(MAX(id),0) FROM {$table} WHERE user_id=%d",
        $user_id
    ));
    $unread_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$table} WHERE user_id=%d AND seen=0",
        $user_id
    ));

    $send('meta', [
        'latest_id'    => $latest_id,
        'unread_count' => $unread_count,
        'changed'      => ($latest_id > $last_id),
    ]);

    // Stream loop: wait until a new row appears (server-side check)
    while ((time() - $start) < $timeout) {
        // Heartbeat comment (keeps connection alive in some proxies)
        echo ": ping\n\n";
        @flush();

        $latest_id_now = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(MAX(id),0) FROM {$table} WHERE user_id=%d",
            $user_id
        ));

        if ($latest_id_now > $latest_id) {
            $latest_id = $latest_id_now;

            $unread_count = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE user_id=%d AND seen=0",
                $user_id
            ));

            $send('meta', [
                'latest_id'    => $latest_id,
                'unread_count' => $unread_count,
                'changed'      => true,
            ]);
        }

        sleep($sleepSec);
    }

    // End stream (browser will reconnect)
    exit;
}

add_action('wp_ajax_novel_notifications_stream', 'novel_notifications_ajax_sse_stream');

function novel_notifications_ajax_sse_stream() {
    if (!is_user_logged_in()) {
        status_header(401);
        echo "Not logged in";
        exit;
    }

    if (function_exists('session_write_close')) {
        @session_write_close();
    }
    @ignore_user_abort(true);
    @set_time_limit(0);

    nocache_headers();
    header('Content-Type: text/event-stream; charset=UTF-8');
    header('Cache-Control: no-cache, no-transform');
    header('Connection: keep-alive');
    header('X-Accel-Buffering: no');

    while (ob_get_level() > 0) { @ob_end_flush(); }
    @ob_implicit_flush(true);

    global $wpdb;
    $user_id = get_current_user_id();
    $table   = "{$wpdb->prefix}author_notifications";

    $last_id  = isset($_GET['last_id']) ? (int) $_GET['last_id'] : 0;
    $timeout  = 55;
    $start    = time();
    $sleepSec = 2;

    $send = function (string $event, array $data) {
        echo "event: {$event}\n";
        echo 'data: ' . wp_json_encode($data) . "\n\n";
        @flush();
    };

    // Initial meta
    $latest_id = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COALESCE(MAX(id),0) FROM {$table} WHERE user_id=%d",
        $user_id
    ));
    $unread_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$table} WHERE user_id=%d AND seen=0",
        $user_id
    ));

    $send('meta', [
        'latest_id'    => $latest_id,
        'unread_count' => $unread_count,
        'changed'      => ($latest_id > $last_id),
    ]);

    // Wait loop
    while ((time() - $start) < $timeout) {
        echo ": ping\n\n";
        @flush();

        $latest_id_now = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(MAX(id),0) FROM {$table} WHERE user_id=%d",
            $user_id
        ));

        if ($latest_id_now > $latest_id) {
            $latest_id = $latest_id_now;

            $unread_count = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE user_id=%d AND seen=0",
                $user_id
            ));

            $send('meta', [
                'latest_id'    => $latest_id,
                'unread_count' => $unread_count,
                'changed'      => true,
            ]);
        }

        sleep($sleepSec);
    }

    exit;
}