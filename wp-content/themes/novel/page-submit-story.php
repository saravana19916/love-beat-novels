<?php
/* Template Name: Submit Story */
get_header();
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <?php 
                $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
                if (isset($_GET['competition_id']) && is_numeric($_GET['competition_id'])) {
                    $competition_id = intval($_GET['competition_id']);
                    $competition_title = get_the_title($competition_id);
                } else {
                    $competition_id = 0;
                    $competition_title = 'Unknown Competition';
                }

                if ($post_id) {
                    $post = get_post($post_id);
                    if ($post) {
                        $title = esc_attr($post->post_title);
                        $content = esc_textarea($post->post_content);
                    } else {
                        $title = '';
                        $content = '';
                    }
                } else {
                    $title = '';
                    $content = '';
                }
            ?>

            <h3 class="text-primary-color">Create Story for: <?php echo esc_html($competition_title); ?></h3>

            <div class="shadow-lg rounded my-5 p-4">
                <?php if (is_user_logged_in()) : ?>
                    <form id="competition-post-form" class="p-4">
                        <input type="hidden" id="post-id" value="<?php echo $post_id; ?>">
                        <input type="hidden" id="competition-id" value="<?php echo $competition_id; ?>">
                        <div class="row mb-4">
                            <label for="title" class="col-sm-2 col-form-label">தலைப்பு <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="post-title" placeholder="கதைத் தலைப்பு" value="<?php echo $title; ?>">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="post-content" class="col-sm-2 col-form-label">கதை <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea id="post-content" class="form-control" placeholder="கதை" rows="10"><?php echo $content; ?></textarea>
                            </div>
                        </div>

                        <div class="row mb-4 align-items-center">
                            <div class="col-sm-9 offset-sm-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <?php if ($post_id): ?>
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        &nbsp;&nbsp; Update
                                    <?php else: ?>
                                        <i class="fa-solid fa-plus"></i>
                                        &nbsp;&nbsp; Create
                                    <?php endif; ?>
                                </button>
                                <a href="<?php echo esc_url(get_permalink($competition_id)); ?>" class="btn btn-secondary btn-sm ms-3"> <i class="fa-solid fa-arrow-left"></i>&nbsp;&nbsp; Back</a>
                            </div>
                        </div>
                    </form>
                    <div id="post-response"></div>
                <?php else : ?>
                    <p>Please <a href="<?php echo wp_login_url(get_permalink()); ?>">log in</a> to submit a story.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

<script>
jQuery(document).ready(function($) {
    let editor;

    ClassicEditor
        .create(document.querySelector('#post-content'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', 'code', 'subscript', 'superscript', '|',
                'bulletedList', 'numberedList', 'todoList', '|',
                'alignment', 'outdent', 'indent', '|',
                'link', 'blockQuote', 'imageUpload', 'insertTable', 'mediaEmbed', '|',
                'undo', 'redo'
            ]
        })
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
            console.error('CKEditor error:', error);
        });

    $('#competition-post-form').submit(function(e) {
        e.preventDefault();

        let isValid = true;
        let title = $('#post-title').val().trim();
        let content = editor.getData().trim();

        $('.error-message').remove();

        if (title === '') {
            isValid = false;
            $('#post-title').after('<small class="text-danger error-message">Title is required.</small>');
        }

        if (content === '') {
            isValid = false;
            $('#post-content').after('<small class="text-danger error-message">Story is required.</small>');
        }

        if (!isValid) return;

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                action: 'submit_competition_post',
                post_title: title,
                post_content: content,
                competition_id: $('#competition-id').val(),
                post_id: $('#post-id').val(),
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.redirect_url;
                } else {
                    $('#post-response').text(response.data);
                }
            }
        });
    });
});
</script>
