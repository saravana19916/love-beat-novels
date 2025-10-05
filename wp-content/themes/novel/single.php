<?php get_header(); ?>

<!-- <div class="container mt-5"> -->

    <?php

    $args = array(
    'post_type'      => 'post',
    'meta_query'     => array(
        'relation' => 'OR',
        array(
            'key'     => 'parent_blog_id',
            'value'   => get_the_ID(),
            'compare' => '='
        ),
        array(
            'key'     => 'my_creation_parent_blog_id',
            'value'   => get_the_ID(),
            'compare' => '='
        ),
    ),
    'orderby'        => 'date',
    'order'          => 'ASC',
    'posts_per_page' => -1
);

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ?>

        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="shadow-lg p-4 rounded shadow-div">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <?php if (has_post_thumbnail()) : ?>
                                    <img src="<?php the_post_thumbnail_url('large'); ?>" class="img-fluid mx-auto rounded shadow" alt="<?php the_title(); ?>" style="height: 300px; width: 220px;">
                                <?php else : ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpeg" class="img-fluid mx-auto d-block my-3" alt="Default Image" style="height: 300px; width: 220px;">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-9 mt-4 mt-md-0 p-4">
                                <h6 class="fw-bold mb-3"><?php the_title(); ?></h6>
                                <div class="mb-3 col-md-9">
                                    <?php echo wpautop(get_the_content()); ?>
                                </div>

                                <div class="mt-4">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="mt-5 mb-4 fw-bold">அத்தியாயங்கள்</h>

            <div class="row mb-5 episode-padding">
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
                    <div class="col-lg-6 col-xxl-4">
                        <div class="shadow-lg rounded mt-4 shadow-div">
                            <div class="d-flex justify-content-between align-items-center px-4 pt-4">
                                <h6 class="mb-0 fw-bold">
                                    <?php echo sprintf("%2d", $count + 1); ?>.&nbsp
                                    <a href="<?php the_permalink(); ?>" class="text-primary-color"><?php the_title(); ?></a>
                                </h6>

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

                                <span class="text-muted fs-custom text-primary-color"><p class="text-primary-color"><?php echo $tamil_date; ?></p></span>
                            </div>
                            <div class="d-flex align-items-center px-5 my-3 fs-custom">
                                <p class="me-4">
                                    <i class="fa-solid fa-eye"></i>&nbsp;&nbsp;<?php echo format_view_count($views); ?>
                                </p>
                                <p>
                                    <i class="fa-solid fa-star" style="color: gold;"></i>&nbsp;&nbsp;<?php echo $average_rating; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                <?php
                    $count++;
                }
                ?>
            </div>
        </div>
    <?php
        } else {
    ?>

    <?php
        $args = array(
            'post_type' => 'post',
            'meta_query' => array(
                array(
                    'p' => get_the_ID()
                )
                ),
                'orderby' => 'date',
                'order'   => 'ASC'
        );
        $mainQuery = new WP_Query($args);

        if ($mainQuery->found_posts > 0) {
            increase_story_view_count();
        }

        if ($mainQuery->have_posts()) {
    ?>
    
            <div class="container details-page mt-5">
                <div class="row mb-5 details-page">
                    <div class="col-md-12">
                        <h5 class="fw-bold text-center my-4 text-primary-color"><?php the_title(); ?></h5>
                        
                        <div class="shadow-lg rounded mt-4 p-4 shadow-div">
                            <div class="mb-5 fs-6 custom-content">
                                <?php echo wpautop(get_the_content()); ?>
                                <?php track_recently_read_post(get_the_ID()); ?>
                            </div>

                            <!-- Next & Previous Episode Navigation -->
                            <?php
                            $parent_blog_id = get_post_meta(get_the_ID(), 'parent_blog_id', true);
                            $my_creation_parent_blog_id = get_post_meta(get_the_ID(), 'my_creation_parent_blog_id', true);

                            // Fetch all episodes of the parent blog in ascending order
                            $args = array(
                                'post_type'      => 'post',
                                'posts_per_page' => -1,
                                'meta_query'     => array(
                                    'relation' => 'OR',
                                    array(
                                        'key'     => 'parent_blog_id',
                                        'value'   => $parent_blog_id,
                                        'compare' => '='
                                    ),
                                    array(
                                        'key'     => 'my_creation_parent_blog_id',
                                        'value'   => $my_creation_parent_blog_id,
                                        'compare' => '='
                                    ),
                                ),
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

                                        <div class="emoji-reactions sec-comment bg-transparent shadow-div" data-episode="<?php echo $episode_id; ?>" data-user="<?php echo $user_id; ?>">
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
    <?php    } }

        wp_reset_postdata();
    ?>
<!-- </div> -->

<?php get_footer(); ?>
