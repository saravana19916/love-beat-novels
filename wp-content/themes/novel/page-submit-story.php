<?php
/* Template Name: Submit Story */
get_header();
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12" style="padding-left: 15rem; padding-right: 15rem;">
            <?php 
            if (isset($_GET['competition_id']) && is_numeric($_GET['competition_id'])) {
                $competition_id = intval($_GET['competition_id']);
                $competition_title = get_the_title($competition_id);
            } else {
                $competition_id = 0;
                $competition_title = 'Unknown Competition';
            }
            ?>

            <h3 class="text-primary-color">Create Story for: <?php echo esc_html($competition_title); ?></h3>

            <div class="shadow-lg rounded mt-5 p-4">
                <?php if (is_user_logged_in()) : ?>
                    <form id="competition-post-form" class="p-4">
                        <input type="hidden" id="competition-id" value="<?php echo $competition_id; ?>">
                        <div class="row mb-3 align-items-center">
                            <label for="title" class="col-sm-2 col-form-label">Title <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="post-title" placeholder="Post Title" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label for="title" class="col-sm-2 col-form-label">Story <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <textarea id="post-content" class="form-control" placeholder="Your Story" rows="10" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-9 offset-sm-2">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-floppy-disk"></i>&nbsp;&nbsp; Submit</button>
                                <a href="<?php echo esc_url(get_permalink($competition_id)); ?>" class="btn btn-secondary btn-sm"> <i class="fa-solid fa-arrow-left"></i>&nbsp;&nbsp; Back</a>
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
    $('#competition-post-form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                action: 'submit_competition_post',
                post_title: $('#post-title').val(),
                post_content: $('#post-content').val(),
                competition_id: $('#competition-id').val(),
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
