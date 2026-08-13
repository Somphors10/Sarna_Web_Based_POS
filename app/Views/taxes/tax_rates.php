<?php
/**
 * @var string $controller_name
 * @var string $tax_rate_table_headers
 * @var array $config
 */
?>

<script type="text/javascript">
    $(document).ready(function() {
        <?= view('partial/bootstrap_tables_locale') ?>
        table_support.init({
            resource: '<?= esc($controller_name) ?>',
            headers: <?= $tax_rate_table_headers ?>,
            pageSize: <?= table_page_size($config['lines_per_page']) ?>,
            uniqueId: 'tax_rate_id'
        });
    });
</script>

<div class="tax-rates-toolbar">
    <div class="tax-rates-toolbar__actions">
        <button id="delete" class="btn btn-default tax-rates-btn tax-rates-btn--danger">
            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            <?= lang('Common.delete') ?>
        </button>
        <button class="btn btn-primary tax-rates-btn modal-dlg" data-btn-submit="<?= lang('Common.submit') ?>" data-href="<?= esc("$controller_name/view") ?>" title="<?= lang(ucfirst($controller_name) . '.new') ?>">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?= lang(ucfirst($controller_name) . '.new') ?>
        </button>
    </div>
</div>

<div id="toolbar"></div>

<div id="table_holder" class="tax-rates-table-wrap">
    <table id="table"></table>
</div>
