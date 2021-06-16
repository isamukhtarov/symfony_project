let list = $('.language-list').find('a');

$.each(list, function (index, item) {
    let lang = $(item).data('lang');
    $(`#city_translations_${lang}_title,#city_translations_${lang}_slug`).bind('input', function () {
        $(`#city_translations_${lang}_slug`).val(url_slug($(this).val()));
    });
});