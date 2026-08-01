<?php
/**
 * @var string $controller_name
 */

$empty_message_key = preg_match('/(customers|suppliers|employees)/', $controller_name)
    ? 'Common.no_persons_to_display'
    : ucfirst($controller_name) . '.no_' . $controller_name . '_to_display';
$empty_message = lang($empty_message_key);
if ($empty_message === '' || $empty_message === $empty_message_key) {
    $empty_message = 'No records to display.';
}

$confirm_module = ucfirst($editable ?? $controller_name);
$confirm_delete = lang($confirm_module . '.confirm_delete');
if ($confirm_delete === '' || $confirm_delete === $confirm_module . '.confirm_delete') {
    $confirm_delete = 'Are you sure you want to delete the selected row(s)?';
}
$confirm_restore = lang($confirm_module . '.confirm_restore');
if ($confirm_restore === '' || $confirm_restore === $confirm_module . '.confirm_restore') {
    $confirm_restore = 'Are you sure you want to restore the selected row(s)?';
}

$locale_code = current_language_code();
?>

(function($) {
    'use strict';

    $.fn.bootstrapTable.locales[<?= json_encode($locale_code) ?>] = {
        formatLoadingMessage: function() {
            return <?= json_encode(lang('Bootstrap_tables.loading')) ?>;
        },
        formatRecordsPerPage: function(pageNumber) {
            return <?= json_encode(lang('Bootstrap_tables.rows_per_page')) ?>.replace('{0}', pageNumber);
        },
        formatShowingRows: function(pageFrom, pageTo, totalRows) {
            return <?= json_encode(lang('Bootstrap_tables.page_from_to')) ?>
                .replace('{0}', pageFrom)
                .replace('{1}', pageTo)
                .replace('{2}', totalRows);
        },
        formatSearch: function() {
            return <?= json_encode(lang('Common.search')) ?>;
        },
        formatNoMatches: function() {
            return <?= json_encode($empty_message) ?>;
        },
        formatPaginationSwitch: function() {
            return <?= json_encode(lang('Bootstrap_tables.hide_show_pagination')) ?>;
        },
        formatRefresh: function() {
            return <?= json_encode(lang('Bootstrap_tables.refresh')) ?>;
        },
        formatToggle: function() {
            return <?= json_encode(lang('Bootstrap_tables.toggle')) ?>;
        },
        formatColumns: function() {
            return <?= json_encode(lang('Bootstrap_tables.columns')) ?>;
        },
        formatAllRows: function() {
            return <?= json_encode(lang('Bootstrap_tables.all')) ?>;
        },
        formatConfirmAction: function(action) {
            if (action === 'delete') {
                return <?= json_encode($confirm_delete) ?>;
            }

            return <?= json_encode($confirm_restore) ?>;
        }
    };

    $.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales[<?= json_encode($locale_code) ?>]);

})(jQuery);
