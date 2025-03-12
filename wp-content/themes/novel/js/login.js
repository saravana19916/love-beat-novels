jQuery(document).ready(function ($) {
    $('#login-form').on('submit', function (e) {
        e.preventDefault();

        const username = $('#username').val();
        const password = $('#password').val();
        const messageContainer = $('#login-message');

        let isValid = true;
        let title = $('#username').val().trim();
        let content = $('#password').val().trim();

        $('.error-message').remove();

        if (title === '') {
            isValid = false;
            $('#username').after('<small class="text-danger error-message">Username is required.</small>');
        }

        if (content === '') {
            isValid = false;
            $('#password').after('<small class="text-danger error-message">Password is required.</small>');
        }

        if (!isValid) return;

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

    $('#loginModal').on('hidden.bs.modal', function () {
        $('#login-form')[0].reset();
        $('.error-message').remove();
    });
});
