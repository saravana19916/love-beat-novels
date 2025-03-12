<?php get_header(); ?>

<div class="container mt-5">

    <?php

    $args = array(
        'post_type' => 'my_creation_sub_blog',
        'meta_query' => array(
            array(
                'key' => 'my_creation_parent_blog_id',
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

        <h4 class="mt-5 mb-4">அத்தியாயங்கள்</h4>

        <div class="row mb-5">
            <?php
            $count = 0;
            while ($query->have_posts()) {
                $query->the_post();
                $views = get_post_meta(get_the_ID(), 'episode_view_count', true);
                $views = $views ?: 0;

                // get average rating
                $episode_id = get_the_ID();
                $average_rating = get_average_episode_rating($episode_id);
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

                        <div class="d-flex align-items-center px-5 my-3">
                            <p class="me-4">
                                <i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($views); ?>
                            </p>
                            <p>
                                <i class="fa-solid fa-star"></i>&nbsp;&nbsp;<?php echo $average_rating; ?>
                            </p>
                        </div>
                    </div>
                </div>

            <?php
                $count++;
            }
            ?>
        </div>
    <?php
        } else {
    ?>

    <?php
        $args = array(
            'post_type' => 'my_creation_blog',
            'meta_query' => array(
                array(
                    'p' => get_the_ID()
                )
                ),
                'orderby' => 'date',
                'order'   => 'ASC'
        );
        $mainQuery = new WP_Query($args);
        if ($mainQuery->have_posts()) {
    ?>
    
            <div class="row mb-5">
                <div class="col-md-12 single-page-padding">
                    <h3 class="fw-bold text-center my-4 text-primary-color"><?php the_title(); ?></h3>
                    
                    <div class="shadow-lg rounded mt-4 p-4">
                        <div class="mb-5">
                            <?php echo wpautop(get_the_content()); ?>
                        </div>

                        <!-- Reaction start -->
                        <hr/>
                        <?php
                            $user_id = null;
                            $episode_id = get_the_ID();
                            $emojis = ['thumb' => '👍', 'heart' => '❤️', 'lough' => '😂', 'cry' => '😢', 'fire' => '🔥'];

                            $emoji_counts = [];
                            foreach ($emojis as $key => $emoji) {
                                $emoji_counts[$key] = $wpdb->get_var($wpdb->prepare(
                                    "SELECT COUNT(*) FROM {$wpdb->prefix}episode_reactions WHERE episode_id = %d AND reaction = %s",
                                    $episode_id, $key
                                ));
                            }
                        ?>

                        <p class="me-3 text-primary-color"><span style="color:red;">***</span> இந்தப் படைப்பு உங்களுக்கு பிடித்திருக்கிறதா? <span style="color:red;">***</span></p>
                        <div class="emoji-reactions" data-episode="<?php echo $episode_id; ?>" data-user="<?php echo $user_id; ?>">
                            <?php foreach ($emojis as $key => $emoji) : ?>
                                <button class="emoji-btn" data-emo-symbol="<?php echo $key; ?>" data-emoji="<?php echo $emoji; ?>">
                                    <?php echo $emoji; ?> <span class="count" data-emoji="<?php echo $emoji; ?>"><?php echo $emoji_counts[$key] ?? 0; ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <hr/>
                        <!-- Reaction End -->

                        <!-- Rating start -->
                        <p class="mt-3 mb-0 text-primary-color"><span style="color:red;">***</span> <?php echo the_title(); ?> - படைப்பை ரேட் செய்யுங்கள் <span style="color:red;">***</span></p>
                        <?php
                            $user_id = get_current_user_id();
                            $episode_id = get_the_ID();
                            $rating = get_user_meta($user_id, "episode_rating_{$episode_id}", true);
                        ?>

                        <div class="star-rating" data-episode="<?php echo $episode_id; ?>">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?php echo ($i <= $rating) ? 'rated' : ''; ?>" data-value="<?php echo $i; ?>">&#9733;</span>
                            <?php endfor; ?>
                        </div>
                        <hr/>
                        <!-- Rating end -->

                        <div class="mt-4">
                            <?php
                                if (comments_open() || get_comments_number()) {
                                    comments_template();
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
    <?php    } }

        wp_reset_postdata();
    ?>
</div>

<?php get_footer(); ?>
