<?php

function ajax_register_user() {
    // check_ajax_referer('register_nonce', 'security');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $user_id = intval($_POST['user_id']);
    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];
    $email = sanitize_email($_POST['email']);
    $firstname = sanitize_text_field($_POST['firstname']);
    $lastname = sanitize_text_field($_POST['lastname']);
    $aboutUser = sanitize_text_field($_POST['about_user']);

    $errors = [];

    $required_fields = ['email', 'firstname', 'lastname'];

    if (empty($user_id)) {
        $required_fields[] = 'username';
        $required_fields[] = 'password';
    }

    foreach ($required_fields as $field) {
        if (empty($$field)) {
            wp_send_json_error('All fields are required.');
        }
    }

    if (!is_email($email)) {
        wp_send_json_error('Invalid email address.');
    }

    if (username_exists($username)) {
        wp_send_json_error('Username is already taken.');
    }

    $existing_user_id = email_exists($email);

    if ($existing_user_id && $existing_user_id != $user_id) {
        wp_send_json_error('Email is already registered.');
    }

    if ($user_id) {
       $update_data = [
            'ID'           => $user_id,
            'user_email'   => $email,
            'display_name' => trim($firstname . ' ' . $lastname),
        ];

        if (!empty($password)) {
            $update_data['user_pass'] = $password;
        }

        wp_update_user($update_data);

        update_user_meta($user_id, 'about_user', sanitize_textarea_field($_POST['about_user']));

        wp_send_json_success(['message' => 'Profile updated successfully', 'user_id' => $user_id]);
    } else {
        $user_id = wp_insert_user([
            'user_login' => $username,
            'user_pass' => $password,
            'user_nicename' => $username,
            'user_email' => $email,
            'display_name' => $firstname . ' ' . $lastname,
            'role' => 'subscriber',
        ]);

        if (is_wp_error($user_id)) {
            wp_send_json_error('An error occurred: ' . $user_id->get_error_message());
        }

        update_user_meta($user_id, 'about_user', $aboutUser);

        if (isset($_FILES['profile_picture']) && !empty($_FILES['profile_picture']['tmp_name'])) {
            $uploaded = media_handle_upload('profile_picture', 0);
            if (!is_wp_error($uploaded)) {
                update_user_meta($user_id, 'profile_picture', $uploaded);
            }
        }

        $code = wp_generate_password(20, false);
        update_user_meta($user_id, 'email_verification_code', $code);
        update_user_meta($user_id, 'email_verified', 0);

        $verification_url = site_url("?verify_email=$code&user_id=$user_id");

        ob_start();
        $template_path = locate_template('template-parts/email-verification-email-template.php');
        if ($template_path) {
            $args = [
                'firstname' => $firstname,
                'verification_url' => $verification_url
            ];
            extract($args);
            include $template_path;
            $message = ob_get_clean();
        } else {
            $message = 'Email template not found.';
        }

        add_filter('wp_mail_content_type', 'set_html_content_type');
        add_filter( 'wp_mail_from', 'custom_wp_mail_from_email' );
        add_filter( 'wp_mail_from_name', 'custom_wp_mail_from_name' );
        wp_mail($email, 'Verify your email', $message);
        remove_filter( 'wp_mail_from', 'custom_wp_mail_from_email' );
        remove_filter( 'wp_mail_from_name', 'custom_wp_mail_from_name' );
        remove_filter('wp_mail_content_type', 'set_html_content_type');

        wp_send_json_success(['message' => 'Registration successful! Check your email to verify your account.', 'user_id' => ''] );
    }
}
add_action('wp_ajax_register_user', 'ajax_register_user');
add_action('wp_ajax_nopriv_register_user', 'ajax_register_user');

add_action('init', function() {
    if (isset($_GET['verify_email']) && isset($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);
        $code = sanitize_text_field($_GET['verify_email']);
        $saved_code = get_user_meta($user_id, 'email_verification_code', true);

        if ($code === $saved_code) {
            update_user_meta($user_id, 'email_verified', 1);
            delete_user_meta($user_id, 'email_verification_code');
            wp_redirect(home_url('/login/?verified=1'));
            exit;
        } else {
            wp_redirect(home_url('/login/?verified=0'));
            exit;
        }
    }
});

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

// Add custom action link for resend verification email start
add_filter('user_row_actions', 'add_resend_verification_link', 10, 2);
function add_resend_verification_link($actions, $user){
    $verified = get_user_meta($user->ID, 'email_verified', true);
    if(!$verified){  
        $resend_url = wp_nonce_url(
            admin_url("users.php?action=resend_verification&user_id=" . $user->ID),
            'resend_email_verification'
        );
        $actions['resend_verification'] = "<a href='$resend_url'>Resend Verification Email</a>";
    }
    return $actions;
}

