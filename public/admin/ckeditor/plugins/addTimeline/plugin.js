(function () {

    if (CKEDITOR.plugins.get('addTimeline')) {
        return false;
    }

    CKEDITOR.plugins.add('addTimeline', {
        lang: 'en',
        icons: 'addTimeline',
        hidpi: false,
        init: function (editor) {
            var pluginName = 'addTimeline';
            editor.addCommand(pluginName, {
                exec: function (e) {
                    $('#addTimelineModal').modal('show');
                    return false;
                }
            });

            editor.ui.addButton && editor.ui.addButton('addTimeline', {
                label: editor.lang.addTimeline.toolbar,
                command: pluginName,
                toolbar: 'doctools,50'
            });
        }
    });

    $('#addTimelineModal').on('shown.bs.modal', function () {
        $('#timeline').focus();
    });

})();