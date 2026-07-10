(() => {
  if (!document.getElementById("novel-notification-app")) return;
  if (!window.Vue || !window.NOVEL_NOTIF) return;

  const { createApp, ref, onMounted, onBeforeUnmount } = window.Vue;

  createApp({
    setup() {
      const unread = ref(0);
      const lastId = ref(0);
      const status = ref("connecting");
      let es = null;

      const fetchFullList = async () => {
        const res = await fetch(`${NOVEL_NOTIF.ajaxUrl}?action=fetch_notifications`, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
          body: "" // keep it simple; action is in URL
        });

        const json = await res.json();
        if (json?.success) {
          unread.value = Number(json.data.unread_count || 0);

          // Update existing PHP-rendered dropdown DOM
          var notificationsHtml = json.data.notifications_html;

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
      };

      const startSSE = () => {
        if (es) es.close();
        status.value = "connecting";

        // ✅ SSE via admin-ajax (same-origin cookies + no REST nonce)
        const u = new URL(NOVEL_NOTIF.ajaxUrl, window.location.origin);
        u.searchParams.set("action", "novel_notifications_stream");
        u.searchParams.set("last_id", String(lastId.value || 0));

        es = new EventSource(u.toString());

        es.addEventListener("meta", (e) => {
          status.value = "connected";
          const data = JSON.parse(e.data || "{}");

          unread.value = Number(data.unread_count || 0);

          const latest = Number(data.latest_id || 0);
          const changed = !!data.changed;
          if (latest) lastId.value = latest;

          if (changed) fetchFullList().catch(() => {});
        });

        es.onerror = () => {
          status.value = "error";
        };
      };

      const stopSSE = () => {
        if (es) { try { es.close(); } catch {} }
        es = null;
      };

      onMounted(() => {
        startSSE();

        document.addEventListener("visibilitychange", () => {
          if (document.visibilityState === "hidden") stopSSE();
          if (document.visibilityState === "visible") startSSE();
        });
      });

      onBeforeUnmount(() => {
        stopSSE();
      });

      return { unread, status };
    },
    template: `<div style="display:none">{{ status }} {{ unread }}</div>`
  }).mount("#novel-notification-app");
})();