</div>

<?php
$terms_page = get_page_by_path('terms-and-conditions');
?>

<?php if ($terms_page): ?>
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content text-primary-color shadow-div">
      <div class="modal-header bg-primary-color">
        <h5 class="modal-title" id="termsModalLabel"><?php echo esc_html($terms_page->post_title); ?></h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <?php echo apply_filters('the_content', $terms_page->post_content); ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php
$privacy_page = get_page_by_path('privacy-policy');
?>

<?php if ($privacy_page): ?>
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content text-primary-color shadow-div">
      <div class="modal-header bg-primary-color">
        <h5 class="modal-title" id="privacyModalLabel"><?php echo esc_html($privacy_page->post_title); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <?php echo apply_filters('the_content', $privacy_page->post_content); ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

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
                <?php
                    $social_links = get_option('custom_social_links', []);
                    if (!empty($social_links)):
                ?>
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start mt-4">
                        <?php foreach ($social_links as $link): ?>
                            <span class="mx-2">
                                <a target="_blank" href="<?= esc_url($link['url']) ?>" class="nav-link" title="<?= esc_attr($link['title']) ?>">
                                    <img src="<?= esc_url($link['image']) ?>" alt="<?= esc_attr($link['title']) ?>" class="img-fluid rounded" style="width: 50px; height: 50px;">
                                </a>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Copyright Section -->
    <div class="footer" style="border-top: solid 1px;">
        <!-- <p class="mb-0 py-2">© <?php echo date("Y"); ?> Love Beat Novels. All Rights Reserved.</p> -->
        <div class="container text-center">
            <p class="mb-0 my-2 pb-2">
                © <?php echo date("Y"); ?> Love Beat Novels. All Rights Reserved.
                &nbsp; | &nbsp;
                <a href="" class="text-white text-decoration-none" data-bs-toggle="modal" data-bs-target="#termsModal">
                    Terms & Conditions
                </a>
                &nbsp; | &nbsp;
                <a href="" class="text-white text-decoration-none" data-bs-toggle="modal" data-bs-target="#privacyModal">
                    Privacy Policy
                </a>
            </p>
        </div>
    </div>
</footer>


<?php wp_footer(); ?>
</body>
</html>

<script>
    document.addEventListener('contextmenu', function (e) {
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

    // Comment reply section start
    function toggleChildComments(commentID) {
        const childBox = document.getElementById('child-comments-' + commentID);
        if (childBox) childBox.classList.toggle('d-none');
    }

    document.addEventListener("DOMContentLoaded", function () {
        jQuery('.reply-form-text').emojioneArea({
            pickerPosition: "top",
            tonesStyle: "radio",
            autogrow: true
        });

        document.querySelectorAll("form.reply-form").forEach(function (form) {
            form.addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch('<?php echo site_url("/wp-comments-post.php"); ?>', {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    location.reload();
                })
                .catch(error => {
                    console.error("Error submitting comment:", error);
                });
            });
        });
    });
    // Comment reply section end

    document.addEventListener('DOMContentLoaded', function () {
        new Swiper('.swiper-container', {
            slidesPerView: 'auto',
            spaceBetween: 10,
            freeMode: true, 
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const showMoreButtons = document.querySelectorAll('.show-more-btn');

        showMoreButtons.forEach(showMoreBtn => {
            showMoreBtn.addEventListener('click', function () {
                const buttonText = this.textContent.trim();
                const targetClass = this.getAttribute('data-target');
                const elements = document.querySelectorAll(`.${targetClass}`);
                console.log(elements.length);

                // Show all items
                if (buttonText == 'Show More') {
                    elements.forEach(el => el.classList.remove('d-none'));
                } else {
                    elements.forEach(el => el.classList.add('d-none'));
                }

                this.textContent = buttonText == "Show Less" ? "Show More" : "Show Less";
            });
        });
    });

    document.getElementById('show-more-competitions')?.addEventListener('click', function () {
        const button = this;
        const currentText = button.innerText.trim().toLowerCase();

        const isShowMore = currentText === 'show more';

        document.querySelectorAll('.more-competitions').forEach(el =>
            el.classList.toggle('d-none', !isShowMore)
        );

        button.innerText = isShowMore ? 'Show Less' : 'Show More';
    });

    document.getElementById('trendingStories')?.addEventListener('click', function () {
        const button = this;
        const currentText = button.innerText.trim().toLowerCase();

        const isShowMore = currentText === 'show more';

        document.querySelectorAll('.more-trending-stories').forEach(el =>
            el.classList.toggle('d-none', !isShowMore)
        );

        button.innerText = isShowMore ? 'Show Less' : 'Show More';
    });

    // document.addEventListener('DOMContentLoaded', function () {
    //     const toggle = document.getElementById('theme-toggle');
    //     const body = document.body;
    //     const currentTheme = localStorage.getItem('theme');

    //     if (currentTheme === 'dark') {
    //         body.classList.add('dark-mode');
    //     }

    //     toggle.addEventListener('click', () => {
    //         body.classList.toggle('dark-mode');
    //         let theme = body.classList.contains('dark-mode') ? 'dark' : 'light';
    //         localStorage.setItem('theme', theme);
    //     });
    // });

    document.addEventListener('DOMContentLoaded', function () {
        const dropdown = document.getElementById('notificationDropdown');

        if (dropdown) {
            dropdown.addEventListener('click', () => {
                fetch('<?php echo admin_url("admin-ajax.php?action=mark_notifications_seen"); ?>');
            });
        }

        const mobileDropdown = document.getElementById('mobileNotificationDropdown');

        if (mobileDropdown) {
            mobileDropdown.addEventListener('click', () => {
                fetch('<?php echo admin_url("admin-ajax.php?action=mark_notifications_seen"); ?>');
            });
        }

        // user dropdown start
        function setupToggle(toggleId, dropdownId) {
            const toggleBtn = document.getElementById(toggleId);
            const dropdown = document.getElementById(dropdownId);

            if (toggleBtn && dropdown) {
                toggleBtn.addEventListener("click", function () {
                    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
                });

                // Close dropdown if clicked outside
                document.addEventListener("click", function (e) {
                    if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.style.display = "none";
                    }
                });
            }
        }

        setupToggle("userToggleDesktop", "userDropdownDesktop");
        setupToggle("userToggleMobile", "userDropdownMobile");
        // user dropdown end

        // dark and light mode start
        const moons = document.querySelectorAll('.moon');
        const suns = document.querySelectorAll('.sun');
        const element = document.documentElement;

        function applyTheme(theme) {
            if (theme === 'dark') {
                element.classList.add('dark-mode');
                moons.forEach(m => m.classList.add('d-none'));
                suns.forEach(s => s.classList.remove('d-none'));
            } else {
                element.classList.remove('dark-mode');
                moons.forEach(m => m.classList.remove('d-none'));
                suns.forEach(s => s.classList.add('d-none'));
            }
        }

        function toggleTheme() {
            const newTheme = element.classList.contains('dark-mode') ? 'light' : 'dark';
            localStorage.setItem('theme', newTheme);
            applyTheme(newTheme);
        }

        moons.forEach(m => m.addEventListener('click', toggleTheme));
        suns.forEach(s => s.addEventListener('click', toggleTheme));
        // dark and light mode end

    });

</script>

