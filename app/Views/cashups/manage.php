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
        // When any filter is clicked and the dropdown window is closed
        $('#filters').on('hidden.bs.select', function(e) {
            table_support.refresh();
        });

        // Load the preset datarange picker
        <?= view('partial/daterangepicker') ?>

        $("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {
            table_support.refresh();
        });

        <?= view('partial/bootstrap_tables_locale') ?>

        table_support.init({
            resource: '<?= esc($controller_name) ?>',
            headers: <?= $table_headers ?>,
            pageSize: <?= $config['lines_per_page'] ?>,
            uniqueId: 'cashup_id',
            queryParams: function() {
                return $.extend(arguments[0], {
                    "end_date": end_date,
                    "filters": $("#filters").val(),
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
            <p class="neo-module-subtitle"><?= lang('Common.welcome_message') ?></p>
        </div>
        <div id="title_bar" class="print_hide btn-toolbar neo-module-actions">
            <button onclick="javascript:printdoc()" class="btn btn-info btn-sm">
                <span class="glyphicon glyphicon-print">&nbsp;</span><?= lang('Common.print') ?>
            </button>
            <button class="btn btn-primary btn-sm modal-dlg" data-btn-submit="<?= lang('Common.submit') ?>" data-href="<?= "$controller_name/view" ?>" title="<?= lang(ucfirst($controller_name) . ".new") ?>">
                <span class="glyphicon glyphicon-tags">&nbsp;</span><?= lang(esc(ucfirst($controller_name)) . '.new')    // TODO: String Interpolation ?>
            </button>
        </div>
    </header>

    <div id="toolbar" class="neo-table-toolbar">
        <div class="form-inline" role="toolbar">
            <button id="delete" class="btn btn-default btn-sm print_hide">
                <span class="glyphicon glyphicon-trash">&nbsp;</span><?= lang('Common.delete') ?>
            </button>
            <?= form_input(['name' => 'daterangepicker', 'class' => 'form-control input-sm', 'id' => 'daterangepicker']) ?>
            <?= form_multiselect('filters[]', $filters, [''], [
                'id'                        => 'filters',
                'data-none-selected-text'   => lang('Common.none_selected_text'),
                'class'                     => 'selectpicker show-menu-arrow',
                'data-selected-text-format' => 'count > 1',
                'data-style'                => 'btn-default btn-sm',
                'data-width'                => 'fit'
            ]) ?>
        </div>
    </div>

    <div id="table_holder" class="neo-table-holder">
        <table id="table"></table>
    </div>
</section>

<?= view('partial/footer') ?>
