jQuery(document).ready(function ()  {

    $('.filter').on('change', function(e) {
        e.preventDefault();

        let timePicker = $('.date-filter');

        if ($('.filter').val() === 'spec_date') {
            console.log($('.filter').val());
            timePicker.removeAttr('disabled');
            return false;
        }else {
            timePicker.prop('disabled', 'disabled');
            timePicker.val('');
        }
    });
});