<?php

// Function to get the last episode number for a series
function get_last_episode_number($series_id) {
    // Query the posts that belong to the given series
    $args = array(
        'post_type'      => 'post',
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => 'parent_blog_id',
                'value'   => $series_id,
                'compare' => '=',
            ),
            array(
                'key'     => 'my_creation_parent_blog_id',
                'value'   => $series_id,
                'compare' => '=',
            ),
            array(
                'key'     => 'competition_parent_id',
                'value'   => $series_id,
                'compare' => '=',
            ),
        ),
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'episode_number',
        'order'          => 'DESC',
        'posts_per_page' => 1,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $post = $query->posts[0];
        $last_episode_number = get_post_meta($post->ID, 'episode_number', true);
        return (int) $last_episode_number;
    }

    return 0;
}

function find_episode_is_locked($parentId, $episode_id, $episodeNumber) {
    $user_id = get_current_user_id();

    if (current_user_can('administrator')) {
        return false;
    }

    $lock_after = get_option('global_episode_lock_after', 0);

    $subscription_expiry = get_user_meta($user_id, 'subscription_active_expiry', true);
    $has_active_subscription = ($subscription_expiry && strtotime($subscription_expiry) > time());

    if ($has_active_subscription) {
        $is_locked = false;
    } else {
        $globally_unlocked = (array) get_option('global_unlocked_series', []);
        if (in_array($parentId, $globally_unlocked)) {
            $is_locked = false;
        } else {
            $unlocked = get_user_meta($user_id, 'unlocked_episodes', true);
            $is_unlocked = (is_array($unlocked) && in_array($episode_id, $unlocked));
            $is_locked = ($episodeNumber > $lock_after && !$is_unlocked);
        }
    }

    return $is_locked;
}