<?php
    $args = array(
        'post_type'      => 'competition',
        'posts_per_page' => -1,
    );
    $query = new WP_Query($args);
    
    if ($query->have_posts()) :
?>
    <div class="row mb-5 shadow rounded d-none d-lg-flex">
        <h6 class="text-primary px-4 py-2 text-white" style="background-color: #061148">நாவல் போட்டி</h6>
        <div class="row px-4">
            <?php
                $count = 0;
                while ($query->have_posts()) :
                $query->the_post();
                $count++;
                $hidden_class = $count > 8 ? 'd-none more-competitions' : '';
            ?>
                <div class="col-12 col-lg-6 col-xl-4 col-xxl-3 my-2 <?php echo $hidden_class; ?>">                                                
                    <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                        <div class="shadow p-3 mb-4 card-hover">
                            <div class="card-header py-2 text-center">
                                <h6 class="mb-0 text-primary-color fs-14px fw-bold"><?php the_title(); ?></h6>
                            </div>
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php the_post_thumbnail_url('medium'); ?>" class="card-img-top story-img-custom-size img-fluid mx-3 d-block my-3" alt="<?php the_title(); ?>">
                            <?php else : ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg" class="card-img-top story-img-custom-size img-fluid mx-3 d-block my-3" alt="Default Image">
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>

            <?php if ($query->post_count > 8): ?>
                <div class="text-center my-3">
                    <button class="btn btn-primary btn-sm text-decoration-none" id="show-more-competitions">
                        Show More
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-5 d-lg-none">
        <h6 class="text-primary px-4 py-2 text-white" style="background-color: #061148">நாவல் போட்டி</h6>
        <div class="swiper-container px-3">
            <div class="swiper-wrapper">
                <?php while ($query->have_posts()) :
                    $query->the_post();
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
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php endif; ?>