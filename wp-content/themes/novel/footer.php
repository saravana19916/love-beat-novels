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

        <!-- Episode Locked Modal -->
        <div class="modal fade" id="episodeLockModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-div">
                    <div class="modal-header bg-primary-color">
                        <h5 class="modal-title">Unlock Episode</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body fw-normal">
                        <p class="text-normal text-primary-color">
                            இந்த எபிசோடு லாக் <i class="fas fa-lock" style="color: red;"></i> &nbsp; செய்யப்பட்டிருக்கிறது
                        </p>

                        <p class="text-normal text-primary-color">
                            <strong><?php echo get_option('coins_to_unlock'); ?> காயின்ஸ்</strong>
                            செலுத்தி அல்லது சப்ஸ்க்ரைப் செய்து திறக்கலாம்.
                        </p>

                        <p class="text-primary-color">Your coins: 
                            <strong id="userCoins">
                                <?php
                                    if (is_user_logged_in()) {
                                        $coins = get_user_meta(get_current_user_id(), 'user_coin_balance', true);
                                        echo $coins !== '' ? $coins : 0;
                                    } else {
                                        echo 0;
                                    }
                                ?>
                            </strong>
                        </p>

                        <input type="hidden" id="unlockEpisodeId">
                        <input type="hidden" id="unlockParentId">
                        <input type="hidden" id="unlockEpisodeNumber">

                        <div class="d-flex flex-column flex-md-row justify-content-center gap-2 w-100">
                            <button class="btn btn-primary w-100 w-md-auto" id="unlockBtn">
                                Pay coins &nbsp;
                                <img src="<?php echo get_template_directory_uri() . '/images/coin.png'; ?>" alt="Coin" class="img-fluid rounded">
                            </button>

                            <a href="subscription" class="btn btn-primary text-decoration-none w-100 w-md-auto">
                                Subscribe now <i class="fa-solid fa-crown"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php $coin_prices = get_option('coin_pack_prices_setting', []); ?>

        <div class="modal fade" id="buyCoinsModal" tabindex="-1" aria-labelledby="buyCoinsModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content shadow-div">
                    <div class="modal-header bg-primary-color">
                        <h5 class="modal-title text-white">காயின்கள் வாங்க</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 px-5">
                        <?php
                            $coins = get_user_meta(get_current_user_id(), 'user_coin_balance', true);
                            $coins = $coins ?: 0;
                        ?>
                        <p class="text-primary-color p-4 shadow shadow-div">உங்கள் பேலன்ஸ்: &nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $coins; ?></strong> &nbsp;
                            <img src="<?php echo get_template_directory_uri() . '/images/coin.png'; ?>" alt="Coin" class="img-fluid rounded">
                        </p>
                        <?php if(!empty($coin_prices)): ?>
                            <label class="text-primary-color mb-3">காயினைத் தேர்வுசெய்யுங்கள்</label>
                            <?php foreach ($coin_prices as $coin_price):?>
                                <label class="d-flex align-items-center border rounded-3 p-2 mb-2 coin-radio-label">
                                <input type="radio" name="coin_pack_price" value="<?= $coin_price['price'] ?>" data-coin="<?= $coin_price['coin'] ?>" class="me-2">
                                <span class="flex-grow-1 text-primary-color">
                                    &nbsp;&nbsp;<img src="<?php echo get_template_directory_uri() . '/images/coin.png'; ?>" alt="Coin" class="img-fluid rounded">&nbsp;
                                    <?= $coin_price['coin'] ?> &nbsp;&nbsp;
                                    காய்ன்ஸ்
                                </span>
                                <span class="fw-bold" style="color: red;">₹<?= $coin_price['price'] ?></span>
                                </label>
                            <?php endforeach; ?>
                            <span class="mt-1 d-none" style="color: red;" id="coinRequired">தயவு செய்து ஒரு காயின் பேக் தேர்வு செய்யவும்</span>
                        <?php else: ?>
                            <p class="text-danger">⚠️ காயின் பேக் அமைப்புகள் இல்லை</p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="text-primary-color">
                                <strong id="showPrice"></strong>
                            </span>
                            <button class="btn btn-primary ms-3" onclick="paymentProcess()">காயின்கள் வாங்க</button>
                        </div>
                    </div>
                </div>
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
                <a href="terms-and-conditions" class="text-white text-decoration-none">
                    Terms & Conditions
                </a>
                &nbsp; | &nbsp;
                <a href="privacy-policy" class="text-white text-decoration-none">
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

    document.addEventListener("DOMContentLoaded", function() {
  // Duplicate content to make the scroll seamless
  const marquee = document.querySelector('.marquee-content');
  if (marquee) {
      marquee.innerHTML += marquee.innerHTML;
  }
});

