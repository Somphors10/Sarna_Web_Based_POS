(function () {
    'use strict';

    const form = document.querySelector('.lp-reg__form');
    if (!form) {
        return;
    }

    const messages = {
        required: 'This field is required.',
        company_name: 'Enter at least 2 characters.',
        tenant_code: 'Use letters, numbers, or dashes (min 3 characters).',
        owner_first_name: 'Enter at least 2 characters.',
        owner_last_name: 'Enter at least 2 characters.',
        owner_email: 'Enter a valid email address.',
        owner_username: 'Username must be at least 4 characters.',
        owner_password: 'Password must be at least 8 characters.',
        payment_reference: 'Enter your transaction or receipt ID (min 3 characters).'
    };

    const requiredFields = form.querySelectorAll('.lp-field-input[required]');

    const getFieldContainer = function (input) {
        return input.closest('.col-md-6, .col-12') || input.parentElement;
    };

    const getOrCreateErrorEl = function (input) {
        const container = getFieldContainer(input);
        if (!container) {
            return null;
        }

        let errorEl = container.querySelector('.lp-field-error');
        if (!errorEl) {
            errorEl = document.createElement('span');
            errorEl.className = 'lp-field-error';
            errorEl.setAttribute('role', 'alert');
            container.appendChild(errorEl);
        }

        return errorEl;
    };

    const setFieldError = function (input, message) {
        input.classList.add('is-invalid');
        input.setAttribute('aria-invalid', 'true');

        const errorEl = getOrCreateErrorEl(input);
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.hidden = false;
        }
    };

    const clearFieldError = function (input) {
        input.classList.remove('is-invalid');
        input.removeAttribute('aria-invalid');

        const container = getFieldContainer(input);
        const errorEl = container ? container.querySelector('.lp-field-error') : null;
        if (errorEl) {
            errorEl.textContent = '';
            errorEl.hidden = true;
        }
    };

    const validateField = function (input, showMessage) {
        const name = input.name;
        const raw = input.value;
        const value = input.type === 'password' ? raw : raw.trim();

        if (value === '') {
            if (showMessage) {
                setFieldError(input, messages.required);
            }
            return false;
        }

        let valid = true;
        let message = messages[name] || messages.required;

        switch (name) {
            case 'company_name':
            case 'owner_first_name':
            case 'owner_last_name':
                valid = value.length >= 2;
                break;
            case 'tenant_code':
                valid = /^[a-zA-Z0-9_-]+$/.test(value) && value.length >= 3;
                break;
            case 'owner_email':
                valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                break;
            case 'owner_username':
                valid = value.length >= 4;
                break;
            case 'owner_password':
                valid = raw.length >= 8;
                break;
            case 'payment_reference':
                valid = value.length >= 3;
                break;
            default:
                valid = true;
        }

        if (!valid) {
            if (showMessage) {
                setFieldError(input, message);
            }
            return false;
        }

        clearFieldError(input);
        return true;
    };

    requiredFields.forEach(function (input) {
        input.addEventListener('input', function () {
            if (input.classList.contains('is-invalid')) {
                validateField(input, true);
            }
        });

        input.addEventListener('blur', function () {
            validateField(input, true);
        });
    });

    form.addEventListener('submit', function (event) {
        let firstInvalid = null;
        let allValid = true;

        requiredFields.forEach(function (input) {
            if (!validateField(input, true)) {
                allValid = false;
                if (!firstInvalid) {
                    firstInvalid = input;
                }
            }
        });

        if (!allValid) {
            event.preventDefault();
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    const serverErrors = window.saasRegisterFieldErrors || {};
    Object.keys(serverErrors).forEach(function (fieldName) {
        const input = form.querySelector('[name="' + fieldName + '"]');
        if (input) {
            setFieldError(input, serverErrors[fieldName]);
        }
    });
})();
