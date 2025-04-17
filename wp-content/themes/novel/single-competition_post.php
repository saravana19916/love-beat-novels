<?php get_header(); ?>

<?php
    $story_id = get_the_ID();
    $competition_id = get_post_meta($story_id, 'competition_id', true);
    $current_user_id = get_current_user_id();
    $story_author_id = get_post_field('post_author', $story_id);
?>

<div class="container mt-5">
    <?php
        $competition_closed = get_post_meta($competition_id, '_competition_closed', true);
        if ($competition_closed == '1') {
    ?>
        <div class="alert alert-warning text-center mx-auto competition-alert mb-4">
            This competition is currently closed for submissions.
        </div>
    <?php } ?>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="shadow-lg p-4 rounded">
                <div class="row">
                    <div class="col-md-4">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url('large'); ?>" class="img-fluid rounded shadow" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>

                    <div class="col-md-8">
                        <h5 class="fw-bold mb-3"><?php the_title(); ?></h5>
                        <div class="mb-3 col-md-8">
                            <?php echo wpautop(get_the_content()); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5 mb-4">
        <div class="col-md-6">
            <h6 class="text-primary-color fw-bold">
                அத்தியாயங்கள்
            </h6>
        </div>
        <div class="col-md-6 text-end">
            <?php if (is_user_logged_in()) { ?>

                <?php if ($current_user_id == $story_author_id && $competition_closed != '1') {
                    $submit_story_url = get_permalink(get_page_by_path('submit-competition-episode')) . '?story_id=' . $story_id;
                ?>
                    <button class="btn btn-primary btn-sm" onclick="window.location.href='<?php echo esc_url($submit_story_url); ?>'">
                        <i class="fa-solid fa-plus fa-lg"></i>&nbsp; Create Episode
                    </button>
                <?php } ?>
            <?php } else { ?>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">Login to create episodes</button>
            <?php } ?>
        </div>
    </div>

    <div class="row mb-5 episode-padding">
        <?php
        $count = 0;
        $args = array(
            'post_type' => 'competition_episode',
            'meta_query' => array(
                array(
                    'key' => 'story_id',
                    'value' => $story_id,
                    'compare' => '='
                ),
            ),
            'orderby' => 'date',
            'order'   => 'ASC',
            'posts_per_page' => -1
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
            $query->the_post();
            $views = get_post_meta(get_the_ID(), 'episode_view_count', true);
            $views = $views ?: 0;

            // get average rating
            $episode_id = get_the_ID();
            $average_rating = get_average_episode_rating($episode_id);

            $edit_url = get_permalink(get_page_by_path('submit-competition-episode')) . '?story_id=' . $story_id . '&post_id=' . $episode_id;

            $episode_created_date = get_the_date('Y-m-d', $episode_id);
            $two_days_after = date('Y-m-d', strtotime($episode_created_date . ' +2 days'));
            $current_date = date('Y-m-d');
        ?>
            <div class="col-lg-6 col-xxl-4">
                <div class="shadow-lg rounded mt-4">
                    <div class="d-flex justify-content-between align-items-center px-4 pt-4">
                        <h6 class="mb-0 fw-bold">
                            <?php echo sprintf("%2d", $count + 1); ?>.&nbsp
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h6>

                        <?php 
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
                        ?>

                        <span class="text-muted fs-custom"><?php echo $tamil_date; ?></span>
                    </div>

                    <div>
                    
                    </div>

                    <div class="d-flex justify-content-between align-items-center px-5 my-3 fs-custom">
                        <div class="d-flex align-items-center">
                            <p class="me-4">
                                <i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($views); ?>
                            </p>
                            <p>
                                <i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?>
                            </p>
                        </div>
                        
                        <?php if ($current_user_id == $story_author_id && $current_date <= $two_days_after) { ?>
                            <a href="<?php echo esc_url($edit_url); ?>" class="text-muted mb-4">
                                <i class="fa-solid fa-pen-to-square fa-lg text-primary-color"></i>
                            </a>
                        <?php } ?>
                    </div>

                </div>
            </div>

        <?php
            $count++;
            }
        }
        wp_reset_postdata();
        ?>
    </div>

</div>

<?php get_footer(); ?>
