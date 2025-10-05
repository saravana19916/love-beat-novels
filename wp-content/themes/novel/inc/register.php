<?php

add_action('wp_ajax_get_user_profile', 'get_user_profile');

function get_user_profile() {
    $user_id = get_current_user_id();
    if (!$user_id) wp_send_json_error(['message' => 'Not logged in']);

    $user_info = get_userdata($user_id);

    $display_name = $user_info->display_name;

    $name_parts = explode(' ', $display_name, 2);
    $firstname = $name_parts[0] ?? '';
    $lastname  = $name_parts[1] ?? '';

    $response = [
        'ID'        => $user_info->ID,
        'username'  => $user_info->user_login,
        'email'     => $user_info->user_email,
        'firstname' => $firstname,
        'lastname'  => $lastname,
        'about_user'=> get_user_meta($user_id, 'about_user', true),
    ];

    wp_send_json_success($response);
}
