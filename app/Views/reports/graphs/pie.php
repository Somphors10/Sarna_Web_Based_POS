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

$palette = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#14b8a6', '#64748b'];
?>

<script type="text/javascript">
    (function () {
        var data = {
            labels: <?= json_encode($labels_1, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
            series: <?= json_encode($series_data_1, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
        };

        var palette = <?= json_encode($palette, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

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
                return Math.round(value) + '%';
            },
            plugins: [
                Chartist.plugins.tooltip({
                    transformTooltipTextFnc: function (value) {
                        <?php if ($show_currency): ?>
                            <?php if (is_right_side_currency_symbol()): ?>
                                return value + '<?= esc($config['currency_symbol'], 'js') ?>';
                            <?php else: ?>
                                return '<?= esc($config['currency_symbol'], 'js') ?>' + value;
                            <?php endif; ?>
                        <?php else: ?>
                            return value;
                        <?php endif; ?>
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
