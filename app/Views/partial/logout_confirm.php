<style>
#pos-logout-confirm.pos-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    padding: 1rem;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
}

#pos-logout-confirm.pos-modal-overlay.is-open {
    display: flex !important;
}

#pos-logout-confirm .pos-modal {
    width: min(420px, 100%);
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 24px 50px rgba(15, 23, 42, 0.28);
    overflow: hidden;
    font-size: 15px;
    line-height: 1.5;
    color: #0f172a;
}

#pos-logout-confirm .pos-modal__head {
    padding: 1.1rem 1.25rem 0.45rem;
    font-size: 18px;
    font-weight: 700;
    line-height: 1.3;
    color: #0f172a;
    letter-spacing: -0.01em;
}

#pos-logout-confirm .pos-modal__body {
    padding: 0.35rem 1.25rem 1.1rem;
    font-size: 15px;
    font-weight: 400;
    color: #475569;
    line-height: 1.5;
}

#pos-logout-confirm .pos-modal__actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.55rem;
    padding: 0.9rem 1.25rem 1.05rem;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
}

#pos-logout-confirm .pos-modal-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 78px;
    height: 38px;
    padding: 0 16px;
    border: none;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    line-height: 1;
    cursor: pointer;
    transition: filter 0.15s ease, transform 0.1s ease, background 0.15s ease;
}

#pos-logout-confirm .pos-modal-btn:active {
    transform: translateY(1px);
}

#pos-logout-confirm .pos-modal-btn--ghost {
    background: #fff;
    color: #475569;
    border: 1px solid #e2e8f0;
}

#pos-logout-confirm .pos-modal-btn--ghost:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

#pos-logout-confirm .pos-modal-btn--danger-solid {
    background: #dc2626;
    color: #fff;
}

#pos-logout-confirm .pos-modal-btn--danger-solid:hover {
    filter: brightness(1.05);
}
</style>

<div id="pos-logout-confirm" class="pos-modal-overlay" aria-hidden="true">
    <div class="pos-modal" role="dialog" aria-modal="true" aria-labelledby="pos-logout-confirm-title">
        <div class="pos-modal__head" id="pos-logout-confirm-title"><?= lang('Login.confirm_logout_title') ?></div>
        <div class="pos-modal__body"><?= lang('Login.confirm_logout') ?></div>
        <div class="pos-modal__actions">
            <button type="button" class="pos-modal-btn pos-modal-btn--ghost" id="pos-logout-confirm-cancel"><?= lang('Datepicker.cancel') ?></button>
            <button type="button" class="pos-modal-btn pos-modal-btn--danger-solid" id="pos-logout-confirm-continue"><?= lang('Login.logout') ?></button>
        </div>
    </div>
</div>

<script>
(function() {
    var logoutOverlay = document.getElementById('pos-logout-confirm');
    if (!logoutOverlay) {
        return;
    }

    if (logoutOverlay.parentNode !== document.body) {
        document.body.appendChild(logoutOverlay);
    }

    var logoutCancelBtn = document.getElementById('pos-logout-confirm-cancel');
    var logoutContinueBtn = document.getElementById('pos-logout-confirm-continue');
    var pendingLogoutHref = null;

    var closeLogoutModal = function() {
        logoutOverlay.classList.remove('is-open');
        logoutOverlay.setAttribute('aria-hidden', 'true');
        pendingLogoutHref = null;
    };

    var openLogoutModal = function(href) {
        if (!href) {
            return false;
        }
        pendingLogoutHref = href;
        logoutOverlay.classList.add('is-open');
        logoutOverlay.setAttribute('aria-hidden', 'false');
        return false;
    };

    window.osposConfirmLogout = function(link) {
        var href = link.getAttribute('data-logout-url') || link.getAttribute('href');
        return openLogoutModal(href);
    };

    document.querySelectorAll('.pos-logout-link').forEach(function(link) {
        if (link.getAttribute('onclick')) {
            return;
        }
        link.addEventListener('click', function(event) {
            event.preventDefault();
            openLogoutModal(link.getAttribute('data-logout-url') || link.getAttribute('href'));
        });
    });

    if (logoutContinueBtn) {
        logoutContinueBtn.addEventListener('click', function() {
            if (!pendingLogoutHref) {
                closeLogoutModal();
                return;
            }
            window.location.assign(pendingLogoutHref);
        });
    }

    if (logoutCancelBtn) {
        logoutCancelBtn.addEventListener('click', closeLogoutModal);
    }

    logoutOverlay.addEventListener('click', function(event) {
        if (event.target === logoutOverlay) {
            closeLogoutModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && logoutOverlay.classList.contains('is-open')) {
            closeLogoutModal();
        }
    });
})();
</script>
