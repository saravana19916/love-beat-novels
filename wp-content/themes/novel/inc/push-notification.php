<?php

function send_push_notification($title, $message, $data = [], $segments = ['All']) {

    $onesignal_app_id = '08d7143d-6a7c-43b9-8463-ee35d26131aa';
    $onesignal_api_key = 'os_v2_app_bdlriplkprb3tbdd5y25eyjrvlwx43l3naie5bviomv4aryrb3f4o2iqrokknqstkr7omsivge7pnwi4v5cc4dvzhmm75yqpgudaqjq';

    $payload = [
        'app_id' => $onesignal_app_id,
        'headings' => [
            'en' => $title
        ],
        'contents' => [
            'en' => $message
        ],
        'included_segments' => $segments,
        'data' => $data, // extra data like post_id, type, etc.
    ];

    $args = [
        'headers' => [
            'Content-Type'  => 'application/json; charset=utf-8',
            'Authorization' => 'Basic ' . $onesignal_api_key,
        ],
        'body'    => json_encode($payload),
        'timeout' => 20,
    ];

    $response = wp_remote_post(
        'https://onesignal.com/api/v1/notifications',
        $args
    );

    return $response;
}

add_action('story_created', function ($post_id) {

    $title = 'New Story Published';
    $message = get_the_title($post_id) . ' has been published';

    send_push_notification(
        $title,
        $message,
        [
            'post_id' => $post_id,
            'type'    => 'story'
        ]
    );

});

// Trigger it when you create a story:
$post_id = wp_insert_post($post_data);

do_action('story_created', $post_id);

add_action('episode_created', function ($story_id, $episode_id) {

    $title = 'New Episode Released';
    $message = 'A new episode is added to ' . get_the_title($story_id);

    send_push_notification(
        $title,
        $message,
        [
            'story_id'  => $story_id,
            'episode_id'=> $episode_id,
            'type'      => 'episode'
        ]
    );

}, 10, 2);

do_action('episode_created', $story_id, $episode_id);
