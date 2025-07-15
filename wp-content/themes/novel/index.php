<?php
/* Template Name: Paginated Blogs by Category */

get_header(); // Include the header
?>

<?php
    // $banner_image = get_theme_mod('banner_image');
    // $banner_link = get_theme_mod('banner_link');

    // if ($banner_image) {
    //     echo '<a href="' . esc_url($banner_link) . '" target="_blank">';
    //     echo '<img src="' . esc_url($banner_image) . '" class="banner" alt="Banner Image" style="width: 100%;height: 450px;">';
    //     echo '</a>';
    // } else {
    //     echo '<p>No banner image selected.</p>';
    // }
?>

<?php
// Add the extisting stories to post start
// $main_blog_posts = get_posts(array(
//     'post_type'   => ['main_blog', 'sub_blog', 'my_creation_blog', 'my_creation_sub_blog', 'competition_post', 'competition_episode'],
//     'numberposts' => -1,
//     'post_status' => 'publish',
// ));

// foreach ($main_blog_posts as $post) {
//     $synced_post_id = get_post_meta($post->ID, '_synced_wp_post_id', true);

//     $post_data = array(
//         'post_title'   => $post->post_title,
//         'post_content' => $post->post_content,
//         'post_status'  => 'publish',
//         'post_type'    => 'post',
//     );

//     if ($synced_post_id && get_post($synced_post_id)) {
//         $post_data['ID'] = $synced_post_id;
//         $original_thumb_id = get_post_thumbnail_id($post->ID);

//         $updated_post_id = wp_update_post($post_data);

        
//     } else {
//         $original_thumb_id = get_post_thumbnail_id($post->ID);
//         $updated_post_id = wp_insert_post($post_data);

//         if ($updated_post_id) {
//             update_post_meta($post->ID, '_synced_wp_post_id', $updated_post_id);
//         }
//     }

//     if ($original_thumb_id) {
//         set_post_thumbnail($updated_post_id, $original_thumb_id);
//     }

//     if ($updated_post_id) {
//         update_post_meta($updated_post_id, '_original_custom_post_id', $post->ID);
//         update_post_meta($updated_post_id, '_original_custom_post_type', $post->post_type);
//     }
// }
// Add the extisting stories to post end

// Delete posts
// $custom_posts = get_posts([
//     'post_type'      => 'post',
//     'posts_per_page' => -1,
//     'post_status'    => 'any',
// ]);

// foreach ($custom_posts as $post) {

//     if ($post->ID && get_post($post->ID)) {
//         wp_delete_post($post->ID, true);

//         delete_post_meta($post->ID, '_synced_wp_post_id');
//         delete_post_meta($post->ID, '_original_custom_post_id');
//         delete_post_meta($post->ID, '_original_custom_post_type');
//     }
// }
?>

<div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
    <!-- Indicators -->
    <div class="carousel-indicators">
        <?php
        $banner_images = get_theme_mod('banner_images', []);

        foreach ($banner_images as $index => $image) {
            $active = $index === 0 ? 'active' : '';
            echo "<button type='button' data-bs-target='#bannerCarousel' data-bs-slide-to='{$index}' class='{$active}' aria-current='true' aria-label='Slide " . ($index + 1) . "'></button>";
        }
        ?>
    </div>
    <!-- <pre><?php print_r($banner_images); ?></pre> -->


    <!-- Slides -->
    <div class="carousel-inner">
        <?php foreach ($banner_images as $index => $image): ?>
            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <a href="<?php echo esc_url($image['link']); ?>" target="_blank">
                    <img src="<?php echo esc_url($image['image']); ?>" class="d-block w-100 custom-img-height" alt="<?php echo esc_attr($image['alt']); ?>">
                </a>
                <!-- <div class="carousel-caption d-none d-md-block">
                    <h5>வலைத்தளத்தில் எழுத புதிய எழுத்தாளர்கள் வரவேற்கப்படுகிறார்கள்.</h5>
                </div> -->
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>


<div class="container my-4">
    <div class="shadow rounded px-4 d-flex align-items-center justify-content-center fw-bold text-primary-color h-auto h-lg-100">
        <?php
            $writePageUrl = get_permalink(get_page_by_path('write'));
        ?>
        <i class="fa-solid fa-book fa-lg"></i> &nbsp; &nbsp;
            <span class="p-3">
                வலைத்தளத்தில் எழுத புதிய எழுத்தாளர்கள் வரவேற்கப்படுகிறார்கள். உங்கள் பதிவை எழுத &nbsp;
                <a class="text-underline text-danger" href="<?php echo esc_url($writePageUrl); ?>">click here</a>
            </span>
        &nbsp; &nbsp; <i class="fa-solid fa-book fa-lg"></i>
    </div>

    <div class="col-md-12 mt-4">
        <?php 
            $latest_post_query = new WP_Query([
                'post_type'      => ['main_blog', 'sub_blog'],
                'posts_per_page' => 10,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);

            if ($latest_post_query->have_posts()) :
        ?>
            <div class="row">
                <div class="col-lg-10 px-4">

                    <!-- Trending stories start -->
                    <?php get_template_part('template-parts/trending-stories'); ?>
                    <!-- Trending stories end -->

                    <!-- Other stories start -->
                    <?php get_template_part('template-parts/other-stories'); ?>
                    <!-- Other stories end -->

                     <!-- Competition stories start -->
                    <?php get_template_part('template-parts/competition-stories'); ?>
                    <!-- Competition stories end -->                  

                </div>

                <div class="col-lg-2 px-4">
                    <div class="row mb-5 shadow rounded sticky-top" style="height: 25rem; top: 20px; overflow-y: auto">
                        <div class="col-md-12 p-0 text-center" id="latestPosts">
                            <h6 class="text-primary px-4 py-2 text-white" style="background-color: #061148"><?php echo "Latest posts"; ?></h6>
                            <?php
                                // $latest_post_query = new WP_Query([
                                //     'post_type'      => ['main_blog', 'sub_blog'],
                                //     'posts_per_page' => 10,
                                //     'orderby'        => 'date',
                                //     'order'          => 'DESC',
                                // ]);

                                if ($latest_post_query->have_posts()) :
                                    while ($latest_post_query->have_posts()) : $latest_post_query->the_post();
                                ?>
                                    <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                                <?php
                                    endwhile;
                                    wp_reset_postdata();
                                else :
                                    echo '<p>No latest post found.</p>';
                                endif;
                                ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php else : ?>
                <div class="row justify-content-center">
                    <div class="col-md-6 text-center mt-5">
                        <h4 class="text-primary-color">No post found.</h4>
                    </div>
                </div>
            <?php endif; ?>
    </div>
</div>

<?php get_footer(); // Include the footer ?>
