<?php
require_once get_template_directory() . '/inc/wp-bootstrap-navwalker.php';

// Enqueue Bootstrap and Font Awesome
function my_theme_enqueue_styles() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
    
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

    wp_enqueue_style('theme-style', get_stylesheet_uri());

    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array(), null, true);

    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

// Enable featured images
add_theme_support('post-thumbnails');

// Logo upload start
function mytheme_custom_logo() {
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'mytheme_custom_logo');
// Logo upload end

// Banner image start
function theme_customize_register($wp_customize) {
    $wp_customize->add_section('banner_section', array(
        'title'    => __('Banner Image', 'your-theme'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('banner_image', array(
        'default'   => '',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'banner_image_control', array(
        'label'    => __('Upload Banner Image', 'your-theme'),
        'section'  => 'banner_section',
        'settings' => 'banner_image',
    )));

    // Banner Link Setting
    $wp_customize->add_setting('banner_link', array(
        'default'   => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw', // Ensure valid URL
    ));

    $wp_customize->add_control('banner_link_control', array(
        'label'   => __('Banner Link', 'your-theme'),
        'section' => 'banner_section',
        'settings' => 'banner_link',
        'type'    => 'url',
    ));
}
add_action('customize_register', 'theme_customize_register');
// Banner image end

// Header menu icon
function custom_nav_menu($items) {
    foreach ($items as $item) {
        if ($item->description) {
            $item->title = '<div class="menu-icon text-center"><i class="'.$item->description.' fa-lg"></i></div><span class="menu-text">'.$item->title.'</span>';
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'custom_nav_menu');


// category and blog start
function create_custom_post_types() {
    register_post_type('main_blog',
        array(
            'labels'      => array(
                'name'          => __('Main Blogs'),
                'singular_name' => __('Main Blog'),
            ),
            'public'      => true,
            'has_archive' => true,
            'supports'    => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'taxonomies'  => array('category'),
            'hierarchical' => true, 
            'rewrite' => array('slug' => 'main-blog', 'with_front' => false), // Ensure correct slug
        )
    ); 

    register_post_type('sub_blog',
        array(
            'labels'      => array(
                'name'          => __('Sub Blogs'),
                'singular_name' => __('Sub Blog'),
            ),
            'public'      => true,
            'has_archive' => true,
            'supports'    => array('title', 'editor', 'thumbnail', 'custom-fields', 'comments'),
            'taxonomies'  => array('category'), // Add category support if needed
            'hierarchical' => false,
            'rewrite' => array('slug' => 'sub-blogs'),
        )
    );
}
add_action('init', 'create_custom_post_types');

function add_parent_blog_meta_box() {
    add_meta_box(
        'parent_blog_meta',
        'Select Parent Blog',
        'parent_blog_meta_callback',
        'sub_blog',
        'side'
    );
}

function parent_blog_meta_callback($post) {
    $selected = get_post_meta($post->ID, 'parent_blog_id', true);
    $args = array('post_type' => 'main_blog', 'posts_per_page' => -1);
    $blogs = get_posts($args);

    echo '<select name="parent_blog_id">';
    echo '<option value="">Select a Main Blog</option>';
    foreach ($blogs as $blog) {
        echo '<option value="' . $blog->ID . '" ' . selected($selected, $blog->ID, false) . '>' . $blog->post_title . '</option>';
    }
    echo '</select>';
}

function save_parent_blog_meta($post_id) {
    if (isset($_POST['parent_blog_id'])) {
        update_post_meta($post_id, 'parent_blog_id', $_POST['parent_blog_id']);
    }
}

add_action('add_meta_boxes', 'add_parent_blog_meta_box');
add_action('save_post', 'save_parent_blog_meta');
// category and blog end

// competition section start
function create_competition_cpt() {
    register_post_type('competition',
        array(
            'labels' => array(
                'name' => __('Competitions'),
                'singular_name' => __('Competition')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'thumbnail'),
            'rewrite' => array('slug' => 'competitions'),
        )
    );
}
add_action('init', 'create_competition_cpt');

function create_competition_post_type() {
    register_post_type('competition_post', array(
        'labels' => array(
            'name' => __('Competition Posts'),
            'singular_name' => __('Competition Post')
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'competition-posts'),
        'supports' => array('title', 'editor', 'author'),
    ));
}
add_action('init', 'create_competition_post_type');

//competition submit
function handle_competition_post_submission() {
    if (!is_user_logged_in()) {
        wp_send_json_error("You must be logged in.");
    }

    $post_title = sanitize_text_field($_POST['post_title']);
    $post_content = sanitize_textarea_field($_POST['post_content']);
    $competition_id = intval($_POST['competition_id']);
    $redirect_url = get_permalink($competition_id);
    
    $post_id = wp_insert_post(array(
        'post_title'   => $post_title,
        'post_content' => $post_content,
        'post_status'  => 'publish',
        'post_type'    => 'competition_post',
        'post_author'  => get_current_user_id(),
        'meta_input'   => array('competition_id' => $competition_id),
    ));

    if ($post_id) {
        $redirect_url = get_permalink($competition_id);
        wp_send_json_success(['redirect_url' => $redirect_url]);
    } else {
        wp_send_json_error("Error submitting post.");
    }
}
add_action('wp_ajax_submit_competition_post', 'handle_competition_post_submission');
add_action('wp_ajax_nopriv_submit_competition_post', 'handle_competition_post_submission');


// function fetch_competition_posts() {
//     // Ensure competition_id is received
//     $competition_id = isset($_POST['competition_id']) ? intval($_POST['competition_id']) : 0;

//     // Query arguments
//     $args = array(
//         'post_type'      => 'competition_post',
//         'meta_query'     => array(
//             array(
//                 'key'   => 'competition_id',
//                 'value' => $competition_id,
//                 'compare' => '='
//             ),
//         ),
//         'posts_per_page' => 10,
//     );

//     $query = new WP_Query($args);
//     $output = '';

//     if ($query->have_posts()) {
//         while ($query->have_posts()) {
//             $query->the_post();
//             $user = get_userdata(get_the_author_meta('ID'));
//             $output .= '<tr>
//                 <td class="p-4">
//                     <a class="fs-4 fw-bold" href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a>
//                     <p style="font-size: 0.8rem;" class="m-0">' . esc_html($user->display_name) . '</p>
//                 </td>
//                 <td class="p-4">' . get_the_date() . '</td>
//             </tr>';
//         }
//     } else {
//         $output .= '<tr><td colspan="3">No posts found.</td></tr>';
//     }

//     wp_reset_postdata();
//     wp_send_json_success($output);
// }
// add_action('wp_ajax_fetch_competition_posts', 'fetch_competition_posts');
// add_action('wp_ajax_nopriv_fetch_competition_posts', 'fetch_competition_posts');

function fetch_competition_posts() {
    // Ensure competition_id and page number are received
    $competition_id = isset($_POST['competition_id']) ? intval($_POST['competition_id']) : 0;
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $posts_per_page = 10;

    // Query arguments
    $args = array(
        'post_type'      => 'competition_post',
        'meta_query'     => array(
            array(
                'key'   => 'competition_id',
                'value' => $competition_id,
                'compare' => '='
            ),
        ),
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
    );

    $query = new WP_Query($args);
    $output = '';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $user = get_userdata(get_the_author_meta('ID'));

            $date = get_the_date('j F Y');
            $tamil_months = array(
                'January' => 'ஜனவரி',
                'February' => 'பிப்ரவரி',
                'March' => 'மார்ச்',
                'April' => 'ஏப்ரல்',
                'May' => 'மே',
                'June' => 'ஜூன்',
                'July' => 'ஜூலை',
                'August' => 'ஆகஸ்ட்',
                'September' => 'செப்டம்பர்',
                'October' => 'அக்டோபர்',
                'November' => 'நவம்பர்',
                'December' => 'டிசம்பர்'
            );
        
            $tamil_date = str_replace(array_keys($tamil_months), array_values($tamil_months), $date);

            $output .= '<tr>
                <td class="px-4">
                    <a class="fs-6 fw-bold" href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a>
                    <p style="font-size: 0.8rem;" class="m-0">' . esc_html($user->display_name) . '</p>
                </td>
                <td>
                    <p class="mb-0 mt-2">' . $tamil_date . '</p>
                </td>
            </tr>';
        }
    } else {
        $output .= '<tr><td colspan="2">No posts found.</td></tr>';
    }

    // Pagination
    $total_pages = $query->max_num_pages;
    $pagination = '';

    if ($total_pages > 1) {
        $pagination .= '<nav><ul class="pagination justify-content-end">';
        $prev_disabled = ($paged > 1) ? '' : 'disabled cursor-pointer';
        $pagination .= '<li class="page-item ' . $prev_disabled . '">
                            <a href="#" class="page-link pagination-link" data-page="' . ($paged - 1) . '">
                                <i class="fa-solid fa-angles-left"></i>
                            </a>
                        </li>';
        for ($i = 1; $i <= $total_pages; $i++) {
            $active_class = ($i == $paged) ? 'active' : '';
            $pagination .= '<li class="page-item ' . $active_class . '">
                                <a href="#" class="page-link pagination-link" data-page="' . $i . '">' . $i . '</a>
                            </li>';
        }

        // Next button
        $nextDisabled = ($paged < $total_pages) ? '' : 'disabled cursor-pointer';
        // if ($paged < $total_pages) {
            $pagination .= '<li class="page-item ' . $nextDisabled . '">
                                <a href="#" class="page-link pagination-link" data-page="' . ($paged + 1) . '">
                                    <i class="fa-solid fa-angles-right"></i>
                                </a>
                            </li>';
        // }
        $pagination .= '</ul></nav>';
    }

    wp_reset_postdata();
    wp_send_json_success(['table_data' => $output, 'pagination' => $pagination]);
}
add_action('wp_ajax_fetch_competition_posts', 'fetch_competition_posts');
add_action('wp_ajax_nopriv_fetch_competition_posts', 'fetch_competition_posts');


// competition section end

function my_theme_setup() {
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'my-bootstrap-theme' ),
    ) );
}
add_action( 'after_setup_theme', 'my_theme_setup' );

