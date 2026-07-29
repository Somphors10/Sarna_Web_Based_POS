<?php
/**
 * @var array $labels_1
 * @var array $series_data_1
 * @var bool $show_currency
 * @var array $config
 */

if (empty($series_data_1)) {
    return;
}

$palette = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#14b8a6', '#64748b'];
$total = array_sum(array_map(static fn ($item) => (float) ($item['value'] ?? 0), $series_data_1));
$legend_total = max($total, 1);
?>

<div class="neo-report-pay-breakdown" role="list" aria-label="<?= esc(lang('Reports.payments_summary_report')) ?>">
    <?php foreach ($labels_1 as $index => $label): ?>
        <?php
            $amount = (float) ($series_data_1[$index]['value'] ?? 0);
            $pct = round($amount / $legend_total * 100, 1);
            $color = $palette[$index % count($palette)];
        ?>
        <div class="neo-report-pay-item" role="listitem">
            <div class="neo-report-pay-item__top">
                <span class="neo-report-pay-item__dot" style="background-color: <?= esc($color) ?>"></span>
                <span class="neo-report-pay-item__name"><?= esc($label) ?></span>
                <span class="neo-report-pay-item__amount"><?= to_currency($amount) ?></span>
                <span class="neo-report-pay-item__pct"><?= esc($pct) ?>%</span>
            </div>
            <div class="neo-report-pay-item__bar" aria-hidden="true">
                <span class="neo-report-pay-item__fill" style="width: <?= esc($pct) ?>%; background-color: <?= esc($color) ?>"></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
