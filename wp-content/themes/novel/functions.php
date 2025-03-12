<?php
require_once get_template_directory() . '/inc/wp-bootstrap-navwalker.php';

// Enqueue Bootstrap and Font Awesome
function my_theme_enqueue_styles() {
    wp_enqueue_style('google-font-css', 'https://fonts.googleapis.com/css2?family=Noto+Sans+Tamil&display=swap');

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

// Remove Howdy start
function change_howdy_text($translated_text, $text, $domain) {
    if ($text === 'Howdy, %s') {
        return '%s';
    }
    return $translated_text;
}
add_filter('gettext', 'change_howdy_text', 10, 3);
// Remove Howdy end

if (class_exists('Kirki')) {
    Kirki::add_config('your_theme_config', array(
        'capability'    => 'edit_theme_options',
        'option_type'   => 'theme_mod',
    ));

    Kirki::add_section('banner_section', array(
        'title'    => __('Banner Carousel', 'your-theme'),
        'priority' => 30,
    ));

    Kirki::add_field('your_theme_config', [
        'type'        => 'repeater',
        'settings'    => 'banner_images',
        'label'       => esc_html__('Banner Images', 'your-theme'),
        'section'     => 'banner_section',
        'priority'    => 10,
        'row_label'   => [
            'type'  => 'text',
            'value' => esc_html__('Banner Image', 'your-theme'),
        ],
        'fields' => [
            'image' => [
                'type'  => 'image',
                'label' => esc_html__('Image', 'your-theme'),
            ],
            'link' => [
                'type'  => 'url',
                'label' => esc_html__('Link', 'your-theme'),
            ],
        ],
    ]);
}

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
            'supports'    => array('title', 'editor', 'thumbnail', 'custom-fields', 'comments'),
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

function enable_comments_for_main_blog($post_types) {
    $post_types[] = 'main_blog'; // Ensure 'main_blog' supports comments
    return $post_types;
}
add_filter('comments_open', function ($open, $post_id) {
    $post = get_post($post_id);
    if ($post->post_type === 'main_blog') {
        return true; // Force open comments
    }
    return $open;
}, 10, 2);

// category and blog end

// My creation start
function create_custom_post_types_for_my_creation() {
    register_post_type('my_creation_blog',
        array(
            'labels'      => array(
                'name'          => __('My Creation Blogs'),
                'singular_name' => __('My Creation Blog'),
            ),
            'public'      => true,
            'show_ui'     => true,
            'show_in_menu' => true,
            'has_archive' => true,
            'supports'    => array('title', 'editor', 'thumbnail', 'custom-fields', 'comments'),
            'taxonomies'  => array('category'),
            'hierarchical' => true, 
            'rewrite' => array('slug' => 'my-creation-blog', 'with_front' => false),
        )
    ); 

    register_post_type('my_creation_sub_blog',
        array(
            'labels'      => array(
                'name'          => __('My Creation Sub Blogs'),
                'singular_name' => __('My Creation Sub Blogs'),
            ),
            'public'      => true,
            'has_archive' => true,
            'supports'    => array('title', 'editor', 'thumbnail', 'custom-fields', 'comments'),
            'taxonomies'  => array('category'),
            'hierarchical' => false,
            'rewrite' => array('slug' => 'my-creation-sub-blogs'),
        )
    );
}
add_action('init', 'create_custom_post_types_for_my_creation');

function add_parent_blog_meta_box_for_my_creation() {
    add_meta_box(
        'parent_blog_meta',
        'Select Parent Blog',
        'my_creation_parent_blog_meta_callback',
        'my_creation_sub_blog',
        'side'
    );
}

function my_creation_parent_blog_meta_callback($post) {
    $selected = get_post_meta($post->ID, 'my_creation_parent_blog_id', true);
    $args = array('post_type' => 'my_creation_blog', 'posts_per_page' => -1);
    $blogs = get_posts($args);

    echo '<select name="my_creation_parent_blog_id">';
    echo '<option value="">Select a Main Blog</option>';
    foreach ($blogs as $blog) {
        echo '<option value="' . $blog->ID . '" ' . selected($selected, $blog->ID, false) . '>' . $blog->post_title . '</option>';
    }
    echo '</select>';
}

function save_parent_blog_meta_for_my_creation($post_id) {
    if (isset($_POST['my_creation_parent_blog_id'])) {
        update_post_meta($post_id, 'my_creation_parent_blog_id', $_POST['my_creation_parent_blog_id']);
    }
}

add_action('add_meta_boxes', 'add_parent_blog_meta_box_for_my_creation');
add_action('save_post', 'save_parent_blog_meta_for_my_creation');
// My creation end

// competition start 20220214
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
            'name' => __('Competition Stories'),
            'singular_name' => __('Competition Stories')
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
        wp_send_json_error("You must be logged in to submit a story.");
    }

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $post_title = sanitize_text_field($_POST['post_title']);
    $post_content = wp_kses_post(wp_unslash($_POST['post_content']));
    $competition_id = isset($_POST['competition_id']) ? intval($_POST['competition_id']) : 0;
    $redirect_url = get_permalink($competition_id);

    $post_data = [
        'post_title'   => $post_title,
        'post_content' => $post_content,
        'post_status'  => 'publish',
        'post_type'    => 'competition_post',
        'post_author'  => get_current_user_id(),
        'meta_input'   => ['competition_id' => $competition_id]
    ];

    if ($post_id > 0) {
        $post_data['ID'] = $post_id;
        wp_update_post($post_data);
    } else {
        $post_id = wp_insert_post($post_data);
    }

    if ($post_id) {
        $redirect_url = get_permalink($competition_id);
        wp_send_json_success(['redirect_url' => $redirect_url]);
    } else {
        wp_send_json_error("Error submitting post.");
    }
}
add_action('wp_ajax_submit_competition_post', 'handle_competition_post_submission');
add_action('wp_ajax_nopriv_submit_competition_post', 'handle_competition_post_submission');

