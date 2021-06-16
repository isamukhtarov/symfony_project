$('form.grid-filter input').keypress(function (e) {
    if (e.which == 13) {
        $('form.grid-filter').submit();
        return false;
    }
});

$('form.grid-filter select').on('change', function (e) {
    $('form.grid-filter').submit();
    return false;
});