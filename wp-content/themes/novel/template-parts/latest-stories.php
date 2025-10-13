<?php
    $latest_post_query = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 20,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if ($latest_post_query->have_posts()) :
?>
    <div class="row mb-5 shadow rounded shadow-div overflow-hidden">
        <h6 class="px-4 py-2 bg-category-color head-title">Latest Stories</h6>

        <div class="marquee-wrapper py-3">
            <div class="marquee-content d-flex align-items-stretch">
                <?php 
                while ($latest_post_query->have_posts()) :
                    $latest_post_query->the_post();
                    $story_id = get_the_ID();

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

                    $total_views = get_story_total_views('post', $sub_meta_key, $story_id);
                    $average_rating = get_story_average_rating('post', $sub_meta_key, $story_id);
                ?>
                <div class="card mx-3 bg-transparent shadow-div" style="min-width: 300px; flex: 0 0 180px;">
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
                                    'style' => 'height: 300px;'
                                ]); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg" class="img-fluid mx-auto d-block my-3" alt="Default Image" style="height: 300px;">
                            </a>
                        <?php endif; ?>

                        <p class="card-text text-primary-color">
                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                        </p>
                    </div>
                    <div class="card-footer">
                        <p class="mb-0 text-primary-color d-inline me-4">
                            <i class="fa-solid fa-eye"></i>&nbsp;<?php echo format_view_count($total_views); ?>
                        </p>
                        <p class="mb-0 text-primary-color d-inline">
                            <i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;<?php echo $average_rating; ?>
                        </p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    
    <?php else : ?>
        <h5 class="text-primary-color">No post found.</h5>
    <?php endif; ?>