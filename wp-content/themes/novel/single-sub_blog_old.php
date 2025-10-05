<?php get_header(); ?>

<div class="container details-page my-4">
    <div class="row">
        <div class="col-md-12">
            <h5 class="fw-bold text-center my-4 text-primary-color"><?php the_title(); ?></h5>
            
            <div class="shadow-lg rounded mt-4 p-4">
                <div class="mb-5 fs-6 custom-content">
                    <?php echo wpautop(get_the_content()); ?>
                </div>

                 <!-- Next & Previous Episode Navigation -->
                <?php
                $parent_blog_id = get_post_meta(get_the_ID(), 'parent_blog_id', true);

                // Fetch all episodes of the parent blog in ascending order
                $args = array(
                    'post_type'      => 'sub_blog',
                    'posts_per_page' => -1,
                    'meta_key'       => 'parent_blog_id',
                    'meta_value'     => $parent_blog_id,
                    'orderby'        => 'date',
                    'order'          => 'ASC',
                );
                $episodes = get_posts($args);

                $current_index = -1;
                foreach ($episodes as $index => $episode) {
                    if ($episode->ID == get_the_ID()) {
                        $current_index = $index;
                        break;
                    }
                }

                $prev_episode = ($current_index > 0) ? $episodes[$current_index - 1] : null;
                $next_episode = ($current_index < count($episodes) - 1) ? $episodes[$current_index + 1] : null;
                ?>

                <div class="navigation my-4">
                    <div class="d-flex justify-content-between">
                        <div class="prev-episode">
                            <?php if ($prev_episode): ?>
                                <a href="<?php echo get_permalink($prev_episode->ID); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>&nbsp முந்திய பாகத்தை படிக்க
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="next-episode">
                            <?php if ($next_episode): ?>
                                <a href="<?php echo get_permalink($next_episode->ID); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>&nbsp அடுத்த பாகத்தை படிக்க
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- </div> -->

                <!-- Reaction start -->
                <div class="row justify-content-center text-center">
                    <div class="col-12">
                        <div class="reactions-container">
                            <?php
                                $user_id = get_current_user_id();
                                $episode_id = get_the_ID();
                                $emojis = ['thumb' => '👍', 'heart' => '❤️', 'lough' => '😂', 'cry' => '😢', 'fire' => '🔥'];

                            // Fetch counts for this episode
                            $emoji_counts = [];
                            foreach ($emojis as $key => $emoji) {
                                $emoji_counts[$key] = $wpdb->get_var($wpdb->prepare(
                                    "SELECT COUNT(*) FROM {$wpdb->prefix}episode_reactions WHERE episode_id = %d AND reaction = %s",
                                    $episode_id, $key
                                ));
                            }
                            ?>

                            <div class="emoji-reactions sec-comment" data-episode="<?php echo $episode_id; ?>" data-user="<?php echo $user_id; ?>">
                                <?php foreach ($emojis as $key => $emoji) : ?>
                                    <button class="emoji-btn" data-emo-symbol="<?php echo $key; ?>" data-emoji="<?php echo $emoji; ?>">
                                        <?php echo $emoji; ?> <span class="count" data-emoji="<?php echo $emoji; ?>"><?php echo $emoji_counts[$key] > 0 ? $emoji_counts[$key] : ''; ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <!-- Reaction end -->

                            <div class="sec-comment comment-sec">
                                <?php
                                    if (comments_open() || get_comments_number()) {
                                        comments_template();
                                    }
                                ?>
                            </div>

                            <!-- Rating start -->
                            <?php
                                $user_id = get_current_user_id();
                                $episode_id = get_the_ID();
                                $rating = get_user_meta($user_id, "episode_rating_{$episode_id}", true);
                            ?>

                            <div class="sec-comment" data-episode="<?php echo $episode_id; ?>">
                                <p class="mt-2 mb-0 text-primary-color"><span style="color:red;">***</span> <?php echo the_title(); ?> - படைப்பை ரேட் செய்யுங்கள் <span style="color:red;">***</span></p>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?php echo ($i <= $rating) ? 'rated' : ''; ?>" data-value="<?php echo $i; ?>">&#9733;</span>
                                <?php endfor; ?>
                            </div>
                            <!-- Rating end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
