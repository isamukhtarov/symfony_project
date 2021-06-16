$(document).ready(function () {
    let e = $(".latest-news-container .latest-news");
    $.ajax({url: e.data("url"), type: "GET"}).done(function (a) {
        let t = "";
        a.posts.slice(0, 10).forEach((e) => {
            t += `<div class="news-item flex ${e.is_important || e.is_main ? "highlighted" : ""}"><div class="time"><span>${e.published_at}</span></div><div class="info"><a class="title ga-event-link" href="${e.url}">${
                e.preparedTitle
            }</a><a class="category" href="${e.categoryUrl}">${e.category_title}</a></div></div>`;
        });
        e.append(t);
    });
});