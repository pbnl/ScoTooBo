(function ($) {
    var settings;
    $.fn.zxcvbnProgress = function (options) {
        settings = $.extend({
            timeBox: null,
            timeText: null,
            ratings: ["Sehr schwach", "Schwach", "OK", "Stark", "Sehr stark"],
            progressClasses: ['bg-danger', 'bg-danger', 'bg-warning', 'bg-success', 'bg-success']
        }, options);
        var $passwordInput = $(settings.passwordInput),
            $progress = this,
            $ideaBox = $(settings.ideaBox),
            $ideaText = settings.ideaText;
        if (!settings.passwordInput) throw new TypeError('Please enter password input');
        $passwordInput.on('keyup', function () {
            updateProgress($passwordInput, $progress, $ideaBox, $ideaText);
        });
        updateProgress($passwordInput, $progress, $ideaBox, $ideaText);
    };
    function updateProgress($passwordInput, $progress, $ideaBox, $ideaText) {
        var passwordValue = $passwordInput.val();
        if (passwordValue) {
            var result = zxcvbn(passwordValue, settings.userInputs),
                score = result.score,
                scorePercentage = (score + 1) * 20;
            $progress.css('width', scorePercentage + '%');
            $progress.removeClass(settings.progressClasses.join(' ')).addClass(settings.progressClasses[score]).text(settings.ratings[score]);

        } else {
            $progress.css('width', 0 + '%');
            $progress.removeClass(settings.progressClasses.join(' ')).text('');
        }
        $ideaBox.text($ideaText + " " + result.feedback.suggestions.join("  "))
    }
})(jQuery);