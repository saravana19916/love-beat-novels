<?php

add_action('admin_menu', function () {
    add_menu_page('Social Media Links', 'Social Links', 'manage_options', 'social-links', 'render_social_links_page');
});

function render_social_links_page() {
    $social_links = get_option('custom_social_links', []);

    // Handle form submission
    if (isset($_POST['social_links_form_submitted'])) {
        $titles = $_POST['social_title'] ?? [];
        $urls = $_POST['social_url'] ?? [];
        $images = $_POST['social_image'] ?? [];

        $new_links = [];
        for ($i = 0; $i < count($titles); $i++) {
            if (trim($titles[$i]) === '') continue;

            $new_links[] = [
                'title' => sanitize_text_field($titles[$i]),
                'url'   => esc_url_raw($urls[$i]),
                'image' => esc_url_raw($images[$i]),
            ];
        }

        update_option('custom_social_links', $new_links);
        echo '<div class="updated"><p>Saved successfully.</p></div>';
    }

    ?>
    <div class="wrap">
        <h1>Social Media Links</h1>
        <form method="post">
            <input type="hidden" name="social_links_form_submitted" value="1" />

            <table class="form-table" id="social-links-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>URL</th>
                        <th>Image</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($social_links as $index => $link): ?>
                        <tr>
                            <td><input type="text" name="social_title[]" value="<?= esc_attr($link['title']) ?>" class="regular-text" /></td>
                            <td><input type="text" name="social_url[]" value="<?= esc_url($link['url']) ?>" class="regular-text" /></td>
                            <td>
                                <input type="text" name="social_image[]" value="<?= esc_url($link['image']) ?>" class="regular-text social-image-field" />
                                <button class="button upload-image-btn">Upload</button>
                            </td>
                            <td><button class="button remove-row">Remove</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><button id="add-social-link" class="button">Add Social Link</button></p>
            <?php submit_button(); ?>
        </form>
    </div>

    <script>
        jQuery(document).ready(function($) {
            function mediaUploaderCallback(input) {
                var customUploader = wp.media({
                    title: 'Select Image',
                    button: { text: 'Use this image' },
                    multiple: false
                }).on('select', function () {
                    var attachment = customUploader.state().get('selection').first().toJSON();
                    input.val(attachment.url);
                }).open();
            }

            $('#add-social-link').on('click', function(e) {
                e.preventDefault();
                $('#social-links-table tbody').append(`
                    <tr>
                        <td><input type="text" name="social_title[]" value="" class="regular-text" /></td>
                        <td><input type="text" name="social_url[]" value="" class="regular-text" /></td>
                        <td>
                            <input type="text" name="social_image[]" value="" class="regular-text social-image-field" />
                            <button class="button upload-image-btn">Upload</button>
                        </td>
                        <td><button class="button remove-row">Remove</button></td>
                    </tr>
                `);
            });

            $(document).on('click', '.remove-row', function(e) {
                e.preventDefault();
                $(this).closest('tr').remove();
            });

            $(document).on('click', '.upload-image-btn', function(e) {
                e.preventDefault();
                mediaUploaderCallback($(this).siblings('.social-image-field'));
            });
        });
    </script>
    <?php
}
