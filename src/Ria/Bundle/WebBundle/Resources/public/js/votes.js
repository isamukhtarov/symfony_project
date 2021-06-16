$(document).ready(function () {
    let container = $('.quiz-wrapper');

    $.ajax({
        url: container.data('url'),
        type: "POST",
        dataType: 'json',
        success: function (response) {
            if (response.vote == null) {
                return;
            }

            container.append(getRenderedDiv(response.vote, response.voteAddUrl, response.votesArchiveUrl));
            container.show();

            if (response.vote.showRecaptcha) {
                $.getScript(`https://www.google.com/recaptcha/api.js?render=${gRecaptchaOptions.siteKey}`, function() {
                    grecaptcha.ready(function() {
                        grecaptcha.execute(gRecaptchaOptions.siteKey, {action: gRecaptchaOptions.action}).then(function(token) {
                            $('#voteform-recaptcha').val(token);
                        });

                        $('.grecaptcha-badge').css('display', 'none');
                    });
                });
            }
        }
    });

    $(document).on('click', '.quiz-block button', function (e) {
        e.preventDefault();

        let container = $(this).parents('.quiz-block');
        let form = container.find('form');
        let optionId = container.find('input[type=radio]:checked').val();
        let votedCount = container.find('.voted_count');
        if (typeof optionId === 'undefined') {
            return;
        }

        let data = {
            voteId: form.data('vote'),
            optionId: optionId
        };

        if ($('#voteform-recaptcha').length > 0) {
            data.recaptcha = $('#voteform-recaptcha').val()
        }

        $.ajax({
            url: form.data('action'),
            type: "POST",
            dataType: 'json',
            data: data,
            success: function (response) {
                if (response.success) {
                    form.remove();
                    container.append(renderResults(response.data.vote, response.data.selected));
                    votedCount.html(parseInt(votedCount.text()) + 1);
                }
            }
        });
    });

});

function renderVote(vote, voteAddUrl) {
    let html = `<form data-action="${voteAddUrl}" data-vote="${vote.id}">`;

    vote.options.forEach((option) => {
        html += `<div class="form-group">
                    <input type="radio" name="quiz" value="${option.id}" id="answer${option.id}">
                    <label for="answer${option.id}">${option.title}</label>
                 </div>`;
    });

    html += `
        ${vote.showRecaptcha
        ? `<div class="form-group field-voteform-recaptcha">
                <input type="hidden" id="voteform-recaptcha" name="recaptcha" value="">
           </div>`: ''}
           <div class="divider"></div>
           <button>${Translator.trans('do vote', {}, 'votes')}</button>
        </form>`;

    return html;
}

function renderResults(vote, selectedOption) {
    let html = `<div class="quiz-block__content"><div class="quiz-results">`;

    vote.options.forEach((option) => {
        html += `
            <div class="quiz-option${option.id == selectedOption ? ' selected' : ''}">
                <div class="percentage-indicator" style="width:${option.percentage}%;"></div>
                <p class="label">${option.title}</p>
                <p class="percent">${option.percentage}%</p>
            </div>
        `;
    });

    html += '</div></div>';

    return html;
}

function getHtml(vote, content, votesArchiveUrl) {
    return `<h4 class="section-title"><a href="${votesArchiveUrl}">${Translator.trans('vote', {}, 'votes')}</a></h4>
                <div class="quiz-block">
                    <h4 class="title">${vote.title}</h4>
                    <div class="quiz-info flex">
                        ${vote.end_date
        ? `<span>${Translator.trans('end_time', {}, 'votes')} &nbsp; ${vote.end_date_long}</span>`
        : ''}
                        
                        <span></span>
                    </div>
                    ${content}
                </div>`;
}

function getRenderedDiv(vote, voteAddUrl, votesArchiveUrl) {
    let votedCookie = getCookie('voted');

    if (votedCookie !== '') {
        let voted = Object.values(JSON.parse(unescape(votedCookie)));
        let exist = {};

        voted.forEach((cookie) => {
            if (cookie.vote === vote.id) {
                exist = cookie;
            }
        });

        if (Object.keys(exist).length !== 0) {
            return getHtml(vote, renderResults(vote, exist.option), votesArchiveUrl);
        }
    }

    return getHtml(vote, renderVote(vote, voteAddUrl), votesArchiveUrl);
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}