function register_footer_menu() {
    register_nav_menu('footer', __('Footer Menu'));
}
add_action('init', 'register_footer_menu');


function register_footer_category_menu() {
    register_nav_menu('footer-categories', __('Footer Categories Menu'));
}
add_action('init', 'register_footer_category_menu');

// register
function enqueue_register_script() {
    wp_enqueue_script('register-script', get_template_directory_uri() . '/js/register.js', ['jquery'], null, true);
    wp_localize_script('register-script', 'ajaxurl', [
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('register_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_register_script');

function ajax_register_user() {
    check_ajax_referer('register_nonce', 'security');

    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];
    $email = sanitize_text_field($_POST['email']);
    $firstname = sanitize_text_field($_POST['firstname']);
    $lastname = sanitize_text_field($_POST['lastname']);

    $errors = [];

    if (empty($username) || empty($password) || empty($email)) {
        wp_send_json_error('All fields are required.');
    }

    if (!is_email($email)) {
        wp_send_json_error('Invalid email address.');
    }

    if (username_exists($username)) {
        wp_send_json_error('Username is already taken.');
    }

    if (email_exists($email)) {
        wp_send_json_error('Email is already registered.');
    }

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

    wp_send_json_success('Registration successful!');
}
add_action('wp_ajax_register_user', 'ajax_register_user');
add_action('wp_ajax_nopriv_register_user', 'ajax_register_user');

// login
function enqueue_ajax_login_script() {
    wp_enqueue_script('ajax-login-script', get_template_directory_uri() . '/js/login.js', array('jquery'), null, true);
    wp_localize_script('ajax-login-script', 'ajax_login_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('ajax-login-nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_login_script');

function ajax_login_handler() {
    // Verify nonce
    check_ajax_referer('ajax-login-nonce', 'security');

    $response = array();

    // Validate input fields
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
        $response['status'] = 'error';
        $response['message'] = 'Invalid username or password.';
    } else {
        $response['status'] = 'success';
        $response['message'] = 'Login successful!';
        $response['redirect_url'] = home_url(); // Redirect URL
    }

    wp_send_json($response);
}
add_action('wp_ajax_nopriv_ajax_login', 'ajax_login_handler');
add_action('wp_ajax_ajax_login', 'ajax_login_handler');

function handle_frontend_login() {
    if (isset($_POST['frontend_login']) && $_POST['frontend_login'] == '1') {
        $creds = array(
            'user_login'    => sanitize_text_field($_POST['username']),
            'user_password' => sanitize_text_field($_POST['password']),
            'remember'      => isset($_POST['rememberme']),
        );

        $user = wp_signon($creds, is_ssl());

        if (is_wp_error($user)) {
            // Login failed, store error message in session
            session_start();
            $_SESSION['login_error'] = $user->get_error_message();
        } else {
            // Login successful, clear session and redirect
            session_start();
            unset($_SESSION['login_error']);
            wp_redirect(home_url());
            exit;
        }
    }
}
add_action('init', 'handle_frontend_login');

// comment like button
// Add Like button to comments
// function add_like_button_to_comments($comment_text, $comment) {
//     if (is_singular() && $comment->comment_approved == 1) {
//         $comment_id = $comment->comment_ID;
//         $likes = get_comment_meta($comment_id, 'likes', true) ?: 0;
//         $button = '<button class="btn btn-outline-primary btn-sm like-comment" data-comment-id="' . $comment_id . '">Like (' . $likes . ')</button>';
//         $comment_text .= '<div class="comment-actions">' . $button . '</div>';
//     }
//     return $comment_text;
// }
// add_filter('comment_text', 'add_like_button_to_comments', 10, 2);

function get_like_button($comment_id) {
    $likes = get_comment_meta($comment_id, 'likes', true) ?: 0;
    return '<button class="btn btn-outline-primary btn-sm like-comment" data-comment-id="' . $comment_id . '">Like (' . $likes . ')</button>';
}

// Generate Subscribe Button
function get_subscribe_button() {
    return '<button class="btn btn-outline-success btn-sm">Subscribe</button>';
}

// Generate Reply Button
function get_reply_button($args, $depth, $comment_id) {
    return get_comment_reply_link(array_merge($args, [
        'reply_text' => 'Reply',
        'depth'      => $depth,
        'max_depth'  => $args['max_depth'],
        'add_below'  => 'comment',
        'class'      => 'btn btn-outline-secondary btn-sm'
    ]), $comment_id);
}

// Handle Like button click
function handle_comment_like() {
    $comment_id = intval($_POST['comment_id']);
    $likes = get_comment_meta($comment_id, 'likes', true) ?: 0;
    $likes++;
    update_comment_meta($comment_id, 'likes', $likes);
    wp_send_json_success($likes);
}
add_action('wp_ajax_like_comment', 'handle_comment_like');
add_action('wp_ajax_nopriv_like_comment', 'handle_comment_like');

// Enqueue scripts
function enqueue_like_comment_script() {
    wp_enqueue_script('comment-like', get_template_directory_uri() . '/js/comment-like.js', ['jquery'], null, true);
    wp_localize_script('comment-like', 'commentLike', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_like_comment_script');

function bootstrap5_comment_callback($comment, $args, $depth) {
    ?>
    <li <?php comment_class('mb-4'); ?> id="comment-<?php comment_ID(); ?>">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Author Photo -->
                    <div class="col-12 col-md-2 text-center border-end border-2">
                        <?php echo get_avatar($comment, 64, '', '', ['class' => 'rounded-circle img-fluid']); ?>
                        <div>
                            <h5 class="card-title mb-1"><?php comment_author(); ?></h5>
                            <small class="text-muted"><?php comment_date(); ?> <?php comment_time(); ?></small>
                        </div>
                    </div>
                    <!-- Comment Content -->
                    <div class="col-12 col-md-9 mx-3">
                        <!-- Author Name and Date -->
                        
                        <!-- Comment Text -->
                        <p class="card-text mt-2"><?php comment_text(); ?></p>
                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap gap-2 mt-3">
                        <?php
                            echo get_like_button($comment->comment_ID);
                            echo get_subscribe_button();
                            echo get_reply_button($args, $depth, $comment->comment_ID);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
    <?php
}

// Episode wise view count start
function increase_episode_view_count() {
    global $post;
    $count = get_post_meta($post->ID, 'episode_view_count', true);
    $count = $count ? $count + 1 : 1;
    update_post_meta($post->ID, 'episode_view_count', $count);
}
add_action('wp_head', 'increase_episode_view_count');

function format_view_count($count) {
    if ($count >= 1000000) {
        return round($count / 1000000, 1) . 'M+';
    } elseif ($count >= 1000) {
        return round($count / 1000, 1) . 'K+';
    }
    return $count;
}
// Episode wise view count end

// contact mail start
function handle_contact_form_submission() {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = sanitize_textarea_field($_POST['message']);

        $admin_email = get_option('admin_email'); // Get the admin email
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $name . ' <' . $email . '>'
        ];

        $email_subject = "New Contact Form Submission: " . $subject;
        $email_body = "
            <h3>New Contact Form Submission</h3>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Subject:</strong> $subject</p>
            <p><strong>Message:</strong><br>$message</p>
        ";

        wp_mail($admin_email, $email_subject, $email_body, $headers);

        // Redirect to a thank you page or back to the form with a success message
        wp_redirect(home_url('/thank-you'));
        exit;
    }
}
add_action('admin_post_submit_contact_form', 'handle_contact_form_submission');
add_action('admin_post_nopriv_submit_contact_form', 'handle_contact_form_submission');
// contact mail end




?>
