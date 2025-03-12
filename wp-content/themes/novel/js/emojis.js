document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".emoji-btn").forEach(button => {
        button.addEventListener("click", function () {
            let reaction = this.getAttribute("data-emo-symbol");
            let emoji = this.getAttribute("data-emoji");
            let episode_id = this.closest(".emoji-reactions").getAttribute("data-episode");
            let user_id = this.closest(".emoji-reactions").getAttribute("data-user");

            fetch(ajaxurl.url, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=add_reaction&episode_id=${episode_id}&user_id=${user_id}&emoji=${encodeURIComponent(emoji)}&reaction=${reaction}`
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    document.querySelector(`.count[data-emoji="${emoji}"]`).textContent = res.data.count;
                    // button.find(".count").text(res.data.count);
                }
            });
        });
    });
});

