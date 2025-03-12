<?php
get_header();
?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 submit-page-padding">
                <?php 
                if (isset($_GET['story_id']) && is_numeric($_GET['story_id'])) {
                    $story_id = intval($_GET['story_id']);
                    $story_title = $story_id ? get_the_title($story_id) : '';

                    $episode_id = isset($_GET['post_id']) && is_numeric($_GET['post_id']) ? intval($_GET['post_id']) : 0;

                    // if ($episode_id) {
                    //     $post = get_post($episode_id);
                    //     if ($post) {
                    //         $episode_title = esc_attr($post->post_title);
                    //         $episode_content = esc_textarea($post->post_content);
                    //     } else {
                    //         $episode_title = '';
                    //         $episode_content = '';
                    //     }
                    // } else {
                    //     $episode_title = '';
                    //     $episode_content = '';
                    // }

                    $episode = $episode_id ? get_post($episode_id) : null;
                    $episode_title = $episode ? esc_attr($episode->post_title) : '';
                    $episode_content = $episode ? esc_textarea($episode->post_content) : '';
                } else {
                    $story_id = 0;
                    $story_title = 'Unknown Competition';
                }
                ?>

                <h3 class="text-primary-color">Create Episode for: <?php echo esc_html($story_title); ?></h3>

                <div class="shadow-lg rounded my-5 p-4">
                    <?php if (is_user_logged_in()) : ?>
                        <form id="episode-form" class="p-4">
                            <input type="hidden" id="story-id" value="<?php echo $_GET['story_id']; ?>">
                            <input type="hidden" id="episode-id" value="<?php echo $episode_id; ?>">

                            <div class="row mb-4">
                                <label for="title" class="col-sm-2 col-form-label">தலைப்பு <span class="text-danger">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="episode-title" placeholder="அத்தியாயம் தலைப்பு" value="<?php echo $episode_title; ?>">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="title" class="col-sm-2 col-form-label">கதை <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <textarea id="episode-content" class="form-control" placeholder="கதை" rows="10"><?php echo $episode_content; ?></textarea>
                                </div>
                            </div>

                            <div class="row mb-4">
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
                                    <a href="<?php echo esc_url(get_permalink($story_id)); ?>" class="btn btn-secondary btn-sm ms-3"> <i class="fa-solid fa-arrow-left"></i>&nbsp;&nbsp; Back</a>
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

<script>
jQuery(document).ready(function($) {
    let editor;

    ClassicEditor
        .create(document.querySelector('#episode-content'), {
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

    $('#episode-form').submit(function(e) {
        e.preventDefault();

        let isValid = true;
        let title = $('#episode-title').val().trim();
        // let content = $('#episode-content').val().trim();
        let content = editor.getData().trim();

        $('.error-message').remove();

        if (title === '') {
            isValid = false;
            $('#episode-title').after('<small class="text-danger error-message">Title is required.</small>');
        }

        if (content === '') {
            isValid = false;
            $('#episode-content').after('<small class="text-danger error-message">Story is required.</small>');
        }

        if (!isValid) return;

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                action: 'submit_episode',
                episode_id: $('#episode-id').val(),
                episode_title: title,
                episode_content: content,
                story_id: $('#story-id').val(),
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

<?php get_footer(); ?>
