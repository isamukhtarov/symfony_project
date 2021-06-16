$(document).ready(function () {
    let e = $(".news-list-wrapper .news-list");
    $.ajax({url: e.data("url"), type: "GET"}).done(function (t) {
        let a = "";
        t.posts.slice(0, 15).forEach((e) => {
            a += `<div class="inline-news ${e.is_important || e.is_main ? "highlighted" : ""}"><div class="time">${e.published_at}</div><a class="title" href="${e.url}">${e.preparedTitle}</a><a class="category" href="${e.categoryUrl}">${
                e.category_title
            }</a></div>`;
        });
        e.prepend(a);
    });
});