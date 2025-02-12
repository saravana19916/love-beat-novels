<?php
// Exit if accessed directly
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area mb-5">

    <?php if (have_comments()) : ?>
        <h2 class="comments-title mt-5">
            <?php
            $comments_number = get_comments_number();
            printf(
                _n('%s Comment', '%s Comments', $comments_number, 'textdomain'),
                number_format_i18n($comments_number)
            );
            ?>
        </h2>

        <ul class="comment-list list-unstyled">
            <?php
            wp_list_comments([
                'style'       => 'ul',
                'short_ping'  => true,
                'avatar_size' => 64,
                'callback'    => 'bootstrap5_comment_callback', // Custom callback
            ]);
            ?>
        </ul>

        <?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php
        // Display the comment form
        if (is_user_logged_in()) {
            comment_form([
                'class_form'       => 'needs-validation',
                'class_submit'     => 'btn btn-primary',
                'title_reply'      => 'Leave a Reply',
                'comment_field'    => '<div class="mb-3"><label for="comment" class="form-label">Comment</label><textarea id="comment" name="comment" class="form-control" rows="4" required></textarea></div>',
                // 'fields'           => [
                //     'author' => '<div class="mb-3"><label for="author" class="form-label">Name</label><input id="author" name="author" type="text" class="form-control" required></div>',
                //     'email'  => '<div class="mb-3"><label for="email" class="form-label">Email</label><input id="email" name="email" type="email" class="form-control" required></div>',
                //     'url'    => '<div class="mb-3"><label for="url" class="form-label">Website</label><input id="url" name="url" type="url" class="form-control"></div>',
                // ],
            ]);
        } else {
    ?>
        <div class="text-end">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">You must log in to reply here</button>
        </div>
    <?php } ?>

</div><!-- .comments-area -->
