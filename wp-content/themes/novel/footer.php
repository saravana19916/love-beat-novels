</div>
<footer class="text-white footer">
    <div class="container">
        <div class="row py-4">
            <div class="col-12 col-md-6 col-lg-3 d-flex align-items-center justify-content-center justify-content-md-start">
                <a class="navbar-brand" href="<?php echo home_url(); ?>">
                    <img src="<?php echo get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : get_template_directory_uri().'/assets/img/default-logo.png'; ?>" 
                        alt="<?php bloginfo('name'); ?>" 
                        height="80">
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-3 m-3 m-md-0">
                <h6 class="fw-bold footer-border">
                    <p>Quick Links</p>
                </h6>
                <nav class="footer-nav mt-4 d-flex justify-content-center justify-content-md-start">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'container' => false,
                        'menu_class' => 'footer-menu p-0',
                    ));
                    ?>
                </nav>
            </div>

            <div class="col-12 col-md-6 col-lg-3 m-3 my-md-3 mx-md-0 m-lg-0">
                <h6 class="fw-bold footer-border">தொடர்புக்கு</h6>
                <p class="mt-4 text-center text-md-start"><i class="fa-solid fa-envelope"></i> &nbsp; contact@lovebeatnovels.com</p>
            </div>

            <div class="col-12 col-md-6 col-lg-3 m-3 my-md-3 mx-md-0 m-lg-0">
                <h6 class="fw-bold footer-border">சமூக வலைதளங்களில் தொடர</h6>
                <div class="d-flex align-items-center justify-content-center justify-content-md-start mt-4">
                    <span>
                        <a target="_blank" href="https://www.youtube.com/@SarmiCreations" class="nav-link">
                            <img src="<?php echo get_template_directory_uri() . '/images/youtube.png'; ?>" alt="youtube" class="img-fluid rounded" style="width: 55px; height: 55px;">
                        </a>
                    </span>
                    <span class="mr-3">
                        <a target="_blank" href="https://www.facebook.com/Sarmi.SSfan" class="nav-link">
                            <img src="<?php echo get_template_directory_uri() . '/images/facebook.png'; ?>" alt="facebook" class="img-fluid rounded" style="width: 50px; height: 50px; margin-left: 6px;">
                        </a>
                    </span>
                    <span class="ml-3">
                        <a target="_blank" href="https://www.instagram.com/sarmi_ss/" class="nav-link">
                            <img src="<?php echo get_template_directory_uri() . '/images/instagram.png'; ?>" alt="instagram" class="img-fluid rounded" style="width: 50px; height: 50px; margin-left: 10px">
                        </a>
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- Copyright Section -->
    <div class="text-center footer" style="border-top: solid 1px;">
        <p class="mb-0 py-2">© <?php echo date("Y"); ?> Love Beat Novels. All Rights Reserved.</p>
    </div>
</footer>


<?php wp_footer(); ?>
</body>
</html>

<script>
    // For not allow screenshot and copy the text start
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });

    // document.addEventListener('selectstart', function (e) {
    //     e.preventDefault();
    // });

    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && (e.key === 'c' || e.key === 'u' || e.key === 'p')) {
            e.preventDefault();
        }
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "PrintScreen") {
            document.body.style.filter = "blur(10px)";
            setTimeout(() => {
                document.body.style.filter = "none";
            }, 1000);
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
    const searchIcon = document.querySelector('.menu-search .search-icon');
    const searchBox = document.querySelector('.menu-search .search-box');

    if (!searchIcon || !searchBox) {
        console.warn("Search elements not found!");
        return;
    }

    searchIcon.addEventListener('click', function (e) {
        e.preventDefault();
        searchBox.classList.toggle('d-none');
    });

    document.addEventListener('click', function (e) {
        if (!searchBox.contains(e.target) && !searchIcon.contains(e.target)) {
            searchBox.classList.add('d-none');
        }
    });
});

</script>

