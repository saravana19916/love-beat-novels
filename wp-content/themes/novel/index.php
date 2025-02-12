<?php
/* Template Name: Paginated Blogs by Category */

get_header(); // Include the header
?>

<?php
    $banner_image = get_theme_mod('banner_image');
    $banner_link = get_theme_mod('banner_link');

    if ($banner_image) {
        echo '<a href="' . esc_url($banner_link) . '" target="_blank">';
        echo '<img src="' . esc_url($banner_image) . '" class="banner" alt="Banner Image" style="width: 100%;height: 450px;">';
        echo '</a>';
    } else {
        echo '<p>No banner image selected.</p>';
    }
?>

<div class="container my-4">
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <!-- Indicators -->
        <!-- <div class="carousel-indicators">
            <?php
            $banner_images = [
                ['url' => get_template_directory_uri() . '/images/banner.jpg', 'alt' => 'Slide 1'],
                ['url' => 'image2.jpg', 'alt' => 'Slide 2'],
                ['url' => 'image3.jpg', 'alt' => 'Slide 3'],
            ];
            foreach ($banner_images as $index => $image) {
                $active = $index === 0 ? 'active' : '';
                echo "<button type='button' data-bs-target='#bannerCarousel' data-bs-slide-to='{$index}' class='{$active}' aria-current='true' aria-label='Slide " . ($index + 1) . "'></button>";
            }
            ?>
        </div> -->

        <!-- Slides -->
        <!-- <div class="carousel-inner">
            <?php foreach ($banner_images as $index => $image): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo esc_url($image['url']); ?>" class="d-block w-100" alt="<?php echo esc_attr($image['alt']); ?>">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Slide <?php echo $index + 1; ?></h5>
                        <p>Sample description for Slide <?php echo $index + 1; ?>.</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div> -->

        <!-- Controls -->
        <!-- <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button> -->
    </div>

    <div class="col-md-12 mt-4">
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
                                    <?php while ($query->have_posts()) : $query->the_post(); ?>
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
                                                <div class="card-footer text-end">
                                                    <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white" style="background-color: #061148">Read More</a>
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
                            $latest_post_query = new WP_Query([
                                'posts_per_page' => 10
                            ]);

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
    </div>
</div>

<?php get_footer(); // Include the footer ?>
