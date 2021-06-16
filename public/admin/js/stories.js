let list = $('.language-list').find('a');

$.each(list, function (index, item) {
    let lang = $(item).data('lang');
    $(`#story_translations_${lang}_title,#story_translations_${lang}_slug`).bind('input', function () {
        $(`#story_translations_${lang}_slug`).val(url_slug($(this).val()));
    });
});