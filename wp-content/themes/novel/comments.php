<?php
// Exit if accessed directly
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php
        $comments_count = get_comments([
            'post_id' => get_the_ID(),
            'parent' => 0,
            'status' => 'approve',
            'count'  => true,
        ]);
    ?>
    <a href="" data-bs-toggle="modal" data-bs-target="#commentsModal" class="fs-6 fw-bold text-primary-color position-relative">
        Comments &nbsp;
        <span class="icon-container position-relative">
            <i class="fa-solid fa-comments fa-2xl"></i>
            <span class="count-badge"><?php echo $comments_count; ?></span>
        </span>
    </a>


    <!-- Comments Modal -->
    <div class="modal fade" id="commentsModal" tabindex="-1" aria-labelledby="commentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentsModalLabel"><?php echo $comments_count . ' Comments'; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height: 31rem; overflow-y: scroll;">
                    <?php
                        $comments = get_comments([
                            'post_id' => get_the_ID(),
                            'status' => 'approve',
                            'order' => 'DESC'
                        ]);

                        echo '<ul class="comment-list list-unstyled">';

                        if ($comments) {
                            wp_list_comments([
                                'style'       => 'ul',
                                'short_ping'  => true,
                                'avatar_size' => 64,
                                'callback'    => 'bootstrap5_comment_callback',
                                'reply_text'  => __('Reply'),
                            ]);
                        }

                        echo '</ul>';

                        if (!$comments) {
                            echo '<p class="text-center no-comment">No comments yet.</p>';
                        }
                    ?>
                </div>
                <div class="modal-footer" style="display: flow;">
                    <?php
                        if (is_user_logged_in()) {
                            comment_form([
                                'id_form'      => 'ajax-comment-form',
                                'class_form'    => 'needs-validation comment-section position-relative w-100', 
                                'class_submit'  => 'd-none',
                                'title_reply'   => '', 
                                'label_submit'  => '', 
                                'comment_field' => '
                                    <div class="comment-box p-2 rounded position-relative">
                                        <div class="d-flex align-items-start">
                                            <!-- Author Photo -->
                                            <div class="me-2">
                                                <img src="' . get_avatar_url(get_current_user_id(), ["size" => 50]) . '" class="rounded-circle" alt="Author">
                                            </div>
                                            <!-- Comment Input -->
                                            <div class="flex-grow-1 position-relative">
                                                <textarea id="comment" name="comment" class="form-control comment-text" rows="1" placeholder="Write.." required></textarea>
                                                <!-- Emoji Picker -->
                                                <div id="emoji-picker" class="position-absolute start-0 bottom-0 mb-1">
                                                    <i class="fa-regular fa-face-smile emoji-trigger" style="cursor:pointer;"></i>
                                                </div>
                                            </div>
                                            <!-- Send Button -->
                                            <button type="button" class="btn btn-primary send-comment">
                                                <i class="fa-solid fa-paper-plane"></i>
                                            </button>
                                        </div>
                                    </div>
                                ',
                                'logged_in_as' => '', 
                            ]);
                        } else {
                    ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">You must log in to reply here</button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
jQuery(document).ready(function($) {
    // Emoji Picker
    $('#comment').emojioneArea({
        pickerPosition: "top",
        tonesStyle: "radio",
        autogrow: true
    });

    $('.reply-form-text').emojioneArea({
        pickerPosition: "top",
        tonesStyle: "radio",
        autogrow: true
    });

    // Click send button to submit form
    // $('.send-comment').on('click', function() {
    //     $('form.comment-form').submit();
    // });

    $('.send-comment').on('click', function () {
        $('#ajax-comment-form').submit();
    });

    function initializeEmojiAreas(scope) {
        $(scope).find('.reply-form-text').each(function () {
            $(this).emojioneArea({
                pickerPosition: "top",
                tonesStyle: "radio",
                autogrow: true
            });
        });
    }

    $('#ajax-comment-form').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = form.serialize();

        $.ajax({
        type: 'POST',
        url: '<?php echo admin_url("admin-ajax.php"); ?>',
        data: formData + '&action=ajax_comment',
        success: function (response) {
            if (response.success) {
                var $newComment = $(response.data.comment_html);

                $('.comment-list').prepend(response.data.comment_html);

                initializeEmojiAreas($newComment);

                $('.no-comment').addClass('d-none');

                // Clear comment text
                var emojioneInstance = $("#comment").data("emojioneArea");
                emojioneInstance.setText('');

                // Update count
                let count = parseInt($('.count-badge').text());
                count++;
                $('.count-badge').text(count);
                $('#commentsModalLabel').text(count + ' Comments');
            } else {
                alert('Error: ' + response.data);
            }
        },
        error: function () {
            alert('Something went wrong. Please try again.');
        }
        });
    });

    // Reply save
    $(document).on('click', '.send-comment-reply', function (e) {
        e.preventDefault();

        const button = $(this);
        const formContainer = button.closest('.comment-box');
        const commentText = formContainer.find('textarea[name="comment"]').val();
        const postID = formContainer.find('input[name="comment_post_ID"]').val();
        const parentID = formContainer.find('input[name="comment_parent"]').val();

        if (!commentText.trim()) {
            alert('Please enter your reply.');
            return;
        }

        const formData = {
            action: 'ajax_reply_comment',
            comment: commentText,
            comment_post_ID: postID,
            comment_parent: parentID
        };

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            data: formData,
            success: function (response) {
                if (response.success) {
                    const childContainer = $('#child-comments-' + parentID);
                    childContainer.removeClass('d-none');
                    const replyBox = childContainer.find('.comment-box').first();
                    if (replyBox.length) {
                        $(response.data.comment_html).insertBefore(replyBox);
                    } else {
                        childContainer.append(response.data.comment_html);
                    }

                    // Clear the reply input
                    const textarea = formContainer.find('.reply-form-text');
                    const emojioneInstance = textarea.data('emojioneArea');
                    if (emojioneInstance) {
                        emojioneInstance.setText('');
                    }

                    $('.reply-form-text').val('');

                    // Update reply count
                    let count = parseInt($('.reply-count-' + parentID).text());
                    count = count ? count : 0;
                    count++;
                    $('.reply-count-' + parentID).text(count);

                    $('.reply-count-text-' + parentID).text(count == 1 ? 'Reply' : 'Replies');
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function () {
                alert('Something went wrong. Please try again.');
            }
        });
    });
});
</script>

<style>
    .emojionearea .emojionearea-editor {
        min-height: 2.5rem !important;
        padding: 10px 24px 10px 12px !important;
        text-align: left;
    }

    #emoji-picker, .comment-reply-title, .form-submit {
        display: none;
    }

    .send-comment {
        margin-left: 10px;
    }
</style>

