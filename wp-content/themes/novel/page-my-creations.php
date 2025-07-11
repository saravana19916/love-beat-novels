<?php
get_header();
?>

<!-- <div style="position: relative; display: inline-block; width: 100%;">
    <h5 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
               color: white; padding: 5px 10px; border-radius: 5px; width: 70%; font-size: 14px; line-height: 25px;" class="banner-story text-center text-danger fw-bold">
        நான் உருவாக்கும் ஒவ்வொரு கதையும் ஆர்வத்தின் பிரதிபலிப்பு, கற்பனை, வாசகர்களுடன் இணையும் ஆசை. 
        எபிசோடிக் கதைசொல்லல் மூலமாகவோ, கற்பனையான பிரபஞ்சங்கள் மூலமாகவோ அல்லது இதயப்பூர்வமான 
        கதைகள் மூலமாகவோ, எதிரொலிக்கும் வகையில் கதைகளை உயிர்ப்பிப்பதே எனது குறிக்கோள்.
    </h5>
    <img src="<?php echo get_template_directory_uri() . '/images/write.jpg'; ?>" alt="My creation" class="img-fluid rounded" style="width: 100%; height: 200px;">
</div> -->

<div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
    <!-- Indicators -->
    <div class="carousel-indicators">
        <?php
        $banner_images = get_theme_mod('my_creation_banner_images', []);

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
                'post_type'      => ['my_creation_blog', 'my_creation_sub_blog'],
                'posts_per_page' => 10,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);

            if ($latest_post_query->have_posts()) :
        ?>
            <div class="row">
                <div class="col-lg-10 px-4">
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
                                'post_type' => 'my_creation_blog',
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
                                            while ($query->have_posts()) :
                                                $query->the_post();
                                                $count++;
                                                $story_id = get_the_ID();
                                                $total_views = get_story_total_views('my_creation_sub_blog', 'my_creation_parent_blog_id', $story_id);
                                                $average_rating = get_story_average_rating('my_creation_sub_blog', 'my_creation_parent_blog_id', $story_id);
                                                $hidden_class = $count > 6 ? 'd-none more-post-'.$category->term_id : '';
                                        ?>
                                            <div class="col-md-4 p-3 <?php echo $hidden_class; ?>">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <!-- Title -->
                                                        <h6 class="card-title text-center fw-bold fs-14px">
                                                            <a href="<?php the_permalink(); ?>" class="text-decoration-none" style="color: #061148">
                                                                <?php the_title(); ?>
                                                            </a>
                                                        </h6>
                                                        <!-- Image -->
                                                        <?php if (has_post_thumbnail()) : ?>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php the_post_thumbnail('medium', ['class' => 'img-fluid mx-auto d-block my-3']); ?>
                                                            </a>
                                                        <?php else : ?>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg" class="img-fluid mx-auto d-block my-3" alt="Default Image" style="height: 300px;">
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
                                                                    <i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?>
                                                                </p>
                                                            </div>
                                                            <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white fs-12px" style="background-color: #061148">
                                                                மேலும் படிக்க
                                                            </a>
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
                                                $total_views = get_story_total_views('my_creation_sub_blog', 'my_creation_parent_blog_id', $story_id);
                                                $average_rating = get_story_average_rating('my_creation_sub_blog', 'my_creation_parent_blog_id', $story_id);
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
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php
                            $external_novels = get_option('external_novels_links', []);

                            if (!empty($external_novels)) :
                            ?>
                                <div class="row mb-5 shadow rounded d-none d-lg-flex">
                                    <h6 class="text-primary px-4 py-2 text-white" style="background-color: #061148">Other Novels</h6>
                                    <div class="row px-4">
                                        <?php foreach ($external_novels as $novel): ?>
                                            <div class="col-md-3 p-3">
                                                <div class="card h-100">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title fw-bold">
                                                            <a href="<?php echo esc_url($novel['url']); ?>" target="_blank" class="text-decoration-none fs-14px" style="color: #061148;">
                                                                <?php echo esc_html($novel['title']); ?>
                                                            </a>
                                                        </h6>

                                                        <?php if (esc_url($novel['image'])) : ?>
                                                            <a href="<?php echo esc_url($novel['url']); ?>" target="_blank" class="text-decoration-none">
                                                                <img src="<?php echo esc_url($novel['image']); ?>" 
                                                                    class="img-fluid mx-auto d-block my-3" 
                                                                    style="height: 300px; width: auto;" 
                                                                    alt="<?php echo esc_attr($novel['title']); ?>"
                                                                >
                                                            </a>
                                                        <?php else : ?>
                                                            <a href="<?php echo esc_url($novel['url']); ?>" target="_blank" class="text-decoration-none">
                                                                <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg" class="img-fluid mx-auto d-block my-3" alt="Default Image" style="height: 300px; width: auto;">
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="row mb-5 d-lg-none">
                                    <h6 class="text-primary px-4 py-2 text-white" style="background-color: #061148">Other Novels</h6>
                                    <div class="swiper-container px-3">
                                        <div class="swiper-wrapper">
                                            <?php foreach ($external_novels as $novel): ?>
                                            <div class="swiper-slide custom-width">
                                                <div class="col-lg-3 py-3">
                                                    <div class="card h-100">
                                                        <div class="card-body text-center">
                                                            <div class="title-wrapper d-flex align-items-center justify-content-center text-center px-2" style="height: 2rem;">
                                                                <h6 class="card-title fw-bold fs-14px mb-0">
                                                                    <a href="<?php echo esc_url($novel['url']); ?>" target="_blank" class="text-decoration-none" style="color: #061148;">
                                                                        <?php
                                                                            $title = $novel['title'];
                                                                            $trimmed_title = mb_strimwidth($title, 0, 50, '...');
                                                                            echo esc_html($trimmed_title);
                                                                        ?>
                                                                    </a>
                                                                </h6>
                                                            </div>

                                                            <?php if (esc_url($novel['image'])) : ?>
                                                                <a href="<?php echo esc_url($novel['url']); ?>" target="_blank" class="text-decoration-none">
                                                                    <img src="<?php echo esc_url($novel['image']); ?>" 
                                                                        class="img-fluid mx-auto d-block my-3" 
                                                                        style="height: 250px; width: 200px;" 
                                                                        alt="<?php echo esc_attr($novel['title']); ?>"
                                                                    >
                                                                </a>
                                                            <?php else : ?>
                                                                <a href="<?php echo esc_url($novel['url']); ?>" target="_blank" class="text-decoration-none">
                                                                    <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg" class="img-fluid mx-auto d-block my-3" alt="Default Image" style="height: 250px; width: 200px;">
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                </div>

                <div class="col-lg-2 px-4">
                    <div class="row mb-5 shadow rounded sticky-top" style="height: 25rem; top: 20px; overflow-y: auto">
                        <div class="col-md-12 p-0 text-center" id="latestPosts">
                            <h6 class="text-primary px-4 py-2 text-white fs-14px" style="background-color: #061148"><?php echo "Latest posts"; ?></h6>
                            <?php
                                $latest_post_query = new WP_Query([
                                    'post_type'      => ['my_creation_blog', 'my_creation_sub_blog'],
                                    'posts_per_page' => 10,
                                    'orderby'        => 'date',
                                    'order'          => 'DESC',
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
