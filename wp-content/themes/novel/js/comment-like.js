
jQuery(document).ready(function ($) {
    $(document).on("click", ".like-comment", function () {
        var $this = $(this);
        var commentId = $this.data("comment-id");
        var likeCountSpan = $this.find(".like-count");

        $.ajax({
            type: "POST",
            url: commentLike.ajax_url,
            data: {
                action: "like_comment",
                comment_id: commentId,
            },
            success: function (response) {
                if (response.success) {
                    likeCountSpan.text(response.data);
                    $this.toggleClass("liked");
                }
            },
        });
    });

    $(".unlike-comment").on("click", function () {
        var commentId = $(this).data("comment-id");
        var unlikeCountSpan = $(this).find(".unlike-count");

        $.ajax({
            type: "POST",
            url: commentUnLike.ajax_url,
            data: {
                action: "unlike_comment",
                comment_id: commentId,
            },
            success: function (response) {
                if (response.success) {
                    unlikeCountSpan.text(response.data);
                }
            },
        });
    });
});

