const managerModal = $('#photo-manager');
const uploadedImagesField = $('#upload-images-field');
const filesList = $('.files-list');
const searchForm = $('#files-search-form');

const insertModal = $('#photo-insert');

const updateModal = $('#photo-update');

const cropModal = $('#photo-crop');

let searchQuery = '';

managerModal.find('.toggle-search').on('click', function () {
    // noinspection JSValidateTypes
    managerModal.find('.files-search').slideToggle(150);
});

managerModal.find('.upload-images .btn').on('click', function () {
    uploadedImagesField.data('with-logo', $(this).hasClass('btnUploadWithWatermark'));

    managerModal.find('.upload-images input').trigger('click');
});

managerModal.on('show.bs.modal', function () {
    loadList(managerModal.data('url'));
});

// Infinite scroll
let ajaxRunning = false;
let scrollSum = 0;
let photosFinished = false;
$("#photo-manager .modal-body").on("scroll", function () {
    scrollSum = Math.ceil($(this).scrollTop() + $(this).innerHeight() + 350);
    if (scrollSum >= $(this)[0].scrollHeight && !ajaxRunning && !photosFinished) {
        request(
            managerModal.data('url'),
            'GET',
            {
                created_at: filesList.find('.files-list-item:last').attr('data-datetime'),
                q: searchQuery
            },
            res => {
                ajaxRunning = false;
                if (res.content === '') {
                    photosFinished = true;
                } else {
                    filesList.append(res.content);
                }
            },
            {
                beforeSend: () => ajaxRunning = true
            }
        );
    }
});

filesList.on('click', '.files-list-item', function (e) {
    if (!e.ctrlKey && !e.shiftKey) {
        filesList.find('.files-list-item').removeClass('selected');
    }

    $(this).addClass('selected');

    if (e.shiftKey) {
        const selected = $('.selected');
        if (selected.length >= 2) {
            const firstSelect = selected.get(0);
            $(firstSelect).nextUntil('.selected').addClass('selected');
        }
    }
});

$(document).on('click', '.trigger-update', function () {
    request($(this).attr('data-url'), 'GET', null, res => {
        updateModal.find('.modal-content').html('').append(res.form);
        updateModal.modal('show');
    });
});

$(document).on('click', '.trigger-crop', function () {
    const item = $(this).closest('.files-list-item');
    const btn = $(this);

    // noinspection HtmlRequiredAltAttribute,RequiredAttributes
    const img = $('<img>', {
        'class': 'img-default img-to-crop',
        'src': item.attr('data-src') + '?' + (new Date()).getTime(),
        'data-id': item.attr('data-id'),
        'data-url': btn.attr('data-url'),
    });

    cropModal.find('.modal-body > div').empty().append(img).end().modal('show');

    cropModal.on('shown.bs.modal', function () {
        img.cropper({
            checkOrientation: false,
            checkCrossOrigin: false,
            resizable: true,
            responsive: true,
            aspectRatio: 16 / 9
        });
    })
});

$('.btn-save-crop').on('click', function () {
    const img = cropModal.find('.img-default');
    const cropData = img.cropper('getData');
    const btn = $(this);

    request(
        img.data('url'),
        'POST',
        {
            'x': cropData['x'],
            'y': cropData['y'],
            'width': cropData['width'],
            'height': cropData['height']
        },
        res => {
            if (res.success) {
                const old = $('.files-list-item[data-id=' + img.attr('data-id') + '] img');
                old.attr('src', old.attr('src') + '?' + (new Date()).getTime());
                cropModal.modal('hide');
            } else {
                // noinspection JSValidateTypes
                alert(res.errors);
            }
        },
        {
            dataType: 'json',
            async: true,
            cache: false,
        }
    );
});

$('.btn-add-image').on('click', function () {
    const selectedIds = $('.selected').map((idx, item) => { return $(item).data('id') });
    const btn = $(this);

    if (selectedIds.length < 1) return false;

    request(
        btn.data('url'),
        'POST',
        {
            ids: Array.from(selectedIds),
            formName: $('#form_name').data('formName'),
            main: $('.main-radio:checked').val()
        },
        res => {
            $('.post-images').append(res.content);
            const main = $(document).find('.main-radio');
            if (!main.is(':checked')) {
                main.first().prop('checked', true);
            }
            managerModal.modal('hide');
        },
    );
});

