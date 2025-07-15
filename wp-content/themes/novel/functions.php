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

    add_action('wp_footer', function () {
        echo '<script src="https://www.google.com/ime/transliteration?hl=en&callback=initTamilTyping" async defer></script>';
    });
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function enqueue_swiper_slider() {
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_swiper_slider');


function enqueue_comment_scripts() {
    // Enqueue jQuery (WordPress provides this by default)
    wp_enqueue_script('jquery');

    // Enqueue Textcomplete (must be before EmojiOneArea)
    wp_enqueue_script('jquery-textcomplete', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-textcomplete/1.8.5/jquery.textcomplete.min.js', ['jquery'], null, true);

    // Enqueue EmojiOneArea with textcomplete as a dependency
    wp_enqueue_script('emojionearea', 'https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js', ['jquery', 'jquery-textcomplete'], null, true);

    // CSS for EmojiOneArea and FontAwesome
    wp_enqueue_style('emojionearea-css', 'https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_comment_scripts');


// Enable featured images
add_theme_support('post-thumbnails');

// Logo upload start
function mytheme_custom_logo() {
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'mytheme_custom_logo');
// Logo upload end

// Remove admin bar start
add_filter('show_admin_bar', '__return_false');
// Remove admin bar end

function enqueue_trumbowyg() {
    wp_enqueue_style('trumbowyg-style', 'https://cdn.jsdelivr.net/npm/trumbowyg/dist/ui/trumbowyg.min.css');
    wp_enqueue_script('jquery'); // Ensure jQuery is loaded
    wp_enqueue_script('trumbowyg-script', 'https://cdn.jsdelivr.net/npm/trumbowyg/dist/trumbowyg.min.js', array('jquery'), null, true);
    wp_enqueue_script('trumbowyg-emoji', 'https://cdn.jsdelivr.net/npm/trumbowyg/dist/plugins/emoji/trumbowyg.emoji.min.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_trumbowyg');


// disable content select start
function disable_text_selection_script() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            $("body").css({
                "user-select": "none",
                "-webkit-user-select": "none",
                "-moz-user-select": "none",
                "-ms-user-select": "none"
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'disable_text_selection_script');
// disable content select end

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

    Kirki::add_field('your_theme_config', [
        'type'        => 'repeater',
        'settings'    => 'my_creation_banner_images',
        'label'       => esc_html__('My Creation Banner Images', 'your-theme'),
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

    Kirki::add_field('your_theme_config', [
        'type'        => 'repeater',
        'settings'    => 'competition_banner_images',
        'label'       => esc_html__('Competition Banner Images', 'your-theme'),
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

function register_blog_type_taxonomy() {
    register_taxonomy(
        'blog_type',
        'post', // attach to default post type
        array(
            'label' => __('Blog Type'),
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'blog-type'),
        )
    );
}
add_action('init', 'register_blog_type_taxonomy');

function add_parent_blog_meta_box() {
    add_meta_box(
        'parent_blog_selector',
        'Parent Blog',
        'render_parent_blog_meta_box',
        'post',
        'side'
    );
}
add_action('add_meta_boxes', 'add_parent_blog_meta_box');

// function render_parent_blog_meta_box($post) {
//     $selected_value = get_post_meta($post->ID, 'parent_blog_id', true);

//     echo '<label for="parent_blog_meta_field">Select Parent Blog:</label>';
//     echo '<select name="parent_blog_meta_field" class="widefat">';
//     echo '<option value="">Select Parent Blog</option>';
//     if (!empty($selected_value)) {
//         $post_obj = get_post($selected_value);
//         if ($post_obj) {
//             echo '<option value="' . esc_attr($post_obj->ID) . '" selected>' . esc_html($post_obj->post_title) . '</option>';
//         }
//     }
//     echo '</select>';
// }

function render_parent_blog_meta_box($post) {
    $selected_value_main = get_post_meta($post->ID, 'parent_blog_id', true);
    $selected_value_creation = get_post_meta($post->ID, 'my_creation_parent_blog_id', true);

    $terms = wp_get_post_terms($post->ID, 'blog_type');
    $blog_type = (!empty($terms)) ? $terms[0]->slug : '';

    $parent_posts = [];

    if ($blog_type === 'main-blog') {
        $parent_posts = get_posts([
            'post_type' => 'post',
            'tax_query' => [[
                'taxonomy' => 'blog_type',
                'field'    => 'slug',
                'terms'    => 'main-blog'
            ]],
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'exclude' => [$post->ID],
        ]);
    } elseif ($blog_type === 'my-creation-blog') {
        $parent_posts = get_posts([
            'post_type' => 'post',
            'tax_query' => [[
                'taxonomy' => 'blog_type',
                'field'    => 'slug',
                'terms'    => 'my-creation-blog'
            ]],
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'exclude' => [$post->ID],
        ]);
    }

    echo '<select name="parent_blog_meta_field" class="widefat">';
    echo '<option value="">Select Parent Blog</option>';
    foreach ($parent_posts as $parent) {
        $selected = '';
        if ($blog_type === 'main-blog' && $selected_value_main == $parent->ID) {
            $selected = 'selected';
        } elseif ($blog_type === 'my-creation-blog' && $selected_value_creation == $parent->ID) {
            $selected = 'selected';
        }

        echo '<option value="' . esc_attr($parent->ID) . '" ' . $selected . '>' . esc_html($parent->post_title) . '</option>';
    }
    echo '</select>';

    if (empty($blog_type)) {
        echo '<p style="color:#c00;"><strong>Select "Blog Type" first, then save to view parent options.</strong></p>';
    }
}

add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'post-new.php' || $hook === 'post.php') {
        wp_enqueue_script('blog-type-parent-meta', get_template_directory_uri() . '/js/blog-type-parent-meta.js', ['jquery'], null, true);
        wp_localize_script('blog-type-parent-meta', 'BlogTypeMetaAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fetch_parent_blogs'),
        ]);
    }
});

add_action('wp_ajax_fetch_parent_blogs', function () {
    check_ajax_referer('fetch_parent_blogs');
    global $wpdb;

    $blog_type_slug = sanitize_text_field($_POST['blogTypeSlug'] ?? '');
    $current_post_id = intval($_POST['current_post_id'] ?? 0);

    if (!$blog_type_slug) {
        wp_send_json_error('Missing blog type');
    }

    $term = get_term_by('slug', $blog_type_slug, 'blog_type');

    if ($term && !is_wp_error($term)) {
        $term_id = $term->term_id;
    } else {
        $term_id = null;
    }

    $parent_posts = get_posts([
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'tax_query'      => [
            [
                'taxonomy' => 'blog_type',
                'field'    => 'term_id',
                'terms'    => $term_id,
            ],
        ],
        'exclude'        => $current_post_id ? [$current_post_id] : [],
    ]);

    $result = array_map(function ($post) {
        return [
            'ID' => $post->ID,
            'title' => $post->post_title
        ];
    }, $parent_posts);

    wp_send_json_success($result);
});

function save_parent_blog_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['parent_blog_meta_field'])) {
        $parent_id = (int) $_POST['parent_blog_meta_field'];

        // Detect current blog type
        $terms = wp_get_post_terms($post_id, 'blog_type');
        $blog_type = (!empty($terms)) ? $terms[0]->slug : '';

        if ($blog_type === 'main-blog') {
            update_post_meta($post_id, 'parent_blog_id', $parent_id);
        } elseif ($blog_type === 'my-creation-blog') {
            update_post_meta($post_id, 'my_creation_parent_blog_id', $parent_id);
        }
    }
}
add_action('save_post', 'save_parent_blog_meta');

