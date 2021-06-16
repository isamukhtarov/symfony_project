let list = $('.language-list').find('a');

$.each(list, function (index, item) {
    let lang = $(item).data('lang');

    $(`#person_translations_${lang}_first_name,#person_translations_${lang}_last_name,#person_translations_${lang}_slug`).bind('input', function () {
        let id = $(this).data('id');
        let slug = $(`#person_translations_${lang}_slug`);
        let inputVal = $(this).val();

        if(id === 'firstName') {
            let lastName = $(`#person_translations_${lang}_last_name`).val();
            slug.val(url_slug(inputVal + '-' + lastName));
        } else if (id === 'lastName') {
            let firstName = $(`#person_translations_${lang}_first_name`).val();
            slug.val(url_slug(firstName + '-' + inputVal));
        }
    });
});