<?php
    $user_id = $args['user_id'] ?? '';
    $current_user = get_userdata($user_id);
    $profile_picture_id = get_user_meta($current_user->ID, 'profile_picture', true);
    $profile_picture_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : get_template_directory_uri() . '/images/profile-no-image.jpg';

    $post_count = count_user_posts($current_user->ID, 'post');

    $isWriter = false;
    if ($post_count > 0) {
        $icon_html = '<i class="fa-solid fa-pen-nib me-1"></i>';
        $isWriter = true;
    } else {
        $icon_html = '<i class="fa-solid fa-book-reader me-1"></i>';
        $isWriter = false;
    }

    // Handle profile picture update
    if (isset($_POST['update_profile_picture']) && !empty($_FILES['profile_picture']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attachment_id = media_handle_upload('profile_picture', 0);

        if (!is_wp_error($attachment_id)) {
            update_user_meta($current_user->ID, 'profile_picture', $attachment_id);
            $profile_picture_url = wp_get_attachment_url($attachment_id);
        }
    }

    $about_user = get_user_meta($current_user->ID, 'about_user', true);

    // count followers
$all_users = get_users();
$followers_count = 0;
foreach ($all_users as $user) {
    $user_following = get_user_meta($user->ID, 'following_users', true);
    if (is_array($user_following) && in_array($current_user->ID, $user_following)) {
        $followers_count++;
    }
}

// Following: how many this author is following
$author_following = get_user_meta($current_user->ID, 'following_users', true);
$author_following_count = is_array($author_following) ? count($author_following) : 0;
?>
        <div class="card shadow-md text-center text-md-start shadow-div bg-transparent pt-3 px-3">
            <div class="row gx-4 align-items-center justify-content-center">
                <!-- Profile Image -->
                <div class="col-auto d-flex flex-column align-items-center mb-3 mb-md-0">
                    <img src="<?php echo esc_url($profile_picture_url); ?>" 
                        class="rounded-circle img-fluid shadow-sm border border-3 border-white mb-2" 
                        width="120" 
                        alt="Profile Photo"
                    >

                    <?php if (get_current_user_id() == $user_id) { ?>
                        <form method="post" enctype="multipart/form-data" id="profile-picture-form">
                            <input type="file" name="profile_picture" id="profile-picture-input" style="display:none" required>
                            <input type="hidden" name="update_profile_picture" value="1">
                            <button id="change-photo-btn" class="btn btn-sm" style="background-color: #061148 !important; border: #061148 !important; color: white;">Change Photo</button>                        
                        </form>
                    <?php } ?>

                    <p class="mt-3 mb-2 text-primary-color fs-16px"><strong><?php echo $isWriter ? 'Author' : 'Reader' ?>:&nbsp; </strong> <span> <?php echo $icon_html . esc_html($current_user->display_name); ?></span></p>
                    <div class="mt-2">
                        <?php if (!empty($about_user)) : ?>
                            <p class="text-primary-color text-center fs-6"><?php echo esc_html($about_user); ?></p>
                        <?php elseif(get_current_user_id() == $user_id) : ?>
                            <p class="text-primary-color">
                                Still you have not added about you. 
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#aboutUserModal">
                                    Click here
                                </button>
                            </p>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Followers / Following + Button -->
                <div class="col-auto d-flex flex-column align-items-center ms-md-5">
                    <div class="d-flex gap-4 mb-3 justify-content-center">
                        <?php if ($isWriter) { ?>
                            <div class="text-center">
                                <p class="mb-0 text-primary-color"><?php echo $followers_count; ?></p>
                                <p class="mb-0 fw-bold fs-6 text-primary-color">Followers</p>
                            </div>
                            <?php } ?>
                        <div class="text-center">
                            <p class="mb-0 text-primary-color"><?php echo $author_following_count; ?></p>
                            <p class="mb-0 fw-bold fs-6 text-primary-color">Following</p>
                        </div>
                    </div>
                    <?php
                        $current_user_id = get_current_user_id();
                        $author_id = get_post_field('post_author', get_the_ID());

                        // Don't show follow button for the current user
                        if ($current_user_id && $current_user_id != $user_id && $isWriter) {

                            // Get list of users that current user follows
                            $following = get_user_meta($current_user_id, 'following_users', true);
                            $following = is_array($following) ? $following : array();

                            $is_following = in_array($user_id, $following);

                            $btn_text = $is_following ? 'Following' : 'Follow';
                            $btn_disabled = $is_following ? 'disabled' : '';
                    ?>
                            <div class="d-flex justify-content-center w-100">
                                <button class="btn btn-primary btn-sm follow-btn"
                                        data-author-id="<?php echo $user_id; ?>"
                                        <?php echo $btn_disabled; ?>>
                                    <?php echo $btn_text; ?>
                                </button>
                            </div>
                    <?php } ?>
                </div>

                <?php
                    $uid = get_current_user_id();
                    $current_plan   = get_user_meta($uid, 'subscription_active_plan', true);
                    $current_period = get_user_meta($uid, 'subscription_active_period', true);
                    $expiry         = get_user_meta($uid, 'subscription_active_expiry', true);
                    $coin_balance   = get_user_meta($uid, 'user_coin_balance', true) ?: 0;
                ?>

                <div class="d-flex justify-content-center w-100">
                    <div class="card rounded-4 shadow-lg bg-transparent shadow-div mb-3" style="max-width:600px !important; width:600px !important;">
                        <div class="card-body p-4">

                            <h5 class="card-title fw-bold text-primary-color mb-3">Your Plan Details</h5>

                            <?php if ($current_plan): ?>

                                <p class="mb-2 text-primary-color">
                                    உங்களுடைய தற்போதைய ஆக்ட்டிவ் பிளான் :<strong> <?= esc_html($current_plan); ?></strong>
                                    <?php if (in_array($current_period, ['1 Month', '3 Months'])): ?>
                                        <a href="<?= esc_url(site_url('/subscription')); ?>" class="btn btn-warning btn-sm ms-2">
                                            Upgrade Plan
                                        </a>
                                    <?php endif; ?>
                                </p>

                                <p class="mb-3 text-primary-color">
                                    உங்கள் பிளான் காலாவதி தேதி: 
                                    <strong><?= $expiry ? date_i18n(get_option('date_format'), strtotime($expiry)) : 'N/A'; ?></strong>
                                </p>

                                <?php
                                    $queue = get_user_meta(get_current_user_id(), 'subscription_queue', true);
                                    $queue = is_array($queue) ? $queue : [];
                                ?>

                                <?php if (!empty($queue)): ?>
                                    <div class="mt-3 text-primary-color">
                                        <h6><strong class="d-block mb-2">Upcoming Plans:</strong></h6>
                                        <?php foreach ($queue as $q): ?>
                                            <div class="border rounded-3 p-2 mb-2 w-100" style="max-width:320px;">
                                                <div class="fw-bold"><?= esc_html($q['plan']); ?></div>
                                                <small>Starts: <?= esc_html($q['from']); ?></small><br>
                                                <small>Expires: <?= esc_html($q['expiry']); ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                            <?php else: ?>
                                <p class="text-danger fw-semibold">No Active Subscription Plan</p>
                                <a href="<?= esc_url(site_url('/subscription')); ?>" class="btn btn-primary btn-sm mb-3">Subscribe Now</a>
                            <?php endif; ?>

                            <hr class="my-2">

                            <div class="text-primary-color mt-3">
                                <h6>
                                    <img src="<?php echo get_template_directory_uri() . '/images/coin.png'; ?>" width="24" class="me-2" alt="Coin">
                                    <strong>Coin Balance:</strong> <span class="ms-1"><?= esc_html($coin_balance); ?></span>
                                </h6>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="aboutUserModal" tabindex="-1" aria-labelledby="aboutUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aboutUserModalLabel">Add About You</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="aboutUserForm">
                    <div class="mb-3">
                        <textarea class="form-control" id="about_user_text" rows="4" placeholder="Write something about yourself..."></textarea>
                    </div>
                    <button type="submit" class="btn primary-btn">Save</button>
                    </form>
                </div>
                </div>
            </div>
            </div>
        </div>



    