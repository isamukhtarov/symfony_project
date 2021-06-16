$('#formAddPostTimeLine').submit(function (e) {
    e.preventDefault();

    let timeline = $('#timeline').val();

    if (!timeline.length) {
        alert($(this).data('empty-time'));
        return false;
    }

    $('#timeline').val('');

    let id = (CKEDITOR.instances['post_create_content'] === undefined) ? 'post_update_content' : 'post_create_content';

    CKEDITOR.instances[id].insertHtml(
        `<div class="time-divider">
            <span>${timeline}</span>
        </div>`
    );

    $('#addTimelineModal').modal('hide');
});