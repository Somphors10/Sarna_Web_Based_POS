(function(window, $) {
    'use strict';

    if (!$ || !$.validator) {
        return;
    }

    var pattern = window.WBPOS_STRONG_PASSWORD_PATTERN || '^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,}$';
    var message = window.WBPOS_STRONG_PASSWORD_MESSAGE || 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.';

    if ($.validator.methods.strongPassword) {
        return;
    }

    $.validator.addMethod('strongPassword', function(value, element) {
        if (this.optional(element)) {
            return true;
        }

        return new RegExp(pattern).test(value);
    }, message);
}(window, window.jQuery));
