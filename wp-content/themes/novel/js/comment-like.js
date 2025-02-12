jQuery(document).on('click', '.like-comment', function () {
    const button = jQuery(this);
    const commentId = button.data('comment-id');

    jQuery.post(commentLike.ajax_url, {
        action: 'like_comment',
        comment_id: commentId
    }, function (response) {
        if (response.success) {
            button.text('Like (' + response.data + ')');
        }
    });
});
