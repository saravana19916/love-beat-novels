<?php get_header(); ?>

<div class="container my-5">
    <?php
        $competition_closed = get_post_meta(get_the_ID(), '_competition_closed', true); 
        if ($competition_closed == '1') {
    ?>
        <div class="alert alert-warning text-center mx-auto competition-alert mb-5">
            This competition is currently closed for submissions.
        </div>
    <?php } ?>

    <div>
        <?php if (have_posts()) :
            while (have_posts()) : the_post(); ?>

                <!-- List Existing Posts -->
                 <h4 class="text-primary-color fw-bold text-center"><?php the_title(); ?></h4>
                <div class="card border border-2 border-primary rounded">
                    <div class="card-body p-0">
                        <div class="card-text mt-3 px-3 py-2" style="max-height: 600px; overflow-y: auto;">
                            <?php
                                $content = get_post_meta(get_the_ID(), '_rules', true);
                                echo wpautop(wp_strip_all_tags($content));
                            ?>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="competition-id" value="<?php echo get_the_ID(); ?>">
                <div class="row mt-5">
                    <div class="col-md-6">
                        <h5 class="text-primary-color fw-bold">
                            Related stories
                        </h>
                    </div>
                    <div class="col-md-6 text-end">
                        <?php if (is_user_logged_in()) { ?>
                            <?php
                                $submit_story_url = get_permalink(get_page_by_path('submit-story')) . '?competition_id=' . get_the_ID();
                                if ($competition_closed != '1') {
                            ?>
                                <button class="btn primary-btn btn-sm" onclick="window.location.href='<?php echo esc_url($submit_story_url); ?>'">
                                    <i class="fa-solid fa-plus fa-lg"></i>&nbsp; Create Story
                                </button>
                            <?php } ?>
                        <?php } else { ?>
                            <button class="btn primary-btn btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">Login to create stories</button>
                        <?php } ?>
                    </div>
                </div>

                <?php
// Get competition ID
$competition_id = get_the_ID();

// Get default category ID stored in meta
$default_category_id = get_post_meta($competition_id, '_default_category', true);

// Only show related stories if category exists
if ($default_category_id) :
    // Query posts in that category

    $related_args = array(
        'post_type'      => 'post',
        'cat'            => $default_category_id,
        'meta_query'     => array(
            array(
                'key'   => 'competition_id',
                'value' => $competition_id,
                'compare' => '='
            ),
        ),
        'post_status'    => 'publish',
        'posts_per_page' => 6,
    );

    $related_query = new WP_Query($related_args);

    if ($related_query->have_posts()) :
?>
        <div class="related-stories mt-4">
            <div class="row">
                <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border border-secondary rounded shadow-sm">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="card-img-top overflow-hidden" style="max-height:180px;">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', ['class' => 'img-fluid w-100']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title text-primary fw-bold">
                                    <a href="<?php the_permalink(); ?>" class="text-decoration-none text-primary">
                                        <?php the_title(); ?>
                                    </a>
                                </h6>
                                <p class="card-text small text-muted mb-2">
                                    <?php echo wp_trim_words(get_the_content(), 20, '...'); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent text-end">
                                <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
<?php
    endif;
    wp_reset_postdata();
endif;
?>


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