add_action('admin_init', 'process_resend_verification_email');
function process_resend_verification_email(){
    if(isset($_GET['action']) && $_GET['action'] === 'resend_verification'){
        
        if (! wp_verify_nonce($_GET['_wpnonce'], 'resend_email_verification')){
            wp_die('Security check failed');
        }

        $user_id = intval($_GET['user_id']);
        $user = get_user_by('ID', $user_id);

        if($user){
            // Check if already verified
            $verified = get_user_meta($user_id, 'email_verified', true);
            if($verified == 1){
                wp_redirect(add_query_arg('verified_already', 1, admin_url('users.php')));
                exit;
            }

            // Generate new code
            $code = wp_generate_password(20, false);
            update_user_meta($user_id, 'email_verification_code', $code);

            // Email details
            $firstname = $user->first_name;
            $verification_url = site_url("?verify_email=$code&user_id=$user_id");

            // Get email template
            ob_start();
            $template_path = locate_template('template-parts/email-verification-email-template.php');
            if($template_path){
                $args = [
                    'firstname' => $firstname,
                    'verification_url' => $verification_url
                ];
                extract($args);
                include $template_path;
                $message = ob_get_clean();
            } else {
                $message = 'Email template not found.';
            }

            // Set HTML and custom From
            add_filter('wp_mail_content_type', 'set_html_content_type');
            add_filter('wp_mail_from', 'custom_wp_mail_from_email');
            add_filter('wp_mail_from_name', 'custom_wp_mail_from_name');

            wp_mail($user->user_email, 'Verify your email', $message);

            // Remove Filters
            remove_filter('wp_mail_from', 'custom_wp_mail_from_email');
            remove_filter('wp_mail_from_name', 'custom_wp_mail_from_name');
            remove_filter('wp_mail_content_type', 'set_html_content_type');

            wp_redirect(add_query_arg('resend_success', 1, admin_url('users.php')));
            exit;
        }
    }
}


add_action('admin_notices', function(){
    if(isset($_GET['resend_success'])){
        echo '<div class="notice notice-success is-dismissible"><p>Verification email resent successfully.</p></div>';
    }
    if(isset($_GET['verified_already'])){
        echo '<div class="notice notice-warning is-dismissible"><p>User already verified.</p></div>';
    }
});

// Register bulk action
add_filter('bulk_actions-users', function($bulk_actions){
    $bulk_actions['bulk_resend_verification'] = 'Resend Verification Email';
    return $bulk_actions;
});

add_filter('handle_bulk_actions-users', function($redirect_url, $action, $user_ids){

    if($action !== 'bulk_resend_verification'){
        return $redirect_url;
    }

    $sent = 0;
    $skipped = 0;

    foreach($user_ids as $user_id){

        $verified = get_user_meta($user_id, 'email_verified', true);
        if($verified == 1){
            $skipped++;  
            continue;
        }

        // Generate new code
        $code = wp_generate_password(20, false);
        update_user_meta($user_id, 'email_verification_code', $code);

        $user = get_user_by('ID', $user_id);
        if(! $user) continue;

        $firstname = $user->first_name;
        $verification_url = site_url("?verify_email=$code&user_id=$user_id");

        // Load email template
        ob_start();
        $template_path = locate_template('template-parts/email-verification-email-template.php');
        if($template_path){
            $args = [
                'firstname' => $firstname,
                'verification_url' => $verification_url
            ];
            extract($args);
            include $template_path;
            $message = ob_get_clean();
        } else {
            $message = 'Email template not found.';
        }

        // Enable HTML & custom From
        add_filter('wp_mail_content_type', 'set_html_content_type');
        add_filter('wp_mail_from', 'custom_wp_mail_from_email');
        add_filter('wp_mail_from_name', 'custom_wp_mail_from_name');

        wp_mail($user->user_email, 'Verify your email', $message);

        // Remove filters
        remove_filter('wp_mail_content_type', 'set_html_content_type');
        remove_filter('wp_mail_from', 'custom_wp_mail_from_email');
        remove_filter('wp_mail_from_name', 'custom_wp_mail_from_name');

        $sent++;
    }

    // Add results to redirect URL
    $redirect_url = add_query_arg([
        'bulk_resend_sent' => $sent,
        'bulk_resend_skipped' => $skipped
    ], $redirect_url);

    return $redirect_url;

}, 10, 3);

add_action('admin_notices', function(){

    if(isset($_GET['bulk_resend_sent'])){
        $sent = intval($_GET['bulk_resend_sent']);
        $skipped = intval($_GET['bulk_resend_skipped']);

        echo '<div class="notice notice-success is-dismissible"><p>';
        echo "Verification emails sent: <strong>$sent</strong><br>";
        echo "Already verified users skipped: <strong>$skipped</strong>";
        echo '</p></div>';
    }

});

function set_html_content_type() {
    return 'text/html';
}

function custom_wp_mail_from_email( $original_email_address ) {
    return 'contact@lovebeatnovels.com';
}

function custom_wp_mail_from_name( $original_email_from ) {
    return 'Love Beat Novels';
}
// Add custom action link for resend verification email end

// Add column email verification and search filter start
add_filter('manage_users_columns', function($columns){
    $columns['email_verified'] = 'Email Verified?';
    return $columns;
});

add_filter('manage_users_custom_column', function($value, $column_name, $user_id){
    if($column_name === 'email_verified'){
        $verified = get_user_meta($user_id, 'email_verified', true);
        return $verified == 1
        ? '<span style="color:green;font-weight:bold;">Verified</span>'
        : '<span style="color:red;font-weight:bold;">Not Verified</span>';
    }
    return $value;
}, 10, 3);

add_action('pre_get_users', function($query){
    if (!is_admin()) return;

    $search = isset($_GET['s']) ? trim($_GET['s']) : '';

    if ($search === 'verified' || $search === 'not verified') {

        if ($search === 'verified') {

            $query->set('meta_query', [
                [
                    'key'     => 'email_verified',
                    'value'   => '1',
                    'compare' => '='
                ]
            ]);

        } else {

            $query->set('meta_query', [
                'relation' => 'OR',

                [
                    'key'     => 'email_verified',
                    'value'   => '0',
                    'compare' => '='
                ],

                [
                    'key'     => 'email_verified',
                    'compare' => 'NOT EXISTS'
                ]
            ]);
        }

        $query->set('search', '');
    }
});

// Add column email verification and search filter end