function enable_page_attributes_for_posts() {
    add_post_type_support('post', 'page-attributes');
}
add_action('init', 'enable_page_attributes_for_posts');

/////////////////////
function convert_main_blogs_to_posts_with_term() {
    // global $wpdb;

    // $term_taxonomy_id = null;
    // $term_id = $wpdb->get_var("
    //     SELECT term_id FROM {$wpdb->terms}
    //     WHERE slug = 'main-blog'
    //     Limit 1
    // ");

    // if ($term_id) {
    //    $term_taxonomy_id = $wpdb->get_var("
    //         SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy}
    //         WHERE term_id = {$term_id}
    //         Limit 1
    //     ");

    //     $converted_post_ids = [];

    //     $main_blog_posts = $wpdb->get_results("
    //         SELECT ID FROM {$wpdb->posts}
    //         WHERE post_type = 'main_blog'
    //         AND post_status = 'publish'
    //         AND ID = 50
    //     ");

    //     foreach ($main_blog_posts as $main_post) {
    //         $main_post_id = $main_post->ID;

    //         $wpdb->update(
    //             $wpdb->posts,
    //             ['post_type' => 'post'],
    //             ['ID' => $main_post_id]
    //         );
    //         $converted_post_ids[] = $main_post_id;

    //         $wpdb->insert(
    //             "{$wpdb->term_relationships}",
    //             [
    //                 'object_id' => $main_post_id,
    //                 'term_taxonomy_id' => $term_taxonomy_id,
    //             ]
    //         );

    //         $sub_blog_ids = $wpdb->get_col($wpdb->prepare("
    //             SELECT post_id FROM {$wpdb->postmeta}
    //             WHERE meta_key = 'parent_blog_id'
    //             AND meta_value = %d
    //         ", $main_post_id));

    //         foreach ($sub_blog_ids as $sub_post_id) {
    //             $wpdb->update(
    //                 $wpdb->posts,
    //                 ['post_type' => 'post'],
    //                 ['ID' => $sub_post_id]
    //             );
    //             $converted_post_ids[] = $sub_post_id;

    //             $wpdb->insert(
    //                 "{$wpdb->term_relationships}",
    //                 [
    //                     'object_id' => $sub_post_id,
    //                     'term_taxonomy_id' => $term_taxonomy_id,
    //                 ]
    //             );
    //         }
    //     }

    //     $added_count = count($converted_post_ids);
    //     $wpdb->query($wpdb->prepare("
    //         UPDATE {$wpdb->term_taxonomy}
    //         SET count = count + %d
    //         WHERE term_id = %d
    //     ", $added_count, $term_id));

    //     echo "Updated {$added_count} posts and applied term_taxonomy_id = {$term_taxonomy_id}.";
    // }
}

