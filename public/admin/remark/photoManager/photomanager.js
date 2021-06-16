// Images selection
$('.files-list').on('click', '.files-list-item', function(e) {
    var _self = $(this);

    if (e.shiftKey) {
        selectRangeOfImages(_self);
    } else if(e.ctrlKey) {
        selectMultipleImages(_self);
    } else {
        selectSingleImage(_self);
    }
});

$(document).on('click', function(e) {
    if ($(e.target).is('.files-list')) {
        unselectAllImages();
    }

    if ($(e.target).is('.modal-fmanager')) {
        $('.modal-fmanager').fadeOut(250);
    }
});

function selectSingleImage(element) {
    $('.files-list .files-list-item').removeClass('selected');
    element.addClass('selected');
};

function selectMultipleImages(element) {
    element.addClass('selected');
};

function selectRangeOfImages(element) {
    element.addClass('selected');

    if ($('.selected').length >= 2) {
        var firstSelect = $('.selected').get(0);
        $(firstSelect).nextUntil('.selected').addClass('selected');
    }
};

function unselectAllImages() {
    $('.files-list .files-list-item').removeClass('selected');
};

$('.upload-images .btn').on('click', function() {
    $('#upload-images-field').trigger('click');
});

// Images search
$('.modal-photomanager .toggle-search').on('click', function() {
    $('.modal-photomanager .files-search').slideToggle(150);
});

// Update eimage info
$('.file-actions .trigger-update').on('click', function() {
    $('.update-file-info').slideDown(150);
});