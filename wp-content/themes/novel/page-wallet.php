<?php
/**
 * Template Name: Subscription Plans
 */

get_header();
?>

<?php

    $coins = get_user_meta(get_current_user_id(), 'user_coin_balance', true);
    $coins = $coins ?: 0;

?>

<div class="container d-flex justify-content-center align-items-center py-4" style="min-height: 80vh;">
    <div class="wallet-card shadow shadow-div rounded-4 bg-primary-color py-3" style="width: 650px;">

        <div class="m-4 rounded-top-4 bg-primary-color">
            <?php
                $user_id = get_current_user_id();
                $user = get_userdata($user_id);
                $display_name = $user ? $user->display_name : '';
            ?>
            <h6 class="text-white mb-3">
                Welcome, <?php echo esc_html($display_name); ?>
            </h6>
            <h4 class="text-white fw-bold">Current Balance</h4>
            <h4 class="fw-bold"><img src="<?php echo get_template_directory_uri() . '/images/coin.png'; ?>" alt="Coin" class="img-fluid rounded"> &nbsp;<?php echo $coins; ?></h4>

            <div class="border p-3 rounded-3 mt-4 bg-secondary-color">
                <?php
                    $current_plan = get_user_meta(get_current_user_id(), 'subscription_active_plan', true);
                ?>

                <?php if ($current_plan): ?>
                    <?php
                        $current_period = get_user_meta(get_current_user_id(), 'subscription_active_period', true);
                        $subscription_active_expiry = get_user_meta(get_current_user_id(), 'subscription_active_expiry', true);
                    ?>
                    <p class="text-white">
                        உங்களுடைய தற்போதைய ஆக்ட்டிவ் பிளான் :
                        <span class="fw-bold fs-16px"><?php echo esc_html($current_plan); ?></span>

                        <?php
                        $upgradeable_peirod = ['1 Month', '3 Months'];

                        if (in_array($current_period, $upgradeable_peirod)) :
                            $upgrade_url = site_url('/subscription');
                        ?>
                            <a href="<?php echo esc_url($upgrade_url); ?>"
                            class="btn btn-warning btn-sm mt-2 text-decoration-none">
                                Upgrade Plan
                            </a>
                        <?php endif; ?>
                    </p>

                    <p class="text-white mt-2">
                        உங்கள் <span class="fw-bold fs-16px"><?php echo esc_html($current_plan); ?></span> பிளான்
                        <span class="fw-bold fs-16px">
                            <?php
                                if ($subscription_active_expiry) {
                                    echo date_i18n(get_option('date_format'), strtotime($subscription_active_expiry));
                                } else {
                                    echo 'N/A';
                                }
                            ?>
                        </span>
                        அன்று நிறைவடைகிறது.
                    </p>

                    <?php
                        $queue = get_user_meta(get_current_user_id(), 'subscription_queue', true);
                        $queue = is_array($queue) ? $queue : [];
                    ?>

                    <?php if (!empty($queue)): ?>
                        <div class="mt-3 text-white">
                            <h6><strong class="d-block mb-2">Upcoming Plans:</strong></h6>
                            <?php foreach ($queue as $q): ?>
                                <div class="border rounded-3 p-2 mb-2 w-100" style="max-width:320px;">
                                    <div class="fw-bold"><?= esc_html($q['plan']); ?></div>
                                    <small>Starts: <?= esc_html($q['from']); ?></small><br>
                                    <small>Expires: <?= esc_html($q['expiry']); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <p class="mt-4 text-white">
                        நீங்கள் எந்த பிளானும் ஆக்டிவேட் செய்யவில்லை... தடையில்லாத வாசிப்பை தொடர இப்பொழுதே
                        <a href="<?php echo esc_url(site_url('/subscription')); ?>" style="color: #FF5733; font-weight: bold;">
                            சப்ஸ்க்ரைப்
                        </a>
                        செய்யுங்கள்
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <?php $coin_prices = get_option('coin_pack_prices_setting', []); ?>

        <?php if(!empty($coin_prices)): ?>
            <h6 class="m-4 mb-0 mt-5">காயினைத் தேர்வுசெய்யுங்கள்</h6>
            <?php foreach ($coin_prices as $coin_price):?>
                <div class="m-4 px-3 py-2 bg-white rounded d-flex align-items-center">
                    <span class="flex-grow-1 fs-16px" style="color: #061148;">
                        &nbsp;&nbsp;
                        <img src="<?php echo get_template_directory_uri() . '/images/coin.png'; ?>" alt="Coin" class="img-fluid rounded">&nbsp;
                        <?= $coin_price['coin'] ?> &nbsp;&nbsp;
                        காய்ன்ஸ்
                    </span>
                    <button class="btn btn-primary ms-auto w-25" onclick="startPayment(<?= $coin_price['price'] ?>, <?= $coin_price['coin'] ?>)">₹<?= $coin_price['price'] ?></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
