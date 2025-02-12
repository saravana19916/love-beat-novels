<?php get_header(); ?>

<div class="container mt-5">

    <?php

    $args = array(
        'post_type' => 'sub_blog',
        'meta_query' => array(
            array(
                'key' => 'parent_blog_id',
                'value' => get_the_ID(),
                'compare' => '='
            )
            ),
            'orderby' => 'date',
            'order'   => 'ASC'
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ?>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="shadow-lg p-4 rounded">
                    <div class="row">
                        <div class="col-md-4">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php the_post_thumbnail_url('large'); ?>" class="img-fluid rounded shadow" alt="<?php the_title(); ?>">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-8">
                            <h3 class="fw-bold mb-3"><?php the_title(); ?></h3>
                            <div class="mb-3 col-md-8">
                                <?php echo wpautop(get_the_content()); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mt-5 mb-4">Chapters</h4>

        <div class="row mb-5">
            <?php
            $count = 0;
            while ($query->have_posts()) {
                $query->the_post();
                $views = get_post_meta(get_the_ID(), 'episode_view_count', true);
                $views = $views ?: 0;
            ?>
                <div class="col-md-6">
                    <div class="shadow-lg rounded mt-4">
                        <div class="d-flex justify-content-between align-items-center px-4 pt-4">
                            <h5 class="mb-0">
                                <?php echo sprintf("%2d", $count + 1); ?>.&nbsp
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h5>

                            <?php 
                                $date = get_the_date('j F Y');
                                $tamil_months = array(
                                    'January' => 'ஜனவரி',
                                    'February' => 'பிப்ரவரி',
                                    'March' => 'மார்ச்',
                                    'April' => 'ஏப்ரல்',
                                    'May' => 'மே',
                                    'June' => 'ஜூன்',
                                    'July' => 'ஜூலை',
                                    'August' => 'ஆகஸ்ட்',
                                    'September' => 'செப்டம்பர்',
                                    'October' => 'அக்டோபர்',
                                    'November' => 'நவம்பர்',
                                    'December' => 'டிசம்பர்'
                                );
                            
                                $tamil_date = str_replace(array_keys($tamil_months), array_values($tamil_months), $date); 
                            ?>

                            <span class="text-muted"><?php echo $tamil_date; ?></span>
                        </div>
                        <p class="px-5 pt-3 pb-4"><i class="fa-solid fa-eye" style="margin-left: 0.5rem;"></i>&nbsp <?php echo format_view_count($views); ?></p>
                    </div>
                </div>

            <?php
                $count++;
            }
            ?>
        </div>
    <?php
        } else {
            echo '<h2>Blog Details</h2>';
            the_content();
        }

        wp_reset_postdata();
    ?>
</div>

<?php get_footer(); ?>
