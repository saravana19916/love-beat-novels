<?php
/**
 * Template Name: Subscription Plans
 */

get_header();

// get all plan details from admin
$plans = get_option('plan_details');
?>

<div class="container py-5">
    <h2 class="mb-5 fw-bold text-center text-primary-color">Choose Your Subscription Plan</h2>

    <div class="row g-5 align-items-stretch px-3">

        <?php foreach ($plans as $plan): ?>

            <div class="col-lg-4 d-flex">

                <div class="pricing-card shadow shadow-div d-flex flex-column w-100 p-3">

                    <!-- CURVED HEADER -->
                    <div class="text-center text-primary-color">
                        <h4 class="fw-bold mb-0"><?php echo esc_html($plan['name']); ?></h4>
                    </div>

                    <div class="pt-4 flex-grow-1">

                        <div class="d-flex justify-content-center align-items-center mb-1">
                            <span class="text-primary-color text-decoration-line-through me-2 fs-20px">
                                ₹<?php echo esc_html($plan['price']); ?>.00
                            </span>

                            <?php if (!empty($plan['price']) && !empty($plan['offerprice'])):
                                $discount = round((($plan['price'] - $plan['offerprice']) / $plan['price']) * 100);
                            ?>
                                <span class="badge bg-danger p-2 fs-14px">
                                    <?php echo $discount; ?>% OFF
                                </span>
                            <?php endif; ?>
                        </div>

                        <h2 class="fw-bold mb-0 mt-3 text-primary-color text-center">
                            ₹<?php echo esc_html($plan['offerprice']); ?>.00
                        </h2>

                        <p class="mt-2 mb-3 fw-bold text-primary-color text-center">
                            For <?php echo esc_html($plan['period']); ?>
                        </p>
                    </div>

                    <div class="flex-grow-1">
                        <!-- FEATURES -->
                        <div class="px-3 my-2 text-center">
                            <?php echo wpautop($plan['description']); ?>
                        </div>

                    </div>

                    <!-- BUY BUTTON -->
                    <div class="pricing-footer text-center mt-auto mt-4 mb-2">
                        <?php if ( ! is_user_logged_in() ) { ?>
                            <button 
                                type="button" 
                                class="btn primary-btn"
                                data-bs-toggle="modal" data-bs-target="#loginModal"
                            >
                                Subscribe Now
                            </button>
                        <?php } else { ?>
                            <?php
                                $user_id = get_current_user_id();

                                $current_plan = get_user_meta($user_id, 'subscription_active_plan', true);

                                $queue = get_user_meta($user_id, 'subscription_queue', true);
                                $queue = is_array($queue) ? $queue : [];

                                $is_current_plan  = ($current_plan === $plan['name']);
                                $is_upcoming_plan = false;

                                foreach ($queue as $queued_plan) {
                                    if (!empty($queued_plan['plan']) && $queued_plan['plan'] === $plan['name']) {
                                        $is_upcoming_plan = true;
                                        break;
                                    }
                                }
                            ?>
                            <?php if ($is_upcoming_plan): ?>
                                <?php
                                    $from_date = '';
                                    $to_date = '';
                                    foreach ($queue as $queued_plan) {
                                        if (!empty($queued_plan['plan']) && $queued_plan['plan'] === $plan['name']) {
                                            $from_date = !empty($queued_plan['from']) ? date_i18n(get_option('date_format'), strtotime($queued_plan['from'])) : '';
                                            $to_date = !empty($queued_plan['expiry']) ? date_i18n(get_option('date_format'), strtotime($queued_plan['expiry'])) : '';
                                            break;
                                        }
                                    }
                                ?>
                                <div class="alert alert-info py-2 mb-2">
                                    <span class="fw-bold">Scheduled for Upcoming Activation</span>
                                    <?php if ($from_date && $to_date): ?>
                                        <br>
                                        <span>From: <?php echo $from_date; ?> To: <?php echo $to_date; ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($is_current_plan): ?>
                                <?php
                                    $expiry = get_user_meta($user_id, 'subscription_active_expiry', true);
                                ?>
                                <div class="alert alert-success py-2 mb-2">
                                    <span class="fw-bold">Activated</span>
                                    <?php if ($expiry): ?>
                                        <br>
                                        <span>Expires on: <?php echo date_i18n(get_option('date_format'), strtotime($expiry)); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <button 
                                type="button"
                                class="btn primary-btn buy-plan-btn"
                                data-plan-name="<?php echo esc_attr($plan['name']); ?>"
                                data-plan-period="<?php echo esc_attr($plan['period']); ?>" 
                                data-plan-price="<?php echo esc_attr($plan['price']); ?>"
                                data-offer-price="<?php echo esc_attr($plan['offerprice']); ?>"
                            >

                                <?php
                                    if ($is_current_plan) {
                                        echo 'Renew Current Plan';
                                    } else {
                                        echo 'Subscribe Now';
                                    }
                                ?>
                            </button>
                        <?php } ?>
                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>


<?php get_footer(); ?>

<script>
    jQuery(document).on("click", ".buy-plan-btn", function () {
        let failureLoggedOrders = new Set();

        var planName = jQuery(this).data('plan-name');
        var period = jQuery(this).data('plan-period');
        var price = jQuery(this).data('plan-price');
        var offerPrice = jQuery(this).data('offer-price');

        var amount = (offerPrice == '' || offerPrice == 0) ? price : offerPrice;

        let amountInPaisa = amount * 100;

        jQuery.post(
            "<?php echo admin_url('admin-ajax.php'); ?>",
            {
                action: "create_razorpay_order",
                amount: amountInPaisa
            },
            function (orderRes) {

                if (!orderRes.success) {
                    alert("Order creation failed");
                    return;
                }

                var options = {
                    key: RazorpayConfig.key,
                    amount: amountInPaisa,
                    currency: "INR",
                    name: "Subscription Purchase",
                    order_id: orderRes.data.order_id,
                    handler: function (response) {
                        jQuery.post(
                            "<?php echo admin_url('admin-ajax.php'); ?>",
                            {
                                action: "verify_razorpay_payment",
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_signature: response.razorpay_signature,
                                amount: amount,
                                api: 'subscription',
                                period: period,
                                name: planName
                            },
                            function (res) {
                                if (res.success) {
                                    alert("Subscription Activated!");
                                    location.reload();
                                } else {
                                    alert("Payment verification failed!");
                                }
                            }
                        );
                    }
                };

                var rzp = new Razorpay(options);

                rzp.on('payment.failed', function(response) {
                    let orderId = orderRes.data.order_id;

                    if (failureLoggedOrders.has(orderId)) {
                        console.warn("Failure already logged for this order:", orderId);
                        return;
                    }

                    failureLoggedOrders.add(orderId); // mark as logged

                    jQuery.post(ajaxurl, {
                        action: 'log_razorpay_failure',
                        razorpay_payment_id: response.error.metadata.payment_id,
                        razorpay_order_id: response.error.metadata.order_id,
                        amount: amount,
                        api: 'subscription',
                        name: planName
                    });

                    alert("Payment failed!");
                });

                rzp.open();
            }
        );
    });
</script>
