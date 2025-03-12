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
                'callback'    => 'bootstrap5_comment_callback',
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
                'title_reply'      => 'Comments',
                'label_submit' => 'கருத்தை வெளியிடவும்',
                'comment_field'    => '<div class="mb-3"><label for="comment" class="form-label">கருத்து</label><textarea id="comment" name="comment" class="form-control" rows="4" required></textarea></div>',
                'logged_in_as' => ''
            ]);
        } else {
    ?>
        <div class="text-end">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">You must log in to reply here</button>
        </div>
    <?php } ?>

</div><!-- .comments-area -->
