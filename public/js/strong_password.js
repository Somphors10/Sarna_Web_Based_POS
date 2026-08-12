(function(window, $) {
    'use strict';

    if (!$ || !$.validator) {
        return;
    }

    var message = window.WBPOS_STRONG_PASSWORD_MESSAGE || 'Password must be at least 8 characters and include both letters and numbers.';

    function isStrongPassword(value) {
        if (window.WBPOS && typeof window.WBPOS.isStrongPassword === 'function') {
            return window.WBPOS.isStrongPassword(value);
        }

        if (value.length < 8) {
            return false;
        }

        if (!/[a-zA-Z]/.test(value)) {
            return false;
        }

        if (!/\d/.test(value)) {
            return false;
        }

        if (/^(.)\1+$/.test(value)) {
            return false;
        }

        return true;
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
