const loadContainer = $(".load-container");
if (loadContainer.length > 0) {
    loadDataOnScroll();
}

function loadDataOnScroll() {
    let loadingData = false;
    let dataEnded = false;
    let scrollSum = null;
    $(window).on("scroll", function () {
        scrollSum = Math.ceil($(window).scrollTop() + $(window).innerHeight());
        if (scrollSum >= ($(this.document).height() - 600) && !loadingData) {
            loadingData = true;

            if (dataEnded) return false;

            let lastTimestamp = loadContainer.find('.infinity-item:last').data('timestamp');

            $.ajax({
                url: loadContainer.data("url"),
                type: "GET",
                data: {
                    timestamp: lastTimestamp
                }
            })
            .done(function(response) {
                loadContainer.append(response.html);

                let $lazyImages = $(".lazy img");
                if ($lazyImages.length) {
                    $lazyImages.lazy();
                }

                // this feature works in playlist page
                if (response.count) {
                    $(".news-player").slice(-response.count).each(function(i) {
                        initAudioPlayers($(this)[0]);
                    });
                }

                if (response.hasOwnProperty("limitReached")) {
                    dataEnded = response.limitReached;
                }
                loadingData = false;
            });
        }
    });
}