$(document).ready(function () {
    let a = $(".latest-news.news-container");
    $.ajax({url: a.data("url"), type: "GET"}).done(function (e) {
        let n = "";
        e.posts.slice(0, 8).forEach((a) => {
            n += `\n<div class="news-item flex ${a.is_important || a.is_main ? "highlighted" : ""}" data-id="${a.id}">\n<div class="time">\n<span>${a.published_at}</span>\n</div>\n<div class="info">\n<a class="title ga-event-link" href="${
                a.url
            }" data-event="last_news | main_page">\n${a.preparedTitle}\n</a>\n<a class="category" href="${a.categoryUrl}">${a.category_title}</a>\n</div>\n</div>\n`;
        });
        a.append(n);
    });
});