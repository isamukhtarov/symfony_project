$(document).ready(function () {

    $('#btnUploadSpeech').click(function () {
        const thisElem = $(this);

        thisElem.text(thisElem.data('label-uploading'));

        let formData = new FormData();

        formData.append($('#speech-postId').attr('name'), $('#speech-postId').val());
        formData.append($('#speech-file').attr('name'), $('#speech-file').get(0).files[0]);

        if (thisElem.attr('data-id') !== null) {
            formData.append('id', thisElem.attr('data-id'));
        }

        let url = thisElem.attr('data-url');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: (response) => {
                if (response.success) {
                    $('.audioSpeech').html(response.player);
                    thisElem.attr('data-id', response.speech.id);
                    thisElem.attr('data-url', response.updateUrl)
                    $('#btnDeleteSpeech').parent().show();
                    $('#btnDeleteSpeech').attr('data-id', response.speech.id);
                    $('#speech-file').val('');
                } else {
                    alert(response.message)
                }
                thisElem.text(thisElem.data('label-upload'));
            },
            error: (error) => {
                console.log(error)
            }
        });
    });

    $('#btnDeleteSpeech').on('click', function (){
        const thisElem = $(this);

        let id =  thisElem.attr('data-id');
        let url = thisElem.attr('data-url');
        let data = {'id': id};

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: (response) => {
                if (response.success) {
                    $('.audioSpeech').empty();
                    $('#btnDeleteSpeech').parent().hide();
                    thisElem.attr('data-id', '');
                    $('#btnUploadSpeech').attr('data-id', '');
                    $('#btnUploadSpeech').attr('data-url', response.createUrl);
                } else {
                    alert(response.message)
                }
            },
            error: (error) => {
                console.log(error)
            }
        });
    });
});