function fetch_competition_posts() {
    $competition_id = isset($_POST['competition_id']) ? intval($_POST['competition_id']) : 0;
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $posts_per_page = 10;

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
            $story_id = get_the_ID();
            $total_views = get_story_total_views('competition_episode', 'story_id', $story_id);
            $average_rating = get_story_average_rating('competition_episode', 'story_id', $story_id);

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
                <td class="px-4 class="align-middle"">
                    <a class="fs-6 fw-bold" href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a>
                    <p style="font-size: 0.8rem;" class="m-0">' . esc_html($user->display_name) . '</p>
                </td>
                <td class="align-middle">
                    <div class="d-flex justify-content-between align-items-center my-1" style="font-size: 0.9rem;">
                        <div class="d-flex align-items-center">
                            <p class="me-4 mb-0">
                                <i class="fa-solid fa-eye"></i>&nbsp;&nbsp;' . format_view_count($total_views) . '
                            </p>
                            <p class="mb-0">
                                <i class="fa-solid fa-star"></i>&nbsp;&nbsp; ' . $average_rating . '
                            </p>
                        </div>
                    </div>
                </td>
                <td class="align-middle">
                    <p class="mb-0 mt-2">' . $tamil_date . '</p>
                </td>
                <td class="align-middle">';

            if (get_current_user_id() === get_the_author_meta('ID')) {
                $edit_url = get_permalink(get_page_by_path('submit-story')) . '?competition_id=' . $competition_id . '&post_id=' . get_the_ID();
                $output .= '<a href="' . esc_url($edit_url) . '"><i class="fa-solid fa-pen-to-square fa-xl"></i></a>';
            }
            
            $output .= '</td></tr>';
        }
    } else {
        $output .= '<tr><td colspan="2">No stories found.</td></tr>';
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

// episodes
function create_episode_post_type() {
    register_post_type('competition_episode', array(
        'labels' => array(
            'name' => __('Competition Story - Episodes'),
            'singular_name' => __('Competition Story - Episodes')
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'episodes'),
        'supports' => array('title', 'editor', 'author', 'comments'),
    ));
}
add_action('init', 'create_episode_post_type');

function add_episode_meta_boxes() {
    add_meta_box(
        'episode_story_meta',
        __('Select Story'),
        'episode_story_meta_callback',
        'competition_episode',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_episode_meta_boxes');

function episode_story_meta_callback($post) {
    $selected_story = get_post_meta($post->ID, 'story_id', true);
    $stories = get_posts(array('post_type' => 'competition_post', 'numberposts' => -1));

    echo '<select name="story_id">';
    echo '<option value="">Select a Story</option>';
    foreach ($stories as $story) {
        echo '<option value="' . $story->ID . '" ' . selected($selected_story, $story->ID, false) . '>' . $story->post_title . '</option>';
    }
    echo '</select>';
}

function save_episode_meta($post_id) {
    if (isset($_POST['story_id'])) {
        update_post_meta($post_id, 'story_id', sanitize_text_field($_POST['story_id']));
    }
}
add_action('save_post', 'save_episode_meta');

// Add custom column to Episodes admin table start
function add_episode_columns($columns) {
    $new_columns = array();

    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value; // Add existing columns
        
        if ($key == 'title') { 
            $new_columns['story'] = __('Story'); // Insert Story column after Title
        }
    }

    return $new_columns;
}
add_filter('manage_edit-competition_episode_columns', 'add_episode_columns');

// Populate the custom column with the mapped story title
function fill_episode_columns($column, $post_id) {
    if ($column == 'story') {
        $story_id = get_post_meta($post_id, 'story_id', true);
        if ($story_id) {
            $story_title = get_the_title($story_id);
            echo '<a href="' . get_edit_post_link($story_id) . '">' . esc_html($story_title) . '</a>';
        } else {
            echo __('No Story Mapped');
        }
    }
}
add_action('manage_competition_episode_posts_custom_column', 'fill_episode_columns', 10, 2);


function make_episode_columns_sortable($columns) {
    $columns['story'] = 'story';
    return $columns;
}
add_filter('manage_edit-competition_episode_sortable_columns', 'make_episode_columns_sortable');

function sort_episodes_by_story($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($orderby = $query->get('orderby')) {
        if ($orderby == 'story') {
            $query->set('meta_key', 'story_id');
            $query->set('orderby', 'meta_value_num');
        }
    }
}
add_action('pre_get_posts', 'sort_episodes_by_story');
// Add custom column to Episodes admin table end

// episode submission
function handle_episode_submission() {
    if (!is_user_logged_in()) {
        wp_send_json_error("You must be logged in submit an episode.");
    }

    $episode_title = sanitize_text_field($_POST['episode_title']);
    $episode_content = wp_kses_post(wp_unslash($_POST['episode_content']));
    $story_id = isset($_POST['story_id']) ? intval($_POST['story_id']) : 0;
    $episode_id = isset($_POST['episode_id']) ? intval($_POST['episode_id']) : 0;

    $post_data = array(
        'post_title'   => $episode_title,
        'post_content' => $episode_content,
        'post_status'  => 'publish',
        'post_type'    => 'competition_episode',
        'post_author'  => get_current_user_id()
    );

    if ($episode_id) {
        $post_data['ID'] = $episode_id;
        $episode_id = wp_update_post($post_data);
    } else {
        $post_data['post_parent'] = $story_id;
        $episode_id = wp_insert_post($post_data);
        update_post_meta($episode_id, 'story_id', $story_id);
    }

    if ($episode_id) {
        $redirect_url = get_permalink($story_id);
        wp_send_json_success(['redirect_url' => $redirect_url]);
    } else {
        wp_send_json_error("Error submitting episode.");
    }
}
add_action('wp_ajax_submit_episode', 'handle_episode_submission');
add_action('wp_ajax_nopriv_submit_episode', 'handle_episode_submission');






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
    // check_ajax_referer('register_nonce', 'security');

    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];
    $email = sanitize_text_field($_POST['email']);
    $firstname = sanitize_text_field($_POST['firstname']);
    $lastname = sanitize_text_field($_POST['lastname']);

    $errors = [];

    if (empty($username) || empty($password) || empty($email) || empty($firstname) || empty($lastname)) {
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
    $comment_id = get_comment_ID(); 
    $episode_id = get_comment($comment_id)->comment_post_ID; // Get Episode (Post) ID
    $user_id = $comment->user_id; // Get User ID
    $rating = get_user_meta($user_id, "episode_rating_{$episode_id}", true);

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

                            <?php if ($rating) : ?>
                        <div class="comment-rating">
                            <?php for ($i = 1; $i <= 5; $i++) {
                                echo ($i <= $rating) ? '<span style="color: #061148;">★</span>' : '<span style="color: #061148;">☆</span>';
                            } ?>
                        </div>
                    <?php endif; ?> 
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

function increase_story_view_count() {
    global $post;
    $count = get_post_meta($post->ID, 'story_view_count', true);
    $count = $count ? $count + 1 : 1;
    update_post_meta($post->ID, 'story_view_count', $count);
}
add_action('wp_head', 'increase_story_view_count');

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
// function handle_contact_form_submission() {
//     if ($_SERVER["REQUEST_METHOD"] === "POST") {
//         $name = sanitize_text_field($_POST['name']);
//         $email = sanitize_email($_POST['email']);
//         $subject = sanitize_text_field($_POST['subject']);
//         $message = sanitize_textarea_field($_POST['message']);

//         $admin_email = get_option('admin_email');
//         $headers = [
//             'Content-Type: text/html; charset=UTF-8',
//             'From: ' . $name . ' <' . $email . '>'
//         ];

//         $email_subject = "New Contact Form Submission: " . $subject;
//         $email_body = "
//             <h3>New Contact Form Submission</h3>
//             <p><strong>Name:</strong> $name</p>
//             <p><strong>Email:</strong> $email</p>
//             <p><strong>Subject:</strong> $subject</p>
//             <p><strong>Message:</strong><br>$message</p>
//         ";

//         wp_mail($admin_email, $email_subject, $email_body, $headers);

//         wp_redirect(home_url('/thank-you'));
//         exit;
//     }
// }
// add_action('admin_post_submit_contact_form', 'handle_contact_form_submission');
// add_action('admin_post_nopriv_submit_contact_form', 'handle_contact_form_submission');

function send_contact_mail() {
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_textarea_field($_POST['message']);

    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        wp_send_json_error('All fields are required.');
    }

    if (!is_email($email)) {
        wp_send_json_error('Invalid email address.');
    }

    $admin_email = get_option('admin_email');

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        "From: $name <$email>",
        "Reply-To: $email"
    ];

    // HTML Email Body
    $body = '
        <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
            <h2 style="background: #061148; color: white; padding: 10px; text-align: center;">New Contact Form Submission</h2>
            <p><strong>Name:</strong> ' . $name . '</p>
            <p><strong>Email:</strong> ' . $email . '</p>
            <p><strong>Message:</strong></p>
            <div style="background: #f8f9fa; padding: 10px; border-left: 3px solid #061148; overflow: hidden !important; display: block !important; height: auto !important; min-height: 1px !important; max-height: none !important;">
                ' . nl2br($message) . '
            </div>
            <p style="text-align: center;">Thank you!</p>
        </div>
    ';

    // Send email
    if (wp_mail($admin_email, $subject, $body, $headers)) {
        wp_send_json_success('Message sent successfully.');
    } else {
        wp_send_json_error('Failed to send message. Try again later.');
    }

    wp_die();
}
add_action('wp_ajax_send_contact_mail', 'send_contact_mail');
add_action('wp_ajax_nopriv_send_contact_mail', 'send_contact_mail');


