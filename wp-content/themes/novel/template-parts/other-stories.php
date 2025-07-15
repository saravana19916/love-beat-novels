<?php
    $categories = get_categories([
        'orderby' => 'id',
        'order'   => 'ASC',
    ]);

    $novel_category = null;
    $other_categories = [];

    foreach ($categories as $category) {
        if (strtolower($category->slug) === 'novel' || strtolower($category->slug) === 'novels' || $category->slug === 'நாவல்') {
            $novel_category = $category;
        } else {
            $other_categories[] = $category;
        }
    }

    $orderedCategories = [];
    if ($novel_category) {
        $orderedCategories[] = $novel_category;
    }
    $orderedCategories = array_merge($orderedCategories, $other_categories);

    foreach ($orderedCategories as $category) :
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    
        $args = array(
            'post_type' => ['main_blog', 'my_creation_blog'],
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $category->slug,
                ),
            ),
            'meta_key' => 'story_view_count',
            'orderby' => 'meta_value_num',
            'posts_per_page' => -1,
            'paged' => $paged,
        );
    
        $query = new WP_Query($args);
        if ($query->have_posts()) :
    ?>
        <div class="row mb-5 shadow rounded d-none d-lg-flex">
            <h6 class="text-primary px-4 py-2 text-white" style="background-color: #061148"><?php echo esc_html($category->name); ?></h6>
            <div class="row px-4">
                <?php 
                $count = 0;
                while ($query->have_posts()) : $query->the_post();
                    $count++;
                    $story_id = get_the_ID();

                    $post_type = get_post_type();
                    if ($post_type === 'main_blog') {
                        $sub_post_type = 'sub_blog';
                        $sub_meta_key  = 'parent_blog_id';
                    } elseif ($post_type === 'my_creation_blog') {
                        $sub_post_type = 'my_creation_sub_blog';
                        $sub_meta_key  = 'my_creation_parent_blog_id';
                    }

                    $total_views = get_story_total_views($sub_post_type, $sub_meta_key, $story_id);
                    $average_rating = get_story_average_rating($sub_post_type, $sub_meta_key, $story_id);
                    $hidden_class = $count > 6 ? 'd-none more-post-'.$category->term_id : '';
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
                                        <p class="me-4 mb-0"><i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($total_views); ?></p>
                                        <p class="mb-0"><i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?></p>
                                    </div>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white fs-12px" style="background-color: #061148">மேலும் படிக்க</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

                <?php if ($query->post_count > 6): ?>
                    <div class="text-center my-3">
                        <button class="btn btn-primary btn-sm text-decoration-none show-more-btn" data-target="more-post-<?php echo $category->term_id; ?>" id="show-more-<?php echo $category->term_id; ?>">
                            Show More
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row mb-5 d-lg-none">
            <h6 class="text-primary px-4 py-2 text-white" style="background-color: #061148"><?php echo esc_html($category->name); ?></h6>
            <div class="swiper-container px-3">
                <div class="swiper-wrapper">
                    <?php while ($query->have_posts()) :
                        $query->the_post();
                        $story_id = get_the_ID();
                        $post_type = get_post_type();
                        if ($post_type === 'main_blog') {
                            $sub_post_type = 'sub_blog';
                            $sub_meta_key  = 'parent_blog_id';
                        } elseif ($post_type === 'my_creation_blog') {
                            $sub_post_type = 'my_creation_sub_blog';
                            $sub_meta_key  = 'my_creation_parent_blog_id';
                        }

                        $total_views = get_story_total_views($sub_post_type, $sub_meta_key, $story_id);
                        $average_rating = get_story_average_rating($sub_post_type, $sub_meta_key, $story_id);
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
                                        <p class="me-4 mb-0"><i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($total_views); ?></p>
                                        <p class="mb-0"><i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    <?php 
        endif;
    endforeach;
    ?>