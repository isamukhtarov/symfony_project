// Ajax code will add here
$(document).ready(function () {
    $("#btnSaveQuote").click(function (e) {
        e.preventDefault();

        let expertId = $("#expert_quote_expertId").val();
        let text = CKEDITOR.instances['expert_quote_text'].getData();
        let postId = $("#expert_quote_postId").val();
        let language = $("#expert_quote_language").val();
        let url = $(this).attr('data-url');

        let data = {
            'expertId': expertId,
            'text': text,
            'postId': postId,
            'language' : language
        };

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: (response) => {
                let id = (CKEDITOR.instances['post_create_content'] === undefined) ? 'post_update_content' : 'post_create_content';

                if (typeof response.quote !== 'undefined') {
                    CKEDITOR.instances[id].insertText('{{expert-quote-' + response.quote.id + '}}')
                }
                $('#addExpertQuoteModal').modal('hide');
            },
            error: (error) => {
                alert(error.responseJSON.message)
            }
        });

        return false;
    })
})