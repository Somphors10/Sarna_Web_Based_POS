(function(window) {
    'use strict';

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
        if (!value || value.length < 8) {
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

        if (weakPasswords.indexOf(value.toLowerCase()) !== -1) {
            return false;
        }

        return true;
    }

    window.WBPOS = window.WBPOS || {};
    window.WBPOS.isStrongPassword = isStrongPassword;
}(window));
