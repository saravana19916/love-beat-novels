<?php get_header(); ?>

<div class="container mt-4">
    <!-- Search Form -->
    <!-- <form class="d-flex mb-4" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input class="form-control me-2" type="search" placeholder="Search posts..." aria-label="Search" name="s" value="<?php echo get_search_query(); ?>">
        <button class="btn btn-primary" type="submit">Search</button>
    </form> -->

    <?php
    // Check if a search query is present
    // if (get_search_query()) :
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        // Search query
        $search_query = new WP_Query(array(
            's' => get_search_query(),
            'posts_per_page' => 10, // Adjust number of search results per page
            'paged' => $paged,
        ));

        if ($search_query->have_posts()) :
    ?>
            <h2 class="mb-4">Search Results for: <?php echo get_search_query(); ?></h2>
            <div class="row">
                <?php while ($search_query->have_posts()) : $search_query->the_post(); ?>
                    <div class="col-md-4 p-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <!-- Title -->
                                <h5 class="card-title text-center fw-bold">
                                    <a href="<?php the_permalink(); ?>" class="text-decoration-none" style="color: #061148">
                                        <?php the_title(); ?>
                                    </a>
                                </h5>
                                <!-- Image -->
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', ['class' => 'img-fluid mx-auto d-block my-3']); ?>
                                    </a>
                                <?php endif; ?>
                                <!-- Description -->
                                <p class="card-text">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </p>
                            </div>
                            <div class="card-footer text-end">
                                <a href="<?php the_permalink(); ?>" class="btn btn-sm text-white" style="background-color: #061148">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '?paged=%#%',
                        'total' => $search_query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '&laquo; Previous',
                        'next_text' => 'Next &raquo;',
                    ));
                    ?>
                </ul>
            </nav>
        <?php
        else :
            echo '<p>No posts found for "' . get_search_query() . '".</p>';
        endif;

        wp_reset_postdata(); ?>
</div>

<?php get_footer(); ?>