<?php get_header(); ?>

<div class="blog-post container py-5 mb-5">
    <div class="row">
        <div class="col-md-10 px-4">
            <div class="shadow rounded p-5">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                    <!-- Post Title -->
                    <h2 class="post-title text-center text-decoration-underline heading mb-3"><?php the_title(); ?></h2>

                    <!-- Post Featured Image -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-image text-center mb-3">
                            <?php the_post_thumbnail('medium', ['class' => 'img-fluid']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Post Content -->
                    <div class="post-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Previous and Next Post Links -->
                    <!-- <div class="post-navigation">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="nav-previous">
                                    <?php previous_post_link('%link', '&laquo; Previous Post: %title'); ?>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="nav-next">
                                    <?php next_post_link('%link', 'Next Post: %title &raquo;'); ?>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <?php
                        if (comments_open() || get_comments_number()) {
                            comments_template();
                        }
                    ?>



                <?php endwhile; else : ?>
                    <p>No post found!</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-2 px-4">
            <div class="row mb-5 shadow rounded sticky-top" style="height: 25rem; top: 20px;">
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

<?php get_footer(); ?>
