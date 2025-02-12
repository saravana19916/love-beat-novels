<?php
/* Template Name: Paginated Blogs by Category */

get_header(); // Include the header
?>

<div class="container my-5">

<?php
$args = array(
    'post_type'      => 'competition',
    'posts_per_page' => 10, // Adjust as needed
);
$query = new WP_Query($args);

if ($query->have_posts()) : ?>
    <div class="container">
        <div class="row">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="col-md-4">
                    <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                        <div class="shadow p-2 mb-4 card-hover">
                            <div class="card-header py-2">
                                <h3 class="mb-0 text-primary-color"><?php the_title(); ?></h3>
                            </div>
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php the_post_thumbnail_url('medium'); ?>" class="competition-img" alt="<?php the_title(); ?>">
                            <?php else : ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/default-image.jpg" class="card-img-top" alt="Default Image">
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php
wp_reset_postdata();
else :
    echo '<p>No competitions found.</p>';
endif;
?>

</div>

<?php get_footer(); // Include the footer ?>
