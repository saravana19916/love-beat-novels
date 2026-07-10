<?php
$view = $args['view'] ?? 'desktop';
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

<?php
// Print the JS only once (e.g. only for desktop view)
if (is_user_logged_in() && $view === 'desktop'): ?>
<script>
jQuery(function ($) {
  // Run-once guard (prevents duplicate init when template is included twice)
  if (window.__NOVEL_NOTIF_INIT__) return;
  window.__NOVEL_NOTIF_INIT__ = true;

  const ajaxUrl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
  let timer = null;
  let inFlight = false;

  function updateNotificationBadge(unreadCount) {
    unreadCount = parseInt(unreadCount || 0, 10);
    ["#notification-badge-desktop", "#notification-badge-mobile"].forEach(function (id) {
      const $badge = $(id);
      if (!$badge.length) return;
      if (unreadCount > 0) $badge.text(unreadCount > 99 ? "99+" : unreadCount).show();
      else $badge.hide();
    });
  }

  function updateNotificationList(notificationsHtml) {
    const headerHtml = `<li class="fs-14px dropdown-header text-primary-color"><strong>Notifications</strong></li>`;
    const contentHtml = notificationsHtml && notificationsHtml.trim()
      ? notificationsHtml
      : `<li><span class="dropdown-item text-primary-color">No new notifications</span></li>`;

    const fullHtml = headerHtml + contentHtml;

    const $desktop = $("#notification-list-desktop");
    if ($desktop.length) $desktop.html(fullHtml);

    const $mobile = $("#notification-list-mobile");
    if ($mobile.length) $mobile.html(fullHtml);
  }

  function fetchNotifications() {
    if (inFlight || document.visibilityState !== "visible") return;
    inFlight = true;

    return $.ajax({
      url: ajaxUrl + "?action=fetch_notifications",
      type: "POST",
      dataType: "json",
      timeout: 15000
    })
    .done(function (response) {
      if (response && response.success) {
        updateNotificationBadge(response.data.unread_count);
        updateNotificationList(response.data.notifications_html);
      }
    })
    .always(function () {
      inFlight = false;
    });
  }

  function schedule(ms) {
    clearTimeout(timer);
    timer = setTimeout(function () {
      fetchNotifications();
      schedule(ms);
    }, ms);
  }

  // Fetch when user opens (desktop dropdown)
  $(document).on("shown.bs.dropdown", "#notificationDropdown", function () {
    fetchNotifications();
  });

  // Fetch when user opens (mobile modal)
  $(document).on("shown.bs.modal", "#mobileNotificationModal", function () {
    fetchNotifications();
  });

  // Optional: light refresh every 30s (badge/list updated if open later)
  schedule(30000);

  // Refresh when tab becomes visible
  document.addEventListener("visibilitychange", function () {
    if (document.visibilityState === "visible") fetchNotifications();
  });
});
</script>
<?php endif; ?>

