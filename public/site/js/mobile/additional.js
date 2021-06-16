let lang = Cookies.get("priority-lang");
if (lang !== undefined && lang !== "ge") {
    let url = "/" + lang + "/";
    if (location.pathname === '/' || (document.referrer == null || document.referrer.indexOf(window.location.hostname) < 0)) {
        location.assign(url);
    }
}

$('.langs li > a').on('click', function (e) {
    e.preventDefault();

    let $self = $(this);

    $.post('/set-language/', {language: $self.data('lang')}, function () {
        location.assign($self.attr('href'));
    });
});

$(document).on('click', '.ga-event-link', function () {
    const link = $(this);
    ga('send', 'event', link.data('event'), 'Mobile');
});