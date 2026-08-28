<?php
/**
 * @var string $controller_name
 * @var string $table_headers
 * @var array $config
 */
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    $(document).ready(function() {
        <?= view('partial/soft_delete_list_script', ['hide_when_deleted' => '#generate_barcodes']) ?>
        <?= view('partial/bootstrap_tables_locale') ?>

        table_support.init({
            resource: '<?= esc($controller_name) ?>',
            headers: <?= $table_headers ?>,
            pageSize: <?= table_page_size($config['lines_per_page']) ?>,
            uniqueId: 'item_kit_id',
            queryParams: function() {
                var selectedFilters = mergeDeletedTableFilters([]);

                return $.extend(arguments[0], {
                    "filters": selectedFilters
                });
            }
        });

        $('#generate_barcodes').click(function() {
            window.open(
                'index.php/item_kits/generateBarcodes/' + table_support.selected_ids().join(':'),
                '_blank'
            );
        });
    });
</script>

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= ucfirst($controller_name) ?></h3>
            <p class="neo-module-subtitle"><?= lang('Common.welcome_message') ?></p>
        </div>
        <div id="title_bar" class="btn-toolbar neo-module-actions">
            <button class="btn btn-primary btn-sm modal-dlg" data-btn-submit="<?= lang('Common.submit') ?>" data-href="<?= esc("$controller_name/view") ?>" title="<?= lang(ucfirst($controller_name) . '.new') ?>">
                <span class="glyphicon glyphicon-tags">&nbsp;</span><?= lang(ucfirst($controller_name) . '.new') ?>
            </button>
        </div>
    </header>

    <?= view('partial/list_tabs', ['hide_when_deleted' => '#generate_barcodes']) ?>

    <div id="toolbar" class="neo-table-toolbar">
        <div class="btn-toolbar">
            <button id="delete" class="btn btn-default btn-sm">
                <span class="glyphicon glyphicon-trash">&nbsp;</span><?= lang('Common.delete') ?>
            </button>
            <button id="restore" class="btn btn-default btn-sm hidden">
                <span class="glyphicon glyphicon-repeat">&nbsp;</span><?= lang('Common.restore') ?>
            </button>
            <button id="generate_barcodes" class="btn btn-default btn-sm" data-href="<?= esc("$controller_name/generateBarcodes") ?>">
                <span class="glyphicon glyphicon-barcode">&nbsp;</span><?= lang('Items.generate_barcodes') ?>
            </button>
        </div>
    </div>

    <div id="table_holder" class="neo-table-holder">
        <table id="table"></table>
    </div>
</section>

<?= view('partial/footer') ?>
