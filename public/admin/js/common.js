function url_slug(s, opt) {
    s = String(s);
    opt = Object(opt);

    var defaults = {
        'delimiter': '-',
        'limit': undefined,
        'lowercase': true,
        'replacements': {},
        'transliterate': (typeof (XRegExp) === 'undefined')
    };

    // Merge options
    for (var k in defaults) {
        if (!opt.hasOwnProperty(k)) {
            opt[k] = defaults[k];
        }
    }

    var char_map = {
        // Latin
        'À': 'A', 'Á': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A', 'Æ': 'AE', 'Ç': 'C',
        'È': 'E', 'É': 'E', 'Ê': 'E', 'Ë': 'E', 'Ì': 'I', 'Í': 'I', 'Î': 'I', 'Ï': 'I',
        'Ð': 'D', 'Ñ': 'N', 'Ò': 'O', 'Ó': 'O', 'Ô': 'O', 'Õ': 'O', 'Ö': 'O', 'Ő': 'O',
        'Ø': 'O', 'Ù': 'U', 'Ú': 'U', 'Û': 'U', 'Ü': 'U', 'Ű': 'U', 'Ý': 'Y', 'Þ': 'TH',
        'ß': 'ss',
        'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a', 'æ': 'ae', 'ç': 'c',
        'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e', 'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i',
        'ð': 'd', 'ñ': 'n', 'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o', 'ő': 'o',
        'ø': 'o', 'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u', 'ű': 'u', 'ý': 'y', 'þ': 'th',
        'ÿ': 'y',

        // Georgian
        'ა' : 'a', 'ბ' : 'b', 'გ' : 'g', 'დ' : 'd', 'ე' : 'e',  'ვ' : 'v',  'ზ' : 'z',  'თ' : 't',  'ი' : 'i',
        'კ' : 'k', 'ლ' : 'l', 'მ' : 'm', 'ნ' : 'n', 'ო' : 'o',  'პ' : 'p',  'ჟ' : 'zh', 'რ' : 'r',  'ს' : 's',
        'ტ' : 't', 'უ' : 'u', 'ფ' : 'f', 'ქ' : 'k', 'ღ' : 'gh', 'ყ' : 'q',  'შ' : 'sh', 'ჩ' : 'ch', 'ც' : 'ts',
        'ძ' : 'dz', 'წ' : 'ts', 'ჭ' : 'ch', 'ხ' : 'kh', 'ჯ' : 'j', 'ჰ' : 'h',

        // Azerbaijani
        'Ş': 'S', 'İ': 'I', 'Ç': 'C', 'Ü': 'U', 'Ö': 'O', 'Ğ': 'G', 'Ə' : 'E',
        'ş': 's', 'ı': 'i', 'ç': 'c', 'ü': 'u', 'ö': 'o', 'ğ': 'g', 'ə' : 'e',

        // Russian
        'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'Yo', 'Ж': 'Zh',
        'З': 'Z', 'И': 'I', 'Й': 'J', 'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N', 'О': 'O',
        'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U', 'Ф': 'F', 'Х': 'H', 'Ц': 'C',
        'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Sh', 'Ъ': '', 'Ы': 'Y', 'Ь': '', 'Э': 'E', 'Ю': 'Yu',
        'Я': 'Ya',
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh',
        'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
        'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'c',
        'ч': 'ch', 'ш': 'sh', 'щ': 'sh', 'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu',
        'я': 'ya',

        // Ukrainian
        'Є': 'Ye', 'І': 'I', 'Ї': 'Yi', 'Ґ': 'G',
        'є': 'ye', 'і': 'i', 'ї': 'yi', 'ґ': 'g',
    };

    // Make custom replacements
    for (var k in opt.replacements) {
        s = s.replace(RegExp(k, 'g'), opt.replacements[k]);
    }

    // Transliterate characters to ASCII
    if (opt.transliterate) {
        for (var k in char_map) {
            s = s.replace(RegExp(k, 'g'), char_map[k]);
        }
    }

    // Replace non-alphanumeric characters with our delimiter
    var alnum = (typeof (XRegExp) === 'undefined') ? RegExp('[^a-z0-9]+', 'ig') : XRegExp('[^\\p{L}\\p{N}]+', 'ig');
    s = s.replace(alnum, opt.delimiter);

    // Remove duplicate delimiters
    s = s.replace(RegExp('[' + opt.delimiter + ']{2,}', 'g'), opt.delimiter);

    // Truncate slug to max. characters
    s = s.substring(0, opt.limit);

    // Remove delimiter from ends
    //s = s.replace(RegExp('(^' + opt.delimiter + '|' + opt.delimiter + '$)', 'g'), '');

    return opt.lowercase ? s.toLowerCase() : s;
}