// lock and unlock episode
jQuery(document).on('click', '.locked-episode', function () {
    let id = jQuery(this).data('episode-id');
    let parentId = jQuery(this).data('parent-id');
    let episodeNumber = jQuery(this).data('episode-number');
    jQuery("#unlockEpisodeId").val(id);
    jQuery("#unlockParentId").val(parentId);
    jQuery("#unlockEpisodeNumber").val(episodeNumber);
});

jQuery("#unlockBtn").click(function(){
    let episodeID = jQuery("#unlockEpisodeId").val();
    let parentId = jQuery("#unlockParentId").val();
    let episodeNumber = jQuery("#unlockEpisodeNumber").val();

    unlockEpisode(episodeID, parentId, episodeNumber);
});

function unlockEpisode(episodeID, parentId, episodeNumber) {
    var isUserLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false'; ?>;

    if (!isUserLoggedIn) {
        jQuery('#episodeLockModal').modal('hide');
        jQuery('#loginModal').modal('show');
        return;
    }

    let userCoins = <?php echo (int) get_user_meta(get_current_user_id(), 'user_coin_balance', true) ?: 0; ?>;

    if (userCoins === 0) {
        jQuery('#episodeLockModal').modal('hide');
        jQuery('#buyCoinsModal').modal('show');
        return;
    }

    jQuery.post(
        "<?php echo admin_url('admin-ajax.php'); ?>",
        {
            action: "verify_unlock_episodes",
            episode_id: episodeID,
            parent_id: parentId,
            episode_number: episodeNumber
        },
        function(response){
            if(response.success){
                let totalEpisodes = response.data.count;
                let totalCharge   = response.data.cost;

                let confirmMsg = `நீங்கள் ${totalEpisodes} எபிசோடுகளை திறக்க தேர்வு செய்துள்ளீர்கள்.\n` +
                                `இதற்கு ${totalCharge} நாணயங்கள் கட்டணமாக வசூலிக்கப்படும்.\n` +
                                `உறுதியாக தொடரலாமா?`;

                if (!confirm(confirmMsg)) {
                    return;
                }


                jQuery.post(
                    "<?php echo admin_url('admin-ajax.php'); ?>",
                    {
                        action: "unlock_episode",
                        episode_id: episodeID,
                        parent_id: parentId,
                        episode_number: episodeNumber
                    },
                    function(response){
                        if(response.success){
                            alert("Episode Unlocked!");
                            location.reload();
                        } else {
                            if (response.data && response.data.message && response.data.message == 'Not enough coins' ) {
                                alert("You don't have enough coins to unlock the episodes. Please buy more coins.");
                                jQuery('#episodeLockModal').modal('hide');
                                jQuery('#buyCoinsModal').modal('show');
                            } else {
                                alert(response.data.message);
                            }
                        }
                    }
                );
            } else {
                if (response.data && response.data.message && response.data.message == 'Not enough coins' ) {
                    alert("You don't have enough coins to unlock the episodes. Please buy more coins.");
                    jQuery('#episodeLockModal').modal('hide');
                    jQuery('#buyCoinsModal').modal('show');
                } else {
                    alert(response.data.message);
                }
            }
        }
    );
}

jQuery(document).on('click', 'input[name="coin_pack_price"]', function() {
    let price = jQuery('input[name="coin_pack_price"]:checked').val();
    jQuery('#showPrice').text('₹ ' + price);
});

