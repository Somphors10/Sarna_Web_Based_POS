<?php
/**
 * Super Admin notification bell + panel (used on subscription pages and POS feature shell).
 *
 * @var list<array<string, mixed>> $notification_items
 * @var string $sa_notify_mode 'button' | 'panel'
 */

$notification_items = $notification_items ?? [];
$sa_notify_mode = $sa_notify_mode ?? 'panel';
$notify_count = count($notification_items);

if ($sa_notify_mode === 'button'): ?>
<button type="button" class="sa-notify-btn" id="sa_notify_btn" aria-label="Notifications" aria-expanded="false" aria-controls="sa_notify_panel">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
    </svg>
    <span class="sa-notify-badge<?= $notify_count > 0 ? '' : ' sa-notify-badge--hidden' ?>" id="sa_navbar_badge"><?= $notify_count ?></span>
</button>
<?php
    return;
endif;
?>

<div id="sa_notify_backdrop" class="sa-notify-backdrop" hidden aria-hidden="true"></div>
<aside id="sa_notify_panel" class="sa-notify-panel" aria-hidden="true" aria-labelledby="sa_notify_panel_title">
    <div class="sa-notify-panel__head">
        <div class="sa-notify-panel__title-row">
            <div class="sa-notify-panel__title-wrap">
                <h2 id="sa_notify_panel_title">Notifications</h2>
                <span class="sa-notify-panel__count<?= $notify_count > 0 ? '' : ' sa-notify-panel__count--hidden' ?>" id="sa_notify_panel_count"><?= $notify_count ?></span>
            </div>
            <button type="button" class="sa-notify-panel__close" id="sa_notify_close" aria-label="Close notifications">&times;</button>
        </div>
        <div class="sa-notify-panel__actions">
            <button type="button" class="sa-notify-panel__action sa-notify-panel__action--read" id="sa_notify_mark_read">Mark all as read</button>
            <button type="button" class="sa-notify-panel__action sa-notify-panel__action--clear" id="sa_notify_clear">Clear</button>
        </div>
    </div>
    <div class="sa-notify-panel__body">
        <ul class="sa-notify-panel__list" id="sa_notify_list">
            <?php foreach ($notification_items as $item): ?>
                <li class="sa-notify-card" data-notify-key="<?= esc($item['key'], 'attr') ?>">
                    <button type="button" class="sa-notify-card__dismiss" aria-label="Dismiss notification">&times;</button>
                    <div class="sa-notify-card__row">
                        <span class="sa-notify-card__icon sa-notify-card__icon--<?= esc($item['type']) ?>" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"></path><path d="M5 21V7l8-4v18"></path><path d="M19 21V11l-6-4"></path></svg>
                        </span>
                        <div class="sa-notify-card__head">
                            <strong><?= esc($item['title']) ?></strong>
                            <?php if (($item['relative_time'] ?? '') !== ''): ?>
                                <time datetime="<?= esc($item['created_at'], 'attr') ?>"><?= esc($item['relative_time']) ?></time>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (($item['subtitle'] ?? '') !== ''): ?>
                        <p class="sa-notify-card__subtitle"><?= esc($item['subtitle']) ?></p>
                    <?php endif; ?>
                    <p class="sa-notify-card__body"><?= esc($item['body']) ?></p>
                    <?php if (($item['meta'] ?? '') !== ''): ?>
                        <p class="sa-notify-card__meta"><?= esc($item['meta']) ?></p>
                    <?php endif; ?>
                    <a class="sa-notify-card__link" href="<?= esc($item['review_url'], 'attr') ?>">View details</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="sa-notify-panel__empty<?= empty($notification_items) ? '' : ' sa-notify-panel__empty--hidden' ?>" id="sa_notify_empty">No alerts right now.</p>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifyBtn = document.getElementById('sa_notify_btn');
    const notifyPanel = document.getElementById('sa_notify_panel');
    const notifyBackdrop = document.getElementById('sa_notify_backdrop');
    const notifyCloseBtn = document.getElementById('sa_notify_close');
    const notifyMarkReadBtn = document.getElementById('sa_notify_mark_read');
    const notifyClearBtn = document.getElementById('sa_notify_clear');
    const notifyList = document.getElementById('sa_notify_list');
    const notifyEmpty = document.getElementById('sa_notify_empty');
    if (!notifyBtn || !notifyPanel) {
        return;
    }

    const dismissedStorageKey = 'sa_dismissed_notifications';

    const getDismissedKeys = function() {
        try {
            const parsed = JSON.parse(sessionStorage.getItem(dismissedStorageKey) || '[]');
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    };

    const setDismissedKeys = function(keys) {
        sessionStorage.setItem(dismissedStorageKey, JSON.stringify(keys));
    };

    const getVisibleNotifyCards = function() {
        if (!notifyList) {
            return [];
        }
        return Array.from(notifyList.querySelectorAll('.sa-notify-card')).filter(function(card) {
            return card.style.display !== 'none';
        });
    };

    const updateNotifyCounts = function(total) {
        const count = Math.max(0, parseInt(total, 10) || 0);
        const navbarBadge = document.getElementById('sa_navbar_badge');
        const panelCount = document.getElementById('sa_notify_panel_count');
        if (navbarBadge) {
            navbarBadge.textContent = String(count);
            navbarBadge.classList.toggle('sa-notify-badge--hidden', count <= 0);
        }
        if (panelCount) {
            panelCount.textContent = String(count);
            panelCount.classList.toggle('sa-notify-panel__count--hidden', count <= 0);
        }
    };

    const syncNotifyEmptyState = function() {
        if (!notifyEmpty || !notifyList) {
            return;
        }
        const visibleCount = getVisibleNotifyCards().length;
        notifyEmpty.classList.toggle('sa-notify-panel__empty--hidden', visibleCount > 0);
        notifyList.hidden = visibleCount === 0;
    };

    const syncNotifyCountsFromVisible = function() {
        if (sessionStorage.getItem('sa_notifications_marked_read') === '1') {
            updateNotifyCounts(0);
            return;
        }
        updateNotifyCounts(getVisibleNotifyCards().length);
    };

    const applyDismissedNotifications = function() {
        if (!notifyList) {
            return;
        }
        const dismissed = getDismissedKeys();
        notifyList.querySelectorAll('.sa-notify-card').forEach(function(card) {
            const key = card.getAttribute('data-notify-key') || '';
            if (dismissed.indexOf(key) !== -1) {
                card.style.display = 'none';
            }
        });
        syncNotifyEmptyState();
        syncNotifyCountsFromVisible();
    };

    const setOpen = function(open) {
        notifyPanel.classList.toggle('is-open', open);
        notifyPanel.setAttribute('aria-hidden', open ? 'false' : 'true');
        notifyBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('sa-notify-open', open);
        if (notifyBackdrop) {
            notifyBackdrop.hidden = !open;
            notifyBackdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
        }
        if (open) {
            applyDismissedNotifications();
        }
    };

    notifyBtn.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        setOpen(!notifyPanel.classList.contains('is-open'));
    });

    if (notifyCloseBtn) {
        notifyCloseBtn.addEventListener('click', function() {
            setOpen(false);
        });
    }
    if (notifyBackdrop) {
        notifyBackdrop.addEventListener('click', function() {
            setOpen(false);
        });
    }
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });

    if (notifyList) {
        notifyList.addEventListener('click', function(event) {
            const dismissBtn = event.target.closest('.sa-notify-card__dismiss');
            if (!dismissBtn) {
                return;
            }
            const card = dismissBtn.closest('.sa-notify-card');
            if (!card) {
                return;
            }
            const key = card.getAttribute('data-notify-key') || '';
            if (key !== '') {
                const keys = getDismissedKeys();
                if (keys.indexOf(key) === -1) {
                    keys.push(key);
                    setDismissedKeys(keys);
                }
            }
            card.style.display = 'none';
            syncNotifyEmptyState();
            syncNotifyCountsFromVisible();
        });
    }

    if (notifyMarkReadBtn) {
        notifyMarkReadBtn.addEventListener('click', function() {
            sessionStorage.setItem('sa_notifications_marked_read', '1');
            updateNotifyCounts(0);
        });
    }

    if (notifyClearBtn) {
        notifyClearBtn.addEventListener('click', function() {
            if (!notifyList) {
                return;
            }
            const keys = getDismissedKeys();
            notifyList.querySelectorAll('.sa-notify-card').forEach(function(card) {
                const key = card.getAttribute('data-notify-key') || '';
                if (key !== '' && keys.indexOf(key) === -1) {
                    keys.push(key);
                }
                card.style.display = 'none';
            });
            setDismissedKeys(keys);
            sessionStorage.setItem('sa_notifications_marked_read', '1');
            syncNotifyEmptyState();
            updateNotifyCounts(0);
        });
    }

    applyDismissedNotifications();
});
</script>
