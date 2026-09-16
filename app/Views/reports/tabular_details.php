<?php
/**
 * @var string $title
 * @var string $subtitle
 * @var array $overall_summary_data
 * @var array $details_data
 * @var array $headers
 * @var array $summary_data
 * @var array $config
 */
?>

<?= view('partial/header') ?>

<link rel="stylesheet" href="css/reports.css?v=8">

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
        <?php foreach ($overall_summary_data as $name => $value) { ?>
            <div class="neo-report-summary__item summary_row" data-summary="<?= esc((string) $name, 'attr') ?>">
                <span class="neo-report-summary__label"><?= esc(lang("Reports.$name")) ?></span>
                <strong class="neo-report-summary__value"><?= to_currency($value) ?></strong>
            </div>
        <?php } ?>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function () {
        <?= view('partial/bootstrap_tables_locale') ?>

        var details_data = <?= json_encode(esc($details_data)) ?>;
        <?php if ($config['customer_reward_enable'] && !empty($details_data_rewards)) { ?>
            var details_data_rewards = <?= json_encode(esc($details_data_rewards)) ?>;
        <?php } ?>
        <?= view('partial/visibility_js') ?>

        var init_dialog = function () {
            dialog_support.init("a.modal-dlg");
        };

        $('#table')
            .addClass("table-striped")
            .bootstrapTable({
                columns: applyColumnVisibility(<?= transform_headers(esc($headers['summary']), true, false) ?>),
                pageSize: <?= table_page_size($config['lines_per_page']) ?>,
                pageList: <?= json_encode(table_page_list()) ?>,
                pagination: true,
                smartDisplay: false,
                paginationParts: ['pageInfo', 'pageSize', 'pageList'],
                paginationHAlign: 'right',
                paginationDetailHAlign: 'left',
                sortable: true,
                showColumns: true,
                uniqueId: 'id',
                showExport: true,
                exportDataType: 'all',
                exportTypes: ['csv', 'excel', 'pdf'],
                data: <?= json_encode($summary_data) ?>,
                iconSize: 'sm',
                paginationVAlign: 'bottom',
                detailView: true,
                escape: true,
                search: true,
                toolbar: '#toolbar',
                onPageChange: init_dialog,
                onPostBody: function () {
                    dialog_support.init("a.modal-dlg");
                    table_support.fix_toolbar_dropdowns($('#table'));
                    $('#table').closest('.bootstrap-table').find('a.detail-icon').each(function () {
                        var $btn = $(this);
                        $btn.attr('data-label-open', <?= json_encode(lang('Reports.row_show_items')) ?>);
                        $btn.attr('data-label-close', <?= json_encode(lang('Reports.row_hide_items')) ?>);
                        $btn.attr('title', <?= json_encode(lang('Reports.row_show_items')) ?>);
                    });
                },
                onExpandRow: function (index, row, $detail) {
                    $detail.html(
                        '<div class="neo-report-line-items">' +
                            '<div class="neo-report-line-items__title"><?= esc(lang('Reports.line_items_title'), 'js') ?></div>' +
                            '<table></table>' +
                        '</div>'
                    ).find('table').bootstrapTable({
                        columns: <?= transform_headers_readonly(esc($headers['details'])) ?>,
                        data: details_data[(!isNaN(row.id) && row.id) || $(row[0] || row.id).text().replace(
                            /(POS|RECV)\s*/g, '')]
                    });

                    <?php if ($config['customer_reward_enable'] && !empty($details_data_rewards)) { ?>
                        $detail.find('.neo-report-line-items').append('<table></table>').find('table').last().bootstrapTable({
                            columns: <?= transform_headers_readonly(esc($headers['details_rewards'])) ?>,
                            data: details_data_rewards[(!isNaN(row.id) && row.id) || $(row[0] || row.id).text().replace(
                                /(POS|RECV)\s*/g, '')]
                        });
                    <?php } ?>
                }
            });

        init_dialog();
        table_support.fix_toolbar_dropdowns($('#table'));
    });
</script>

<script src="<?= base_url('js/hide_cost_profit.js') ?>"></script>

<?= view('partial/footer') ?>
