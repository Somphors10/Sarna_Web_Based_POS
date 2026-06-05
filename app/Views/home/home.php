<?php
/**
 * @var array $allowed_modules
 * @var array $config
 * @var array $kpis
 * @var array $charts
 * @var string $period_label
 */

$pie_palette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#64748b'];
?>

<?= view('partial/header') ?>

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
                        $is_line = ($chart['chart_type'] ?? '') === 'home/charts/line';
                        $card_class = 'neo-chart-card' . ($is_line ? ' neo-chart-card--line' : ' neo-chart-card--pie');
                    ?>
                    <article class="<?= esc($card_class) ?>">
                        <div class="neo-chart-card__header">
                            <div class="neo-chart-card__heading">
                                <h4 class="neo-chart-card__title"><?= esc($chart['title']) ?></h4>
                                <?php if (!empty($chart['summary']) && !empty($chart['summary_keys'])): ?>
                                    <div class="neo-chart-card__chips">
                                        <?php foreach ($chart['summary_keys'] as $key): ?>
                                            <?php if (isset($chart['summary'][$key]) && is_numeric($chart['summary'][$key])): ?>
                                                <span class="neo-chart-chip">
                                                    <?= lang("Reports.$key") ?>
                                                    <strong><?= to_currency($chart['summary'][$key]) ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($chart['report_url'])): ?>
                                <a class="neo-chart-card__link" href="<?= esc($chart['report_url']) ?>">Full report</a>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($chart['has_data'])): ?>
                            <div class="neo-chart-card__body">
                                <div class="neo-chart-card__canvas ct-chart" id="<?= esc($chart['chart_id']) ?>"></div>
                            </div>

                            <?php if (!$is_line && !empty($chart['labels_1'])): ?>
                                <div class="neo-chart-legend">
                                    <?php foreach ($chart['labels_1'] as $index => $label): ?>
                                        <?php
                                            $slice_value = $chart['series_data_1'][$index]['value'] ?? 0;
                                            $color = $pie_palette[$index % count($pie_palette)];
                                        ?>
                                        <span class="neo-chart-legend__item">
                                            <span class="neo-chart-legend__dot" style="background-color: <?= esc($color) ?>"></span>
                                            <span class="neo-chart-legend__label"><?= esc($label) ?></span>
                                            <span class="neo-chart-legend__value"><?= to_currency($slice_value) ?></span>
                                        </span>
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
