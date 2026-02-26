<?php
$view = $args['view'] ?? 'desktop';

if (is_user_logged_in()) {

    global $wpdb;
    $user_id = get_current_user_id();


    $notifications = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}author_notifications 
            WHERE user_id = %d 
            ORDER BY created_at DESC 
            LIMIT 20", 
            $user_id
        )
    );

    $unread_count = count(array_filter($notifications, fn($n) => $n->seen == 0));

    // Group notifications
    $grouped_notifications = [
        'Today' => [],
        'Yesterday' => [],
        'Earlier' => [],
    ];

    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    foreach ($notifications as $note) {
        $created_date = date('Y-m-d', strtotime($note->created_at));
        if ($created_date === $today) {
            $grouped_notifications['Today'][] = $note;
        } elseif ($created_date === $yesterday) {
            $grouped_notifications['Yesterday'][] = $note;
        } else {
            $grouped_notifications['Earlier'][] = $note;
        }
    }
}
?>
<?php if (is_user_logged_in()): ?>
    <!-- Desktop: dropdown -->
    <?php if ($view === 'desktop'): ?>
        <div class="dropdown">
            <a href="#" class="position-relative text-white text-decoration-none" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-bell fs-4"></i>
                <span id="notification-badge-desktop"
                    class="position-absolute translate-middle badge rounded-pill bg-danger"
                    style="left: 1.5rem; display:none;">
                </span>
            </a>

            <ul id="notification-list-desktop"
                class="dropdown-menu dropdown-menu-end shadow fs-12px notification-div shadow-div"
                aria-labelledby="notificationDropdown"
                style="min-width: 400px; max-height: 400px; overflow-y: auto;">
                <li class="fs-14px dropdown-header text-primary-color">
                    <strong>Notifications</strong>
                </li>
                <li class="dropdown-item text-muted">Loading…</li>
            </ul>
        </div>
    <?php elseif ($view === 'mobile'): ?>

        <!-- Mobile: full-screen modal -->
        <div>
            <a href="#" class="position-relative text-white text-decoration-none" id="mobileNotificationDropdown" data-bs-toggle="modal" data-bs-target="#mobileNotificationModal">
                <i class="fa-solid fa-bell fs-4"></i>
                <span id="notification-badge-mobile"
                    class="position-absolute translate-middle badge rounded-pill bg-danger"
                    style="left: 1.5rem; display:none;">
                </span>
            </a>

            <!-- Modal -->
            <div class="modal fade" id="mobileNotificationModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-primary-color">Notifications</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" style="max-height: 30vh; overflow-y:auto;">
                            <ul id="notification-list-mobile"
                                class="list-unstyled fs-12px"
                                style="max-height: 400px; overflow-y: auto;">
                                <li class="fs-14px dropdown-header text-primary-color"><strong>Notifications</strong></li>
                                <li class="dropdown-item text-muted">Loading…</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <!-- Not logged in → go to login modal -->
    <a href="#" class="position-relative text-white text-decoration-none" data-bs-toggle="modal" data-bs-target="#loginModal">
        <i class="fa-solid fa-bell fs-4"></i>
    </a>
<?php endif; ?>

<script>
   jQuery(document).ready(function ($) {
    function updateNotificationBadge(unreadCount) {
        ["#notification-badge-desktop", "#notification-badge-mobile"].forEach(function (id) {
            let badge = $(id);
            if (badge.length) {
                if (unreadCount > 0) {
                    badge.text(unreadCount > 10 ? "10+" : unreadCount).show();
                } else {
                    badge.hide();
                }
            }
        });
    }

    function updateNotificationList(html) {
        ["#notification-list-desktop"].forEach(function (id) {
            let list = $(id);
            if (list.length) {
                list.html(`
                    <li class="fs-14px dropdown-header text-primary-color"><strong>Notifications</strong></li>
                    ${html}
                `);
            }
        });

        ["#notification-list-mobile"].forEach(function (id) {
            let list = $(id);
            if (list.length) {
                list.html(`
                    ${html}
                `);
            }
        });
    }

    function fetchNotifications() {
        $.post("<?php echo admin_url('admin-ajax.php'); ?>", { action: "fetch_notifications" }, function (response) {
            if (response.success) {
                // Update badge
                updateNotificationBadge(response.data.unread_count);

                // Update list
                updateNotificationList(response.data.notifications_html);
            }
        });
    }

    // Poll every 10 seconds
    setInterval(fetchNotifications, 1000);
    fetchNotifications();
});


</script>

