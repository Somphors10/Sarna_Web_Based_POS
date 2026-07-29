<?php
/**
 * @var string $title
 * @var string $subtitle
 * @var string $chart_type
 * @var array $summary_data_1
 * @var bool $hide_chart_container
 */
?>

<?= view('partial/header') ?>

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

    <?php if (empty($hide_chart_container)): ?>
        <div class="ct-chart ct-golden-section neo-report-chart" id="chart1"></div>
    <?php endif; ?>

    <div id="toolbar" class="neo-table-toolbar">
        <div class="form-inline" role="toolbar">
            <button id="toggleCostProfitButton" class="btn btn-default btn-sm print_hide">
                <?= lang('Reports.toggle_cost_and_profit') ?>
            </button>
        </div>
    </div>

    <?= view($chart_type) ?>

    <div id="chart_report_summary" class="neo-report-summary">
        <?php foreach ($summary_data_1 as $name => $value) { ?>
            <div class="summary_row"><?= lang("Reports.$name") . ': ' . to_currency($value) ?></div>
        <?php } ?>
    </div>
</section>

<script src="<?= base_url('js/hide_cost_profit.js') ?>"></script>

<?= view('partial/footer') ?>
