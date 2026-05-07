<?php
if (!function_exists('novel_get_email_for_user')) {
    function novel_get_email_for_user($user_id, $fallback_email = '') {
        $user_id = (int) $user_id;
        if ($user_id > 0) {
            $u = get_userdata($user_id);
            if ($u && !empty($u->user_email)) {
                return $u->user_email;
            }
        }
        $fallback_email = (string) $fallback_email;
        return is_email($fallback_email) ? $fallback_email : '';
    }
}

if (!function_exists('novel_send_payment_mail')) {
    function novel_send_payment_mail($to, $subject, $body) {
        $to = (string) $to;
        if (!is_email($to)) return false;

        $subject = (string) $subject;
        $body    = (string) $body;

        // From name/email (override via wp-config constants if you want)
        $from_name  = defined('NOVEL_MAIL_FROM_NAME')
            ? (string) NOVEL_MAIL_FROM_NAME
            : wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);

        $from_email = defined('NOVEL_MAIL_FROM_EMAIL')
            ? (string) NOVEL_MAIL_FROM_EMAIL
            : (string) get_option('admin_email');

        if (!is_email($from_email)) {
            $from_email = (string) get_option('admin_email');
        }

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>',
        ];

        // Body (plain text -> safe HTML)
        $safe_body_html = wpautop(esc_html($body));

        // ✅ Logo (must be a public absolute URL)
        $logo_url = trailingslashit(get_stylesheet_directory_uri()) . 'images/logo.jpeg';
        $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);

        // Email wrapper + logo header
        $html = '
            <!doctype html>
            <html>
            <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            </head>
            <body style="margin:0;padding:0;background:#ffffff;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#ffffff;">
                <tr>
                <td align="center" style="padding:20px 12px;">
                    <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:600px;max-width:600px;">
                    <tr>
                        <td align="center" style="padding:0 0 14px 0;">
                        <img src="' . esc_url($logo_url) . '" alt="' . esc_attr($site_name) . '" width="140" style="width:140px;max-width:100%;height:auto;display:block;border:0;outline:none;text-decoration:none;">
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#111111;">
                        ' . $safe_body_html . '
                        </td>
                    </tr>
                    </table>
                </td>
                </tr>
            </table>
            </body>
            </html>';

        return wp_mail($to, $subject, $html, $headers);
    }
}

if (!function_exists('novel_mail_subscription_success')) {
    function novel_mail_subscription_success($to) {
        $subject = 'Your Subscription Has Been Activated';
        $body = <<<TXT
            Thank you for your subscription.

            We are pleased to inform you that your subscription plan has been successfully activated. You can now enjoy full access to all the subscribed content and features.

            If you have any questions or require assistance, please feel free to contact us

            --
            Thanks & Regards,
            Sarmi SS
            Author | Content Editor
            Instagram: https://www.instagram.com/sarmi_ss/
            Facebook: https://www.facebook.com/Sarmi.SSfan
            Website: https://lovebeatnovels.com
            Whatsapp - +916374401933
            TXT;

        return novel_send_payment_mail($to, $subject, $body);
    }
}

if (!function_exists('novel_mail_coin_success')) {
    function novel_mail_coin_success($to) {
        $subject = 'Coins Successfully Added to Your Wallet';
        $body = <<<TXT
            நீங்கள் வாங்கிய Coins உங்கள் wallet-ல் வெற்றிகரமாக சேர்க்கப்பட்டுள்ளதை மகிழ்ச்சியுடன் தெரிவித்துக் கொள்கிறோம்.

            💡 Coin Usage Tips – For Your Kind Information

            Previous episode-ஐ unlock செய்யாமல், current episode-ஐ unlock செய்ய முடியாது.

            ஒவ்வொரு episode-ஐ unlock செய்ய 3 coins பயன்படுத்தப்படும்.

            எந்த restriction-ம் இல்லாமல் எல்லா stories-ஐயும் read செய்ய Subscription எடுப்பதே best option.

            உங்கள் support-க்கு மனப்பூர்வமான நன்றி.

            Happy Reading! ✨📖--

            --
            Thanks & Regards,
            Sarmi SS
            Author | Content Editor
            Whatsapp - +916374401933
            Instagram: https://www.instagram.com/sarmi_ss/
            Facebook: https://www.facebook.com/Sarmi.SSfan
            Website: https://lovebeatnovels.com
            TXT;

        return novel_send_payment_mail($to, $subject, $body);
    }
}

