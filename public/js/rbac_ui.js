/**
 * Hide Create / Delete / Update controls when the role lacks that permission.
 * Works without rebuilding the gulp JS bundle.
 */
(function($, window) {
    'use strict';

    var crud = window.osposCrud || { create: true, read: true, update: true, delete: true };

    function denyMessage(action) {
        var map = window.osposCrudMessages || {};
        if (action && map[action]) {
            return map[action];
        }
        return window.osposCrudDenyMessage || 'You do not have permission to do this action.';
    }

    function notifyDenied(action) {
        var msg = denyMessage(action);
        if ($.notify) {
            $.notify(msg, { type: 'danger' });
        } else {
            window.alert(msg);
        }
    }

    function isNewRecordHref(href) {
        if (!href) {
            return false;
        }
        // .../view or .../view/ or .../view?-1  (no numeric id)
        var m = String(href).match(/\/view(?:\/(-?\d+))?(?:\?|$)/i);
        if (!m) {
            return /\/view\/?(\?|$)/i.test(href);
        }
        if (m[1] === undefined || m[1] === '' || m[1] === '-1') {
            return true;
        }
        return parseInt(m[1], 10) <= 0;
    }

    function applyToolbar() {
        if (crud.delete === false) {
            // Remove so Active/Deleted tab toggles cannot bring Delete back.
            $('#delete, #restore').remove();
        }

        if (crud.create === false) {
            $('#title_bar .modal-dlg, .neo-module-actions .modal-dlg, #new_customer_btn').each(function() {
                var $el = $(this);
                var href = $el.attr('data-href') || $el.attr('href') || '';
                if (isNewRecordHref(href) || $el.is('#new_customer_btn')) {
                    $el.remove();
                }
            });
        }

        if (crud.update === false) {
            $('#bulk_edit').remove();
        }
    }

    function applyTableActions() {
        if (crud.update === false) {
            $('#table a.modal-dlg').not('.ospos-view-only').each(function() {
                var $a = $(this);
                if ($a.find('.glyphicon-edit').length || ($a.attr('title') || '').toLowerCase().indexOf('edit') >= 0) {
                    $a.addClass('hidden').attr('aria-hidden', 'true');
                }
            });
        }
    }

    function guardClicks() {
        $(document).on('click', '#delete', function(e) {
            if (crud.delete === false) {
                e.preventDefault();
                e.stopImmediatePropagation();
                notifyDenied('delete');
                return false;
            }
        });

        $(document).on('click', '#restore', function(e) {
            if (crud.delete === false) {
                e.preventDefault();
                e.stopImmediatePropagation();
                notifyDenied('delete');
                return false;
            }
        });

        $(document).on('click', '#title_bar .modal-dlg, .neo-module-actions .modal-dlg, #new_customer_btn', function(e) {
            if (crud.create === false) {
                var href = $(this).attr('data-href') || $(this).attr('href') || '';
                if (isNewRecordHref(href) || $(this).is('#new_customer_btn')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    notifyDenied('create');
                    return false;
                }
            }
        });
    }

    window.osposNotifyDenied = notifyDenied;
    window.osposApplyCrudUi = function() {
        applyToolbar();
        applyTableActions();
    };

    $(function() {
        applyToolbar();
        guardClicks();
        // Table rows load async — re-hide edit links after each draw.
        $(document).on('post-body.bs.table load-success.bs.table', '#table', applyTableActions);
        setTimeout(applyTableActions, 800);
    });
})(jQuery, window);
