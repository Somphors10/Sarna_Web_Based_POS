<?php
/**
 * Active / Deleted tabs for manage lists (same pattern as Items).
 *
 * @var string $hide_when_deleted Optional CSS selector of extra toolbar buttons to hide on the Deleted tab
 */
$hide_when_deleted = $hide_when_deleted ?? '';
$tab_active_label = lang('Common.tab_active');
$tab_deleted_label = lang('Common.tab_deleted');
if ($tab_active_label === 'Common.tab_active') {
    $tab_active_label = 'Active';
}
if ($tab_deleted_label === 'Common.tab_deleted') {
    $tab_deleted_label = 'Deleted';
}
?>
<div class="neo-list-tabs" role="tablist" data-hide-when-deleted="<?= esc($hide_when_deleted) ?>" aria-label="<?= esc($tab_active_label . ' / ' . $tab_deleted_label) ?>">
    <button type="button" class="neo-list-tab is-active" data-list="active" role="tab" aria-selected="true"><?= esc($tab_active_label) ?></button>
    <button type="button" class="neo-list-tab" data-list="deleted" role="tab" aria-selected="false"><?= esc($tab_deleted_label) ?></button>
</div>
