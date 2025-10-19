<?php get_header(); ?>

<div class="container my-5">
    <?php
        $competition_closed = get_post_meta(get_the_ID(), '_competition_closed', true); 
        if ($competition_closed == '1') {
    ?>
        <div class="alert alert-warning text-center mx-auto competition-alert mb-5">
            This competition is currently closed for submissions.
        </div>
    <?php } ?>

    <div>
        <?php if (have_posts()) :
            the_post();

            $competition_id = get_the_ID();
            $default_category_id = get_post_meta($competition_id, '_default_category', true);
            $category_name = '';
            if ($default_category_id) {
                $category = get_category($default_category_id);
                $category_name = $category ? $category->name : '';
            }
        ?>
            

                <!-- List Existing Posts -->
                 <h4 class="text-primary-color fw-bold text-center"><?php the_title(); ?></h4>
                 <h6 class="text-primary-color fw-bold text-center">Category: <?php echo $category_name; ?></h6>
                 <div class="shadow-lg rounded mt-4 p-4 shadow-div">
                    <div class="card-text mt-3 px-3 py-2" style="max-height: 600px; overflow-y: auto;">
                            <?php
                                $content = get_post_meta(get_the_ID(), '_rules', true);
                                echo wpautop(wp_kses_post($content));
                            ?>
                        </div>
                </div>

                <input type="hidden" id="competition-id" value="<?php echo get_the_ID(); ?>">
                <div class="row mt-5 mb-3">
                    <div class="col-6">
                        <h5 class="text-primary-color fw-bold">
                            Competition Stories
                        </h5>
                    </div>
                    <div class="col-6 text-end">
                        <?php if (is_user_logged_in()) { ?>
                            <?php
                                $submit_story_url = get_permalink(get_page_by_path('submit-story')) . '?competition_id=' . get_the_ID();
                                if ($competition_closed != '1') {
                            ?>
                                <button class="btn primary-btn btn-sm" onclick="window.location.href='<?php echo esc_url($submit_story_url); ?>'">
                                    <i class="fa-solid fa-plus fa-lg"></i>&nbsp; Create Story
                                </button>
                            <?php } ?>
                        <?php } else { ?>
                            <button class="btn primary-btn btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">Login to create stories</button>
                        <?php } ?>
                    </div>
                </div>

                <?php 
                    $related_args = array(
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

                    $query = new WP_Query($related_args);

                    if ($query->have_posts()) :
                ?>
                    <div class="related-stories mt-4">
                        <div class="row">
                           <?php 
                $count = 0;
                while ($query->have_posts()) : $query->the_post();
                    $count++;
                    $story_id = get_the_ID();
                    $total_views = get_story_total_views('post', 'story_id', $story_id);
                    $average_rating = get_story_average_rating('post', 'story_id', $story_id);

                    $edit_url = get_permalink(get_page_by_path('submit-story')) . '?competition_id=' . $competition_id . '&post_id=' . $story_id;

                    $story_created_date = get_the_date('Y-m-d', $story_id);
                    $two_days_after = date('Y-m-d', strtotime($story_created_date . ' +2 days'));
                    $current_date = date('Y-m-d');
                    $story_author_id = get_post_field('post_author', $story_id);
                    $current_user_id = get_current_user_id();
                ?>
                    <div class="col-lg-4 col-xxl-3 p-3 d-none d-lg-flex">
                        <div class="card h-100 bg-transparent shadow-div w-100">
                            <div class="card-body">
                                <h6 class="card-title text-center fw-bold fs-14px">
                                    <a href="<?php the_permalink(); ?>" class="text-decoration-none text-primary-color">
                                        <?php the_title(); ?>
                                    </a>
                                </h6>
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', ['class' => 'img-fluid mx-auto d-block my-3']); ?>
                                    </a>
                                <?php else : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg" class="img-fluid mx-auto d-block my-3" alt="Default Image" style="height: 300px;">
                                    </a>
                                <?php endif; ?>
                                <p class="card-text text-primary-color"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                            </div>
                            <div class="card-footer shadow-div">
                                <div class="d-flex justify-content-between align-items-center my-1">
                                    <div class="d-flex align-items-center">
                                        <p class="me-4 mb-0 text-primary-color"><i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($total_views); ?></p>
                                        <p class="me-4 mb-0 text-primary-color"><i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?></p>
                                        <?php if ($current_user_id == $story_author_id && $current_date <= $two_days_after) { ?>
                                            <a href="<?php echo esc_url($edit_url); ?>" class="text-muted">
                                                <i class="fa-solid fa-pen-to-square fa-lg text-primary-color"></i>
                                            </a>
                                        <?php } ?>
                                    </div>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white fs-12px primary-btn">மேலும் படிக்க</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

                        </div>
                    </div>
                <?php else : ?>
                    <p class="mt-4">No stories found.</p>
                <?php
                    endif;
                    wp_reset_postdata();
                ?>

                <?php if ($query->have_posts()) : ?>
                    <div class="row mb-5 d-lg-none">
                        <div class="swiper-container px-3">
                            <div class="swiper-wrapper">
                                <?php $count = 0;
                                    while ($query->have_posts()) : $query->the_post();
                                        $count++;
                                        $story_id = get_the_ID();
                                        $total_views = get_story_total_views('post', 'story_id', $story_id);
                                        $average_rating = get_story_average_rating('post', 'story_id', $story_id);

                                        $edit_url = get_permalink(get_page_by_path('submit-story')) . '?competition_id=' . $competition_id . '&post_id=' . $story_id;

                                        $story_created_date = get_the_date('Y-m-d', $story_id);
                                        $two_days_after = date('Y-m-d', strtotime($story_created_date . ' +2 days'));
                                        $current_date = date('Y-m-d');
                                        $story_author_id = get_post_field('post_author', $story_id);
                                        $current_user_id = get_current_user_id();
                                ?>
                                    <div class="swiper-slide custom-width">
                                        <div class="card h-100 bg-transparent shadow-div">
                                            <div class="card-body">
                                                <h6 class="card-title text-center fw-bold fs-14px">
                                                    <a href="<?php the_permalink(); ?>" class="text-decoration-none text-primary-color">
                                                        <?php the_title(); ?>
                                                    </a>
                                                </h6>
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <a href="<?php the_permalink(); ?>">
                                                        <?php the_post_thumbnail('medium', [
                                                            'class' => 'img-fluid mx-auto d-block my-3',
                                                            'style' => 'height: 250px; width: 165px;'
                                                        ]); ?>
                                                    </a>
                                                <?php else : ?>
                                                    <a href="<?php the_permalink(); ?>">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg" class="img-fluid mx-auto d-block my-3" alt="Default Image" style="height: 250px; width: 165px;">
                                                    </a>
                                                <?php endif; ?>
                                                <p class="card-text text-primary-color"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                            </div>
                                            <div class="card-footer shadow-div">
                                                <div class="d-flex justify-content-between align-items-center my-1">
                                                    <div class="d-flex align-items-center">
                                                        <p class="me-4 mb-0 text-primary-color"><i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($total_views); ?></p>
                                                        <p class="me-4 mb-0 text-primary-color"><i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?></p>
                                                        <?php if ($current_user_id == $story_author_id && $current_date <= $two_days_after) { ?>
                                                            <a href="<?php echo esc_url($edit_url); ?>" class="text-muted">
                                                                <i class="fa-solid fa-pen-to-square fa-lg text-primary-color"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <p class="mt-4">No stories found.</p>
                <?php
                    endif;
                    wp_reset_postdata();
                ?>

            <?php
        endif; ?>
    </div>
</div>

<?php get_footer(); ?>

