jQuery(document).ready(function ($) {
    $('#registerForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: ajaxurl.url,
            data: formData + '&security=' + ajaxurl.nonce,
            success: function (response) {
                if (response.success) {
                    $('#registerMessage').html('<div class="alert alert-success">' + response.data + '</div>');
                    $('#registerForm')[0].reset();
                } else {
                    $('#registerMessage').html('<div class="alert alert-danger">' + response.data + '</div>');
                }
            },
        });
    });
});
