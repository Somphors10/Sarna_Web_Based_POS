<?php
$confirm_delete_title = lang('Common.confirm_delete_title');
if ($confirm_delete_title === '' || $confirm_delete_title === 'Common.confirm_delete_title') {
    $confirm_delete_title = 'Confirm delete';
}

$confirm_restore_title = lang('Common.confirm_restore_title');
if ($confirm_restore_title === '' || $confirm_restore_title === 'Common.confirm_restore_title') {
    $confirm_restore_title = 'Confirm restore';
}

$label_delete = lang('Common.delete') ?: 'Delete';
$label_restore = lang('Common.restore') ?: 'Restore';
$label_cancel = lang('Datepicker.cancel') ?: 'Cancel';
?>
<style>
#pos-confirm.pos-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    padding: 1rem;
    font-family: "Khmer OS Siemreap", "Khmer OS", Siemreap, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
}

#pos-confirm.pos-modal-overlay.is-open {
    display: flex !important;
}

#pos-confirm .pos-modal {
    width: min(420px, 100%);
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 24px 50px rgba(15, 23, 42, 0.28);
    overflow: hidden;
    font-size: 15px;
    line-height: 1.5;
    color: #0f172a;
}

#pos-confirm .pos-modal__head {
    padding: 1.1rem 1.25rem 0.45rem;
    font-size: 18px;
    font-weight: 700;
    line-height: 1.3;
    color: #0f172a;
    letter-spacing: -0.01em;
}

#pos-confirm .pos-modal__body {
    padding: 0.35rem 1.25rem 1.1rem;
    font-size: 15px;
    font-weight: 400;
    color: #475569;
    line-height: 1.5;
}

#pos-confirm .pos-modal__actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.55rem;
    padding: 0.9rem 1.25rem 1.05rem;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
}

#pos-confirm .pos-modal-btn {
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

#pos-confirm .pos-modal-btn:active {
    transform: translateY(1px);
}

#pos-confirm .pos-modal-btn--ghost {
    background: #fff;
    color: #475569;
    border: 1px solid #e2e8f0;
}

#pos-confirm .pos-modal-btn--ghost:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

#pos-confirm .pos-modal-btn--danger-solid {
    background: #dc2626;
    color: #fff;
}

#pos-confirm .pos-modal-btn--danger-solid:hover {
    filter: brightness(1.05);
}

#pos-confirm .pos-modal-btn--primary-solid {
    background: #2563eb;
    color: #fff;
}

#pos-confirm .pos-modal-btn--primary-solid:hover {
    filter: brightness(1.05);
}
</style>

<div id="pos-confirm"
     class="pos-modal-overlay"
     aria-hidden="true"
     data-title-delete="<?= esc($confirm_delete_title) ?>"
     data-title-restore="<?= esc($confirm_restore_title) ?>"
     data-label-delete="<?= esc($label_delete) ?>"
     data-label-restore="<?= esc($label_restore) ?>"
     data-label-cancel="<?= esc($label_cancel) ?>">
    <div class="pos-modal" role="dialog" aria-modal="true" aria-labelledby="pos-confirm-title">
        <div class="pos-modal__head" id="pos-confirm-title"><?= esc($confirm_delete_title) ?></div>
        <div class="pos-modal__body" id="pos-confirm-message"></div>
        <div class="pos-modal__actions">
            <button type="button" class="pos-modal-btn pos-modal-btn--ghost" id="pos-confirm-cancel"><?= esc($label_cancel) ?></button>
            <button type="button" class="pos-modal-btn pos-modal-btn--danger-solid" id="pos-confirm-continue"><?= esc($label_delete) ?></button>
        </div>
    </div>
</div>

<script>
(function() {
    var overlay = document.getElementById('pos-confirm');
    if (!overlay) {
        return;
    }

    if (overlay.parentNode !== document.body) {
        document.body.appendChild(overlay);
    }

    var titleEl = document.getElementById('pos-confirm-title');
    var messageEl = document.getElementById('pos-confirm-message');
    var cancelBtn = document.getElementById('pos-confirm-cancel');
    var continueBtn = document.getElementById('pos-confirm-continue');
    var pendingCallback = null;

    var closeModal = function(confirmed) {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        var callback = pendingCallback;
        pendingCallback = null;
        if (typeof callback === 'function') {
            callback(!!confirmed);
        }
    };

    window.osposConfirm = function(options, callback) {
        options = options || {};
        var action = options.action === 'restore' ? 'restore' : 'delete';
        var isRestore = action === 'restore';
        var title = options.title
            || overlay.getAttribute(isRestore ? 'data-title-restore' : 'data-title-delete')
            || (isRestore ? 'Confirm restore' : 'Confirm delete');
        var confirmLabel = options.confirmLabel
            || overlay.getAttribute(isRestore ? 'data-label-restore' : 'data-label-delete')
            || (isRestore ? 'Restore' : 'Delete');
        var message = options.message || '';

        if (titleEl) {
            titleEl.textContent = title;
        }
        if (messageEl) {
            messageEl.textContent = message;
        }
        if (continueBtn) {
            continueBtn.textContent = confirmLabel;
            continueBtn.classList.toggle('pos-modal-btn--danger-solid', !isRestore);
            continueBtn.classList.toggle('pos-modal-btn--primary-solid', isRestore);
        }

        pendingCallback = typeof callback === 'function' ? callback : null;
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        if (continueBtn) {
            continueBtn.focus();
        }
        return false;
    };

    if (continueBtn) {
        continueBtn.addEventListener('click', function() {
            closeModal(true);
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            closeModal(false);
        });
    }

    overlay.addEventListener('click', function(event) {
        if (event.target === overlay) {
            closeModal(false);
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
            closeModal(false);
        }
    });
})();
</script>
