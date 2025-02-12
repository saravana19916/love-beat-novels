<!-- <div class="border-top border-3 pb-3" style="border-color: #061148 !important;"></div> -->
</div>
<footer class="text-white py-3 footer">
<div class="container">
    <div class="row py-3 d-flex align-items-center">
        <div class="col-md-4">
            <!-- <h3 class="pl-2rem">Love Beat Novels</h3> -->
            <a class="navbar-brand" href="<?php echo home_url(); ?>">
                <img src="<?php echo get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : get_template_directory_uri().'/assets/img/default-logo.png'; ?>" 
                    alt="<?php bloginfo('name'); ?>" 
                    height="80">
            </a>
        </div>
        <div class="col-md-4">
            <h4 class="pl-2rem">Quick Links</h4>
            <nav class="footer-nav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'container' => false,
                    'menu_class' => 'footer-menu',
                ));
                ?>
            </nav>
        </div>

        <div class="col-md-4">
            <h4 class="pl-2rem">Stay Connected</h4>
            <div class="d-flex align-items-center pl-2rem">
                <span class="me-3">
                    <a target="_blank" href="https://www.youtube.com/@SarmiCreations" class="nav-link">
                        <i class="fa-brands fa-youtube fa-2x"></i>
                    </a>
                </span>
                <span class="me-3">
                    <a target="_blank" href="https://www.facebook.com/Sarmi.SSfan" class="nav-link">
                        <i class="fa-brands fa-facebook fa-2x"></i>
                    </a>
                </span>
                <span>
                    <a target="_blank" href="https://www.instagram.com/sarmi_ss/" class="nav-link">
                        <i class="fa-brands fa-instagram fa-2x"></i>
                    </a>
                </span>
            </div>
        </div>

    </div>
</div>
</footer>

<!-- <footer class="text-white text-center py-3 footer">
    <div class="container">
        <nav class="footer-nav">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer',
                'container' => false,
                'menu_class' => 'footer-menu',
            ));
            ?>
        </nav>
        <div class="footer-copyright">
            <p>© <?php echo date('Y'); ?> Your Site Name. All rights reserved.</p>
        </div>
    </div>
</footer> -->


<?php wp_footer(); ?>
</body>
</html>

<script>
    // For not allow screenshot and copy the text start
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });

    document.addEventListener('selectstart', function (e) {
        e.preventDefault();
    });

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
        }, 1000); // Clear blur after 1 second
    }
});
    // For not allow screenshot and copy the text end

    // For post search start
    document.addEventListener('DOMContentLoaded', function () {
        const searchIcon = document.querySelector('.menu-search .search-icon');
        const searchBox = document.querySelector('.menu-search .search-box');

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
    // For post search end

</script>