// contact mail end


// emojis start
function get_emoji_count($episode_id, $reaction) {
    global $wpdb;
    return (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM wp_episode_reactions WHERE episode_id = %d AND reaction = %s", 
        $episode_id, $reaction
    ));
}

add_action('wp_ajax_add_reaction', 'add_reaction');
add_action('wp_ajax_nopriv_add_reaction', 'add_reaction');

function add_reaction() {
    global $wpdb;
    $table_name = $wpdb->prefix . "episode_reactions";

    $episode_id = intval($_POST['episode_id']);
    $user_id = intval($_POST['user_id']);
    $emoji = sanitize_text_field($_POST['emoji']);
    $reaction = sanitize_text_field($_POST['reaction']);

    if (!$episode_id || !$emoji) {
        wp_send_json_error(['message' => 'Invalid data']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . "episode_reactions";

    $sql = $wpdb->prepare(
        "INSERT INTO $table_name (episode_id, reaction) VALUES (%d, %s)",
        $episode_id, $reaction
    );

    $wpdb->query($sql);

    // $existing_reaction = $wpdb->get_row($wpdb->prepare(
    //     "SELECT * FROM $table_name WHERE episode_id = %d AND reaction = %s",
    //     $episode_id, $reaction
    // ));

    // if ($existing_reaction) {
        // Remove reaction if already exists
        // $wpdb->delete($table_name, [
        //     'episode_id' => $episode_id,
        //     'user_id' => $user_id,
        //     'reaction' => $reaction
        // ]);
    // } else {
        // Insert new reaction
        // $wpdb->insert($table_name, [
        //     'episode_id' => $episode_id,
        //     'user_id' => $user_id,
        //     'reaction' => $reaction
        // ]);

//         global $wpdb;
// $table_name = $wpdb->prefix . "episode_reactions";

// $sql = $wpdb->prepare(
//     "INSERT INTO $table_name (episode_id, reaction) VALUES (%d, %s)",
//     $episode_id, $reaction
// );

// $wpdb->query($sql);
    // }

    // Get updated reaction count
    $reaction_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE episode_id = %d AND reaction = %s",
        $episode_id, $reaction
    ));

    wp_send_json_success(['count' => $reaction_count]);
}

