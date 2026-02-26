jQuery(document).ready(function ($) {
    $('#registerForm').on('submit', function (e) {
        e.preventDefault();

        // const formData = $(this).serialize();
        const formData = new FormData($('#registerForm')[0]); // safer way
        formData.append('security', ajaxurl.nonce);

        const fileInput = $('#profile_picture')[0];
        if (fileInput && fileInput.files.length > 0) {
            formData.append('profile_picture', fileInput.files[0]);
        }

        $.ajax({
            type: 'POST',
            url: ajaxurl.url,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    $('#registerMessage').html('<div class="alert alert-success">' + response.data.message + '</div>');
                    if (response.data.user_id == '') {
                        $('#registerForm')[0].reset();
                    }

                    setTimeout(function () {
                        if (response.data.user_id) {
                            location.reload();
                        } else {
                            $('#registerModal').modal('hide');
                            $('#loginModal').modal('show');
                        }
                    }, 5000);
                } else {
                    $('#registerMessage').html('<div class="alert alert-danger">' + response.data + '</div>');
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
                $('#registerMessage').html('<div class="alert alert-danger">Something went wrong. Please try again.</div>');
            }
        });
    });

    $('#registerModal').on('hidden.bs.modal', function () {
        $('#registerForm')[0].reset();
        $('#registerMessage').html('');
    });

    $('#editProfileBtn').click(function () {
        // Open modal
        $('#registerModal').modal('show');

        // Get current user data via AJAX
        $.post(ajaxurl, { action: 'get_user_profile' }, function (response) {
            if (response.success) {
                const user = response.data;

                $('#regHeader').text('Profile Update')
                $('#regUsername').val(user.username);
                $('#email').val(user.email);
                $('#firstname').val(user.firstname);
                $('#lastname').val(user.lastname);
                $('#about_user').val(user.about_user);

                // Disable username/email if needed
                $('#regUsername').prop('disabled', true);
                $('#regSubmit').text('Update');

                // Update action for AJAX
                $('#formAction').val('update_user');
                $('#user_id').val(user.ID);

                // Hide image upload
                $('#profile_picture').closest('.row').hide();
                $('#loginLink').hide();
            }
        });
    });
});