convert_main_blogs_to_posts_with_term();
/////////////////////


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

// Sync to create post when story create start
function sync_custom_blog_to_post($post_id) {
    remove_action('save_post_main_blog', 'sync_custom_blog_to_post');
    remove_action('save_post_sub_blog', 'sync_custom_blog_to_post');
    remove_action('save_post_my_creation_blog', 'sync_custom_blog_to_post');
    remove_action('save_post_my_creation_sub_blog', 'sync_custom_blog_to_post');
    remove_action('save_post_competition_post', 'sync_custom_blog_to_post');
    remove_action('save_post_competition_episode', 'sync_custom_blog_to_post');

    $post = get_post($post_id);

    if ($post->post_status != 'publish') {
        return;
    }

    $linked_post_id = get_post_meta($post_id, '_synced_wp_post_id', true);

    $post_data = array(
        'post_title'    => $post->post_title,
        'post_content'  => $post->post_content,
        'post_status'   => 'publish',
        'post_type'     => 'post',
    );

    if (!empty($linked_post_id) && get_post_status($linked_post_id)) {
        $post_data['ID'] = $linked_post_id;
        $original_thumb_id = get_post_thumbnail_id($post_id);

        $updated_post_id = wp_update_post($post_data);
    } else {
        $original_thumb_id = get_post_thumbnail_id($post_id);

        $updated_post_id = wp_insert_post($post_data);
        if ($updated_post_id) {
            update_post_meta($post_id, '_synced_wp_post_id', $updated_post_id);
        }
    }

    if ($original_thumb_id) {
        set_post_thumbnail($updated_post_id, $original_thumb_id);
    }

    add_action('save_post_main_blog', 'sync_custom_blog_to_post');
    add_action('save_post_sub_blog', 'sync_custom_blog_to_post');
    add_action('save_post_my_creation_blog', 'sync_custom_blog_to_post');
    add_action('save_post_my_creation_sub_blog', 'sync_custom_blog_to_post');
    add_action('save_post_competition_post', 'sync_custom_blog_to_post');
    add_action('save_post_competition_episode', 'sync_custom_blog_to_post');
}

add_action('save_post_main_blog', 'sync_custom_blog_to_post');
add_action('save_post_sub_blog', 'sync_custom_blog_to_post');
add_action('save_post_my_creation_blog', 'sync_custom_blog_to_post');
add_action('save_post_my_creation_sub_blog', 'sync_custom_blog_to_post');
add_action('save_post_competition_post', 'sync_custom_blog_to_post');
add_action('save_post_competition_episode', 'sync_custom_blog_to_post');
// Sync to create post when story create end

