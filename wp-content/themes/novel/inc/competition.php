<?php

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

// Add Meta Boxes
function competition_add_meta_boxes() {
    add_meta_box(
        'competition_details',
        'Competition Details',
        'competition_render_meta_box',
        'competition',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'competition_add_meta_boxes');


function competition_render_meta_box($post) {
    // Nonce for security
    wp_nonce_field('competition_save_meta_box', 'competition_meta_box_nonce');

    // Get saved values
    $is_series = get_post_meta($post->ID, '_is_series', true);
    $rules = get_post_meta($post->ID, '_rules', true);
    $default_category = get_post_meta($post->ID, '_default_category', true);

    // Fetch WordPress categories
    $categories = get_categories(array('hide_empty' => false));
    ?>
    <table class="form-table">
        <tr>
            <th><label for="is_series">Is Series?</label></th>
            <td>
                <input type="checkbox" name="is_series" id="is_series" value="1" <?php checked($is_series, '1'); ?> />
                <label for="is_series">Yes, this competition is a series</label>
            </td>
        </tr>
        <tr>
            <th><label for="rules">Rules</label></th>
            <td>
                <?php
                wp_editor($rules, 'rules', array(
                    'textarea_name' => 'rules',
                    'media_buttons' => true,
                    'textarea_rows' => 8,
                    'teeny' => false,
                ));
                ?>
            </td>
        </tr>
        <tr>
            <th><label for="default_category">Default Category</label></th>
            <td>
                <select name="default_category" id="default_category">
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($default_category, $cat->term_id); ?>>
                            <?php echo esc_html($cat->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function competition_save_meta_box_data($post_id) {
    // Check nonce
    if (!isset($_POST['competition_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['competition_meta_box_nonce'], 'competition_save_meta_box')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) return;

    // Save Is Series
    $is_series = isset($_POST['is_series']) ? '1' : '0';
    update_post_meta($post_id, '_is_series', $is_series);

    // Save Rules
    if (isset($_POST['rules'])) {
        update_post_meta($post_id, '_rules', wp_kses_post($_POST['rules']));
    }

    // Save Default Category
    if (isset($_POST['default_category'])) {
        update_post_meta($post_id, '_default_category', sanitize_text_field($_POST['default_category']));
    }
}
add_action('save_post_competition', 'competition_save_meta_box_data');


//competition submit
function handle_competition_post_submission() {
    if (!is_user_logged_in()) {
        wp_send_json_error("You must be logged in to submit a story.");
    }

    $category_id = isset($_POST['post_category']) ? intval($_POST['post_category']) : 0;
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $post_title = sanitize_text_field($_POST['post_title']);
    $post_content = wp_kses_post(wp_unslash($_POST['post_content']));
    $competition_id = isset($_POST['competition_id']) ? intval($_POST['competition_id']) : 0;
    $redirect_url = get_permalink($competition_id);

    $post_data = [
        'post_title'   => $post_title,
        'post_content' => $post_content,
        'post_status'  => 'publish',
        'post_type'    => 'post',
        'post_author'  => get_current_user_id(),
        'meta_input'   => ['competition_id' => $competition_id]
    ];

    if ($post_id > 0) {
        $post_data['ID'] = $post_id;
        wp_update_post($post_data);
    } else {
        $post_id = wp_insert_post($post_data);
    }

   if (!is_wp_error($post_id) && $post_id && $category_id > 0) {
        wp_set_post_categories($post_id, [(int)$category_id]);
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
        if (term_exists('competition-blog', 'blog_type')) {
            wp_insert_term('Competition Blog', 'blog_type', ['slug' => 'competition-blog']);
        }
        wp_set_object_terms($post_id, 'competition-blog', 'blog_type');

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
        'post_type'      => 'post',
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
            $total_views = get_story_total_views('post', 'story_id', $story_id);
            $average_rating = get_story_average_rating('post', 'story_id', $story_id);

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
                <td class="px-4 class="align-middle text-primary-colo"">
                    <a class="fw-bold text-primary-color" href="' . get_permalink(get_the_ID()) . '">' . get_the_title() . '</a>
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