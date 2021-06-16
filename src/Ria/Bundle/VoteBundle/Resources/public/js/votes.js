// let el = $('.sortable-with-handle').get(0);
//
// if (typeof el !== "undefined") {
//     window.Sortable.create(el, {
//         onSort: function (/**Event*/ evt) {
//             $('.sortable-with-handle .row').each(function (index, item) {
//                 $(item).parent().find('.sort').val(index);
//             });
//         }
//     });
// }
//
// $(".dynamicform_wrapper").on("afterInsert", function() {
//     $('.sortable-with-handle .row').each(function (index, item) {
//         $(item).parent().find('.sort').val(index);
//     });
// });


jQuery(document).ready(function () {
    let $optionCollectionHolder = $('div.container-items');

    $optionCollectionHolder.data('index', $optionCollectionHolder.find('input').length);

    let maxField = 100;

    let fieldCount = 1;

    $('.add-item-option').on('click', function (e) {
        if (fieldCount < maxField) {
            fieldCount++;
            let $collectionHolderClass = $(e.currentTarget).data('collectionHolderClass');
            addInputToCollection($collectionHolderClass);
        }
    })

    $('body').on('click','.remove-item', function () {
        console.log('Click')
        $(this).closest('.panel').remove();
    });
});

function addInputToCollection($collectionHolderClass) {
    let $collectionHolder = $('.' + $collectionHolderClass);

    let prototype = $collectionHolder.data('prototype');

    prototype = prototype.replace(prototype.substring(0, 4), '<div class="col-md-9"');
    let index = $collectionHolder.data('index');

    let newInput = prototype;

    newInput = newInput.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);

    let $fullInputHtml = '<div class="item panel">' +
        '<div class="panel-body p-10">' +
        '<div class="row">' +
        '<div class="col-md-1 d-flex align-items-center justify-content-center">' +
        '<i class="icon wb-list sortable-handle font-size-16" aria-hidden="true"></i>' +
        '</div>' +
            newInput +
        '<div class="col-md-2 d-flex align-items-center">' +
        '<button type="button" class="pull-right remove-item btn btn-danger"><i class="fa fa-minus"></i></button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';

    $collectionHolder.append($fullInputHtml);

}