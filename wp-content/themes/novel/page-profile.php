<?php
/* Template Name: User Profile */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

get_header();

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : '';

$current_user = wp_get_current_user();
$profile_picture_id = get_user_meta($current_user->ID, 'profile_picture', true);
$profile_picture_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : get_template_directory_uri() . '/images/profile-no-image.jpg';

// Handle profile picture update
if (isset($_POST['update_profile_picture']) && !empty($_FILES['profile_picture']['name'])) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $attachment_id = media_handle_upload('profile_picture', 0);

    if (!is_wp_error($attachment_id)) {
        update_user_meta($current_user->ID, 'profile_picture', $attachment_id);
        $profile_picture_url = wp_get_attachment_url($attachment_id);
    }
}

$userId = $user_id ? $user_id : get_current_user_id();

$post_count = count_user_posts($userId, 'post');

$isWriter = $post_count > 0 ? true : false;

$loginUserPostCount = count_user_posts(get_current_user_id(), 'post');

$isLoggedInUserWriter = $loginUserPostCount > 0 ? true : false;
?>

<div class="container my-5">
    <div class="d-flex align-items-center mb-2">
        <h3 class="mb-0 me-3 fw-bold">Profile</h3>
        <?php if (get_current_user_id() == $userId) { ?>
            <button class="btn btn-primary" id="editProfileBtn"><i class="fa-solid fa-pen me-1"></i> Edit Your Profile</button>
        <?php } ?>
    </div>

    <!-- Profile Section -->
    <div class="col-12 text-center mb-3">
        <?php get_template_part('template-parts/author-detail', null, ['user_id' => $userId]); ?>
    </div>

    <!-- Stories Section -->
    <?php if ($isWriter): ?>
        <h3>Stories</h3>
        <div class="row mt-3">
            <div class="col-12 px-4">
                <?php get_template_part('template-parts/other-stories', null, ['user_id' => $userId]); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (is_user_logged_in() && !$isLoggedInUserWriter): 
        $user_id = get_current_user_id();
        $recent_posts = get_user_meta($user_id, 'recently_read_posts', true);
    ?>
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                    History
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3" id="profileTabsContent">

                <!-- History Tab -->
                <div class="tab-pane fade show active" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <?php if (!empty($recent_posts)): ?>
                        <div class="row px-2">
                            <?php 
                            $counter = 1;
                            foreach ($recent_posts as $post_id): 
                                $title = get_the_title($post_id);
                                $url   = get_permalink($post_id);

                                // Check parent (could be parent_blog_id or my_creation_parent_blog_id)
                                $parent_id = get_post_meta($post_id, 'parent_blog_id', true);
                                if (!$parent_id) {
                                    $parent_id = get_post_meta($post_id, 'my_creation_parent_blog_id', true);
                                }

                                // Default values
                                $image = $parent_title = $parent_url = '';

                                if ($parent_id) {
                                    // Parent details
                                    $parent_title = get_the_title($parent_id);
                                    $parent_url   = get_permalink($parent_id);

                                    $image = get_the_post_thumbnail(
                                        $parent_id,
                                        'full',
                                        [
                                            'class' => 'img-fluid rounded',
                                            'style' => 'width:160px; height:250px; object-fit:cover;'
                                        ]
                                    );
                                } else {
                                    $image = get_the_post_thumbnail(
                                        $post_id,
                                        'full',
                                        [
                                            'class' => 'img-fluid rounded',
                                            'style' => 'width:160px; height:250px; object-fit:cover;'
                                        ]
                                    );
                                }

                                // Example rating count (replace with your rating logic)
                                $terms = wp_get_post_terms($post_id, 'blog_type');
                                if (empty($terms)) continue;

                                $term_slug = $terms[0]->slug;

                                if ($term_slug === 'main-blog') {
                                    $sub_meta_key = 'parent_blog_id';
                                } elseif ($term_slug === 'my-creation-blog') {
                                    $sub_meta_key = 'my_creation_parent_blog_id';
                                } else {
                                    continue;
                                }

                                if ($parent_id) {
                                    $average_rating = get_story_average_rating('post', $sub_meta_key, $parent_id);
                                } else {
                                    $average_rating = get_story_average_rating('post', $sub_meta_key, $post_id);
                                }
                            ?>
                                <div class="col-12 col-md-6 col-xl-4 col-xxl-3 d-flex align-items-start mt-4">
                                    <?php if ($image): ?>
                                        <div class="me-3 flex-shrink-0" style="width:160px; height:250px; overflow:hidden;">
                                            <?php echo $image; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div>
                                        <?php if ($parent_id): ?>
                                            <div>
                                                <a href="<?php echo esc_url($parent_url); ?>" class="fw-bold text-primary-color d-block fs-6 mb-2">
                                                    <?php echo esc_html($parent_title); ?>
                                                </a>
                                            </div>
                                            <div>
                                                <a href="<?php echo esc_url($url); ?>" class="text-primary-color d-block mb-2" style="font-size: 15px;">
                                                    <?php echo esc_html($title); ?>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div>
                                                <a href="<?php echo esc_url($url); ?>" class="fw-bold text-primary-color d-block fs-6 mb-2">
                                                    <?php echo esc_html($title); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <span class="text-primary-color"><strong>Rating:</strong>&nbsp; <i class="fa-solid fa-star" style="color: gold;"></i>&nbsp; <?php echo $average_rating; ?></span>

                                        <div class="mt-2">
                                            <?php
                                                $author_id = get_post_field('post_author', $post_id);
                                                $author_name = get_the_author_meta('display_name', $author_id);
                                                $profile_picture_id = get_user_meta($author_id, 'profile_picture', true);
                                                $profile_picture_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : get_template_directory_uri() . '/images/profile-no-image.jpg';
                                                $post_count = count_user_posts($author_id, 'post');

                                                if ($post_count > 0) {
                                                    $icon_html = '<i class="fa-solid fa-pen-nib me-1"></i>';
                                                } else {
                                                    $icon_html = '<i class="fa-solid fa-book-reader me-1"></i>';
                                                }
                                            ?>
                                                <a href="<?php echo site_url('/profile/?user_id=' . $author_id); ?>" class="text-primary-color text-decoration-underline mb-1 fs-15px">
                                                    <img src="<?php echo esc_url($profile_picture_url); ?>" alt="<?php echo $author_id ?>" class="rounded-circle me-2" height="40" width="40">
                                                    <?php echo $icon_html; ?>
                                                    <?php echo esc_html($author_name); ?>
                                                </a>
                                            </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                </div>
                    <?php else: ?>
                        <ul class="list-unstyled">
                            <li>No recently read stories</li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

    <?php endif; ?>
</div>

<script>
document.getElementById('change-photo-btn').addEventListener('click', function() {
    document.getElementById('profile-picture-input').click();
});

document.getElementById('profile-picture-input').addEventListener('change', function() {
    if(this.files.length > 0){
        document.getElementById('profile-picture-form').submit();
    }
});
</script>

<?php get_footer(); ?>