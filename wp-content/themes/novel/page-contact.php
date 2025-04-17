<?php
/*
Template Name: Contact Page
*/
get_header();
?>

<!-- Your Page Content -->
<img src="<?php echo get_template_directory_uri() . '/images/contact.jpg'; ?>" alt="Contact Us" class="w-100 custom-img-height">

<div class="container my-4">

    <div class="my-5 mx-2 m-lg-5">
        <h4 class="text-center text-primary-color fw-bold">Need to get in touch with us?</h4>
        <div class="row g-3 align-items-center justify-content-center mt-3 text-primary-color">
            <div class="col-md-8 col-lg-6">
                <div class="border border-2 border-dashed-contact d-flex align-items-center rounded justify-content-center">
                    <i class="fa-solid fa-envelope fa-3x me-3"></i>
                    <span>contact@lovebeatnovels.com</span>
                </div>
            </div>
            <!-- <div class="col-md-3">
                <div class="border border-2 border-dashed-contact d-flex align-items-center rounded justify-content-center">
                    <i class="fa-solid fa-phone fa-3x me-3"></i>
                    <span>9374859205</span>
                </div>
            </div> -->
        </div>
    </div>

    <div class="row align-items-center shadow m-2 p-3">
        <!-- Left Side Image -->
        <div class="col-12 col-md-6 d-none d-md-block">
            <img src="<?php echo get_template_directory_uri() . '/images/contact.png'; ?>" alt="Contact Us" class="img-fluid rounded" style="width: 70%;">
        </div>

        <!-- Right Side Form -->
        <div class="col-12 col-md-6 col-xxl-5">
            <h5 class="px-4 fw-bold">Contact Us &nbsp; <i class="fa-solid fa-envelope-open-text"></i></h5>
            <form id="contactForm" class="p-4 rounded" method="POST">
                <div id="formResponse"></div>
                <div class="mb-3">
                    <input type="text" class="form-control height" id="name" name="name" placeholder="Name *">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control height" id="email" name="email" placeholder="Email *">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control height" id="subject" name="subject" placeholder="Subject *">
                </div>
                <div class="mb-3">
                    <textarea class="form-control" id="message" name="message" rows="4" placeholder="Message *"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-50 w-md-25 primary">Submit</button>
            </form>
        </div>
    </div>

</div>

<?php
get_footer();
?>

<script>
    jQuery(document).ready(function($) {
        $('#contactForm').submit(function(event) {
            event.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: formData + '&action=send_contact_mail',
                success: function(response) {
                    if (response.success) {
                        $('#formResponse').html('<div class="alert alert-success">' + response.data + '</div>');
                        $('#contactForm')[0].reset();
                    } else {
                        $('#formResponse').html('<div class="alert alert-danger">' + response.data + '</div>');
                    }
                },
                error: function(response) {
                    $('#formResponse').html('<div class="alert alert-danger">' + response.responseJSON.data + '</div>');
                }
            });
        });
    });
</script>
