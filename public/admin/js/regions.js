let list = $('.language-list').find('a');

$.each(list, function (index, item) {
    let lang = $(item).data('lang');
    $(`#region_translations_${lang}_title,#region_translations_${lang}_slug`).bind('input', function () {
        $(`#region_translations_${lang}_slug`).val(url_slug($(this).val()));
    });
});