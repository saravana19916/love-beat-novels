<?php get_header(); ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12" style="padding-left: 15rem; padding-right: 15rem;">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <h3 class="fw-bold text-center my-4 text-primary-color"><?php the_title(); ?></h3>
                <div class="shadow-lg rounded mt-4 p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Author:</strong> <?php the_author(); ?></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Posted on:</strong> <?php the_date(); ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-5 mt-4">
                        <?php echo wpautop(get_the_content()); ?>
                    </div>

                    <div class="navigation mt-4">
                        <div class="d-flex justify-content-between">
                            <div class="prev-episode">
                                <?php 
                                $prev_post = get_previous_post();
                                if ($prev_post): ?>
                                    <a href="<?php echo get_permalink($prev_post->ID); ?>" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-eye"></i>&nbsp முந்திய பாகத்தை படிக்க
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="next-episode">
                                <?php 
                                $next_post = get_next_post();
                                if ($next_post): ?>
                                    <a href="<?php echo get_permalink($next_post->ID); ?>" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-eye"></i>&nbsp அடுத்த பாகத்தை படிக்க
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php
                        if (comments_open() || get_comments_number()) {
                            comments_template();
                        }
                    ?>
                </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
