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
                <div class="col-md-10 px-4">

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

                            $total_views = $main_views + $sub_total;

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
                        <div class="row mb-5 shadow rounded d-none d-md-flex">
                            <h5 class="text-primary px-4 py-2 text-white" style="background-color: #061148">Trending Stories</h5>
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
                                                    <a href="<?php the_permalink(); ?>" class="text-decoration-none" style="color: #061148">
                                                        <?php the_title(); ?>
                                                    </a>
                                                </h6>
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <a href="<?php the_permalink(); ?>">
                                                        <?php the_post_thumbnail('medium', ['class' => 'img-fluid mx-auto d-block my-3']); ?>
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
                                                    <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white" style="background-color: #061148">மேலும் படிக்க</a>
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

                        <div class="row mb-5 d-md-none">
                            <h5 class="text-primary px-4 py-2 text-white" style="background-color: #061148">Trending Stories</h5>
                            <div class="swiper-container px-3">
                                <div class="swiper-wrapper">
                                    <?php foreach ($top_stories as $item) :
                                        $post = $item['post'];
                                        $views = $item['views'];
                                        $average_rating = $item['average_rating'];
                                        setup_postdata($post);
                                    ?>
                                    <div class="swiper-slide custom-width">
                                        <div class="col-md-3 py-3">
                                            <div class="card h-100">
                                                <div class="card-body text-center px-0">
                                                    <h6 class="card-title fw-bold">
                                                        <a href="<?php the_permalink(); ?>" class="text-decoration-none" style="color: #061148;">
                                                            <?php the_title(); ?>
                                                        </a>
                                                    </h6>

                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php the_post_thumbnail('medium', [
                                                                'class' => 'img-fluid mx-3 d-block my-3',
                                                                'style' => 'height: 250px; width: 165px;'
                                                            ]); ?>
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
                    <?php 
                        // endif;
                    ?>

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
                            <div class="row mb-5 shadow rounded d-none d-md-flex">
                                <h5 class="text-primary px-4 py-2 text-white" style="background-color: #061148"><?php echo esc_html($category->name); ?></h5>
                                <div class="row px-4">
                                    <?php 
                                    $count = 0;
                                    while ($query->have_posts()) : $query->the_post();
                                        $count++;
                                        $story_id = get_the_ID();
                                        $total_views = get_story_total_views('sub_blog', 'parent_blog_id', $story_id);
                                        $average_rating = get_story_average_rating('sub_blog', 'parent_blog_id', $story_id);
                                        $hidden_class = $count > 6 ? 'd-none more-post-'.$category->term_id : '';
                                    ?>
                                        <div class="col-md-4 p-3 <?php echo $hidden_class; ?>">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title text-center fw-bold">
                                                        <a href="<?php the_permalink(); ?>" class="text-decoration-none" style="color: #061148">
                                                            <?php the_title(); ?>
                                                        </a>
                                                    </h6>
                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php the_post_thumbnail('medium', ['class' => 'img-fluid mx-auto d-block my-3']); ?>
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
                                                        <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white" style="background-color: #061148">மேலும் படிக்க</a>
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

                            <div class="row mb-5 d-md-none">
                                <h5 class="text-primary px-4 py-2 text-white" style="background-color: #061148"><?php echo esc_html($category->name); ?></h5>
                                <div class="swiper-container px-3">
                                    <div class="swiper-wrapper">
                                        <?php while ($query->have_posts()) :
                                            $query->the_post();
                                            $story_id = get_the_ID();
                                            $total_views = get_story_total_views('sub_blog', 'parent_blog_id', $story_id);
                                            $average_rating = get_story_average_rating('sub_blog', 'parent_blog_id', $story_id);
                                        ?>
                                        <div class="swiper-slide custom-width">
                                            <div class="col-md-3 py-3">
                                                <div class="card h-100">
                                                    <div class="card-body text-center px-0">
                                                        <h6 class="card-title fw-bold">
                                                            <a href="<?php the_permalink(); ?>" class="text-decoration-none" style="color: #061148;">
                                                                <?php the_title(); ?>
                                                            </a>
                                                        </h6>

                                                        <?php if (has_post_thumbnail()) : ?>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php the_post_thumbnail('medium', [
                                                                    'class' => 'img-fluid mx-3 d-block my-3',
                                                                    'style' => 'height: 250px; width: 165px;'
                                                                ]); ?>
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

                    <?php
                       $args = array(
                            'post_type'      => 'competition',
                            'posts_per_page' => -1,
                        );
                        $query = new WP_Query($args);
                        
                        if ($query->have_posts()) :
                    ?>
                        <div class="row mb-5 shadow rounded d-none d-md-flex">
                            <h5 class="text-primary px-4 py-2 text-white" style="background-color: #061148">நாவல் போட்டி</h5>
                            <div class="row px-4">
                                <?php
                                    $count = 0;
                                    while ($query->have_posts()) :
                                    $query->the_post();
                                    $count++;
                                    $hidden_class = $count > 6 ? 'd-none more-competitions' : '';
                                ?>
                                    <div class="col-12 col-md-6 col-lg-4 my-2 <?php echo $hidden_class; ?>">
                                        <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                                            <div class="shadow p-3 mb-4 card-hover">
                                                <div class="card-header py-2">
                                                    <h5 class="mb-0 text-primary-color fw-bold"><?php the_title(); ?></h5>
                                                </div>
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <img src="<?php the_post_thumbnail_url('medium'); ?>" class="competition-img" alt="<?php the_title(); ?>">
                                                <?php else : ?>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/default-image.jpg" class="card-img-top" alt="Default Image">
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    </div>
                                <?php endwhile; ?>

                                <?php if ($query->post_count > 6): ?>
                                    <div class="text-center my-3">
                                        <button class="btn btn-primary btn-sm text-decoration-none" id="show-more-competitions">
                                            Show More
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-5 d-md-none">
                            <h5 class="text-primary px-4 py-2 text-white" style="background-color: #061148">நாவல் போட்டி</h5>
                            <div class="swiper-container px-3">
                                <div class="swiper-wrapper">
                                    <?php while ($query->have_posts()) :
                                        $query->the_post();
                                    ?>
                                    <div class="swiper-slide custom-width">
                                        <div class="col-md-3 py-3">
                                            <div class="card h-100">
                                                <div class="card-body text-center px-0">
                                                    <h6 class="card-title fw-bold">
                                                        <a href="<?php the_permalink(); ?>" class="text-decoration-none" style="color: #061148;">
                                                            <?php the_title(); ?>
                                                        </a>
                                                    </h6>

                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php the_post_thumbnail('medium', [
                                                                'class' => 'img-fluid mx-3 d-block my-3',
                                                                'style' => 'height: 250px; width: 165px;'
                                                            ]); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-2 px-4">
                    <div class="row mb-5 shadow rounded sticky-top" style="height: 25rem; top: 20px; overflow-y: auto">
                        <div class="col-md-12 p-0 text-center" id="latestPosts">
                            <h5 class="text-primary px-4 py-2 text-white" style="background-color: #061148"><?php echo "Latest posts"; ?></h5>
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