add_action('template_redirect', function () {
    if (is_single() && get_post_type() === 'post') {
        $original_id = get_post_meta(get_the_ID(), '_original_custom_post_id', true);
        $original_type = get_post_meta(get_the_ID(), '_original_custom_post_type', true);

        if ($original_id && $original_type) {
            $original_url = get_permalink($original_id);
            if ($original_url) {
                wp_redirect($original_url, 301);
                exit;
            }
        }
    }
});


// function add_parent_blog_meta_box() {
//     add_meta_box(
//         'parent_blog_meta',
//         'Select Parent Blog',
//         'parent_blog_meta_callback',
//         'sub_blog',
//         'side'
//     );
// }

// function parent_blog_meta_callback($post) {
//     $selected = get_post_meta($post->ID, 'parent_blog_id', true);
//     $args = array('post_type' => 'main_blog', 'posts_per_page' => -1);
//     $blogs = get_posts($args);

//     echo '<select name="parent_blog_id">';
//     echo '<option value="">Select a Main Blog</option>';
//     foreach ($blogs as $blog) {
//         echo '<option value="' . $blog->ID . '" ' . selected($selected, $blog->ID, false) . '>' . $blog->post_title . '</option>';
//     }
//     echo '</select>';
// }

// function save_parent_blog_meta($post_id) {
//     if (isset($_POST['parent_blog_id'])) {
//         update_post_meta($post_id, 'parent_blog_id', $_POST['parent_blog_id']);
//     }
// }

// add_action('add_meta_boxes', 'add_parent_blog_meta_box');
// add_action('save_post', 'save_parent_blog_meta');

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

    // Handle image upload
    if (isset($_FILES['post_image']) && !empty($_FILES['post_image']['tmp_name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    
        $uploaded = media_handle_upload('post_image', $post_id);
    
        if (!is_wp_error($uploaded)) {
            set_post_thumbnail($post_id, $uploaded);
        }
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
                    <a class="fw-bold" href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a>
                    <p style="font-size: 0.8rem;" class="m-0">' . esc_html($user->display_name) . '</p>
                </td>
                <td class="align-middle">
                    <div class="d-flex justify-content-between align-items-center my-1" style="font-size: 0.9rem;">
                        <div class="d-flex align-items-center">
                            <p class="me-4 mb-0">
                                <i class="fa-solid fa-eye"></i>&nbsp;&nbsp;' . format_view_count($total_views) . '
                            </p>
                            <p class="mb-0">
                                <i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp; ' . $average_rating . '
                            </p>
                        </div>
                    </div>
                </td>
                <td class="align-middle">
                    <p class="mb-0 mt-2">' . $tamil_date . '</p>
                </td>
                <td class="align-middle">';

            $competition_created_date = get_the_date('Y-m-d', get_the_ID());
            $two_days_after = date('Y-m-d', strtotime($competition_created_date . ' +2 days'));
            $current_date = date('Y-m-d');
            if (get_current_user_id() === get_the_author_meta('ID') && $current_date <= $two_days_after) {
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
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);

        $response['status'] = 'success';
        $response['message'] = 'Login successful!';
        // $response['redirect_url'] = home_url();
    }

    wp_send_json($response);
}
add_action('wp_ajax_nopriv_ajax_login', 'ajax_login_handler');
add_action('wp_ajax_ajax_login', 'ajax_login_handler');

// Logout start
add_action('init', 'handle_custom_logout');

function handle_custom_logout() {
    if (isset($_GET['custom_logout']) && is_user_logged_in()) {
        wp_logout();
        wp_safe_redirect(remove_query_arg('custom_logout'));
        exit;
    }
}
// Logout end


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
            wp_clear_auth_cookie();
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, true);

            // Login successful, clear session and redirect
            session_start();
            unset($_SESSION['login_error']);
            wp_redirect(home_url());
            exit;
        }
    }
}
add_action('init', 'handle_frontend_login');

