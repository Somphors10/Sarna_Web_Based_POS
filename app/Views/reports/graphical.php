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

<link rel="stylesheet" href="css/reports.css?v=2">

<script type="text/javascript">
    dialog_support.init("a.modal-dlg");
</script>

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <div class="neo-report-breadcrumb" aria-label="breadcrumb">
                <a href="<?= site_url('reports') ?>"><?= lang('Module.reports') ?></a>
                <span class="neo-report-breadcrumb-sep">/</span>
                <span class="neo-report-breadcrumb-current"><?= esc($title) ?></span>
            </div>
            <h3 class="neo-module-title"><?= esc($title) ?></h3>
            <?php if (!empty($subtitle)): ?>
                <p class="neo-module-subtitle"><?= esc($subtitle) ?></p>
            <?php endif; ?>
        </div>
    </header>

    <?php if ($has_chart_data): ?>
        <?php if (empty($hide_chart_container)): ?>
            <div class="ct-chart ct-golden-section neo-report-chart" id="chart1"></div>
        <?php endif; ?>

        <?= view($chart_type, $chart_view_data) ?>
    <?php else: ?>
        <div class="neo-report-chart-empty" role="status">
            <strong><?= esc($empty_message) ?></strong>
            <p><?= esc($empty_hint) ?></p>
        </div>
    <?php endif; ?>

    <div id="toolbar" class="neo-table-toolbar">
        <div class="form-inline" role="toolbar">
            <button id="toggleCostProfitButton" class="btn btn-default btn-sm print_hide">
                <?= lang('Reports.toggle_cost_and_profit') ?>
            </button>
        </div>
    </div>

    <div id="chart_report_summary" class="neo-report-summary">
        <?php foreach ($summary_data_1 as $name => $value) { ?>
            <div class="summary_row">
                <?= esc(lang("Reports.$name")) . ': ' . report_format_graphical_summary_value((string) $name, $value) ?>
            </div>
        <?php } ?>
    </div>
</section>

<script src="<?= base_url('js/hide_cost_profit.js') ?>"></script>

<?= view('partial/footer') ?>
