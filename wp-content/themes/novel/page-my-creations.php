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
                    <img src="<?php echo esc_url($image['image']); ?>" class="d-block w-100 custom-img-height" alt="banner image <?php echo $index + 1; ?>">
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
    <?php
        $writePageUrl = get_permalink(get_page_by_path('subscription'));
        $messageFromAdmin = get_option('writer_invite_message');

        $linkHtml = '&nbsp;<a class="text-underline text-danger" href="' . esc_url($writePageUrl) . '">சப்ஸ்கிரைப்</a>';
        $message = str_replace('{write_url}', $linkHtml, $messageFromAdmin);
    ?>

    <?php if ($messageFromAdmin) { ?>
        <div class="shadow rounded px-4 d-flex align-items-center justify-content-center fw-bold text-primary-color h-auto h-lg-100 shadow-div">
            <i class="fa-solid fa-crown fa-lg"></i> &nbsp; &nbsp;
            <span class="p-3">
                <?= wp_kses_post($message); ?>
            </span>
            &nbsp; &nbsp; <i class="fa-solid fa-crown fa-lg"></i>
        </div>
    <?php } ?>

    <div class="col-md-12 mt-4">
        <?php
            $latest_post_query = new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 10,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);

            if ($latest_post_query->have_posts()) :
        ?>
            <div class="row">
                <div class="col-lg-12 px-4">

                    <!-- Latest stories start -->
                    <?php get_template_part('template-parts/latest-stories'); ?>
                    <!-- Latest stories end -->

                    <!-- Other stories start -->
                    <?php get_template_part('template-parts/other-stories', null, ['context' => 'my-creations']); ?>
                    <!-- Other stories end -->

                        <?php
                            $external_novels = get_option('external_novels_links', []);

                            if (!empty($external_novels)) :
                            ?>
                                <div class="row mb-5 shadow rounded d-none d-lg-flex shadow-div">
                                    <h6 class="px-4 py-2 bg-category-color head-title">Other Novels</h6>
                                    <div class="row px-4">
                                        <?php foreach ($external_novels as $novel): ?>
                                            <div class="col-md-3 p-3">
                                                <div class="card h-100 bg-transparent shadow-div">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title fw-bold">
                                                            <a href="<?php echo esc_url($novel['url']); ?>" target="_blank" class="text-decoration-none fs-14px text-primary-color">
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
                                    <h6 class="px-4 py-2 bg-category-color head-title">Other Novels</h6>
                                    <div class="swiper-container px-3">
                                        <div class="swiper-wrapper">
                                            <?php foreach ($external_novels as $novel): ?>
                                            <div class="swiper-slide custom-width">
                                                <div class="col-lg-3 py-3">
                                                    <div class="card h-100 bg-transparent shadow-div">
                                                        <div class="card-body text-center">
                                                            <div class="title-wrapper d-flex align-items-center justify-content-center text-center px-2" style="height: 2rem;">
                                                                <h6 class="card-title fw-bold fs-14px mb-0">
                                                                    <a href="<?php echo esc_url($novel['url']); ?>" target="_blank" class="text-decoration-none text-primary-color">
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
