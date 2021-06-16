(function () {

    if (CKEDITOR.plugins.get('addMedia')) {
        return false;
    }

    CKEDITOR.plugins.add('addMedia', {

        lang: 'en',
        icons: 'addMedia',
        hidpi: false,
        init: function (editor) {
            var pluginName = 'addMedia';

            editor.addCommand(pluginName, {
                exec: function (e) {

                    $('#addMediaModal').modal('show').find('#mediaText').focus();
                    return false;
                }
            });

            editor.ui.addButton && editor.ui.addButton('addMedia', {
                label: editor.lang.addMedia.toolbar,
                command: pluginName,
                toolbar: 'doctools,50'
            });

        }
    });

})();