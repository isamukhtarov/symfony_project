const $newsWrapper = $(".news-wrapper");
let loadingData = false;
let dataEnded = false;
let scrollSum = null;
let isIOSDevice = isIOS();

displayApplePodcastIfNecessary();

$(window).on("scroll", function () {
    scrollSum = Math.ceil($(window).scrollTop() + $(window).innerHeight());

    if (scrollSum >= ($(document).height() - 800) && !loadingData && !dataEnded) {
        var postUrl = $('.selected-news:last').attr('data-prev');
        $(".spinner").show();
        loadingData = true;

        $.ajax({
            url: postUrl,
            type: "GET",
        }).done(function (res) {
            let playersCount = $(".news-wrapper").find(".audio-player").length;

            if (res.hasOwnProperty('html')) {
                if (!res.html) {
                    dataEnded = true;
                }
                $newsWrapper.append(res.html);
            } else {
                if (!res) {
                    dataEnded = true;
                }
                $newsWrapper.append($(res).find('.selected-news'));
            }

            $(".spinner").hide();
            loadingData = false;
            // Dynamic change URL && document.title
            changePageTitle();

            let $lazyImages = $(".lazy img");
            if ($lazyImages.length) {
                $lazyImages.lazy();
            }

            // Google Analytics
            ga("set", "page", postUrl);
            ga("send", "pageview");
            // send event
            ga('send', 'event', 'Infinity post', 'Load', 'Mobile');

            // Yandex Metrika
            window.yaCounter51449475.hit(postUrl);
            new Image().src = "//counter.yadro.ru/hit?r" +
                escape(document.referrer) + ((typeof (screen) == "undefined") ? "" : ";s" +
                    screen.width + "*" + screen.height + "*" + (screen.colorDepth ? screen.colorDepth : screen.pixelDepth)) +
                ";u" + escape(document.URL) +
                ";h" + escape(document.title.substring(0, 150)) + ";" + Math.random();

            // Init audioPlayer && newsGallery
            if ($(".news-wrapper").find(".audio-player").length > playersCount) {
                initAudioPlayer($(".news-wrapper").find(".audio-player:last")[0]);
            }
            initNewsGallery($(".news-gallery .swiper-container"));

            displayApplePodcastIfNecessary();
        }).fail(function () {
            dataEnded = true;
        });
    }
});

function changePageTitle() {
    $(".selected-news")
        .bind("enterviewport", function (e) {
            let $target = $(e.target);
            let title = $target.find(".news-title").text();
            let url = $target.data("url");
            history.replaceState("", title, url);
            document.title = title;
        })
        .bullseye();
}

function isIOS() {
    return [
            'iPad',
            'iPhone',
            'iPod'
        ].includes(navigator.platform)
        || (navigator.userAgent.includes("Mac"));
}

function displayApplePodcastIfNecessary() {
    let $applePodcastSubscription = $(".apple-podcast");
    if (isIOSDevice && $applePodcastSubscription.length) {
        $applePodcastSubscription.css("display", "flex");
    }
}