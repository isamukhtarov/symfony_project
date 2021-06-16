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

            let lastId = loadContainer.find('.infinity-item:last').data('id');

            $.ajax({
                url: loadContainer.data("url"),
                type: "GET",
                data: {
                    id: lastId
                }
            })
            .done(function(response) {
                loadContainer.append(response.html);
                if (response.hasOwnProperty("limitReached")) {
                    dataEnded = response.limitReached;
                }
                loadingData = false;
            });
        }
    });
}