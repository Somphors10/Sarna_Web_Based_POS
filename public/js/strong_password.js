(function(window, $) {
    'use strict';

    if (!$ || !$.validator) {
        return;
    }

    var message = window.WBPOS_STRONG_PASSWORD_MESSAGE || 'Please choose a stronger password (at least 8 characters, hard to guess).';

    function passwordStrengthScore(value) {
        var score = 0;

        if (value.length >= 8) {
            score++;
        }
        if (value.length >= 12) {
            score++;
        }
        if (value.length >= 16) {
            score++;
        }
        if (/[a-z]/.test(value)) {
            score++;
        }
        if (/[A-Z]/.test(value)) {
            score++;
        }
        if (/\d/.test(value)) {
            score++;
        }
        if (/[^A-Za-z0-9]/.test(value)) {
            score++;
        }

        return score;
    }

    var weakPasswords = [
        'password',
        '12345678',
        '123456789',
        '1234567890',
        'qwerty123',
        'admin123',
        'pointofsale',
        'password1',
        'letmein',
        'welcome1'
    ];

    function isStrongPassword(value) {
        if (value.length < 8) {
            return false;
        }

        if (/^(.)\1+$/.test(value)) {
            return false;
        }

        if (weakPasswords.indexOf(value.toLowerCase()) !== -1) {
            return false;
        }

        return passwordStrengthScore(value) >= 4;
    }

    if ($.validator.methods.strongPassword) {
        return;
    }

    $.validator.addMethod('strongPassword', function(value, element) {
        if (this.optional(element)) {
            return true;
        }

        return isStrongPassword(value);
    }, message);
}(window, window.jQuery));
