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
<nav class="navbar navbar-expand-lg navbar-light header">
    <div class="container">
        <!-- <a class="navbar-brand text-white" href="<?php echo home_url(); ?>">Love Beat Novels</a> -->
        <a class="navbar-brand" href="<?php echo home_url(); ?>" style="margin-right: 6rem;">
    <img src="<?php echo get_theme_mod('custom_logo') ? wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') : get_template_directory_uri().'/assets/img/default-logo.png'; ?>" 
         alt="<?php bloginfo('name'); ?>" 
         height="80">
</a>

<!-- Search Form Start (Next to Logo) -->
<form class="d-flex align-items-center search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="position-relative search-container">
        <input type="text" name="s" class="form-control search-input" placeholder="தேடு..." value="<?php echo get_search_query(); ?>">
        <i class="fas fa-search search-icon"></i>
    </div>
</form>


<!-- Search Form End -->

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php wp_nav_menu(array(
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'navbar-nav ms-auto',
                'depth' => 2,
                'walker' => new WP_Bootstrap_Navwalker()
            )); ?>
            <?php if (is_user_logged_in()) { ?>
                <a href="<?php echo wp_logout_url(home_url()); ?>" class="text-white text-decoration-none" style="padding-left: 0.5rem;padding-right: 0.5rem;">
                    <span itemprop="name">
                        <div class="menu-icon text-center">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </div>
                        <span class="menu-text">வெளியேறு</span>
                    </span>
                </a>
            <?php } else { ?>
                <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="text-white text-decoration-none" style="padding-left: 0.5rem;padding-right: 0.5rem;">
                    <span itemprop="name">
                        <div class="menu-icon text-center">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </div>
                        <span class="menu-text">உள்நுழைக</span>
                    </span>
                </a>
            <?php } ?>



        </div>

        <!-- Bootstrap Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="login-message" class="mt-3"></div>
                        <form id="login-form">
                            <div class="row mb-3 align-items-center">
                                <label for="username" class="col-sm-3 col-form-label">Username <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label for="password" class="col-sm-3 col-form-label">Password <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="frontend_login" value="1">
                            <div class="row mb-3 align-items-center">
                                <label for="login" class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary d-flex align-items-center w-25">
                                        <i class="fas fa-sign-in-alt me-2"></i> Login
                                    </button>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label for="login" class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-4">
                                    <a href="<?php echo wp_lostpassword_url(); ?>">Lost Password?</a>
                                </div>
                                <div class="col-sm-5">
                                    <!-- <a href="<?php echo wp_lostpassword_url(); ?>">Register?</a> -->
                                    <a href="#" class="text-primary-color text-decoration-none" data-bs-toggle="modal" data-bs-target="#registerModal">Register?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Register</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 px-5">
                        <form id="registerForm">
                            <div class="row mb-3 align-items-center">
                                <label for="username" class="col-sm-3 col-form-label">Username <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label for="email" class="col-sm-3 col-form-label">Email <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fa-solid fa-envelope"></i>
                                        </span>
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Email">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label for="password" class="col-sm-3 col-form-label">Password <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label for="firstname" class="col-sm-3 col-form-label">First Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label for="lastname" class="col-sm-3 col-form-label">Last Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name">
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="action" value="register_user">
                            <div class="row mb-3 align-items-center">
                                <label for="lastname" class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary w-25">
                                        <i class="fa-solid fa-floppy-disk me-2"></i> Register
                                    </button>
                                    <button type="button" data-bs-dismiss="modal" class="btn btn-secondary w-25">
                                    <i class="fa-solid fa-xmark me-2"></i> Close
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div id="registerMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</nav>


