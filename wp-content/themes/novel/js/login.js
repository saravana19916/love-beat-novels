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

        jQuery.ajax({
            url: ajax_login_object.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'ajax_login',
                security: ajax_login_object.security, // ✅ REQUIRED for check_ajax_referer
                username: jQuery('#username').val(),
                password: jQuery('#password').val()
            },
            beforeSend: function () {
                jQuery('#login-message').html('<div class="alert alert-info">Processing...</div>');
            },
            success: function (res) {
                if (res && res.status === 'success') {
                    jQuery('#login-message').html('<div class="alert alert-success">' + res.message + '</div>');
                    setTimeout(() => window.location.reload(), 100);
                } else {
                    jQuery('#login-message').html('<div class="alert alert-danger">' + (res?.message || 'Login failed') + '</div>');
                }
            },
            error: function (xhr) { // ✅ show 403 details
                jQuery('#login-message').html(
                    '<div class="alert alert-danger">AJAX Error ' + xhr.status + ': ' + (xhr.responseText || '') + '</div>'
                );
            }
        });
    });

    $('#loginModal').on('hidden.bs.modal', function () {
        $('#login-form')[0].reset();
        $('.error-message').remove();
    });
});

// Google login
function onGoogleSignIn(response) {
    jQuery.ajax({
        url: ajax_login_object.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'google_login',
            security: ajax_login_object.security, // ✅ REQUIRED (your PHP checks nonce here too)
            id_token: response.credential
        },
        beforeSend: function () {
            jQuery('#login-message').html('<div class="alert alert-info">Processing Google login...</div>');
        },
        success: function (res) {
            if (res && res.status === 'success') {
                jQuery('#login-message').html('<div class="alert alert-success">' + res.message + '</div>');
                setTimeout(() => window.location.reload(), 100);
            } else {
                jQuery('#login-message').html('<div class="alert alert-danger">' + (res?.message || 'Google login failed') + '</div>');
            }
        },
        error: function (xhr) {
            jQuery('#login-message').html(
                '<div class="alert alert-danger">Google AJAX Error ' + xhr.status + ': ' + (xhr.responseText || '') + '</div>'
            );
        }
    });
}
