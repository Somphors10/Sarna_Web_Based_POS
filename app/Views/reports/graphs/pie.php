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

$palette = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#14b8a6', '#64748b'];
?>

<script type="text/javascript">
    (function () {
        var labels = <?= json_encode($labels_1, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var values = <?= json_encode($series_values, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var meta = <?= json_encode($series_meta, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var palette = <?= json_encode($palette, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

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
            donut: true,
            donutWidth: 52,
            startAngle: 270,
            chartPadding: 24,
            labelPosition: 'outside',
            labelDirection: 'explode',
            labelInterpolationFnc: function (value) {
                if (!isFinite(value)) {
                    return '';
                }

                return Math.round(value) + '%';
            },
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

        chart.on('draw', function (ctx) {
            if (ctx.type === 'slice') {
                var color = palette[ctx.index % palette.length];

                ctx.element.attr({
                    style: 'fill: ' + color + '; stroke: #ffffff; stroke-width: 3px;'
                });
            }

            if (ctx.type === 'label') {
                ctx.element.attr({
                    style: 'fill: #334155; font-size: 12px; font-weight: 600;'
                });
            }
        });
    })();
</script>
