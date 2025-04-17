<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?> <!-- WordPress hook for adding scripts/styles -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Tamil&display=swap" rel="stylesheet">
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

    <!-- Second Row: Search Form (Visible below logo on mobile) -->
    <div class="row mt-2 d-sm-none w-100">
        <div class="col-12">
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
            <?php if (is_user_logged_in()) { ?>
                <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="text-white text-decoration-none" style="padding-left: 0.5rem;padding-right: 0.5rem;">
                    <span itemprop="name">
                        <div class="menu-icon text-center custom-menu-icon">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </div>
                        <div class="text-center custom-menu row col-12">
                            <span class="menu-text">வெளியேறு</span>
                        </div>
                    </span>
                </a>
            <?php } else { ?>
                <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="text-white text-decoration-none" style="padding-left: 0.5rem;padding-right: 0.5rem;">
                    <span itemprop="name">
                        <div class="menu-icon text-center custom-menu-icon">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </div>
                        <div class="text-center custom-menu row col-12">
                            <span class="menu-text">உள்நுழைக</span>
                        </div>
                    </span>
                </a>
            <?php } ?>
        </div>

        <!-- Bootstrap Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary-color">
                        <h5 class="modal-title text-white" id="loginModalLabel">Login &nbsp; <i class="fa-solid fa-user"></i></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 d-flex align-items-center">
                                <img src="<?php echo get_template_directory_uri() . '/images/login.png'; ?>" alt="Registration" class="img-fluid rounded">
                            </div>
                            <div class="col-md-6 p-4 d-flex align-items-center justify-content-center">
                                <form id="login-form">
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
                <div class="modal-content">
                    <div class="modal-header bg-primary-color">
                        <h5 class="modal-title text-white" id="registerModalLabel">Register  &nbsp; <i class="fa-solid fa-user-plus"></i></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 px-5">
                        <div class="row">
                            <div class="col-md-6 d-flex align-items-center">
                                <img src="<?php echo get_template_directory_uri() . '/images/register.png'; ?>" alt="Registration" class="img-fluid rounded">
                            </div>
                            <div class="col-md-6">
                                <form id="registerForm">
                                    <div class="row mb-3 text-center">
                                        <i class="fa-solid fa-circle-user text-primary-color" style="font-size: 60px;"></i>
                                    </div>

                                    <div id="registerMessage" class="mt-3"></div>

                                    <div class="row mb-3">
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Username *">
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

                                    <div class="row mb-4">
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name *">
                                        </div>
                                    </div>

                                    <input type="hidden" name="action" value="register_user">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa-solid fa-floppy-disk me-2"></i> Register
                                            </button>
                                            <button type="button" data-bs-dismiss="modal" class="btn btn-secondary ms-3">
                                            <i class="fa-solid fa-xmark me-2"></i> Close
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
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
