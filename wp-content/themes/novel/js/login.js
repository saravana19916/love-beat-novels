jQuery(document).ready(function ($) {
    $('#login-form').on('submit', function (e) {
        e.preventDefault();

        const username = $('#username').val();
        const password = $('#password').val();
        const messageContainer = $('#login-message');

        // Clear previous messages
        messageContainer.html('');

        $.ajax({
            type: 'POST',
            url: ajax_login_object.ajax_url,
            data: {
                action: 'ajax_login',
                username: username,
                password: password,
                security: ajax_login_object.security,
            },
            beforeSend: function () {
                messageContainer.html('<div class="alert alert-info">Processing...</div>');
            },
            success: function (response) {
                if (response.status === 'error') {
                    messageContainer.html('<div class="alert alert-danger">' + response.message + '</div>');
                } else if (response.status === 'success') {
                    messageContainer.html('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function () {
                        window.location.href = response.redirect_url;
                    }, 2000);
                }
            },
        });
    });
});
