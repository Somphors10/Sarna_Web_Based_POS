<?php

/**
 * @var array $allowed_modules
 * @var array $config
 * @var array $kpis
 * @var array $charts
 * @var string $period_label
 */

$summary_labels = [
    'total'                  => 'Total',
    'profit'                 => lang('Reports.profit'),
    'expenses_total_amount'  => 'Total',
];

?>

<?= view('partial/header') ?>

<script src="js/chart.umd.min.js"></script>

<script type="text/javascript">
    dialog_support.init("a.modal-dlg");
</script>

<section class="neo-home">
    <div class="neo-main">
        <header class="neo-dash-header">
            <div>
                <h2 class="neo-dash-header__title">Business Dashboard</h2>
                <p class="neo-dash-header__subtitle"><?= esc($period_label) ?></p>
            </div>
        </header>

        <?php if (!empty($kpis)): ?>
            <div class="neo-kpi-grid">
                <?php foreach ($kpis as $kpi): ?>
                    <div class="neo-kpi-card">
                        <span class="neo-kpi-label"><?= esc($kpi['label']) ?></span>
                        <strong class="neo-kpi-value"><?= esc($kpi['value']) ?></strong>
                        <?php if (!empty($kpi['hint'])): ?>
                            <span class="neo-kpi-hint"><?= esc($kpi['hint']) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($charts)): ?>
            <h3 class="neo-section-title">Reports Overview</h3>

            <div class="neo-chart-grid">
                <?php foreach ($charts as $chart): ?>
                    <?php
                        $is_area = ($chart['chart_type'] ?? '') === 'home/charts/area';
                        $card_class = 'neo-chart-card' . ($is_area ? ' neo-chart-card--area' : ' neo-chart-card--bar');
                        $chart_total = 0;

                        if (!empty($chart['series_data_1'])) {
                            $chart_total = array_sum(array_map(
                                static fn ($item) => (float) ($item['value'] ?? 0),
                                $chart['series_data_1']
                            ));
                        } elseif (!empty($chart['summary']['total'])) {
                            $chart_total = (float) $chart['summary']['total'];
                        } elseif (!empty($chart['summary']['expenses_total_amount'])) {
                            $chart_total = (float) $chart['summary']['expenses_total_amount'];
                        }
                    ?>

                    <article class="<?= esc($card_class) ?>">
                        <div class="neo-chart-card__header">
                            <div class="neo-chart-card__heading">
                                <div class="neo-chart-card__title-row">
                                    <h4 class="neo-chart-card__title"><?= esc($chart['title']) ?></h4>
                                    <?php if ($chart_total > 0): ?>
                                        <div class="neo-chart-card__total">
                                            <span>Total</span>
                                            <strong><?= to_currency($chart_total) ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($chart['subtitle'])): ?>
                                    <p class="neo-chart-card__subtitle"><?= esc($chart['subtitle']) ?></p>
                                <?php endif; ?>

                                <?php if ($chart_total <= 0 && !empty($chart['summary']) && !empty($chart['summary_keys'])): ?>
                                    <div class="neo-chart-card__chips">
                                        <?php foreach ($chart['summary_keys'] as $key): ?>
                                            <?php if (isset($chart['summary'][$key]) && is_numeric($chart['summary'][$key])): ?>
                                                <span class="neo-chart-chip">
                                                    <?= esc($summary_labels[$key] ?? lang("Reports.$key")) ?>
                                                    <strong><?= to_currency($chart['summary'][$key]) ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($chart['has_data'])): ?>
                            <div class="neo-chart-card__body">
                                <canvas id="<?= esc($chart['chart_id']) ?>" class="neo-chart-canvas" aria-label="<?= esc($chart['title']) ?>"></canvas>
                            </div>

                            <?php if (!$is_area && !empty($chart['labels_1'])): ?>
                                <?php
                                    $legend_total = max($chart_total, 1);
                                    $palette = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#14b8a6', '#64748b'];
                                ?>
                                <div class="neo-pay-list" role="list" aria-label="<?= esc($chart['title']) ?> breakdown">
                                    <?php foreach ($chart['labels_1'] as $index => $label): ?>
                                        <?php
                                            $slice_value = (float) ($chart['series_data_1'][$index]['value'] ?? 0);
                                            $pct = round($slice_value / $legend_total * 100, 1);
                                            $color = $palette[$index % count($palette)];
                                        ?>
                                        <div class="neo-pay-item" role="listitem">
                                            <div class="neo-pay-item__top">
                                                <span class="neo-pay-item__dot" style="background-color: <?= esc($color) ?>"></span>
                                                <span class="neo-pay-item__name"><?= esc($label) ?></span>
                                                <span class="neo-pay-item__amount"><?= to_currency($slice_value) ?></span>
                                                <span class="neo-pay-item__pct"><?= esc($pct) ?>%</span>
                                            </div>
                                            <div class="neo-pay-item__bar" aria-hidden="true">
                                                <span class="neo-pay-item__fill" style="width: <?= esc($pct) ?>%; background-color: <?= esc($color) ?>"></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php
                            echo view($chart['chart_type'], [
                                'labels_1'      => $chart['labels_1'],
                                'series_data_1' => $chart['series_data_1'],
                                'show_currency' => $chart['show_currency'] ?? false,
                                'config'        => $config,
                                'chart_id'      => $chart['chart_id'],
                                'chart_var'     => $chart['chart_var'],
                            ]);
                            ?>
                        <?php else: ?>
                            <div class="neo-chart-empty">
                                <p>No data for this period.</p>
                                <span>Charts appear after you record sales or transactions.</span>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="neo-dashboard-empty">
                No report data available. Check your report permissions or add sales data.
            </div>
        <?php endif; ?>
    </div>
</section>

<?= view('partial/footer') ?>
