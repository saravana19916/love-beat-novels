jQuery(document).ready(function($) {
    $('#aboutUserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var aboutText = button.data('about') || '';
        $('#about_user_text').val(aboutText);
    });

    $('#aboutUserForm').on('submit', function(e) {
        e.preventDefault();

        var aboutUser = $('#about_user_text').val();

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'save_about_user',
                about_user: aboutUser,
                security: ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#aboutUserModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.data);
                }
            }
        });
    });

    $('.follow-btn').on('click', function() {
        var btn = $(this);
        var author_id = btn.data('author-id');

        $.ajax({
            type: 'POST',
            url: ajax_object.ajax_url,
            data: {
                action: 'follow_user',
                author_id: author_id,
                security: ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    btn.text('Following').prop('disabled', true);
                    location.reload();
                } else {
                    alert(response.data);
                }
            }
        });
    });
});