function get_like_button($comment_id) {
    $likes = get_comment_meta($comment_id, 'likes', true) ?: 0;
    return '<a href="javascript:void(0);" class="like-comment" data-comment-id="' . $comment_id . '">
                <i class="fa-solid fa-thumbs-up fa-lg"></i> <span class="like-count">' . format_view_count($likes) . '</span>
            </a>';
}

function get_unlike_button($comment_id) {
    $unlikes = get_comment_meta($comment_id, 'unlikes', true) ?: 0;
    return '<a href="javascript:void(0);" class="text-danger unlike-comment" data-comment-id="' . $comment_id . '">
                <i class="fa-solid fa-thumbs-down fa-lg"></i> <span class="unlike-count">' . format_view_count($unlikes) . '</span>
            </a>';
}

// Handle Like button click
function handle_comment_like() {
    $comment_id = intval($_POST['comment_id']);

    if (!$comment_id) {
        wp_send_json_error('Invalid comment ID');
    }

    $user_id = get_current_user_id();
    $user_key = $user_id ? 'user_' . $user_id : 'anon_' . $_SERVER['REMOTE_ADDR'];
    $liked_users = get_comment_meta($comment_id, 'liked_users', true);
    $liked_users = is_array($liked_users) ? $liked_users : [];

    $likes = get_comment_meta($comment_id, 'likes', true) ?: 0;

    if (isset($liked_users[$user_key])) {
        unset($liked_users[$user_key]);
        $likes = max(0, $likes - 1);
    } else {
        $liked_users[$user_key] = current_time('timestamp');
        $likes++;
    }

    update_comment_meta($comment_id, 'likes', $likes);
    update_comment_meta($comment_id, 'liked_users', $liked_users);

    wp_send_json_success($likes);
}
add_action('wp_ajax_like_comment', 'handle_comment_like');
add_action('wp_ajax_nopriv_like_comment', 'handle_comment_like');

// Handle UnLike button click
function handle_comment_unlike() {
    $comment_id = intval($_POST['comment_id']);
    $unlikes = get_comment_meta($comment_id, 'unlikes', true) ?: 0;
    $unlikes++;
    update_comment_meta($comment_id, 'unlikes', $unlikes);
    wp_send_json_success($unlikes);
}
add_action('wp_ajax_unlike_comment', 'handle_comment_unlike');
add_action('wp_ajax_nopriv_unlike_comment', 'handle_comment_unlike');

