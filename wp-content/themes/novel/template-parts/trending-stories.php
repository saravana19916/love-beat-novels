<?php
    $blog_terms = ['main-blog', 'my-creation-blog'];

    $meta_query = array();

    // Add filter for main-blog
    if (in_array('main-blog', $blog_terms)) {
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => 'parent_blog_id',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => 'parent_blog_id',
                'value'   => '0',
                'compare' => '=',
            ),
        );
    }

    // Add filter for my-creation-blog
    if (in_array('my-creation-blog', $blog_terms)) {
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => 'my_creation_parent_blog_id',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => 'my_creation_parent_blog_id',
                'value'   => '0',
                'compare' => '=',
            ),
        );
    }

    $main_stories = get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'blog_type',
                'field'    => 'slug',
                'terms'    => ['main-blog', 'my-creation-blog'],
            ),
        ),
        'meta_query'     => $meta_query,
    ));

    $stories_with_views = [];

    foreach ($main_stories as $story) {
        $main_id = $story->ID;

        $terms = wp_get_post_terms($main_id, 'blog_type');
        if (empty($terms)) continue;

        $term_slug = $terms[0]->slug;

        if ($term_slug === 'main-blog') {
            $sub_meta_key = 'parent_blog_id';
        } elseif ($term_slug === 'my-creation-blog') {
            $sub_meta_key = 'my_creation_parent_blog_id';
        } else {
            continue; // Skip if term is not expected
        }

        // Main story views
        $main_views = (int) get_post_meta($main_id, 'story_view_count', true);

        // Sum sub story views
        $sub_stories = get_posts(array(
            'post_type'      => 'post',
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

        $total_views = get_story_total_views('post', $sub_meta_key, $main_id);

        // Find average rating
        $average_rating = get_story_average_rating('post', $sub_meta_key, $main_id);

        $stories_with_views[] = [
            'post'  => $story,
            'views' => $total_views,
            'average_rating' => $average_rating
        ];
    }

    $competition_args = array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'blog_type',
                'field'    => 'slug',
                'terms'    => ['competition-blog'], // slugs of your blog_type terms
            ),
        ),
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
    <div class="row mb-5 shadow rounded shadow-div overflow-hidden">
    <h6 class="px-4 py-2 bg-category-color head-title">🔥 Trending 🔥</h6>

    <div class="swiper trending-swiper px-4 py-3">
        <div class="swiper-wrapper">
            <?php 
            foreach ($top_stories as $item) {
                $post = $item['post'];
                $views = $item['views'];
                $average_rating = $item['average_rating'];
                setup_postdata($post);
            ?>
            <div class="swiper-slide">
                <div class="card mx-3 bg-transparent shadow-div h-100">
                    <div class="card-body">
                        <h6 class="card-title text-center fw-bold">
                            <a href="<?php the_permalink(); ?>" class="text-decoration-none fs-14px text-primary-color">
                                <?php the_title(); ?>
                            </a>
                        </h6>

                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium', [
                                    'class' => 'img-fluid mx-auto d-block my-3',
                                    'style' => 'height: 300px; object-fit: cover;'
                                ]); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg"
                                     class="img-fluid mx-auto d-block my-3"
                                     alt="Default Image"
                                     style="height: 300px; object-fit: cover;">
                            </a>
                        <?php endif; ?>

                        <p class="card-text text-primary-color">
                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                        </p>
                    </div>
                    <div class="card-footer shadow-div">
                        <div class="d-flex justify-content-between align-items-center my-1">
                            <div class="d-flex align-items-center">
                                <p class="me-4 mb-0 text-primary-color"><i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($views); ?></p>
                                <p class="mb-0 text-primary-color"><i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?></p>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white fs-12px primary-btn d-none d-lg-flex">மேலும் படிக்க</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <!-- Navigation buttons -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

        <!-- Pagination (optional) -->
        <div class="swiper-pagination"></div>
    </div>
</div>

<?php wp_reset_postdata(); ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    new Swiper(".trending-swiper", {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        breakpoints: {
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
        },
    });
});
</script>
