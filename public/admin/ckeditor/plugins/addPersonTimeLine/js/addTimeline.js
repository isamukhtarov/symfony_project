let $timelineYear = $('#timelineYear'),
    $timelineText = $('#timelineText');

$('#btnInsertTimeline').click(function (e) {
    e.preventDefault();

    let year = $timelineYear.val();
    let content = $timelineText.val();

    if (!year.length || !content.length) {
        alert('Media year and content must be filled.');
        return false;
    }

    var instance = $('.nav-lang-tab.active')[0];
    var instanceLang = $(instance).attr('data-lang');
    var currentInstance = 'person_translations_' + instanceLang + '_text';

    $timelineYear.val('');
    $timelineText.val('');

    // var range = CKEDITOR.instances[currentInstance].createRange();
    // range.moveToElementEditEnd( range.root );
    // CKEDITOR.instances[currentInstance].getSelection().selectRanges( [ range ] );

    CKEDITOR.instances[currentInstance].insertHtml(
        `<div class="bio-item flex">
            <div class="year"><span>${year}</span></div>
            <div class="description">
                <p>${content}</p>
            </div>
        </div>`
    );

    $('#addTimelineModal').modal('hide');
});