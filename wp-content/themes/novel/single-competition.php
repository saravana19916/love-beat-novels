<?php get_header(); ?>

<div class="container my-5">
    <div class="mx-5">
        <?php if (have_posts()) :
            while (have_posts()) : the_post(); ?>

                <!-- List Existing Posts -->
                <input type="hidden" id="competition-id" value="<?php echo get_the_ID(); ?>">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="text-primary-color">
                            <?php the_title(); ?>
                        </h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <?php if (is_user_logged_in()) { ?>
                            <?php
                                $submit_story_url = get_permalink(get_page_by_path('submit-story')) . '?competition_id=' . get_the_ID();
                            ?>
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='<?php echo esc_url($submit_story_url); ?>'">
                                <i class="fa-solid fa-plus fa-lg"></i>&nbsp; Create Story
                            </button>
                        <?php } else { ?>
                            <button class="btn btn-primary text-sm" data-bs-toggle="modal" data-bs-target="#loginModal">இங்கே பதிவிட நீங்கள் உள்நுழைய வேண்டும்</button>
                        <?php } ?>
                    </div>
                </div>

                <table class="mt-4 table" style="border: 1px solid lightgray;">
                    <thead>
                    </thead>
                    <tbody id="competition-table-body">
                    </tbody>
                </table>

                <div id="competition-pagination">
                </div>

            <?php endwhile;
        endif; ?>
    </div>
</div>

<?php get_footer(); ?>

<script>
jQuery(document).ready(function($) {
    function loadCompetitionPosts(competition_id, page = 1) {
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                action: 'fetch_competition_posts',
                competition_id: competition_id,
                paged: page
            },
            beforeSend: function() {
                $('#competition-table-body').html('<tr><td colspan="2">Loading...</td></tr>');
            },
            success: function(response) {
                if (response.success) {
                    $('#competition-table-body').html(response.data.table_data);
                    $('#competition-pagination').html(response.data.pagination);
                }
            }
        });
    }

    let competition_id = $('#competition-id').val();
    loadCompetitionPosts(competition_id);

    $(document).on('click', '.pagination-link', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        loadCompetitionPosts(competition_id, page);
    });
});
</script>

