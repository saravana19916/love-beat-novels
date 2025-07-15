<?php
    $main_stories = get_posts(array(
        'post_type'      => ['main_blog', 'my_creation_blog'],
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ));

    $stories_with_views = [];

    foreach ($main_stories as $story) {
        $main_id = $story->ID;

        $post_type = get_post_type($story);
        if ($post_type === 'main_blog') {
            $sub_post_type = 'sub_blog';
            $sub_meta_key  = 'parent_blog_id';
        } elseif ($post_type === 'my_creation_blog') {
            $sub_post_type = 'my_creation_sub_blog';
            $sub_meta_key  = 'my_creation_parent_blog_id';
        }

        // Main story views
        $main_views = (int) get_post_meta($main_id, 'story_view_count', true);

        // Sum sub story views
        $sub_stories = get_posts(array(
            'post_type'      => 'sub_blog',
            'post_status'    => 'publish',
            'meta_key'       => 'parent_blog_id',
            'meta_value'     => $main_id,
            'posts_per_page' => -1,
        ));

        $sub_total = 0;
        foreach ($sub_stories as $sub_story) {
            $sub_total += (int) get_post_meta($sub_story->ID, 'story_view_count', true);
        }

        // $total_views = $main_views + $sub_total;

        $total_views = get_story_total_views($sub_post_type, $sub_meta_key, $main_id);

        // Find average rating
        $average_rating = get_story_average_rating($sub_post_type, $sub_meta_key, $main_id);

        $stories_with_views[] = [
            'post'  => $story,
            'views' => $total_views,
            'average_rating' => $average_rating
        ];
    }

    $competition_args = array(
        'post_type'      => 'competition_post',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    $competition_query = new WP_Query($competition_args);

    if ($competition_query->have_posts()) {
        while ($competition_query->have_posts()) {
            $competition_query->the_post();
            $story_id = get_the_ID();

            $total_views = get_story_total_views('competition_episode', 'story_id', $story_id);
            $average_rating = get_story_average_rating('competition_episode', 'story_id', $story_id);

            $stories_with_views[] = [
                'post'  => get_post($story_id),
                'views' => $total_views,
                'average_rating' => $average_rating
            ];
        }
    }

    usort($stories_with_views, function ($a, $b) {
        return $b['views'] - $a['views'];
    });

    $top_stories = array_slice($stories_with_views, 0, 10);
?>
    <div class="row mb-5 shadow rounded d-none d-lg-flex">
        <h6 class="text-primary px-4 py-2 text-white" style="background-color: #061148">Trending Stories</h6>
        <div class="row px-4">
            <?php 
            $count = 0;
            foreach ($top_stories as $item) {
                $post = $item['post'];
                $views = $item['views'];
                $average_rating = $item['average_rating'];
                setup_postdata($post);
                $count++;
                $hidden_class = $count > 3 ? 'd-none more-trending-stories' : '';
            ?>
                <div class="col-md-4 p-3 <?php echo $hidden_class; ?>">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title text-center fw-bold">
                                <a href="<?php the_permalink(); ?>" class="text-decoration-none fs-14px" style="color: #061148">
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
                            <p class="card-text"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center my-1">
                                <div class="d-flex align-items-center">
                                    <p class="me-4 mb-0"><i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($views); ?></p>
                                    <p class="mb-0"><i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?></p>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white fs-12px" style="background-color: #061148">மேலும் படிக்க</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (count($top_stories) > 3): ?>
                <div class="text-center my-3">
                    <button class="btn btn-primary btn-sm text-decoration-none" id="trendingStories">
                        Show More
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-5 d-lg-none">
        <h6 class="text-primary px-4 py-2 text-white" style="background-color: #061148">Trending Stories</h6>
        <div class="swiper-container px-3">
            <div class="swiper-wrapper">
                <?php foreach ($top_stories as $item) :
                    $post = $item['post'];
                    $views = $item['views'];
                    $average_rating = $item['average_rating'];
                    setup_postdata($post);
                ?>
                <div class="swiper-slide custom-width">
                    <div class="col-lg-3 py-3">
                        <div class="card h-100">
                            <div class="card-body text-center px-0">
                                <div class="title-wrapper d-flex align-items-center justify-content-center text-center px-2" style="height: 2rem;">
                                    <h6 class="card-title fw-bold fs-14px mb-0">
                                        <a href="<?php the_permalink(); ?>" class="text-decoration-none" style="color: #061148;">
                                            <?php
                                                $title = get_the_title();
                                                $trimmed_title = mb_strimwidth($title, 0, 50, '...');
                                                echo esc_html($trimmed_title);
                                            ?>
                                        </a>
                                    </h6>
                                </div>

                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', [
                                            'class' => 'img-fluid mx-3 d-block my-3',
                                            'style' => 'height: 250px; width: 165px;'
                                        ]); ?>
                                    </a>
                                <?php else : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg" class="card-img-top img-fluid mx-3 d-block my-3" alt="Default Image" style="height: 250px; width: 165px;">
                                    </a>
                                <?php endif; ?>

                                <div class="d-flex mx-3">
                                    <p class="me-4 mb-0"><i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($views); ?></p>
                                    <p class="mb-0"><i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>