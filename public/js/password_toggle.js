/**
 * Show/hide toggle for password fields (eye icon).
 */
(function () {
    'use strict';

    const SVG_EYE = '<svg class="password-toggle__icon password-toggle__icon--show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
    const SVG_EYE_OFF = '<svg class="password-toggle__icon password-toggle__icon--hide is-hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-8-10-8a18.45 18.45 0 0 1 5.06-6.94"></path><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 8 10 8a18.5 18.5 0 0 1-2.16 3.19"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';

    function createToggleButton(extraClass) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'js-password-toggle password-toggle__btn' + (extraClass ? ' ' + extraClass : '');
        button.setAttribute('aria-label', 'Show password');
        button.innerHTML = SVG_EYE + SVG_EYE_OFF;
        return button;
    }

    function bindToggle(button, input) {
        if (button.dataset.bound === '1') {
            return;
        }

        button.dataset.bound = '1';

        button.addEventListener('click', function () {
            const reveal = input.type === 'password';
            input.type = reveal ? 'text' : 'password';
            button.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
            button.classList.toggle('is-revealed', reveal);

            button.querySelectorAll('.password-toggle__icon--show, .icon-eye').forEach(function (el) {
                el.classList.toggle('is-hidden', reveal);
            });
            button.querySelectorAll('.password-toggle__icon--hide, .icon-eye-off').forEach(function (el) {
                el.classList.toggle('is-hidden', !reveal);
            });
        });
    }

    function enhancePasswordField(input) {
        if (!(input instanceof HTMLInputElement) || input.type !== 'password') {
            return;
        }

        if (input.dataset.passwordToggleReady === '1') {
            return;
        }

        input.dataset.passwordToggleReady = '1';
        input.classList.add('js-password-input');

        const parent = input.parentElement;
        if (!parent) {
            return;
        }

        const existingToggle = parent.querySelector('.js-password-toggle, .login-field__toggle');
        if (existingToggle) {
            existingToggle.classList.add('js-password-toggle');
            bindToggle(existingToggle, input);
            return;
        }

        if (parent.classList.contains('input-group')) {
            const addon = document.createElement('span');
            addon.className = 'input-group-addon input-sm password-toggle__addon';
            const toggle = createToggleButton();
            addon.appendChild(toggle);
            parent.appendChild(addon);
            bindToggle(toggle, input);
            return;
        }

        const isLoginField = parent.classList.contains('login-field') || parent.closest('.login-field');
        const isLpField = input.classList.contains('lp-field-input') || parent.closest('.lp-reg');

        const wrap = document.createElement('div');
        wrap.className = isLoginField
            ? 'login-field__input-wrap password-field-wrap'
            : (isLpField ? 'lp-field-input-wrap password-field-wrap' : 'password-field-wrap');

        parent.insertBefore(wrap, input);
        wrap.appendChild(input);

        const toggleClass = isLoginField
            ? 'login-field__toggle password-toggle__btn'
            : (isLpField ? 'lp-field-toggle password-toggle__btn' : 'password-toggle__btn');

        if (isLoginField) {
            input.classList.remove('login-field__input--text');
            input.classList.add('login-field__input');
        }

        const toggle = createToggleButton(toggleClass);
        wrap.appendChild(toggle);
        bindToggle(toggle, input);
    }

    function scanRoot(root) {
        if (!root || !root.querySelectorAll) {
            return;
        }

        root.querySelectorAll('input[type="password"]').forEach(enhancePasswordField);
    }

    function init() {
        scanRoot(document);

        if (!document.body || typeof MutationObserver === 'undefined') {
            return;
        }

        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) {
                        return;
                    }

                    if (node.matches && node.matches('input[type="password"]')) {
                        enhancePasswordField(node);
                    }

                    scanRoot(node);
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.enhancePasswordFields = scanRoot;
})();