if (!function_exists('novel_mail_subscription_failed')) {
    function novel_mail_subscription_failed($to) {
        $subject = 'Subscription Payment Failed';
        $body = <<<TXT
            அன்புள்ள வாசகரே,

            நீங்கள் சமீபத்தில் Subscription எடுக்க முயற்சி செய்துள்ளீர்கள். ஆனால், கட்டணம் செலுத்தும் செயல்முறை வெற்றியடையவில்லை.

            தயவுசெய்து மீண்டும் ஒரு முறை Subscription எடுக்க முயற்சி செய்யவும்.

            ⚠️ முக்கிய அறிவிப்பு:
            Facebook அல்லது Instagram app-இல் இருந்து link-ஐ நேரடியாக click செய்து முயற்சி செய்ய வேண்டாம்.

            அதற்குப் பதிலாக, அந்த link-ஐ copy செய்து உங்கள் மொபைல் browser (Google Chrome, Safari போன்றவை) மூலம் திறந்து Subscription செய்ய முயற்சி செய்யவும்.

            சில நேரங்களில் social media app-இல் திறக்கும் போது payment பிரச்சினைகள் ஏற்படலாம். Browser மூலம் முயற்சி செய்தால் சரியாக செயல்படும்.

            🌍 International users-க்கு:
            Debit / Credit card payment மட்டுமே வேலை செய்யும்.
            அதனால், உங்கள் card-ல் international payment enabled ஆக இருக்க வேண்டும்.

            Subscription Link - https://lovebeatnovels.com/subscription/

            இன்னும் ஏதேனும் பிரச்சினை இருந்தால், எங்களை தொடர்பு கொள்ள தயங்க வேண்டாம்.

            உங்கள் ஆதரவுக்கு நன்றி. ❤️
            Thanks & Regards,
            Sarmi SS
            Author | Content Editor
            Whatsapp - +916374401933
            Instagram: https://www.instagram.com/sarmi_ss/
            Facebook: https://www.facebook.com/Sarmi.SSfan
            Website: https://lovebeatnovels.com
            TXT;

        return novel_send_payment_mail($to, $subject, $body);
    }
}

if (!function_exists('novel_mail_subscription_renewal_reminder')) {
    function novel_mail_subscription_renewal_reminder($to, $is_expired = false) {
        $subject = 'Subscription Renewal Reminder';

        if ($is_expired) {
            $body = <<<TXT
                வணக்கம்,

                உங்களின் subscription pack expire ஆகிவிட்டது. உங்கள் வாசிப்பில் interruption ஏற்படாமல் இருக்க, தயவுசெய்து உடனடியாக renew செய்யவும்.

                எங்களின் கதையை தொடர்ந்து வாசிக்க, உங்கள் subscription-ஐ விரைவில் activate செய்யுமாறு கேட்டுக்கொள்கிறோம்.

                Subscription Link - https://lovebeatnovels.com/subscription/
                ஏற்கனவே renew செய்திருந்தால், இந்த message-ஐ ignore செய்யவும்.

                Thanks & Regards,
                Sarmi SS
                Author | Content Editor
                Whatsapp : +916374401933
                Instagram: https://www.instagram.com/sarmi_ss/
                Facebook: https://www.facebook.com/Sarmi.SSfan
                Website: https://lovebeatnovels.com
                TXT;
        } else {
            $body = <<<TXT
                வணக்கம்,

                உங்களின் subscription pack விரைவில் expire ஆகவுள்ளது. உங்கள் வாசிப்பில் interruption ஏற்படாமல் இருக்க, தயவுசெய்து உடனடியாக renew செய்யவும்.

                எங்களின் கதையை தொடர்ந்து வாசிக்க, உங்கள் subscription-ஐ விரைவில் activate செய்யுமாறு கேட்டுக்கொள்கிறோம்.

                Subscription Link - https://lovebeatnovels.com/subscription/
                ஏற்கனவே renew செய்திருந்தால், இந்த message-ஐ ignore செய்யவும்.

                Thanks & Regards,
                Sarmi SS
                Author | Content Editor
                Whatsapp : +916374401933
                Instagram: https://www.instagram.com/sarmi_ss/
                Facebook: https://www.facebook.com/Sarmi.SSfan
                Website: https://lovebeatnovels.com
                TXT;
        }

        return novel_send_payment_mail($to, $subject, $body);
    }
}

