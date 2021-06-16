if (typeof CKEDITOR !== 'undefined') {
    var $postContent = (CKEDITOR.instances['post_create_content'] === undefined)
        ? CKEDITOR.instances['post_update_content']
        : CKEDITOR.instances['post_create_content'];
}

$(document).ready(function () {
    const typeSelect = $('.type-select');
    if (typeSelect.length)
        disableAuthorOrExpertSelect(typeSelect);

    typeSelect.on('change', function () {
        disableAuthorOrExpertSelect(typeSelect);
    });

    function disableAuthorOrExpertSelect(typeSelect) {
        const currentVal = typeSelect.val();
        $('.type-depended').each(function () {
            const select = $(this);
            if (
                (select.data('accept') && select.data('accept') !== currentVal)
                ||
                (select.data('except') && select.data('except') === currentVal)
            ) {
                select.val('').closest('.form-group').hide();
            } else {
                select.closest('.form-group').slideDown();
            }
        });
    }

    /*$('body').on("click", ".nav-item", function (){
        $(".modal-body").scrollTop(0, 0);
    });*/
});

$('.delete-post').on('click', function (e) {
    e.preventDefault();
    if (deactivatePost($(this).attr('href'), 'deleted') === false)
        return false;
});

const getCause = msg => {
    let cause = prompt(msg);
    if (cause === null) {
        return false;
    } else if (cause === '') {
        cause = getCause(msg);
    }
    return cause;
}

function deactivatePost(requestUrl, status = null) {
    const cause = getCause('Укажите причину');
    if (cause === false) return false;
    let breakFormSubmit = false;

    $.ajax({
        type: 'POST',
        url: requestUrl,
        data: {'cause': cause, 'status': status},
        async: false,
    }).done(function (response) {
        if (response.status) {
            window.location.reload(false);
        } else {
            breakFormSubmit = true;
            alert(response.error);
        }
    }).fail(function (error) {
        console.log(error.message);
    });

    return !breakFormSubmit;
}

const form = $('#post-form');
let btnSubmitClicked = false;
form.on('submit', function () {
    if (btnSubmitClicked) {
        alert('Пожалуйста, имейте терпение, новость сохраняется...');
        return false;
    }
    btnSubmitClicked = true;
    const postWasPublished = +$('#postWasPublished').val();
    const postStatus = $.trim($('#post-status').val());

    if (postStatus === 'archived' && postWasPublished) {
        if (archivePost() === false) {
            btnSubmitClicked = false;
            return false;
        }
    }

    const isCreationOfTranslation = +$('#isCreationOfTranslation').val();
    if (!isCreationOfTranslation && postWasPublished && postStatus !== 'read') {
        const requestUrl = '/posts/delete/' + $('#postId').val();
        if (deactivatePost(requestUrl, postStatus) === false) {
            btnSubmitClicked = false;
            return false;
        }
    }

    $postContent.setData(removeUnwantedCharachters($postContent.getData()));
});

function archivePost() {
    const newLink = getCause('Укажите действительную ссылку на другую новость');
    if (newLink === false) return false;
    let breakFormSubmit = false;

    $.ajax({
        type: 'POST',
        url: '/posts/archive/' + $('#postId').val(),
        data: {link: newLink},
        async: false
    }).done(function (response) {
        if (response.status) {
            window.location.reload(false);
        } else {
            breakFormSubmit = true;
            alert(response.error);
        }
    }).fail(function (error) {
        console.log(error.message);
    });

    if (breakFormSubmit) return false;
}

let pending = false;
$('#show-preview').on('click', function (e) {
    e.preventDefault();
    if (pending) return false;
    let data = new FormData(form[0]);
    // data.append('PostForm[slug]', $('#postform-slug').val());
    // data.set('PostForm[content]', CKEDITOR.instances['with-photo-manager'].getData());

    $.ajax({
        type: 'POST',
        url: $(this).attr('href'),
        processData: false,
        contentType: false,
        data,
        beforeSend: function () {
            //pending = true;
        }
    }).done(function (response) {
        pending = false;
        if (response.success) {
            const win = window.open(response.data, '_blank');
            win.focus();
        } else {
            alert(response.error);
        }
    }).fail(function (error) {
        console.log(error.message);
    });
});

$(document).on('click', '#post-view-logs',function (e) {
    e.preventDefault();
    const link = $(this);
    const dataTarget = link.data('target');
    const modal = $(dataTarget);
    $.ajax({
        type: 'GET',
        url: link.attr('href'),
    }).done(function(response) {
        if (response.status) {
            modal.find('.modal-body').html(response.data);
            $(document).find('del, ins').closest('tr').show();
            modal.modal('show');
        } else {
            alert(response.error);
        }
    }).fail(function (error) {
        alert(error.message);
    });
});

$('#post-slug').bind('input', function () {
    let $self = $(this);
    let slug = url_slug($self.val());
    $self.val(slug);
});


function removeUnwantedCharachters(text) {
    text = text.replace('<p>&nbsp;</p>', '');
    var regex = /<(?!(br|\/br|p|\/p|a|\/a|strong|\/strong|sub|\/sub|sup|\/sup|table|\/table|tbody|\/tbody|tr|\/tr|th|\/th|td|\/td|h2|\/h2|section|\/section|div|\/div|span|\/span|ol|\/ol|li|\/li|ul|\/ul|em|\/em|img|\/img|blockquote|\/blockquote|hr|\/hr))([^>])+>/gi;

    return text.replace(regex, '').replace(/(<span>|<\/span>){2,}/gi, '');
}