add_action('wp_ajax_add_reaction', 'add_reaction');
add_action('wp_ajax_nopriv_add_reaction', 'add_reaction');

function enqueue_custom_scripts() {
    wp_enqueue_script('emojis-js', get_template_directory_uri() . '/js/emojis.js', ['jquery'], null, true);
    wp_localize_script('emojis-js', 'ajaxurl', [
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('emojis_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
// emojis end

function enqueue_ckeditor5() {
    wp_enqueue_script('ckeditor5', 'https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_ckeditor5');

// Rating start

function enqueue_rating_scripts() {
    wp_enqueue_script('rating-script', get_template_directory_uri() . '/js/rating.js', ['jquery'], null, true);

    wp_localize_script('rating-script', 'my_ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('rate_episode_nonce'),
        'is_logged_in' => is_user_logged_in() ? true : false,
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_rating_scripts');

function save_episode_rating() {
    check_ajax_referer('rate_episode_nonce', 'security');

    $user_id = get_current_user_id();
    $episode_id = intval($_POST['episode_id']);
    $rating = intval($_POST['rating']);

    if ($user_id && $episode_id && $rating >= 1 && $rating <= 5) {
        update_user_meta($user_id, "episode_rating_{$episode_id}", $rating);
        wp_send_json_success(['message' => 'Rating saved']);
    } else {
        wp_send_json_error(['message' => 'Invalid data']);
    }
}
add_action('wp_ajax_save_episode_rating', 'save_episode_rating');
add_action('wp_ajax_nopriv_save_episode_rating', 'save_episode_rating');

// get rating for episodes
function get_average_episode_rating($episode_id) {
    global $wpdb;

    $meta_key = "episode_rating_{$episode_id}";
    $ratings = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = %s",
            $meta_key
        )
    );

    if (empty($ratings)) {
        return 0;
    }

    $total_ratings = count($ratings);
    $sum_ratings = array_sum($ratings);

    return round($sum_ratings / $total_ratings, 1);
}

// get view count for main story
function get_story_total_views($postType, $postKey, $storyId) {
    global $wpdb;

    $episodes = get_posts([
        'post_type'   => $postType,
        'meta_query' => array(
            array(
                'key' => $postKey,
                'value' => $storyId,
                'compare' => '='
            )
            ),
            'orderby' => 'date',
            'order'   => 'ASC'
    ]);

    if (empty($episodes)) {
        return get_post_meta($storyId, 'story_view_count', true) ?: 0;
    }

    $total_views = 0;
    foreach ($episodes as $episode) {
        $views = get_post_meta($episode->ID, 'episode_view_count', true);
        $total_views += intval($views);
    }

    return $total_views;
}

// get rating for story
function get_story_average_rating($postType, $postKey, $storyId) {
    global $wpdb;

    $episodes = get_posts([
        'post_type'   => $postType,
        'meta_query' => array(
            array(
                'key' => $postKey,
                'value' => $storyId,
                'compare' => '='
            )
            ),
            'orderby' => 'date',
            'order'   => 'ASC'
    ]);

    if (empty($episodes)) {
        $ratings = $wpdb->get_col(
            $wpdb->prepare("SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = %s", "episode_rating_{$storyId}")
        );

        return (!empty($ratings)) ? round(array_sum($ratings) / count($ratings), 1) : 0;
    }

    $all_ratings = [];
    foreach ($episodes as $episode) {
        $meta_key = "episode_rating_{$episode->ID}";
        $ratings = $wpdb->get_col(
            $wpdb->prepare("SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = %s", $meta_key)
        );

        if (!empty($ratings)) {
            $all_ratings = array_merge($all_ratings, array_map('intval', $ratings));
        }
    }

    return (!empty($all_ratings)) ? round(array_sum($all_ratings) / count($all_ratings), 1) : 0;
}


// Rating end





?>
