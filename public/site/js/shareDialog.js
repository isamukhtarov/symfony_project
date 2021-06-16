var dialogLeft = (screen.width / 2) - (800 / 2);
var dialogTop = (screen.height / 2) - (600 / 2);

function openShareDialog(url) {
    window.open(url, 'shareWindow', 'status = 1, height = 600, width = 800, resizable = 0, top=' + dialogTop + ', left=' + dialogLeft);
}

$('.share-icons li > a').click(function (e) {
    if (!$(this).data('dialog')) {
        return true;
    }
    e.preventDefault();

    openShareDialog($(this).attr('href'));
    return false;
});