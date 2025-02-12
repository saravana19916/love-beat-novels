<?php
/*
Template Name: Contact Page
*/
get_header();
?>

<!-- Your Page Content -->
<div class="container my-4">
    <img src="<?php echo get_template_directory_uri() . '/images/connect-with-us.jpg'; ?>" alt="Contact Us" class="img-fluid rounded">

    <div class="mt-5">
        <h2 class="text-center">Need to get in touch with us?</h2>
        <div class="row g-3 align-items-center justify-content-center mt-3">
            <div class="col-md-3">
                <div class="border border-2 border-dashed-contact d-flex align-items-center rounded justify-content-center">
                    <i class="fa-solid fa-envelope fa-3x me-3"></i>
                    <span>lovebeatnovels@gmail.com</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border border-2 border-dashed-contact d-flex align-items-center rounded justify-content-center">
                    <i class="fa-solid fa-phone fa-3x me-3"></i>
                    <span>9374859205</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-center shadow my-5 mb-5">
        <!-- Left Side Image -->
        <div class="col-md-6">
            <img src="<?php echo get_template_directory_uri() . '/images/contact.png'; ?>" alt="Contact Us" class="img-fluid rounded">
        </div>

        <!-- Right Side Form -->
        <div class="col-md-4">
        <form id="contactForm" class="p-4 rounded" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
    <div class="mb-3">
        <input type="text" class="form-control height" id="name" name="name" placeholder="Name" required>
    </div>
    <div class="mb-3">
        <input type="email" class="form-control height" id="email" name="email" placeholder="Email" required>
    </div>
    <div class="mb-3">
        <input type="text" class="form-control height" id="subject" name="subject" placeholder="Subject" required>
    </div>
    <div class="mb-3">
        <textarea class="form-control" id="message" name="message" rows="4" placeholder="Message" required></textarea>
    </div>
    <input type="hidden" name="action" value="submit_contact_form">
    <button type="submit" class="btn btn-primary w-25 primary">Submit</button>
</form>

        </div>
    </div>

</div>

<?php
get_footer();
?>
