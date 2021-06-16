$('#btnInsertMedia').click(function (e) {
    e.preventDefault();

    let content = $('#mediaText').val();
    if (!content.length) {
        alert('Media code must be filled.');
        return false;
    }

    let url = $(this).attr('data-url');

    let params = {'content': content};

    $.post(url, params, function (widget) {
        if (!widget.success) {
            alert(widget.message);
        } else {
            let id = (CKEDITOR.instances['post_create_content'] === undefined) ? 'post_update_content' : 'post_create_content';
            CKEDITOR.instances[id].insertText(widget.token);

            $('#addMediaModal').modal('hide');
        }
    });
});