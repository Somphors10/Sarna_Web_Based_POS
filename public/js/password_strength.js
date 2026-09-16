(function(window, document) {
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

    var CHECK_SVG = '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="currentColor"></circle><path d="M8.5 12.5l2.2 2.2 4.8-5.2" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path></svg>';

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

        if (!/[^A-Za-z0-9]/.test(value)) {
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

    function scorePassword(value) {
        if (!value) {
            return 0;
        }

        var hasLetter = /[a-zA-Z]/.test(value);
        var hasNumber = /\d/.test(value);
        var hasSymbol = /[^A-Za-z0-9]/.test(value);
        var kinds = (hasLetter ? 1 : 0) + (hasNumber ? 1 : 0) + (hasSymbol ? 1 : 0);

        if (isStrongPassword(value)) {
            return 4;
        }

        if (kinds >= 2 && value.length >= 8) {
            return 3;
        }

        if (kinds >= 2 || value.length >= 8) {
            return 2;
        }

        return 1;
    }

    function shouldAttach(input) {
        if (!(input instanceof HTMLInputElement)) {
            return false;
        }

        var name = ((input.name || '') + ' ' + (input.id || '')).toLowerCase();
        if (/current|repeat|confirm|old_password/.test(name)) {
            return false;
        }

        if (input.closest('#login_form, .login-card, .login-page, .sa-login, form[action*="login"]')) {
            return false;
        }

        return true;
    }

    function levelClass(score) {
        if (score >= 4) {
            return 'is-excellent';
        }
        if (score === 3) {
            return 'is-good';
        }
        if (score === 2) {
            return 'is-fair';
        }
        if (score === 1) {
            return 'is-weak';
        }

        return 'is-empty';
    }

    function wrapOf(input) {
        return input.closest('.password-field-wrap, .lp-field-input-wrap, .input-group') || input.parentElement;
    }

    function attachMeter(input) {
        if (!shouldAttach(input) || input.dataset.strengthReady === '1') {
            return;
        }

        input.dataset.strengthReady = '1';

        var wrap = wrapOf(input);
        var meter = document.createElement('div');
        meter.className = 'password-strength is-empty';
        meter.hidden = true;
        meter.innerHTML = '<div class="password-strength__bars" aria-hidden="true"><span></span><span></span><span></span><span></span></div>';

        if (wrap && wrap.parentElement) {
            wrap.parentElement.insertBefore(meter, wrap.nextSibling);
        } else {
            input.insertAdjacentElement('afterend', meter);
        }

        var ok = wrap ? wrap.querySelector('.password-strength-ok') : null;
        if (wrap && !ok) {
            ok = document.createElement('span');
            ok.className = 'password-strength-ok';
            ok.innerHTML = CHECK_SVG;
            wrap.appendChild(ok);
        }

        var update = function() {
            var value = input.value || '';
            var score = scorePassword(value);
            var level = levelClass(score);

            meter.className = 'password-strength ' + level;
            meter.hidden = score === 0;

            input.classList.toggle('is-strength-excellent', score >= 4);
            input.classList.toggle('is-strength-weak', score === 1);
            if (wrap) {
                wrap.classList.toggle('is-strength-excellent', score >= 4);
            }
        };

        input.addEventListener('input', update);
        input.addEventListener('blur', update);
        update();
    }

    function scan(root) {
        (root || document).querySelectorAll('input[type="password"]').forEach(attachMeter);
    }

    window.WBPOS = window.WBPOS || {};
    window.WBPOS.isStrongPassword = isStrongPassword;
    window.WBPOS.scorePassword = scorePassword;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            scan(document);
        });
    } else {
        scan(document);
    }
}(window, document));
