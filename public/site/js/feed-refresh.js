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

        response.posts.forEach(function (post) {
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
        <div class="news-item flex infinity-item ${post.is_important || post.is_main ? 'highlighted' : ''}" 
             data-timestamp="${post.published_at}" data-id="${post.id}">
            <div class="image">
                <a href="${post.url}" target="_blank">
                    <img src="${post.imageThumb}" alt="${post.titleWithoutQuotes}">
                </a>
            </div>
            
            <div class="info">
                <a class="title" href="${post.url}" target="_blank">${post.preparedTitle}</a>
                <p class="description">${post.description}</p>
                <div class="news-date">
                    <span>${post.published_at_long}</span>
                    <span>${post.published_at}</span>
                </div>
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