// Enqueue scripts
function enqueue_like_comment_script() {
    wp_enqueue_script('comment-like', get_template_directory_uri() . '/js/comment-like.js', ['jquery'], null, true);
    wp_localize_script('comment-like', 'commentLike', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);

    wp_localize_script('comment-like', 'commentUnLike', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_like_comment_script');

function bootstrap5_comment_callback($comment, $args, $depth) {
    // Only show parent comments
    if ($comment->comment_parent != 0) return;

    $comment_id = $comment->comment_ID;
    $episode_id = $comment->comment_post_ID;
    $user_id = $comment->user_id;
    $rating = get_user_meta($user_id, "episode_rating_{$episode_id}", true);

    // Get child comments
    $child_comments = get_comments([
        'parent' => $comment_id,
        'status' => 'approve',
        'order' => 'ASC',
    ]);
    $child_count = count($child_comments);
    ?>
    <li id="comment-<?php comment_ID(); ?>">
        <div class="row">
            <div class="col-2">
                <?php echo get_avatar($comment, 64, '', '', ['class' => 'rounded-circle img-fluid']); ?>
            </div>
            <div class="col-10 text-start">
                <h6 class="mb-0"><?php comment_author(); ?></h6>
                <div class="d-flex align-items-center justify-content-between">
                    <?php if ($rating): ?>
                        <div class="comment-rating">
                            <?php for ($i = 1; $i <= 5; $i++) {
                                echo ($i <= $rating) ? '<span style="color: gold;">★</span>' : '<span style="color: gold;">☆</span>';
                            } ?>
                        </div>
                    <?php endif; ?>

                    <small class="text-muted"><?php echo date('F j, Y', strtotime($comment->comment_date)); ?></small>
                </div>

                <div class="mt-2 d-inline-block p-2 border rounded comment-text" style="background-color: #f3f3f3;">
                    <p class="mb-0 text-wrap"><?php comment_text(); ?></p>
                </div>

                <div class="d-flex align-items-center gap-4 mt-3">
                    <?php echo get_like_button($comment_id); ?>

                    <?php if (is_user_logged_in()) { ?>
                        <?php if ($child_count > 0): ?>
                            <a href="#" class="text-decoration-none text-primary-color" onclick="event.preventDefault(); toggleChildComments(<?php echo $comment_id; ?>)">
                                <i class="fa fa-reply"></i> <span class="<?php echo 'reply-count-' . $comment_id; ?>"><?php echo $child_count; ?></span> <span class="<?php echo 'reply-count-text-' . $comment_id; ?>"><?php echo _n('Reply', 'Replies', $child_count); ?></span>
                            </a>
                        <?php else: ?>
                            <a href="#" class="text-decoration-none text-primary-color" onclick="event.preventDefault(); toggleChildComments(<?php echo $comment_id; ?>)">
                                <i class="fa fa-reply"></i> <span class="<?php echo 'reply-count-' . $comment_id; ?>"></span> <span class="<?php echo 'reply-count-text-' . $comment_id; ?>"> Reply</span>
                            </a>
                        <?php endif; ?>
                    <?php } ?>
                </div>

                <!-- Child Comments Container -->
                <div id="child-comments-<?php echo $comment_id; ?>" class="mt-3 d-none">
                    <?php
                        foreach ($child_comments as $child_comment) { ?>
                            <hr/>
                            <div class="row">
                                <div class="col-2">
                                    <?php echo get_avatar($comment, 64, '', '', ['class' => 'rounded-circle img-fluid']); ?>
                                </div>
                                <div class="col-10 text-start">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h6 class="mb-0"><?php echo get_comment_author($child_comment); ?></h6>

                                        <small class="text-muted"><?php echo date('F j, Y', strtotime($child_comment->comment_date)); ?></small>
                                    </div>

                                    <div class="mt-2 d-inline-block p-2 border rounded comment-text" style="background-color: #f3f3f3;">
                                        <p class="mb-0 text-wrap"><?php echo esc_html($child_comment->comment_content); ?></p>
                                    </div>
                                </div>
                            </div>
                    <?php } ?>

                    <?php 
                    if (comments_open($episode_id)) {
                        comment_form([
                            'fields' => [],
                            'submit_field' => '',
                            'submit_button' => '',
                            'logged_in_as' => '',
                            'comment_field' => '
                                <hr/>
                                <div class="comment-box p-2 rounded position-relative">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1 position-relative">
                                            <textarea name="comment" class="reply-form-text form-control comment-text" rows="1" placeholder="Reply..." required></textarea>
                                            <input type="hidden" name="comment_post_ID" value="' . esc_attr($episode_id) . '">
                                            <input type="hidden" name="comment_parent" value="' . esc_attr($comment_id) . '">
                                        </div>
                                        <button type="button" class="btn btn-primary send-comment-reply ms-2">
                                            <i class="fa-solid fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>'
                        ], $episode_id);
                    } else {
                        echo '<p class="text-danger">Comments are closed for this post.</p>';
                    }
                    ?>
                </div>

                <hr/>
            </div>
        </div>
    </li>
    <?php
}


function set_default_story_view_count($post_id) {
    if (get_post_type($post_id) === 'main_blog' || get_post_type($post_id) === 'my_creation_blog') {
        $views = get_post_meta($post_id, 'story_view_count', true);
        if ($views === '') { // If meta field does not exist, set it to 0
            update_post_meta($post_id, 'story_view_count', 0);
        }
    }
}
add_action('save_post', 'set_default_story_view_count');

// Episode wise view count start
function increase_episode_view_count() {
    global $post;
    $count = get_post_meta($post->ID, 'episode_view_count', true);
    $count = $count ? $count + 1 : 1;
    update_post_meta($post->ID, 'episode_view_count', $count);
}
add_action('wp_head', 'increase_episode_view_count');

function increase_story_view_count() {
    if (is_singular('post')) {
        global $post;
        $count = get_post_meta($post->ID, 'story_view_count', true);
        $count = $count ? $count + 1 : 1;
        update_post_meta($post->ID, 'story_view_count', $count);
    }
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

    $existing_reaction = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE episode_id = %d AND reaction = %s AND user_id = %d",
        $episode_id, $reaction, $user_id
    ));

    if (!$existing_reaction) {
        $sql = $wpdb->prepare(
            "INSERT INTO $table_name (episode_id, reaction, user_id) VALUES (%d, %s, %d)",
            $episode_id, $reaction, $user_id
        );

        $wpdb->query($sql);
    } else {
        $wpdb->delete(
            $table_name,
            [
                'episode_id' => $episode_id,
                'reaction' => $reaction,
                'user_id' => $user_id
            ],
            ['%d', '%s', '%d']
        );
    }

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
        'posts_per_page' => -1,
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

// competition close option start
function add_competition_meta_box() {
    add_meta_box(
        'competition_closed_meta_box',
        'Competition Settings',
        'render_competition_closed_meta_box',
        'competition',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'add_competition_meta_box');

function render_competition_closed_meta_box($post) {
    $competition_closed = get_post_meta($post->ID, '_competition_closed', true);
    ?>
    <label for="competition_closed">
        <input type="checkbox" name="competition_closed" id="competition_closed" value="1" <?php checked($competition_closed, '1'); ?>>
        Mark as Closed (Disable Story Submissions)
    </label>
    <?php
}

function save_competition_meta_box($post_id) {
    if (isset($_POST['competition_closed'])) {
        update_post_meta($post_id, '_competition_closed', '1');
    } else {
        update_post_meta($post_id, '_competition_closed', '0');
    }
}
add_action('save_post', 'save_competition_meta_box');

// competition close option end


// external links start
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_media();
});