var youtubeBlock = $('#youtube_id');
youtubeBlock.find('input').on('blur', function () {
    if ($(this).val() === '') {
        youtubeBlock.find('iframe').attr('src', '');
        return;
    }
    var id = getIdFromYoutubeUrl($(this).val());
    youtubeBlock.find('iframe').attr('src', 'https://www.youtube.com/embed/' + id);
    $(this).val(id);
});

function getIdFromYoutubeUrl(url) {
    var videoId = '';
    url = url.replace(/([><])/gi, '').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
    if (url[2] !== undefined) {
        videoId = url[2].split(/[^0-9a-z_\-]/i);
        videoId = videoId[0];
    } else {
        videoId = url.toString();
    }
    return videoId;
}

/**
 * Assign selected image from photomanager
 * @param array photos
 * @return string
 */
function renderPhotoManagerImage(photos) {
    // get first selected photo from photomanager widget
    let selectedPhoto = $(photos).first().find('img').attr('src');

    // split array with photo path, and get filename after last /
    let parts = selectedPhoto.split("/");
    let fileName = parts[parts.length - 1];

    // show selected image for user
    $('#profile-photo').attr('src', selectedPhoto);

    // set selected image filename value to hidden input for insert to db
    $('#photomanager-input').val(fileName);
}

function checkLength(length, min, max) {
    return (length < min || length > max);
}

var hasCounter = $('.has-counter');
hasCounter.bind('input', function () {
    var $counterTarget = $($(this).data('counter-target'));
    $counterTarget.text($(this).val().length);
    if ($(this).hasClass('check-length')) {
        if (checkLength($(this).val().length, $(this).data('min'), $(this).data('max'))) {
            $counterTarget.addClass('has-error');
        } else {
            $counterTarget.removeClass('has-error');
        }
    }
});

hasCounter.each(function () {
    var $counterTarget = $($(this).data('counter-target'));
    $counterTarget.text($(this).val().length);
});

// Configuring datepicker.
$('input[data-plugin="datetimepicker"]').datetimepicker({
    format: 'yyyy-mm-dd hh:ii:ss',
    todayHighlight: true,
    todayBtn: true,
    autoclose: true,
    orientation: 'topright',
});

$('input[date-plugin="datepicker"]').datepicker({
    format: 'yyyy-mm-dd',
    todayHighlight: true,
    todayBtn: true,
    autoclose: true,
    orientation: 'topright',
});

$('.form-input').on('change', function (){
    if ($(this).val().length > 0) {
        $(this).removeClass('empty');
    } else {
        $(this).addClass('empty');
    }
})

// Configuring select2.
$('select[data-selectable]').each(function (key, item) {
    const selector = $('#' + item.id);

    // Configuration.
    let preparedForXhr = typeof selector.data('xhr-route') !== 'undefined';
    let preparedForTags = typeof selector.data('tags') !== 'undefined';
    let preparedForMultiple = typeof selector.attr('multiple') !== 'undefined';
    let inputLength = (typeof selector.data('inputLength') !== 'undefined');
    let placeholder = (typeof selector.data('placeholder') !== 'undefined') ? selector.data('placeholder') : '-';

    let config = {placeholder: placeholder};

    if (inputLength)
        config.minimumInputLength = parseInt(selector.data('inputLength'));

    if (preparedForMultiple) {
        config.multiple = selector.attr('multiple');
        config.allowClear = true;
    }

    if (preparedForTags)
        config.tags = selector.data('tags');

    if (preparedForXhr) {
        config.ajax = {
            url: selector.attr('data-xhr-route'),
            dataType: 'json',
            quietMillis: 100,
            data: function (term) {
                let data = { language: selector.attr('data-language'), term: term.term };
                if (typeof selector.attr('data-type') !== "undefined") {
                    data.type = selector.attr('data-type');
                }
                return data;
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {id: item.id, text: item.text};
                    })
                };
            }
        }
    }

    // Initialization.
    selector.select2(config);
});