<?php
/**
 * @var string $title
 * @var string $subtitle
 * @var array $summary_data
 * @var array $headers
 * @var array $data
 * @var array $config
 */
?>

<?= view('partial/header') ?>

<link rel="stylesheet" href="css/reports.css?v=7">

<script type="text/javascript">
    dialog_support.init("a.modal-dlg");
</script>

<section class="neo-module-page neo-report-page">
    <?= view('reports/partial/report_header', [
        'title'    => $title,
        'subtitle' => $subtitle ?? '',
    ]) ?>

    <div id="toolbar" class="neo-table-toolbar">
        <div class="form-inline" role="toolbar">
            <button id="toggleCostProfitButton" class="btn btn-default btn-sm print_hide">
                <?= lang('Reports.toggle_cost_and_profit') ?>
            </button>
        </div>
    </div>

    <div id="table_holder" class="neo-table-holder neo-report-table-holder">
        <table id="table"></table>
    </div>

    <div id="report_summary" class="neo-report-summary neo-report-summary--cards">
    <?php
    foreach ($summary_data as $name => $value) {
        if ($name == "total_quantity") {
            ?>
            <div class="neo-report-summary__item summary_row" data-summary="<?= esc((string) $name, 'attr') ?>">
                <span class="neo-report-summary__label"><?= esc(lang("Reports.$name")) ?></span>
                <strong class="neo-report-summary__value"><?= esc($value) ?></strong>
            </div>
        <?php } else { ?>
            <div class="neo-report-summary__item summary_row" data-summary="<?= esc((string) $name, 'attr') ?>">
                <span class="neo-report-summary__label"><?= esc(lang("Reports.$name")) ?></span>
                <strong class="neo-report-summary__value"><?= to_currency($value) ?></strong>
            </div>
            <?php
        }
    }
    ?>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function () {
        <?= view('partial/bootstrap_tables_locale') ?>
        <?= view('partial/visibility_js') ?>

        $('#table')
            .addClass("table-striped")
            .bootstrapTable({
                columns: applyColumnVisibility(<?= transform_headers(esc($headers), true, false) ?>),
                pageSize: <?= table_page_size($config['lines_per_page']) ?>,
                pageList: <?= json_encode(table_page_list()) ?>,
                sortable: true,
                showExport: true,
                exportDataType: 'all',
                exportTypes: ['csv', 'excel', 'pdf'],
                pagination: true,
                smartDisplay: false,
                paginationParts: ['pageInfo', 'pageSize', 'pageList'],
                paginationHAlign: 'right',
                paginationDetailHAlign: 'left',
                showColumns: true,
                data: <?= json_encode($data) ?>,
                iconSize: 'sm',
                paginationVAlign: 'bottom',
                escape: true,
                search: true,
                toolbar: '#toolbar',
                onPostBody: function() {
                    table_support.fix_toolbar_dropdowns($('#table'));
                }
            });

        table_support.fix_toolbar_dropdowns($('#table'));
    });
</script>

<script src="<?= base_url('js/hide_cost_profit.js') ?>"></script>

<?= view('partial/footer') ?>
