<?php
    $latest_post_query = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 20,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if ($latest_post_query->have_posts()) :
?>
    <div class="row mb-5 shadow rounded d-none d-lg-flex shadow-div">
        <h6 class="px-4 py-2 bg-category-color head-title">Latest Updates</h6>

        <div class="row px-4">
            <?php 
            $count = 0;
            while ($latest_post_query->have_posts()) :
                $latest_post_query->the_post();
                $story_id = get_the_ID();

                $categories = get_the_category();

                $terms = wp_get_post_terms($story_id, 'blog_type');
                if (empty($terms)) continue;

                $term_slug = $terms[0]->slug;

                if ($term_slug === 'main-blog') {
                    $sub_meta_key = 'parent_blog_id';
                } elseif ($term_slug === 'my-creation-blog') {
                    $sub_meta_key = 'my_creation_parent_blog_id';
                } elseif ($term_slug === 'competition-blog') {
                    $sub_meta_key = 'competition_parent_id';
                } else {
                    continue;
                }

                $parent_id = get_post_meta($story_id, $sub_meta_key, true);

                if ((strtolower($categories[0]->slug) === 'novel' || strtolower($categories[0]->slug) === 'novels' || $categories[0]->slug === 'நாவல்') && !$parent_id) {
                    continue;
                }

                $count++;
                $total_views = get_story_total_views('post', $sub_meta_key, $story_id);
                $average_rating = get_story_average_rating('post', $sub_meta_key, $story_id);
                $hidden_class = $count > 6 ? 'd-none more-latest-post' : '';
            ?>
            <div class="col-md-4 p-3 <?php echo $hidden_class; ?>">
                <div class="card bg-transparent shadow-div h-100">
                    <div class="card-body">
                        <h6 class="card-title text-center fw-bold fs-14px">
                            <a href="<?php the_permalink(); ?>" class="text-decoration-none text-primary-color">
                                <?php the_title(); ?>
                            </a>
                        </h6>

                        <?php if (has_post_thumbnail($story_id)) : ?>
                            <a href="<?php echo get_permalink($story_id); ?>">
                                <?php echo get_the_post_thumbnail($story_id, 'medium', [
                                    'class' => 'img-fluid mx-auto d-block my-3',
                                    'style' => 'height: 300px;',
                                ]); ?>
                            </a>
                        <?php elseif ($parent_id && has_post_thumbnail($parent_id)) : ?>
                            <a href="<?php echo get_permalink($story_id); ?>">
                                <?php echo get_the_post_thumbnail($parent_id, 'medium', [
                                    'class' => 'img-fluid mx-auto d-block my-3',
                                    'style' => 'height: 300px;',
                                ]); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php echo get_permalink($story_id); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg"
                                    class="img-fluid mx-auto d-block my-3"
                                    alt="Default Image"
                                    style="height: 300px;">
                            </a>
                        <?php endif; ?>

                        <p class="card-text text-primary-color">
                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                        </p>
                    </div>
                    <div class="card-footer shadow-div">
                        <div class="d-flex justify-content-between align-items-center my-1">
                            <div class="d-flex align-items-center">
                                <p class="me-4 mb-0 text-primary-color"><i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($total_views); ?></p>
                                <p class="mb-0 text-primary-color"><i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?></p>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white fs-12px primary-btn">மேலும் படிக்க</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>

            <?php if ($count > 6): ?>
                <div class="text-center my-3">
                    <button class="btn btn-sm text-decoration-none show-more-btn primary-btn" data-target="more-latest-post" id="show-more-latest-post">
                        Show More
                    </button>
                </div>
            <?php endif; ?>

            <?php if($count == 0) : ?>
                <h6 class="text-primary-color">No Stories found.</h6>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-5 d-lg-none">
        <h6 class="px-4 py-2 bg-category-color head-title">Latest Updates</h6>

        <div class="swiper-container px-3">
            <div class="swiper-wrapper">
                <?php 
                while ($latest_post_query->have_posts()) :
                    $latest_post_query->the_post();
                    $story_id = get_the_ID();

                    $categories = get_the_category();

                    $terms = wp_get_post_terms($story_id, 'blog_type');
                    if (empty($terms)) continue;

                    $term_slug = $terms[0]->slug;

                    if ($term_slug === 'main-blog') {
                        $sub_meta_key = 'parent_blog_id';
                    } elseif ($term_slug === 'my-creation-blog') {
                        $sub_meta_key = 'my_creation_parent_blog_id';
                    } elseif ($term_slug === 'competition-blog') {
                        $sub_meta_key = 'competition_parent_id';
                    } else {
                        continue;
                    }

                    if (strtolower($categories[0]->slug) === 'novel' || strtolower($categories[0]->slug) === 'novels' || $categories[0]->slug === 'நாவல்') {
                        continue;
                    }

                    $parent_id = get_post_meta($story_id, $sub_meta_key, true);

                    $total_views = get_story_total_views('post', $sub_meta_key, $story_id);
                    $average_rating = get_story_average_rating('post', $sub_meta_key, $story_id);
                ?>
                <div class="swiper-slide custom-width">
                    <div class="col-lg-3 py-3">
                        <div class="card h-100 bg-transparent shadow-div">
                            <div class="card-body text-center px-0">
                                <div class="title-wrapper d-flex align-items-center justify-content-center text-center px-2" style="height: 2rem;">
                                    <h6 class="card-title fw-bold fs-14px mb-0">
                                        <a href="<?php the_permalink(); ?>" class="text-decoration-none text-primary-color">
                                            <?php
                                                $title = get_the_title();
                                                $trimmed_title = mb_strimwidth($title, 0, 50, '...');
                                                echo esc_html($trimmed_title);
                                            ?>
                                        </a>
                                    </h6>
                                </div>

                                <?php if (has_post_thumbnail($story_id)) : ?>
                                    <a href="<?php echo get_permalink($story_id); ?>">
                                        <?php echo get_the_post_thumbnail($story_id, 'medium', [
                                            'class' => 'img-fluid mx-3 d-block my-3',
                                            'style' => 'height: 250px; width: 165px;',
                                        ]); ?>
                                    </a>
                                <?php elseif ($parent_id && has_post_thumbnail($parent_id)) : ?>
                                    <a href="<?php echo get_permalink($story_id); ?>">
                                        <?php echo get_the_post_thumbnail($parent_id, 'medium', [
                                            'class' => 'img-fluid mx-3 d-block my-3',
                                            'style' => 'height: 250px; width: 165px;',
                                        ]); ?>
                                    </a>
                                <?php else : ?>
                                    <a href="<?php echo get_permalink($story_id); ?>">
                                        <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg"
                                            class="img-fluid mx-3 d-block my-3"
                                            alt="Default Image"
                                            style="height: 250px; width: 165px;">
                                    </a>
                                <?php endif; ?>

                                <div class="d-flex mx-3">
                                    <p class="me-4 mb-0 text-primary-color"><i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($total_views); ?></p>
                                    <p class="mb-0 text-primary-color"><i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?></p>
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
        <h5 class="text-primary-color">No post found.</h5>
    <?php endif; ?>