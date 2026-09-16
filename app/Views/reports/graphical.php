<?php
/**
 * @var string $title
 * @var string $subtitle
 * @var string $chart_type
 * @var array $summary_data_1
 * @var bool $hide_chart_container
 * @var array $labels_1
 * @var array $series_data_1
 * @var string $yaxis_title
 * @var string $xaxis_title
 * @var bool $show_currency
 * @var string|null $chart_empty_message
 * @var string|null $chart_empty_hint
 * @var array $config
 */

helper('report');

$labels_1 = $labels_1 ?? [];
$series_data_1 = $series_data_1 ?? [];
$has_chart_data = report_chart_has_data($labels_1, $series_data_1);
$empty_message = $chart_empty_message ?? lang('Reports.graphical_no_chart_data');
$empty_hint = $chart_empty_hint ?? lang('Reports.graphical_no_chart_data_hint');

$chart_view_data = [
    'labels_1'      => $labels_1,
    'series_data_1' => $series_data_1,
    'yaxis_title'   => $yaxis_title ?? '',
    'xaxis_title'   => $xaxis_title ?? '',
    'show_currency' => $show_currency ?? false,
    'config'        => $config ?? config(\Config\OSPOS::class)->settings,
];
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

    <?php if ($has_chart_data): ?>
        <?php
            $list_charts = ['reports/graphs/hbar', 'reports/graphs/payment_breakdown'];
            $is_line_chart = ($chart_type === 'reports/graphs/line');
            $show_ct_box = empty($hide_chart_container) && !in_array($chart_type, $list_charts, true);
        ?>
        <?php if ($show_ct_box): ?>
            <div class="neo-report-chart-panel<?= $is_line_chart ? ' neo-report-chart-panel--line' : '' ?>">
                <div class="ct-chart<?= $is_line_chart ? '' : ' ct-golden-section' ?> neo-report-chart<?= $is_line_chart ? ' neo-report-chart--line' : '' ?>" id="chart1"></div>
            </div>
        <?php endif; ?>

        <?= view($chart_type, $chart_view_data) ?>
    <?php else: ?>
        <div class="neo-report-chart-empty" role="status">
            <strong><?= esc($empty_message) ?></strong>
            <p><?= esc($empty_hint) ?></p>
        </div>
    <?php endif; ?>

    <div class="neo-report-chart-footer">
        <div id="toolbar" class="neo-table-toolbar">
            <div class="form-inline" role="toolbar">
                <button id="toggleCostProfitButton" class="btn btn-default btn-sm print_hide">
                    <?= lang('Reports.toggle_cost_and_profit') ?>
                </button>
            </div>
        </div>

        <div id="chart_report_summary" class="neo-report-summary neo-report-summary--cards">
            <?php foreach ($summary_data_1 as $name => $value) { ?>
                <div class="neo-report-summary__item summary_row" data-summary="<?= esc((string) $name, 'attr') ?>">
                    <span class="neo-report-summary__label"><?= esc(lang("Reports.$name")) ?></span>
                    <strong class="neo-report-summary__value"><?= esc(report_format_graphical_summary_value((string) $name, $value)) ?></strong>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<script src="<?= base_url('js/hide_cost_profit.js') ?>"></script>

<?= view('partial/footer') ?>
