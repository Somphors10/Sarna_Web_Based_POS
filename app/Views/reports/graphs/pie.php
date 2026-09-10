<?php
/**
 * @var array $labels_1
 * @var array $series_data_1
 * @var bool $show_currency
 * @var array $config
 * @var string $chart_id
 * @var string $chart_var
 */

$chart_id = $chart_id ?? 'chart1';
$chart_var = $chart_var ?? 'chart';

if (empty($series_data_1)) {
    return;
}

$series_values = array_map(static function ($item) {
    return (float) (is_array($item) ? ($item['value'] ?? 0) : $item);
}, $series_data_1);

$series_meta = array_map(static function ($item) {
    return is_array($item) ? (string) ($item['meta'] ?? '') : '';
}, $series_data_1);

$palette = ['#60a5fa', '#fbbf24', '#fb7185', '#c4b5fd', '#34d399', '#38bdf8', '#f97316', '#94a3b8'];
$total = array_sum($series_values);
$legend_total = max($total, 1.0);

$legend_rows = [];
foreach ($labels_1 as $index => $label) {
    $amount = $series_values[$index] ?? 0.0;
    $name = html_entity_decode((string) $label, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');
    if ($name === '') {
        $name = lang('Common.unknown');
    }
    $legend_rows[] = [
        'name'   => $name,
        'amount' => $amount,
        'pct'    => round($amount / $legend_total * 100, 1),
        'color'  => $palette[$index % count($palette)],
        'index'  => $index,
    ];
}

usort($legend_rows, static fn ($a, $b) => $b['amount'] <=> $a['amount']);
?>

<script type="text/javascript">
    (function () {
        var labels = <?= json_encode($labels_1, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var values = <?= json_encode($series_values, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var meta = <?= json_encode($series_meta, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var palette = <?= json_encode($palette, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var total = values.reduce(function (sum, value) { return sum + value; }, 0) || 1;
        var chartEl = document.getElementById('<?= esc($chart_id, 'js') ?>');

        if (chartEl) {
            chartEl.classList.add('neo-report-chart--pie-slide');
        }

        var data = {
            labels: labels,
            series: values.map(function (value, index) {
                return {
                    value: value,
                    meta: meta[index] || labels[index] || ''
                };
            })
        };

        var options = {
            width: '100%',
            height: '280px',
            donut: false,
            startAngle: 270,
            chartPadding: 12,
            showLabel: false,
            plugins: [
                Chartist.plugins.tooltip({
                    transformTooltipTextFnc: function (value, label, pointMeta) {
                        var formatted;
                        <?php if ($show_currency): ?>
                            <?php if (is_right_side_currency_symbol()): ?>
                                formatted = value + '<?= esc($config['currency_symbol'], 'js') ?>';
                            <?php else: ?>
                                formatted = '<?= esc($config['currency_symbol'], 'js') ?>' + value;
                            <?php endif; ?>
                        <?php else: ?>
                            formatted = value;
                        <?php endif; ?>

                        return pointMeta ? pointMeta + ' · ' + formatted : formatted;
                    }
                })
            ]
        };

        var chart = new Chartist.Pie('#<?= esc($chart_id, 'js') ?>', data, options);

        chart.on('created', function () {
            if (!chartEl) {
                return;
            }
            var svg = chartEl.querySelector('svg');
            if (svg) {
                svg.setAttribute('overflow', 'visible');
            }
        });

        chart.on('draw', function (ctx) {
            if (ctx.type === 'slice') {
                var color = palette[ctx.index % palette.length];

                ctx.element.attr({
                    style: 'fill: ' + color + '; stroke: ' + color + '; stroke-width: 1px; cursor: pointer;'
                });
            }
        });
    })();
</script>

<p class="neo-report-pie-caption"><?= esc(lang('Reports.graphical_pie_legend_hint')) ?></p>

<div class="neo-report-pie-legend" role="list" aria-label="<?= esc(lang('Reports.graphical_pie_legend_hint')) ?>">
    <?php foreach ($legend_rows as $row): ?>
        <div class="neo-report-pie-legend__item" role="listitem">
            <span class="neo-report-pie-legend__dot" style="background-color: <?= esc($row['color']) ?>" aria-hidden="true"></span>
            <span class="neo-report-pie-legend__name"><?= esc($row['name']) ?></span>
            <span class="neo-report-pie-legend__amount">
                <?php if (!empty($show_currency)): ?>
                    <?= to_currency($row['amount']) ?>
                <?php else: ?>
                    <?= esc(number_format((float) $row['amount'], 2, '.', ',')) ?>
                <?php endif; ?>
            </span>
            <span class="neo-report-pie-legend__pct"><?= esc($row['pct']) ?>%</span>
        </div>
    <?php endforeach; ?>
</div>
