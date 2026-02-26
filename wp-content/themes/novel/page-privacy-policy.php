<?php
get_header();
?>

<div class="container details-page my-5 text-primary-color">
  <h3 class="fw-bold text-center">Privacy Policy</h3>
  <div class="shadow-lg rounded mt-3 p-4 shadow-div">
    <div class="text-primary-color fs-14px" style="line-height: 1.7rem;">
      <?php 
          while ( have_posts() ) : the_post();
              the_content();
          endwhile;
      ?>
    </div>
  </div>
</div>

<?php
get_footer();
?>