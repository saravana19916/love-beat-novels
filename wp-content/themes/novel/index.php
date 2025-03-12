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
                    <img src="<?php echo esc_url($image['image']); ?>" class="d-block w-100" style="width: 100%;height: 450px;" alt="<?php echo esc_attr($image['alt']); ?>">
                </a>
                <div class="carousel-caption d-none d-md-block">
                    <h5>வலைத்தளத்தில் எழுத புதிய எழுத்தாளர்கள் வரவேற்கப்படுகிறார்கள்.</h5>
                </div>
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
                        $categories = get_categories();

                        foreach ($categories as $category) :
                            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                            $args = array(
                                'post_type' => 'main_blog',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'category',
                                        'field'    => 'slug',
                                        'terms'    => $category->slug,
                                    ),
                                ),
                                'paged' => $paged,
                                );
                                
                            $query = new WP_Query($args);
                            
                            if ($query->have_posts()) :
                    ?>
                                <div class="row mb-5 shadow rounded">
                                    <h2 class="text-primary px-4 py-1 text-white" style="background-color: #061148"><?php echo esc_html($category->name); ?></h2>
                                    <div class="row px-4">
                                        <?php while ($query->have_posts()) :
                                            $query->the_post();
                                            $story_id = get_the_ID();
                                            $total_views = get_story_total_views('sub_blog', 'parent_blog_id', $story_id);
                                            $average_rating = get_story_average_rating('sub_blog', 'parent_blog_id', $story_id);
                                        ?>
                                            <div class="col-md-4 p-3">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <!-- Title -->
                                                        <h5 class="card-title text-center fw-bold">
                                                            <a href="<?php the_permalink(); ?>" class="text-decoration-none" style="color: #061148">
                                                                <?php the_title(); ?>
                                                            </a>
                                                        </h5>
                                                        <!-- Image -->
                                                        <?php if (has_post_thumbnail()) : ?>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php the_post_thumbnail('medium', ['class' => 'img-fluid mx-auto d-block my-3']); ?>
                                                            </a>
                                                        <?php endif; ?>
                                                        <!-- Description -->
                                                        <p class="card-text">
                                                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                                        </p>
                                                    </div>
                                                    <div class="card-footer">
                                                        <div class="d-flex justify-content-between align-items-center my-1">
                                                            <div class="d-flex align-items-center">
                                                                <p class="me-4 mb-0">
                                                                    <i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($total_views); ?>
                                                                </p>
                                                                <p class="mb-0">
                                                                    <i class="fa-solid fa-star"></i>&nbsp;&nbsp;<?php echo $average_rating; ?>
                                                                </p>
                                                            </div>
                                                            <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white" style="background-color: #061148">
                                                                மேலும் படிக்க
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <!-- Pagination -->
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            <?php
                                            echo paginate_links(array(
                                                'base' => add_query_arg('paged', '%#%'),
                                                'format' => '?paged=%#%',
                                                'total' => $query->max_num_pages,
                                                'current' => $paged,
                                                'prev_text' => '&laquo; Previous',
                                                'next_text' => 'Next &raquo;',
                                            ));
                                            ?>
                                        </ul>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
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
