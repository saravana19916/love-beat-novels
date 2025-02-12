<?php get_header(); ?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-12" style="padding-left: 15rem; padding-right: 15rem;">
            <h3 class="fw-bold text-center my-4 text-primary-color"><?php the_title(); ?></h3>
            
            <div class="shadow-lg rounded mt-4 p-4">
                <div class="mb-5">
                    <?php echo wpautop(get_the_content()); ?>
                </div>
                
                <!-- Next & Previous Episode Navigation -->
                <div class="navigation my-4">
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
        </div>
    </div>
</div>

<?php get_footer(); ?>
