$(document).ready(function () {
    let container = $('.quiz-wrapper');

    $.ajax({
        url: container.data('url'),
        type: "POST",
        dataType: 'json',
        success: function (response) {
            container.append(getRenderedDiv(response.vote));

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
        let recaptcha = $('#voteform-recaptcha').length > 0 ? $('#voteform-recaptcha').val() : null;
        let votedCount = container.find('.voted_count');
        if (typeof optionId === 'undefined') {
            return;
        }

        $.ajax({
            url: form.data('action'),
            type: "POST",
            dataType: 'json',
            data: {
                voteId: form.data('vote'),
                optionId: optionId,
                recaptcha: recaptcha,
            },
            success: function (response) {
                if (response.success) {
                    form.remove();
                    container.append(renderResults(response.data.vote, response.data.selected));
                    votedCount.html(parseInt(votedCount.html()) + 1);
                }
            }
        });
    });

});

function renderVote(vote) {
    let html = `<form data-action="/votes/add/" data-vote="${vote.id}">`;

    vote.options.forEach((option) => {
        html += `<div class="form-group">
                    <input type="radio" name="quiz" value="${option.id}" id="answer${option.id}">
                    <label for="answer${option.id}">${option.title}</label>
                 </div>`;
    });

    html += `
        ${vote.showRecaptcha 
        ? `<div class="form-group field-voteform-recaptcha">
                <input type="hidden" id="voteform-recaptcha" name="VoteForm[recaptcha]" value="">
           </div>`: ''}
           <div class="divider"></div>
           <button>${Yii.t('votes', 'do vote')}</button>
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

function getHtml(vote, content) {
    return `<h4 class="section-title">${Yii.t('votes','vote')}</h4>
                <div class="quiz-block">
                    <h4 class="title">${vote.title}</h4>
                    <div class="quiz-info flex">
                        ${vote.end_date 
                            ? `<span>${Yii.t('votes', 'end_time')} ${vote.end_date_long}</span>` 
                            : ''}
                        
                        <span>${Yii.t('votes', 'users_count', {count: vote.total_count})}</span>
                    </div>
                    ${content}
                </div>`;
}

function getRenderedDiv(vote) {
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
            return getHtml(vote, renderResults(vote, exist.option));
        }
    }

    return getHtml(vote, renderVote(vote));
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