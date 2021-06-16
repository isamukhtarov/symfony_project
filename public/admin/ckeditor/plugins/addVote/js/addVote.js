if ($('#voteId option').length) {
    $('#btnInsertVote').click(function (e) {
        e.preventDefault();

        let voteId = $('#voteId').val();

        let id = (CKEDITOR.instances['post_create_content'] === undefined) ? 'post_update_content' : 'post_create_content';

        CKEDITOR.instances[id].insertText(`{{vote-${voteId}}}`);

        $('#addVoteModal').modal('hide');
    });
} else {
    $('#btnInsertVote').prop('disabled', true);
}