// if (!function_exists('novel_send_whatsapp_text')) {
//     function novel_send_whatsapp_text($to_phone_e164, $message) {
//         if (!defined('WHATSAPP_PHONE_NUMBER_ID') || !defined('WHATSAPP_ACCESS_TOKEN')) {
//             return false;
//         }

//         $to = preg_replace('/\D+/', '', (string) $to_phone_e164);
//         if ($to === '') return false;

//         $message = trim((string) $message);
//         if ($message === '') return false;

//         $url = 'https://graph.facebook.com/v20.0/' . WHATSAPP_PHONE_NUMBER_ID . '/messages';

//         $payload = [
//             'messaging_product' => 'whatsapp',
//             'to'   => $to,
//             'type' => 'text',
//             'text' => [
//                 'preview_url' => true,
//                 'body'        => $message,
//             ],
//         ];

//         $res = wp_remote_post($url, [
//             'headers' => [
//                 'Authorization' => 'Bearer ' . WHATSAPP_ACCESS_TOKEN,
//                 'Content-Type'  => 'application/json',
//             ],
//             'body'    => wp_json_encode($payload),
//             'timeout' => 15,
//         ]);

//         if (is_wp_error($res)) {
//             error_log('WhatsApp send failed: ' . $res->get_error_message());
//             return false;
//         }

//         $code = (int) wp_remote_retrieve_response_code($res);
//         if ($code >= 300) {
//             error_log('WhatsApp send failed HTTP ' . $code . ': ' . wp_remote_retrieve_body($res));
//             return false;
//         }

//         return true;
//     }
// }

/**
 * Force all wp_mail() sender name/email for this site.
 * Set NOVEL_MAIL_FROM_NAME and NOVEL_MAIL_FROM_EMAIL in wp-config.php.
 */
add_filter('wp_mail_from', function ($from_email) {
    if (defined('NOVEL_MAIL_FROM_EMAIL') && is_email(NOVEL_MAIL_FROM_EMAIL)) {
        return NOVEL_MAIL_FROM_EMAIL;
    }
    return $from_email;
}, 999);

add_filter('wp_mail_from_name', function ($from_name) {
    if (defined('NOVEL_MAIL_FROM_NAME') && NOVEL_MAIL_FROM_NAME !== '') {
        return wp_specialchars_decode((string) NOVEL_MAIL_FROM_NAME, ENT_QUOTES);
    }
    return $from_name;
}, 999);

add_action('phpmailer_init', function ($phpmailer) {
    $name  = (defined('NOVEL_MAIL_FROM_NAME') && NOVEL_MAIL_FROM_NAME !== '')
        ? (string) NOVEL_MAIL_FROM_NAME
        : wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);

    $email = (defined('NOVEL_MAIL_FROM_EMAIL') && is_email(NOVEL_MAIL_FROM_EMAIL))
        ? (string) NOVEL_MAIL_FROM_EMAIL
        : (string) get_option('admin_email');

    if (is_email($email)) {
        $phpmailer->setFrom($email, $name, false);
        $phpmailer->Sender = $email; // envelope-from (helps some providers)
    }
});