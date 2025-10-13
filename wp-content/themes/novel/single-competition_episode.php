<?php get_header(); ?>

<div class="container details-page my-4">
    <div class="row">
        <div class="col-md-12">
            <h5 class="fw-bold text-center my-4 text-primary-color"><?php the_title(); ?></h5>

            <div class="mt-4 text-center">
                <?php
                    $author_id = get_post_field('post_author', get_the_ID());
                    $author_name = get_the_author_meta('display_name', $author_id);
                    $profile_picture_id = get_user_meta($author_id, 'profile_picture', true);
                    $profile_picture_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : get_template_directory_uri() . '/images/profile-no-image.jpg';
                    $post_count = count_user_posts($author_id, 'post');

                    if ($post_count > 0) {
                        $icon_html = '<i class="fa-solid fa-pen-nib me-1"></i>';
                    } else {
                        $icon_html = '<i class="fa-solid fa-book-reader me-1"></i>';
                    }
                ?>
                    <a href="<?php echo site_url('/profile/?user_id=' . $author_id); ?>" class="fs-16px text-primary-color text-decoration-underline mb-1">
                        <img src="<?php echo esc_url($profile_picture_url); ?>" alt="<?php echo $author_id ?>" class="rounded-circle me-2" height="40" width="40">
                        <?php echo $icon_html; ?>
                        <?php echo esc_html($author_name); ?>
                    </a>

                    <?php
                    $current_user_id = get_current_user_id();
                    $author_id = get_post_field('post_author', get_the_ID());

                    // Don't show follow button for current user
                    if ($current_user_id && $current_user_id != $author_id) {

                        // Get current user's following list
                        $following = get_user_meta($current_user_id, 'following_users', true);
                        $following = is_array($following) ? $following : array();

                        $is_following = in_array($author_id, $following);
                        $btn_text = $is_following ? 'Following' : 'Follow';
                        $btn_disabled = $is_following ? 'disabled' : '';
                        ?>
                        <div class="mt-2">
                            <button class="btn btn-primary btn-sm follow-btn" 
                                    data-author-id="<?php echo $author_id; ?>" 
                                    <?php echo $btn_disabled; ?>>
                                <?php echo $btn_text; ?>
                            </button>
                        </div>
                    <?php } ?>
            </div>
            
            <div class="shadow-lg rounded mt-4 p-4">
                <div class="mb-5 fs-6 custom-content">
                    <?php echo wpautop(get_the_content()); ?>
                </div>

                <!-- Next & Previous Episode Navigation -->
                <?php
                $parent_blog_id = get_post_meta(get_the_ID(), 'story_id', true);

                // Fetch all episodes of the parent blog in ascending order
                // $args = array(
                //     'post_type'      => 'competition_episode',
                //     'posts_per_page' => -1,
                //     'meta_key'       => 'story_id',
                //     'meta_value'     => $parent_blog_id,
                //     'orderby'        => 'date',
                //     'order'          => 'ASC',
                // );

                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => -1,
                    'meta_key'       => 'competition_parent_id',
                    'meta_value'     => $competition_id,
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
                                <?php 
                                    $prev_url = add_query_arg(
                                        'episode_id', 
                                        $prev_episode->ID, 
                                        get_permalink($prev_episode->ID)
                                    );
                                ?>
                                <a href="<?php echo esc_url($prev_url); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>&nbsp;முந்திய பாகத்தை படிக்க
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="next-episode">
                            <?php if ($next_episode): ?>
                                <?php 
                                    $next_url = add_query_arg(
                                        'episode_id', 
                                        $next_episode->ID, 
                                        get_permalink($next_episode->ID)
                                    );
                                ?>
                                <a href="<?php echo esc_url($next_url); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye"></i>&nbsp;அடுத்த பாகத்தை படிக்க
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Reaction start -->
                <div class="row justify-content-center text-center">
                    <div class="col-12">
                        <div class="reactions-container">
                            <?php
                                $user_id = get_current_user_id();
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

                            <div class="emoji-reactions sec-comment" data-episode="<?php echo $episode_id; ?>" data-user="<?php echo $user_id; ?>">
                                <?php foreach ($emojis as $key => $emoji) : ?>
                                    <button class="emoji-btn" data-emo-symbol="<?php echo $key; ?>" data-emoji="<?php echo $emoji; ?>">
                                        <?php echo $emoji; ?> <span class="count" data-emoji="<?php echo $emoji; ?>"><?php echo $emoji_counts[$key] > 0 ? $emoji_counts[$key] : ''; ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <!-- Reaction End -->

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

                            <div class="star-rating sec-comment" data-episode="<?php echo $episode_id; ?>">
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
