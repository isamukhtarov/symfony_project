(function () {

    if (CKEDITOR.plugins.get('addVote')) {
        return false;
    }

    CKEDITOR.plugins.add('addVote', {
        lang: 'en',
        icons: 'addVote',
        hidpi: false,
        init: function (editor) {
            var pluginName = 'addVote';

            editor.addCommand(pluginName, {
                exec: function (e) {

                    $('#addVoteModal').modal('show').find('#mediaText').focus();
                    return false;
                }
            });

            editor.ui.addButton && editor.ui.addButton('addVote', {
                label: editor.lang.addVote.toolbar,
                command: pluginName,
                toolbar: 'doctools,50'
            });

        }
    });

})();