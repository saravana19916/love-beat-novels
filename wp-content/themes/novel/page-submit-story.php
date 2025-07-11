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

            <h5 class="text-primary-color fw-bold">Create Story for: <?php echo esc_html($competition_title); ?></h5>

            <div class="shadow-lg rounded my-4 p-4">
                <?php
                    if (is_user_logged_in()) :
                        $image_url = '';
                        if ($post_id && has_post_thumbnail($post_id)) {
                            $image_url = get_the_post_thumbnail_url($post_id, 'medium');
                        }
                ?>
                    <form id="competition-post-form" class="p-0 p-xl-4">
                        <input type="hidden" id="post-id" value="<?php echo $post_id; ?>">
                        <input type="hidden" id="competition-id" value="<?php echo $competition_id; ?>">
                        <div class="row mb-4">
                            <label for="title" class="col-12 col-xl-1 col-form-label">Title <span class="text-danger">*</span></label>
                            <div class="col-sm-6 col-xl-4">
                                <input type="text" class="form-control" id="post-title" placeholder="Title" value="<?php echo $title; ?>">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="post-image" class="col-12 col-xl-1 col-form-label">Image <span class="text-danger">*</span></label>
                            <div class="col-sm-6 col-xl-4">
                                <input type="file" class="form-control" id="post-image" name="post_image" accept="image/*">
                                <div class="mt-2">
                                    <img id="image-preview" src="<?php echo esc_url($image_url); ?>" alt="Image preview" class="img-fluid rounded border" style="max-height: 150px; <?php echo $image_url ? '' : 'display: none;'; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="post-content" class="col-12 col-xl-1 col-form-label">Description <span class="text-danger">*</span></label>
                            <div class="col-12 col-xl-11">
                                <textarea id="post-content" class="form-control" placeholder="Description" rows="10"><?php echo $content; ?></textarea>
                            </div>
                        </div>

                        <div class="row mb-4 align-items-center">
                            <div class="col-sm-9 offset-xl-1">
                                <button type="submit" class="btn btn-primary btn-sm pt-2">
                                    <?php if ($post_id): ?>
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        &nbsp; Update
                                    <?php else: ?>
                                        <i class="fa-solid fa-plus"></i>
                                        &nbsp; Create
                                    <?php endif; ?>
                                </button>
                                <a href="<?php echo esc_url(get_permalink($competition_id)); ?>" class="btn btn-secondary btn-sm ms-3 pt-2"> <i class="fa-solid fa-arrow-left"></i>&nbsp; Back</a>
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
    // let editor;

    // ClassicEditor
    //     .create(document.querySelector('#post-content'), {
    //         toolbar: [
    //             'heading', '|',
    //             'bold', 'italic', 'underline', 'strikethrough', 'code', 'subscript', 'superscript', '|',
    //             'bulletedList', 'numberedList', 'todoList', '|',
    //             'alignment', 'outdent', 'indent', '|',
    //             'link', 'blockQuote', 'imageUpload', 'insertTable', 'mediaEmbed', '|',
    //             'undo', 'redo', 'emoji'
    //         ]
    //     })
    //     .then(newEditor => {
    //         editor = newEditor;
    //     })
    //     .catch(error => {
    //         console.error('CKEditor error:', error);
    //     });
    
    $("#post-content").trumbowyg({
        btns: [
            ['formatting'],
            ['fontsize'],
            ["bold", "italic", "underline"],
            ['unorderedList', 'orderedList'],
            ["emoji"]
        ],
        autogrow: true
    });

    function loadTamilTyping() {
        console.log("tamil");
        if (typeof google === "undefined" || typeof google.elements === "undefined") {
            console.log("tamil1");
            setTimeout(loadTamilTyping, 500); // Retry every 500ms
        } else {
            console.log("tamil");
            google.elements.transliteration.load();
            var options = {
                sourceLanguage: 'en',
                destinationLanguage: ['ta'], // Tamil
                transliterationEnabled: true
            };
            var control = new google.elements.transliteration.TransliterationControl(options);

            // Apply to Trumbowyg editor when initialized
            $('#post-content').on('tbwinit', function () {
                control.makeTransliteratable(['post-content']); // Enable Tamil typing
            });
        }
    }

    // Start checking for API availability
    // loadTamilTyping();

    $('#post-image').on('change', function() {
        const file = this.files[0];
        const preview = $('#image-preview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        } else {
            preview.hide();
        }
    });

    $('#competition-post-form').submit(function(e) {
        e.preventDefault();

        let isValid = true;
        let title = $('#post-title').val().trim();
        let content = $('#post-content').val().trim();
        let image = $('#post-image')[0].files[0];
        // let content = editor.getData().trim();

        $('.error-message').remove();

        if (title === '') {
            isValid = false;
            $('#post-title').after('<small class="text-danger error-message">Title is required.</small>');
        }

        if (content === '') {
            isValid = false;
            $('#post-content').after('<small class="text-danger error-message">Story is required.</small>');
        }

        if (!image && $('#post-id').val() === '0') {
            isValid = false;
            $('#post-image').after('<small class="text-danger error-message">Image is required.</small>');
        }

        if (!isValid) return;

        const formData = new FormData();
        formData.append('action', 'submit_competition_post');
        formData.append('post_title', title);
        formData.append('post_content', content);
        formData.append('competition_id', $('#competition-id').val());
        formData.append('post_id', $('#post-id').val());
        if (image) {
            formData.append('post_image', image);
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: formData,
            processData: false,
            contentType: false,
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