$('.btn-attach-image').on('click', function () {
    const selected = $('.selected');
    if (selected.length < 1) return false;

    const photo = selected.length > 1 ? selected[0] : selected;

    $('#photo-input').val(photo.data('id'));
    $('#photo-img').attr('src', photo.data('src'));
    managerModal.modal('hide');
});

$(document).on('click', '.update-photo-form-submit', function (e) {
    e.preventDefault();
    const form = $(this).closest('.update-photo-form');

    request(
        form.attr('action'),
        'POST',
        form.serializeArray(),
        (res) => {
            if (res.success) {
                updateModal.modal('hide');
            } else {
                // noinspection JSUnresolvedFunction
                // form.yiiActiveForm('updateMessages', res.errors, true);
            }
        }
    );
});

$('.btn-delete-image').on('click', function () {
    const selected = filesList.find('.selected');
    const btn = $(this);

    if (selected.length < 1) return false;

    if (!confirm('Вы действительно хотите удалить выбранные фотографии?')) {
        return false;
    }

    for (let item of selected) {
        const photo = $(item);
        // noinspection JSUnusedGlobalSymbols
        request(
            btn.data('url') + '?' + $.param({id: photo.attr('data-id')}),
            'POST',
            {},
            () => {
                btn.prop('disabled', false);
                photo.remove();
            },
            {
                beforeSend: () => btn.prop('disabled', true),
            }
        )
    }
});

uploadedImagesField.on('change', function (e) {
    const withLogo = !!uploadedImagesField.data('with-logo');

    let $btnUpload = withLogo ? $('.btnUploadWithWatermark') : $('.btnUploadWithoutWatermark');
    $btnUpload.text($btnUpload.data('uploading'));
    $('.upload-images button').prop('disabled', true);

    const inp = $(this);
    for (let file of e.target.files) {
        let data = new FormData();
        data.append('image', file);
        data.append('withLogo', withLogo ? '1' : '0');

        request(
            inp.attr('data-url'),
            'POST',
            data,
            res => {
                if (res.success) {
                    // noinspection JSUnresolvedVariable
                    filesList.prepend(res.photo)

                } else {
                    // noinspection JSValidateTypes
                    alert(res.error);
                }

                $('.upload-images button').prop('disabled', false);
                $btnUpload.text($btnUpload.data('upload'));
            },
            {
                dataType: 'json',
                async: true,
                cache: false,
                contentType: false,
                processData: false,
            }
        );
    }
});

// searchForm.on('submit', function (e) {
//     e.preventDefault();
//     console.log('submit');
//     searchQuery = $.trim(searchForm.find('input').val());
//     loadList(searchForm.attr('action'), {q: searchQuery});
// });

$('#search-button').on('click', function (e) {
    e.preventDefault();
    let photosSearchBlock = $('#photos-search');
    searchQuery = $.trim(photosSearchBlock.find('input').val());
    loadList($(this).attr('data-url'), {q: searchQuery});
})

$(document).on('click', '.btn-remove-card', function () {
    const card = $(this).closest('.photo-thumb-wrapper');
    if (card.find('.main-radio').prop('checked')) {
        $('#empty-main').prop('checked', true);
    }
    card.remove();
});

$(document).on('click', '.btn-insert', function () {
    let id = (CKEDITOR.instances['post_create_content'] === undefined) ? 'post_update_content' : 'post_create_content';

    CKEDITOR.instances[id].insertText('{{photo-big-' + $(this).data('id') + '}}');
});

$('.photo-insert-form').on('submit', function (e) {
    e.preventDefault();
    const form = $(this)[0];
    const data = new FormData(form);
    // noinspection JSUnresolvedVariable,ES6ModulesDependencies
    CKEDITOR.instances['with-photo-manager'].insertText('{{photo-' + data.get('size') + '-' + data.get('id') + '}}');
    form.reset();
    insertModal.modal('hide');
});

$('.post-images').sortable({
    items: '.card',
    handle: '.sort',
}).disableSelection();

const loadList = (url, data = {}) => {
    request(url, 'GET', data, res => filesList.html(res.content));
}


const request = (url, method, data, onSuccess, opts) => {
    $.ajax({
        type: method,
        url,
        data,
        ...opts
    }).done(function (response) {
        onSuccess(response);
    }).fail(function (error) {
        console.log(error.message);
    });
}