function custom_external_novels_menu() {
    add_menu_page(
        'External Novels',
        'External Novels',
        'manage_options',
        'external-novels',
        'custom_external_novels_page_html',
        'dashicons-book',
        20
    );
}
add_action('admin_menu', 'custom_external_novels_menu');

function custom_external_novels_page_html() {
    if (!current_user_can('manage_options')) return;

    if (isset($_POST['external_novels_links'])) {
        update_option('external_novels_links', $_POST['external_novels_links']);
        echo '<div class="updated"><p>Links updated successfully.</p></div>';
    }

    $links = get_option('external_novels_links', []);

    echo '<div class="wrap">';
    echo '<h1>External Novel Links</h1>';
    echo '<form method="post">';
    echo '<table id="novel-links-table">';
    echo '<tr><th>Title</th><th>Image</th><th>External Link</th><th>Action</th></tr>';

    if (!empty($links)) {
        foreach ($links as $index => $link) {
            echo '<tr>
                <td><input type="text" name="external_novels_links[' . $index . '][title]" value="' . esc_attr($link['title']) . '" placeholder="Title"></td>
                <td>
                    <input type="hidden" class="image-url" name="external_novels_links[' . $index . '][image]" value="' . esc_url($link['image']) . '">
                    <img src="' . esc_url($link['image']) . '" class="image-preview" style="max-height: 50px;"><br>
                    <button type="button" class="upload-image-button button">Upload Image</button>
                </td>
                <td><input type="url" name="external_novels_links[' . $index . '][url]" value="' . esc_url($link['url']) . '" placeholder="https://example.com"></td>
                <td><button type="button" class="delete-link button">Delete</button></td>
            </tr>';
        }
    }

    echo '</table>';
    echo '<p><button type="button" id="add-more-links" class="button">Add More</button></p>';
    echo '<p><input type="submit" class="button-primary" value="Save Links"></p>';
    echo '</form>';
    echo '</div>';

    // JS
    echo '
        <script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentIndex = ' . count($links) . ';
            function bindUploadButton(button, container) {
                button.addEventListener("click", function(e) {
                    e.preventDefault();
                    var customUploader = wp.media({
                        title: "Choose Image",
                        button: { text: "Select Image" },
                        multiple: false
                    }).on("select", function() {
                        var attachment = customUploader.state().get("selection").first().toJSON();
                        var imgUrl = attachment.url;
                        container.querySelector(".image-url").value = imgUrl;
                        container.querySelector(".image-preview").src = imgUrl;
                    }).open();
                });
            }

            function bindDeleteButton(button) {
                button.addEventListener("click", function() {
                    const row = button.closest("tr");
                    row.remove();
                });
            }

            // Bind existing buttons
            document.querySelectorAll(".upload-image-button").forEach(function(button) {
                let container = button.closest("td");
                bindUploadButton(button, container);
            });

            document.querySelectorAll(".delete-link").forEach(function(button) {
                bindDeleteButton(button);
            });

            document.getElementById("add-more-links").addEventListener("click", function () {
                var table = document.getElementById("novel-links-table");
                var row = table.insertRow();
                row.innerHTML = `
                    <td><input type="text" name="external_novels_links[${currentIndex}][title]" placeholder="Title"></td>
                    <td>
                        <input type="hidden" class="image-url" name="external_novels_links[${currentIndex}][image]">
                        <img src="" class="image-preview" style="max-height: 50px;"><br>
                        <button type="button" class="upload-image-button button">Upload Image</button>
                    </td>
                    <td><input type="url" name="external_novels_links[${currentIndex}][url]" placeholder="https://example.com"></td>
                    <td><button type="button" class="delete-link button">Delete</button></td>
                `;
                let newContainer = row.querySelector("td:nth-child(2)");
                let newButton = row.querySelector(".upload-image-button");
                let newDeleteButton = row.querySelector(".delete-link");

                bindUploadButton(newButton, newContainer);
                bindDeleteButton(newDeleteButton);

                currentIndex++; // increment for next added row
            });
        });
        </script>';
    
        echo '<style>
            #novel-links-table {
                width: 80%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            #novel-links-table th,
            #novel-links-table td {
                border: 1px solid #ccd0d4;
                padding: 8px;
                text-align: center;
            }
            #novel-links-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            #novel-links-table td:nth-child(2) {
                text-align: center;
            }
            #novel-links-table img.image-preview {
                max-height: 50px;
                display: block;
                margin: 0 auto 5px auto; /* centers image and adds bottom spacing */
            }

            #novel-links-table th {
                background-color: #f1f1f1;
            }
            #novel-links-table img.image-preview {
                max-height: 50px;
                display: block;
                margin-bottom: 5px;
            }
            #novel-links-table td {
                vertical-align: middle;
            }
        </style>';
}

