<?php
/* Template Name: Paginated Blogs by Category */

get_header(); // Include the header
?>

<div class="container my-4">

    <div class="col-md-12 mt-4">
        <div class="row">
            <div class="col-md-10 px-4">
                <?php
                    $categories = get_categories();

                    foreach ($categories as $category) :
                        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                ?>
                    <div class="row mb-5 shadow rounded">
                        <div class="col-md-12 p-0">
                            <h2 class="text-primary px-4 py-1 text-white" style="background-color: #061148"><?php echo esc_html($category->name); ?></h2>
                            <div class="row px-4">
                                <?php
                                // Query posts for the current category
                                $query = new WP_Query(array(
                                    'cat' => $category->term_id,
                                    'posts_per_page' => 4, // Limit the number of posts per page
                                    'paged' => $paged,
                                ));

                                if ($query->have_posts()) :
                                    while ($query->have_posts()) : $query->the_post();
                                ?>
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
                                <?php
                                    endwhile;
                                ?>
                            </div>
                            <!-- Pagination -->
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <?php
                                    echo paginate_links(array(
                                        'base' => add_query_arg('paged', '%#%'),
                                        'format' => '?paged=%#%',
                                        'total' => $query->max_num_pages,
                                        'current' => $paged,
                                        'prev_text' => '&laquo; Previous',
                                        'next_text' => 'Next &raquo;',
                                    ));
                                    ?>
                                </ul>
                            </nav>
                            <?php
                                wp_reset_postdata();
                                else :
                            ?>
                                <p>No posts found in this category.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="col-md-2 px-4">
                <div class="row mb-5 shadow rounded sticky-top" style="height: 25rem; top: 20px; overflow-y: auto">
                    <div class="col-md-12 p-0 text-center" id="latestPosts">
                        <h5 class="text-primary px-4 py-2 text-white" style="background-color: #061148"><?php echo "Latest posts"; ?></h5>
                        <?php
                            $latest_post_query = new WP_Query([
                                'posts_per_page' => 10
                            ]);

                            if ($latest_post_query->have_posts()) :
                                while ($latest_post_query->have_posts()) : $latest_post_query->the_post();
                            ?>
                                <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                            <?php
                                endwhile;
                                wp_reset_postdata();
                            else :
                                echo '<p>No latest post found.</p>';
                            endif;
                            ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); // Include the footer ?>
