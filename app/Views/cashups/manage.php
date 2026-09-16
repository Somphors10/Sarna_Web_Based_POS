<?php
/**
 * @var string $controller_name
 * @var string $table_headers
 * @var array $filters
 * @var array $config
 */
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#filters').on('change', function() {
            table_support.refresh();
        });
        $('#filters').on('hidden.bs.select', function(e) {
            table_support.refresh();
        });

        <?= view('partial/daterangepicker') ?>

        $("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {
            table_support.refresh();
        });

        <?= view('partial/soft_delete_list_script') ?>
        <?= view('partial/bootstrap_tables_locale') ?>

        table_support.init({
            resource: '<?= esc($controller_name) ?>',
            headers: <?= $table_headers ?>,
            pageSize: <?= table_page_size($config['lines_per_page']) ?>,
            uniqueId: 'cashup_id_key',
            queryParams: function() {
                var selectedFilters = mergeDeletedTableFilters($("#filters").val() || []);

                return $.extend(arguments[0], {
                    "end_date": end_date,
                    "filters": selectedFilters,
                    "start_date": start_date
                });
            }
        });
    });
</script>

<?= view('partial/print_receipt', ['print_after_sale' => false, 'selected_printer' => 'takings_printer']) ?>

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= ucfirst($controller_name) ?></h3>
        </div>
        <div id="title_bar" class="print_hide btn-toolbar neo-module-actions">
            <button onclick="javascript:printdoc()" class="btn btn-info btn-sm">
                <span class="glyphicon glyphicon-print">&nbsp;</span><?= lang('Common.print') ?>
            </button>
            <button class="btn btn-primary btn-sm modal-dlg" data-btn-submit="<?= lang('Common.submit') ?>" data-href="<?= "$controller_name/view" ?>" title="<?= lang(ucfirst($controller_name) . ".new") ?>">
                <span class="glyphicon glyphicon-tags">&nbsp;</span><?= lang(esc(ucfirst($controller_name)) . '.new') ?>
            </button>
        </div>
    </header>

    <?= view('partial/list_tabs') ?>

    <div id="toolbar" class="neo-table-toolbar">
        <div class="form-inline" role="toolbar">
            <button id="delete" class="btn btn-default btn-sm print_hide">
                <span class="glyphicon glyphicon-trash">&nbsp;</span><?= lang('Common.delete') ?>
            </button>
            <button id="restore" class="btn btn-default btn-sm print_hide hidden">
                <span class="glyphicon glyphicon-repeat">&nbsp;</span><?= lang('Common.restore') ?>
            </button>
            <?= form_input(['name' => 'daterangepicker', 'class' => 'form-control input-sm', 'id' => 'daterangepicker']) ?>
            <?php if (!empty($filters)): ?>
            <?= form_multiselect('filters[]', $filters, [''], [
                'id'                        => 'filters',
                'class'                     => 'form-control input-sm pos-items-filter-select',
                'size'                      => 1
            ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="table_holder" class="neo-table-holder">
        <table id="table"></table>
    </div>
</section>

<?= view('partial/footer') ?>
