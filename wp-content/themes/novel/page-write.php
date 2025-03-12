<?php
/* Template Name: Paginated Blogs by Category */

get_header(); // Include the header
?>

<div style="position: relative; display: inline-block; width: 100%;">
    <h5 style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
               color: white; padding: 5px 10px; border-radius: 5px; width: 70%; font-size: 14px; line-height: 25px;" class="banner-story text-center text-danger fw-bold">
        இங்கு அனைவரும் எழுதலாம். உங்கள் படைப்புகளை நீங்களே வெளியிட்டுக் கொள்ளலாம். இது எல்லா வகையான எழுத்திற்கும், எழுத்தாளர்களுக்குமான தளம்.
    </h5>
    <img src="<?php echo get_template_directory_uri() . '/images/write.jpg'; ?>" alt="Contact Us" class="img-fluid rounded" style="width: 100%; height: 200px;">
</div>

<div class="container my-5">

<?php
$args = array(
    'post_type'      => 'competition',
    'posts_per_page' => 10,
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
?>
    <div class="row justify-content-center">
        <div class="col-md-6 text-center mt-5">
            <h4 class="text-primary-color">No competitions found.</h4>
        </div>
    </div>
<?php
endif;
?>

</div>

<?php get_footer(); // Include the footer ?>
