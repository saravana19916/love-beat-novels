<?php

// login
function enqueue_ajax_login_script() {
    $handle = 'novel-login';

    $src  = get_template_directory_uri() . '/js/login.js';
    $path = get_template_directory() . '/js/login.js';
    $ver  = file_exists($path) ? filemtime($path) : null;

    wp_enqueue_script($handle, $src, ['jquery'], $ver, true);

    wp_localize_script($handle, 'ajax_login_object', [
        'ajax_url'  => admin_url('admin-ajax.php'),
        'security'  => wp_create_nonce('ajax-login-nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_login_script');

function ajax_login_handler() {
    $nonce = $_POST['security'] ?? '';
    if (!$nonce || !wp_verify_nonce($nonce, 'ajax-login-nonce')) {
        wp_send_json(['status' => 'error', 'message' => 'Security check failed. Please refresh and try again.'], 403);
    }

    $response = array();

    if (empty($_POST['username']) || empty($_POST['password'])) {
        $response['status'] = 'error';
        $response['message'] = 'Username and password are required.';
        wp_send_json($response);
    }

    $creds = array(
        'user_login'    => sanitize_text_field($_POST['username']),
        'user_password' => sanitize_text_field($_POST['password']),
        'remember'      => true,
    );

    $user = wp_signon($creds, is_ssl());

    if (is_wp_error($user)) {
        $error_codes = $user->get_error_codes();

        if (in_array('invalid_username', $error_codes) || in_array('incorrect_password', $error_codes)) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid username or password.';
        } elseif (in_array('email_not_verified', $error_codes)) {
            $response['status'] = 'error';
            $response['message'] = 'Please verify your email before logging in.';
        } else {
            $response['status'] = 'error';
            $response['message'] = $user->get_error_message();
        }
    } else {
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);

        $response['status'] = 'success';
        $response['message'] = 'Login successful!';
    }

    wp_send_json($response);
}
add_action('wp_ajax_nopriv_ajax_login', 'ajax_login_handler');
add_action('wp_ajax_ajax_login', 'ajax_login_handler');

add_filter('authenticate', function($user, $username, $password) {
    if (is_a($user, 'WP_User')) {
        $verified = get_user_meta($user->ID, 'email_verified', true);
        if (!$verified) {
            return new WP_Error('email_not_verified', __('<strong>Error</strong>: Please verify your email before logging in.'));
        }
    }
    return $user;
}, 30, 3);

add_action('wp_ajax_nopriv_google_login', 'ajax_google_login_handler');
add_action('wp_ajax_google_login', 'ajax_google_login_handler');

function ajax_google_login_handler() {
    // Add nonce check (like normal login)
    check_ajax_referer('ajax-login-nonce', 'security');

    if (empty($_POST['id_token'])) {
        wp_send_json(['status' => 'error', 'message' => 'No token received.']);
    }

    $id_token = sanitize_text_field($_POST['id_token']);

    $response = wp_remote_get('https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($id_token), [
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        error_log('Google tokeninfo error: ' . $response->get_error_message());
        wp_send_json(['status' => 'error', 'message' => 'Google verification failed.']);
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);

    if ($code !== 200 || !is_array($body)) {
        error_log('Google tokeninfo non-200: ' . $code . ' body=' . wp_remote_retrieve_body($response));
        wp_send_json(['status' => 'error', 'message' => 'Invalid Google token.']);
    }

    $expected_aud = defined('NOVEL_GOOGLE_CLIENT_ID')
        ? NOVEL_GOOGLE_CLIENT_ID
        : '';

    if (empty($body['email']) || empty($body['aud']) || $body['aud'] !== $expected_aud) {
        error_log('Google token invalid: ' . print_r($body, true));
        wp_send_json(['status' => 'error', 'message' => 'Invalid Google token.']);
    }

    $email = sanitize_email($body['email']);
    $user = get_user_by('email', $email);

    if (!$user) {
        // Register new user
        $username = sanitize_user(current(explode('@', $email)));
        $random_password = wp_generate_password(12, false);
        $user_id = wp_create_user($username, $random_password, $email);
        if (is_wp_error($user_id)) {
            wp_send_json(['status' => 'error', 'message' => 'Could not create user.']);
        }
        $user = get_user_by('id', $user_id);
    }

    // Log in the user
    wp_clear_auth_cookie();
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);
    update_user_meta($user->ID, 'email_verified', true);

    wp_send_json(['status' => 'success', 'message' => 'Logged in with Google Success!']);
}