function paymentProcess() {
    // Block Razorpay inside Instagram/Facebook in-app browser
    if (!canStartRazorpayPayment()) return;

    jQuery('#coinRequired').addClass('d-none');

    let selectedPack = document.querySelector('input[name="coin_pack_price"]:checked');
    if (!selectedPack) {
        jQuery('#coinRequired').removeClass('d-none');
        return;
    }

    let amount = selectedPack.value;
    let coins = selectedPack.getAttribute('data-coin');
    startPayment(amount, coins);
}

let failureLoggedOrders = new Set();

function startPayment(amount, coins) {
    let amountInPaisa = amount * 100;

    jQuery.post(ajaxurl, { action: "create_razorpay_order", coins: coins, amount: amountInPaisa, pay_for: 'coin' }, function(orderRes) {
        if (!orderRes.success) {
            alert("Order creation failed");
            return;
        }

        var options = {
            key: RazorpayConfig.key,
            amount: amountInPaisa,
            currency: "INR",
            name: "Coin Purchase",
            order_id: orderRes.data.order_id,
            handler: function (response) {
                alert("Coins Added Successfully!");
                location.reload();
            }
        };

        var rzp = new Razorpay(options);

        rzp.on('payment.failed', function () {
            alert("Coins Payment failed!");
        });

        rzp.open();

        // var options = {
        //     key: RazorpayConfig.key,
        //     amount: amountInPaisa,
        //     currency: "INR",
        //     name: "Coin Purchase",
        //     order_id: orderRes.data.order_id,
        //     handler: function (response) {
        //         console.log("Payment response:", response);
        //         jQuery.post(ajaxurl, {
        //             action: "verify_razorpay_payment",
        //             razorpay_payment_id: response.razorpay_payment_id,
        //             razorpay_order_id: response.razorpay_order_id,
        //             razorpay_signature: response.razorpay_signature,
        //             amount: amount,
        //             api: 'coin',
        //             coins: coins
        //         }, function(res) {

        //             if (res.success) {
        //                 alert("Coins Added Successfully!");
        //                 location.reload();
        //             } else {
        //                 alert("Payment verification failed!");
        //             }
        //         });
        //     }
        // };

        // let rzp = new Razorpay(options);

        // rzp.on('payment.failed', function(response) {
        //     let orderId = orderRes.data.order_id;

        //     if (failureLoggedOrders.has(orderId)) {
        //         console.warn("Failure already logged for this order:", orderId);
        //         return;
        //     }

        //     failureLoggedOrders.add(orderId);

        //     jQuery.post(ajaxurl, {
        //         action: 'log_razorpay_failure',
        //         razorpay_payment_id: response.error.metadata.payment_id,
        //         razorpay_order_id: response.error.metadata.order_id,
        //         amount: amount,
        //         api: 'coin'
        //     });

        //     alert("Payment failed!");
        // });

        // rzp.open();
    });
}

function isSocialInAppBrowser() {
    const ua = navigator.userAgent || '';
    return /Instagram|InstagramLite|FBAN|FBAV/i.test(ua);
}

// function isIOSDevice() {
//     return /iPhone|iPad|iPod/i.test(navigator.userAgent || '');
// }

function isAndroidDevice() {
    return /Android/i.test(navigator.userAgent || '');
}

function showPaymentBrowserNotice() {
    const currentUrl = window.location.href;
    const url = new URL(currentUrl);

    const chromeIntentUrl =
        'intent://' +
        url.host +
        url.pathname +
        url.search +
        url.hash +
        '#Intent;scheme=' +
        url.protocol.replace(':', '') +
        ';package=com.android.chrome;end';

    // if (isIOSDevice()) {
    //     alert('For payment, open this page in Safari.\nTap menu in Instagram/Facebook and choose "Open in Safari".');
    //     return;
    // }

    if (isAndroidDevice()) {
        // Try opening Chrome directly
        window.location.href = chromeIntentUrl;
        return;
    }

    alert('For payment, open this page in your default browser.');
}

function canStartRazorpayPayment() {
    // Allow normal browsers
    if (!isSocialInAppBrowser()) return true;

    // Only block Android in-app browsers (Instagram/Facebook)
    if (!isAndroidDevice()) return true;

    showPaymentBrowserNotice();
    return false;
}

</script>