<?php get_header(); ?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-12 single-page-padding">
            <h3 class="fw-bold text-center my-4 text-primary-color"><?php the_title(); ?></h3>
            
            <div class="shadow-lg rounded mt-4 p-4">
                <div class="mb-5">
                    <?php echo wpautop(get_the_content()); ?>
                </div>

                <!-- Next & Previous Episode Navigation -->
                <div class="navigation my-4">
                    <div class="d-flex justify-content-between">
                        <div class="prev-episode">
                            <?php 
                            $prev_post = get_previous_post();
                            if ($prev_post): ?>
                                <a href="<?php echo get_permalink($prev_post->ID); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>&nbsp முந்திய பாகத்தை படிக்க
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="next-episode">
                            <?php 
                            $next_post = get_next_post();
                            if ($next_post): ?>
                                <a href="<?php echo get_permalink($next_post->ID); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>&nbsp அடுத்த பாகத்தை படிக்க
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
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

                <?php
                    if (comments_open() || get_comments_number()) {
                        comments_template();
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
