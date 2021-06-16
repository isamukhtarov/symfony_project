(function () {

    if (CKEDITOR.plugins.get('addExpertQuote')) {
        return false;
    }

    CKEDITOR.plugins.add('addExpertQuote', {

        lang: 'en',
        icons: 'addExpertQuote',
        hidpi: false,
        init: function (editor) {
            var pluginName = 'addExpertQuote';

            editor.addCommand(pluginName, {
                exec: function (e) {
                    $(".modal-body .select2-container").removeAttr("style");

                    let selectedText = getSelectedTextInPostEditor();

                    if (isQuoteToken(selectedText)) {
                        let quoteId  = getQuoteId(selectedText);
                        let language = $('#expert_quote_language').val();

                        $.get('/post/quote/get/' + quoteId + '/language/' + language, function (response) {
                            if (!response.success) {
                                alert(response.message);
                                return false;
                            }

                            $("#btnSaveQuote").attr('data-url', '/post/quote/' + quoteId + '/update');
                            setQuoteFormAttributes(response.quote);
                        });
                    } else {
                        $("#btnSaveQuote").attr('data-url', '/post/quote/create');
                        setQuoteFormAttributes({id: null, expert: {id: null}, text: ''});
                    }

                    $('#addExpertQuoteModal').modal('show');

                    return false;
                }
            });

            editor.ui.addButton && editor.ui.addButton('addExpertQuote', {
                label: editor.lang.addExpertQuote.toolbar,
                command: pluginName,
                toolbar: 'doctools,50'
            });

        }
    });

    function getSelectedTextInPostEditor() {
        let id = 'post_create_content';
        if (typeof CKEDITOR.instances['post_create_content'] === 'undefined') {
            id = 'post_update_content';
        }
        return CKEDITOR.instances[id].getSelection().getSelectedText();
    }

    function isQuoteToken(string) {
        return string.match(/{{expert-quote-\d+}}/);
    }

    function getQuoteId(token) {
        let regex = /{{expert-quote-(\d+)}}/;
        let match = regex.exec(token);
        return match[1];
    }

    function setQuoteFormAttributes(quote) {
        $('#expert_quote_id').val(quote.id);

        if (quote.expert.id === null) {
            $('#expert_quote_expertId').val(null).trigger('change');
        } else {
            let newOption = new Option(
                quote.expert.name,
                quote.expert.id,
                true,
                true
            );
            $('#expert_quote_expertId').append(newOption).trigger('change');
        }

        CKEDITOR.instances['expert_quote_text'].setData(quote.text);
    }

})();