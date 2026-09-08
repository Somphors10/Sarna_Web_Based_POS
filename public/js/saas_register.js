(function () {
    'use strict';

    const form = document.querySelector('.lp-reg__form');
    if (!form) {
        return;
    }

    const messages = {
        required: 'This field is required.',
        company_name: 'Enter at least 2 characters.',
        tenant_code: 'Enter a company code.',
        owner_first_name: 'Enter at least 2 characters.',
        owner_last_name: 'Enter at least 2 characters.',
        owner_email: 'Enter a valid email address.',
        owner_phone: 'Enter a valid phone number (min 8 digits).',
        owner_username: 'Username must be at least 4 characters.',
        owner_password: window.WBPOS_STRONG_PASSWORD_MESSAGE || 'Password must be at least 8 characters and include a letter, a number, and a symbol.',
        business_type: 'Select your business type.',
        address: 'Enter the store street address.',
        city: 'Enter city or province.',
        country: 'Enter your country.',
        tax_id: 'Enter your Tax ID / VAT TIN.',
        captcha_code: 'Enter the code from the picture.'
    };

    const requiredFields = form.querySelectorAll('.lp-field-input[required]');

    const getFieldContainer = function (input) {
        return input.closest('.col-md-6, .col-12, .col-md-4, .lp-captcha__field, .lp-captcha') || input.parentElement;
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
            if (name === 'tax_id' || !input.required) {
                clearFieldError(input);
                return true;
            }
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
                valid = value.length >= 1 && value.length <= 50;
                break;
            case 'owner_email':
                valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                break;
            case 'owner_phone':
                valid = value.replace(/\D/g, '').length >= 8;
                break;
            case 'owner_username':
                valid = value.length >= 4;
                break;
            case 'owner_password':
                valid = window.WBPOS && typeof window.WBPOS.isStrongPassword === 'function'
                    ? window.WBPOS.isStrongPassword(raw)
                    : raw.length >= 8;
                break;
            case 'address':
                valid = value.length >= 3;
                break;
            case 'tax_id':
                valid = value.length >= 3;
                break;
            case 'city':
                valid = value.length >= 2;
                break;
            case 'business_type':
            case 'country':
                valid = value.length >= 2;
                break;
            case 'captcha_code':
                valid = value.length >= 4;
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

        input.addEventListener('change', function () {
            if (input.classList.contains('is-invalid')) {
                validateField(input, true);
            }
        });

        input.addEventListener('blur', function () {
            validateField(input, true);
        });
    });

    const taxId = form.querySelector('[name="tax_id"]');
    if (taxId) {
        taxId.addEventListener('blur', function () {
            validateField(taxId, true);
        });
        taxId.addEventListener('input', function () {
            if (taxId.classList.contains('is-invalid')) {
                validateField(taxId, true);
            }
        });
    }

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

        if (taxId && !validateField(taxId, true)) {
            allValid = false;
            if (!firstInvalid) {
                firstInvalid = taxId;
            }
        }

        if (!allValid) {
            event.preventDefault();
            if (firstInvalid) {
                const searchWrap = firstInvalid.closest('[data-lp-search-select]');
                const focusTarget = searchWrap
                    ? searchWrap.querySelector('.lp-search-select__trigger')
                    : firstInvalid;
                if (focusTarget) {
                    focusTarget.focus();
                    focusTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
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

    const syncPlanPrice = function () {
        const selected = form.querySelector('input[name="plan_id"]:checked');
        if (!selected) {
            return;
        }

        const price = selected.getAttribute('data-price') || '0';
        document.querySelectorAll('[data-plan-price]').forEach(function (el) {
            el.textContent = price;
        });
    };

    form.querySelectorAll('input[name="plan_id"]').forEach(function (input) {
        input.addEventListener('change', syncPlanPrice);
    });
    syncPlanPrice();

    const captchaImg = document.getElementById('saas-captcha-img');
    const captchaRefresh = document.getElementById('saas-captcha-refresh');
    const reloadCaptcha = function () {
        if (!captchaImg) {
            return;
        }
        captchaImg.src = captchaImg.src.replace(/([?&]v=)\d+/, '$1' + Date.now());
        const input = form.querySelector('[name="captcha_code"]');
        if (input) {
            input.value = '';
            clearFieldError(input);
            input.focus();
        }
    };
    if (captchaImg) {
        captchaImg.addEventListener('click', reloadCaptcha);
    }
    if (captchaRefresh) {
        captchaRefresh.addEventListener('click', reloadCaptcha);
    }

    const initSearchSelect = function (wrap) {
        const select = wrap.querySelector('select');
        if (!select || wrap.querySelector('.lp-search-select__trigger')) {
            return;
        }

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'lp-search-select__trigger lp-field-input';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');

        const panel = document.createElement('div');
        panel.className = 'lp-search-select__panel';
        panel.hidden = true;

        const search = document.createElement('input');
        search.type = 'search';
        search.className = 'lp-search-select__search';
        search.placeholder = 'Search type…';
        search.setAttribute('autocomplete', 'off');
        search.setAttribute('aria-label', 'Search business type');

        const list = document.createElement('ul');
        list.className = 'lp-search-select__list';
        list.setAttribute('role', 'listbox');

        panel.appendChild(search);
        panel.appendChild(list);
        wrap.appendChild(trigger);
        wrap.appendChild(panel);
        select.classList.add('lp-search-select__native');

        const selectedLabel = function () {
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) {
                return 'Select type';
            }
            return opt.textContent;
        };

        const syncTrigger = function () {
            trigger.textContent = selectedLabel();
            trigger.classList.toggle('is-placeholder', !select.value);
            if (select.classList.contains('is-invalid')) {
                trigger.classList.add('is-invalid');
            } else {
                trigger.classList.remove('is-invalid');
            }
        };

        const buildList = function (query) {
            const q = (query || '').trim().toLowerCase();
            list.innerHTML = '';
            Array.prototype.forEach.call(select.options, function (opt) {
                if (!opt.value) {
                    return;
                }
                const label = opt.textContent;
                if (q && label.toLowerCase().indexOf(q) === -1 && String(opt.value).toLowerCase().indexOf(q) === -1) {
                    return;
                }
                const li = document.createElement('li');
                li.className = 'lp-search-select__option';
                li.setAttribute('role', 'option');
                li.setAttribute('data-value', opt.value);
                li.textContent = label;
                if (opt.selected) {
                    li.classList.add('is-active');
                    li.setAttribute('aria-selected', 'true');
                }
                li.addEventListener('click', function () {
                    select.value = opt.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    syncTrigger();
                    closePanel();
                    clearFieldError(select);
                    trigger.focus();
                });
                list.appendChild(li);
            });
            if (!list.children.length) {
                const empty = document.createElement('li');
                empty.className = 'lp-search-select__empty';
                empty.textContent = 'No matches';
                list.appendChild(empty);
            }
        };

        const openPanel = function () {
            panel.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            wrap.classList.add('is-open');
            search.value = '';
            buildList('');
            window.setTimeout(function () {
                search.focus();
            }, 0);
        };

        const closePanel = function () {
            panel.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
            wrap.classList.remove('is-open');
        };

        trigger.addEventListener('click', function () {
            if (panel.hidden) {
                openPanel();
            } else {
                closePanel();
            }
        });

        search.addEventListener('input', function () {
            buildList(search.value);
        });

        search.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closePanel();
                trigger.focus();
            }
        });

        document.addEventListener('click', function (event) {
            if (!wrap.contains(event.target)) {
                closePanel();
            }
        });

        select.addEventListener('invalid', function () {
            syncTrigger();
        });

        const observer = new MutationObserver(syncTrigger);
        observer.observe(select, { attributes: true, attributeFilter: ['class'] });

        syncTrigger();
    };

    form.querySelectorAll('[data-lp-search-select]').forEach(initSearchSelect);
})();
