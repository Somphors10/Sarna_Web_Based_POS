<?php
/**
 * Ranked horizontal bars (customers, items, etc.) — readable list instead of Chartist.
 *
 * @var array $labels_1
 * @var string $yaxis_title
 * @var array $series_data_1
 * @var bool $show_currency
 * @var string $xaxis_title
 * @var array $config
 */

$labels_1 = $labels_1 ?? [];
$series_data_1 = $series_data_1 ?? [];
$yaxis_title = $yaxis_title ?? lang('Reports.customers');
$xaxis_title = $xaxis_title ?? lang('Reports.revenue');

$rows = [];
foreach ($labels_1 as $index => $label) {
    $raw = $series_data_1[$index] ?? 0;
    $amount = is_array($raw) ? (float) ($raw['value'] ?? 0) : (float) $raw;
    $name = html_entity_decode((string) $label, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $name = str_replace(['\\x20', '\x20'], ' ', $name);
    $name = trim(preg_replace('/\s+/', ' ', $name));
    if ($name === '') {
        $name = lang('Common.unknown');
    }
    $rows[] = [
        'name'   => $name,
        'amount' => $amount,
    ];
}

usort($rows, static fn ($a, $b) => $b['amount'] <=> $a['amount']);

$limit = 12;
$shown = array_slice($rows, 0, $limit);
$chart_total = array_sum(array_column($shown, 'amount'));
$legend_total = max($chart_total, 1);
$palette = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#14b8a6', '#64748b'];
?>

<p class="neo-report-hbar-caption">
    <?= esc(lang('Reports.graphical_hbar_caption', [mb_strtolower($yaxis_title), mb_strtolower($xaxis_title)])) ?>
</p>

<div class="neo-report-pay-breakdown" role="list" aria-label="<?= esc($yaxis_title) ?>">
    <?php foreach ($shown as $index => $row): ?>
        <?php
            $pct = round($row['amount'] / $legend_total * 100, 1);
            $color = $palette[$index % count($palette)];
        ?>
        <div class="neo-report-pay-item" role="listitem">
            <div class="neo-report-pay-item__top">
                <span class="neo-report-pay-item__rank"><?= $index + 1 ?></span>
                <span class="neo-report-pay-item__dot" style="background-color: <?= esc($color) ?>"></span>
                <span class="neo-report-pay-item__name"><?= esc($row['name']) ?></span>
                <span class="neo-report-pay-item__amount"><?= to_currency($row['amount']) ?></span>
                <span class="neo-report-pay-item__pct"><?= esc($pct) ?>%</span>
            </div>
            <div class="neo-report-pay-item__bar" aria-hidden="true">
                <span class="neo-report-pay-item__fill" style="width: <?= esc($pct) ?>%; background-color: <?= esc($color) ?>"></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<p class="neo-report-hbar-hint"><?= esc(lang('Reports.graphical_hbar_total_hint')) ?></p>