add_action('wp_ajax_ajax_comment', 'handle_ajax_comment');
add_action('wp_ajax_nopriv_ajax_comment', 'handle_ajax_comment');

function handle_ajax_comment() {
    $comment_data = wp_handle_comment_submission(wp_unslash($_POST));

    if (is_wp_error($comment_data)) {
        wp_send_json_error($comment_data->get_error_message());
    } else {
        ob_start();
        wp_list_comments([
            'callback' => 'bootstrap5_comment_callback',
            'style'    => 'ul',
            'max_depth' => 1,
        ], [$comment_data]);
        $comment_html = ob_get_clean();

        wp_send_json_success(['comment_html' => $comment_html]);
    }
}

add_action('wp_ajax_ajax_reply_comment', 'handle_ajax_reply_comment');
add_action('wp_ajax_nopriv_ajax_reply_comment', 'handle_ajax_reply_comment');

function handle_ajax_reply_comment() {
    $comment_data = [
        'comment_post_ID' => intval($_POST['comment_post_ID']),
        'comment_content' => sanitize_text_field($_POST['comment']),
        'comment_parent' => intval($_POST['comment_parent']),
        'user_id' => get_current_user_id(),
        'comment_author' => wp_get_current_user()->display_name,
        'comment_author_email' => wp_get_current_user()->user_email,
    ];

    $comment_id = wp_new_comment($comment_data);

    if ($comment_id) {
        $comment = get_comment($comment_id);
        ob_start();
        // Render your single comment HTML structure
        ?>
        <div class="row">
            <div class="col-2">
                <?php echo get_avatar($comment, 64, '', '', ['class' => 'rounded-circle img-fluid']); ?>
            </div>
            <div class="col-10 text-start">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0"><?php echo get_comment_author($comment); ?></h6>
                    <small class="text-muted"><?php echo date('F j, Y', strtotime($comment->comment_date)); ?></small>
                </div>
                <div class="mt-2 d-inline-block p-2 border rounded comment-text" style="background-color: #f3f3f3;">
                    <p class="mb-0 text-wrap"><?php echo esc_html($comment->comment_content); ?></p>
                </div>
            </div>
        </div>
        <hr/>
        <?php
        $html = ob_get_clean();

        wp_send_json_success(['comment_html' => $html]);
    } else {
        wp_send_json_error('Failed to add comment');
    }
}




?>
