jQuery(document).ready(function ($) {
    $('.star').click(function() {
        let episode_id = $(this).parent().data('episode');
        let rating = $(this).data('value');

        if (!my_ajax_object.is_logged_in) {
            $('#loginModal').modal('show');
            return;
        }

        $(this).siblings().removeClass('rated');
        $(this).prevAll().addBack().addClass('rated');

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: "POST",
            data: {
                action: "save_episode_rating",
                episode_id: episode_id,
                rating: rating,
                security: my_ajax_object.nonce,
            },
            success: function(response) {
                if (response.success) {
                    $(".rating-text").text(response.data.message);
                } else {
                    alert(response.data.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
                alert("Something went wrong. Please try again.");
            }
        });
    });
});
