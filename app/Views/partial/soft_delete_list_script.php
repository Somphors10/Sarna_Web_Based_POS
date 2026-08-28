<?php
/**
 * Active / Deleted tabs — same JavaScript flow as items/manage.php.
 *
 * @var string $hide_when_deleted Optional CSS selector of toolbar buttons to hide on Deleted tab
 */
$hide_when_deleted = $hide_when_deleted ?? '';
?>
        var recordListView = 'active';
        var hideWhenDeleted = <?= json_encode($hide_when_deleted) ?>;

        var setRecordListView = function(view) {
            recordListView = view === 'deleted' ? 'deleted' : 'active';
            $('.neo-list-tab').removeClass('is-active').attr('aria-selected', 'false');
            $('.neo-list-tab[data-list="' + recordListView + '"]').addClass('is-active').attr('aria-selected', 'true');
            $('#delete').toggleClass('hidden', recordListView === 'deleted');
            $('#restore').toggleClass('hidden', recordListView !== 'deleted');
            if (hideWhenDeleted) {
                $(hideWhenDeleted).toggleClass('hidden', recordListView === 'deleted');
            }
            table_support.refresh();
        };

        $(document).on('click', '.neo-list-tab', function() {
            setRecordListView($(this).data('list'));
        });

        var mergeDeletedTableFilters = function(selectedFilters) {
            selectedFilters = selectedFilters || [];
            if (!Array.isArray(selectedFilters)) {
                selectedFilters = selectedFilters ? [selectedFilters] : [];
            }
            selectedFilters = selectedFilters.filter(function(filter) {
                return filter && filter !== 'is_deleted';
            });
            if (recordListView === 'deleted') {
                selectedFilters.push('is_deleted');
            }

            return selectedFilters;
        };
