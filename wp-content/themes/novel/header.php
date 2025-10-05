<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?> <!-- WordPress hook for adding scripts/styles -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Tamil&display=swap" rel="stylesheet">

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-mode');
                document.querySelectorAll('.moon').forEach(m => m.classList.add('d-none'));
                document.querySelectorAll('.sun').forEach(s => s.classList.remove('d-none'));
            }
        })();
    </script>
</head>
<body <?php body_class(); ?> class="height: 100%">
<div class="wrapper" style="min-height: 90vh; /* Full viewport height */
    display: flex;
    flex-direction: column;">

<!-- Navbar using Bootstrap -->
<nav class="navbar navbar-expand-xl navbar-light header">
    <div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap header-logo-responsive">
        <a class="navbar-brand me-sm-5 me-0" href="<?php echo home_url(); ?>">
            <img src="<?php echo get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : get_template_directory_uri().'/assets/img/default-logo.png'; ?>" 
                alt="<?php bloginfo('name'); ?>" 
                height="80">
        </a>

        <!-- Search Form (Visible next to logo on lg and above) -->
        <form class="d-none d-sm-flex align-items-center search-form flex-grow-1 mx-3" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="position-relative search-container w-100">
                <input type="text" name="s" class="form-control search-input" placeholder="தேடு..." value="<?php echo get_search_query(); ?>">
                <i class="fas fa-search search-icon"></i>
            </div>
        </form>

        <button class="navbar-toggler bg-white m-3 my-md-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>

    <div class="col-12 col-xl-auto mt-3 mt-xl-0 d-flex justify-content-between align-items-center">

        <div class="d-none d-sm-flex align-items-center mt-2">
            <span class="theme-toggle moon text-white d-flex flex-column align-items-center me-3" style="cursor:pointer;">
                <i class="fa-solid fa-moon fa-xl"></i>
                <span class="menu-text mt-2 pt-1">Dark Mode</span>
            </span>
            <span class="theme-toggle sun text-white d-flex flex-column align-items-center me-3 d-none" style="cursor:pointer;">
                <i class="fa-solid fa-sun fa-xl"></i>
                <span class="menu-text mt-2 pt-1">Light Mode</span>
            </span>
        </div>

        <div class="d-none d-sm-flex align-items-center">
            <div class="notification-wrapper me-3">
                <?php get_template_part('template-parts/header-notification', null, ['view' => 'desktop']); ?>
            </div>

            <div class="position-relative">
                <button id="userToggleDesktop" class="btn btn-link text-white p-0">
                    <i class="fas fa-user fa-lg"></i>
                </button>
                <div id="userDropdownDesktop" class="dropdown-menu dropdown-menu-end p-2 shadow border-0 fs-13px"
                    style="min-width: 200px; display: none; position: absolute; top: 100%; right: 0; z-index: 1000;">
                    <?php if (is_user_logged_in()) : $current_user = wp_get_current_user(); ?>
                        <span class="dropdown-item text-center">Welcome <?php echo esc_html( $current_user->user_login ); ?> </span>
                        <a href="<?php echo site_url('/profile'); ?>" class="dropdown-item text-center">My Profile</a>
                        <a href="<?php echo wp_logout_url(site_url('/')); ?>" class="dropdown-item text-center">Logout</a>
                    <?php else : ?>
                        <a data-bs-toggle="modal" data-bs-target="#loginModal" class="dropdown-item text-center">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>


    <!-- Second Row: Search Form (Visible below logo on mobile) -->
    <div class="row mt-2 d-sm-none w-100">
       <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                
                <!-- Dark/Light Toggle (Left) -->
                <div class="d-flex align-items-center">
                    <span class="theme-toggle moon text-white me-3" style="cursor:pointer;">
                        <i class="fa-solid fa-moon fa-xl"></i>
                        <span class="ms-2">Dark Mode</span>
                    </span>

                    <span class="theme-toggle sun text-white me-3 d-none" style="cursor:pointer;">
                        <i class="fa-solid fa-sun fa-xl"></i>
                        <span class="ms-2">Light Mode</span>
                    </span>
                </div>

                <!-- Notification (Right) -->
                <div class="d-flex align-items-center">
                    <div class="notification-wrapper me-3">
                        <?php get_template_part('template-parts/header-notification', null, ['view' => 'mobile']); ?>
                    </div>

                    <div class="position-relative">
                        <button id="userToggleMobile" class="btn btn-link text-white p-0">
                            <i class="fas fa-user fa-lg"></i>
                        </button>
                        <div id="userDropdownMobile" class="dropdown-menu dropdown-menu-end p-2 shadow border-0 fs-13px"
                            style="min-width: 200px; display: none; position: absolute; top: 100%; right: 0; z-index: 1000;">
                            <?php if (is_user_logged_in()) : $current_user = wp_get_current_user(); ?>
                                <span class="dropdown-item text-center">Welcome <?php echo esc_html( $current_user->user_login ); ?> </span>
                                <a href="<?php echo site_url('/profile'); ?>" class="dropdown-item text-center">My Profile</a>
                                <a href="<?php echo wp_logout_url(site_url('/')); ?>" class="dropdown-item text-center">Logout</a>
                            <?php else : ?>
                                <a data-bs-toggle="modal" data-bs-target="#loginModal" class="dropdown-item text-center">Login</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-12 mt-3">
            <form class="d-flex align-items-center search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="position-relative search-container w-100">
                    <input type="text" name="s" class="form-control search-input" placeholder="தேடு..." value="<?php echo get_search_query(); ?>">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </form>
        </div>
    </div>

        <div class="collapse navbar-collapse header-coll" id="navbarNav">
            <?php wp_nav_menu(array(
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'navbar-nav ms-auto',
                'depth' => 2,
                'walker' => new WP_Bootstrap_Navwalker()
            )); ?>
        </div>

        <!-- Bootstrap Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content shadow-div">
                    <div class="modal-header bg-primary-color">
                        <h5 class="modal-title text-white" id="loginModalLabel">Login &nbsp; <i class="fa-solid fa-user"></i></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 d-flex align-items-center">
                                <img src="<?php echo get_template_directory_uri() . '/images/login.png'; ?>" alt="Registration" class="img-fluid rounded">
                            </div>
                            <div class="col-md-6 p-4 d-flex align-items-center justify-content-center">
                                <form id="login-form" class="w-100">
                                    <div class="row mb-3 text-center">
                                        <i class="fa-solid fa-circle-user text-primary-color" style="font-size: 60px;"></i>
                                    </div>

                                    <div id="login-message" class="mt-3"></div>

                                    <div class="row mb-3 align-items-center">
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Username *">
                                        </div>
                                    </div>
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-12">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password *">
                                        </div>
                                    </div>
                                    <input type="hidden" name="frontend_login" value="1">
                                    <div class="row mb-4">
                                        <div class="col-6">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-sign-in-alt me-2"></i> Login
                                            </button>
                                        </div>
                                        <div class="col-6 d-flex align-items-center justify-content-end">
                                            <a href="#" class="text-primary-color text-decoration-none" data-bs-toggle="modal" data-bs-target="#registerModal">Register?</a>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <a class="text-primary-color text-decoration-none" href="<?php echo wp_lostpassword_url(); ?>">Forget Password?</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content shadow-div">
                    <div class="modal-header bg-primary-color">
                        <h5 class="modal-title text-white" id="regHeader">Register  &nbsp; <i class="fa-solid fa-user-plus"></i></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 px-5">
                        <div class="row">
                            <div class="col-md-6 d-flex align-items-center">
                                <img src="<?php echo get_template_directory_uri() . '/images/register.png'; ?>" alt="Registration" class="img-fluid rounded">
                            </div>
                            <div class="col-md-6">
                                <form id="registerForm">
                                    <input type="hidden" name="action" value="register_user" id="formAction">
                                    <input type="hidden" name="user_id" id="user_id">
                                    <div class="row mb-3 text-center">
                                        <i class="fa-solid fa-circle-user text-primary-color" style="font-size: 60px;"></i>
                                    </div>

                                    <div id="registerMessage" class="mt-3"></div>

                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="regUsername" name="username" placeholder="Username *">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="email" name="email" placeholder="Email *">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password *">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name *">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name *">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <textarea class="form-control" id="about_user" name="about_user" placeholder="About"></textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                        </div>
                                    </div>

                                    <input type="hidden" name="action" value="register_user">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa-solid fa-floppy-disk me-2"></i> <span id="regSubmit">Register</span>
                                            </button>
                                            <button type="button" data-bs-dismiss="modal" class="btn btn-secondary ms-3">
                                            <i class="fa-solid fa-xmark me-2"></i> Close
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mb-3" id="loginLink">
                                        <a href="#" class="text-primary-color text-decoration-none" data-bs-toggle="modal" data-bs-target="#loginModal">Already have an account?</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
