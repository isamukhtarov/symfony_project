let $countLabel   = $('#new-posts-count');
const requestUrl  = $countLabel.data('url');
let $lastNewsFeed = $('.news-container');
let itemCount     = $countLabel.data('count');

let $newPosts = {
    count: 0,
    html: '',
    ids: []
};

function getNewPosts() {
    $.get(requestUrl, function (response) {
        // тут нужно сгенерировать нужное html-ленту
        // itemCount - лимит для ленты
        // $newPosts.html = response.posts;

        let posts = response.posts.slice(0, itemCount);
        posts.forEach(function (post) {
            if (!postExistsInList(post.id) && $.inArray(post.id, $newPosts.ids) === -1) {
                $newPosts.count++;
                $newPosts.ids.push(post.id);
                $newPosts.html += renderPostHtml(post);
            }
        });

        if ($newPosts.count) {
            $countLabel.text($newPosts.count);
            if ($countLabel.is(':hidden')) {
                $countLabel.fadeIn('slow');
            }
        }
    });
}

function renderPostHtml(post) {
    return `
        <div class="news-item flex ${post.is_important || post.is_main ? "highlighted" : ""}" data-id="${post.id}">
            <div class="time">
                <span>${post.published_at}</span>
            </div>
            <div class="info">
                <a class="title ga-event-link" href="${post.url}" data-event="last_news | main_page">
                    ${post.preparedTitle}
                </a>
                <a class="category" href="${post.categoryUrl}">${post.category_title}</a>
            </div>
        </div>`
}

function postExistsInList(postId) {
    return !!$lastNewsFeed.find('.news-item[data-id="' + postId + '"]').length;
}

setInterval(getNewPosts, 20000);

$countLabel.click(function () {
    if (!$newPosts.count) {
        return;
    }

    $lastNewsFeed.prepend($newPosts.html);

    setTimeout(function () {
        for (let i = 0; i < $newPosts.count; i++) {
            $lastNewsFeed.find('.news-item:eq(' + i + ')').addClass('new-post-exists');
        }

        // reset data
        $newPosts.count = 0;
        $newPosts.html = '';
        $newPosts.ids = [];
    }, 100);

    setTimeout(function () {
        $lastNewsFeed.find('.news-item.new-post-exists').removeClass('new-post-exists');
    }, 2000);

    $(this).fadeOut('slow');
});