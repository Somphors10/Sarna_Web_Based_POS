<?php
/**
 * @var array $labels_1
 * @var string $yaxis_title
 * @var array $series_data_1
 * @var bool $show_currency
 * @var string $xaxis_title
 * @var array $config
 * @var string $chart_id
 * @var string $chart_var
 */

$chart_id = $chart_id ?? 'chart1';
$chart_var = $chart_var ?? 'chart';
$json_flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE;

$series_points = [];
foreach ($series_data_1 as $point) {
    if (is_array($point)) {
        $series_points[] = [
            'meta'  => (string) ($point['meta'] ?? ''),
            'value' => (float) ($point['value'] ?? 0),
        ];
    } else {
        $series_points[] = (float) $point;
    }
}
?>

<script type="text/javascript">
    var data = {
        labels: <?= json_encode(array_values($labels_1), $json_flags) ?>,
        series: [{
            name: <?= json_encode((string) $yaxis_title, $json_flags) ?>,
            data: <?= json_encode($series_points, $json_flags) ?>
        }]
    };

    var options = {
        width: '100%',
        height: '320px',
        showPoint: true,
        lineSmooth: Chartist.Interpolation.simple({ divisor: 2 }),
        fullWidth: true,
        chartPadding: {
            top: 24,
            right: 24,
            bottom: 28,
            left: 12
        },
        axisX: {
            offset: 36,
            position: 'end',
            showGrid: false,
            labelOffset: {
                x: 0,
                y: 8
            },
            labelInterpolationFnc: function(value) {
                return value;
            }
        },
        axisY: {
            offset: 56,
            labelOffset: {
                x: -8,
                y: 0
            },
            scaleMinSpace: 36,
            labelInterpolationFnc: function(value) {
                <?php if ($show_currency): ?>
                    <?php if (is_right_side_currency_symbol()): ?>
                        return value + <?= json_encode((string) $config['currency_symbol'], $json_flags) ?>;
                    <?php else: ?>
                        return <?= json_encode((string) $config['currency_symbol'], $json_flags) ?> + value;
                    <?php endif; ?>
                <?php else: ?>
                    return value;
                <?php endif; ?>
            }
        },
        plugins: [
            Chartist.plugins.ctAxisTitle({
                axisX: {
                    axisTitle: <?= json_encode((string) $xaxis_title, $json_flags) ?>,
                    axisClass: 'ct-axis-title',
                    offset: {
                        x: 0,
                        y: 36
                    },
                    textAnchor: 'middle'
                },
                axisY: {
                    axisTitle: <?= json_encode((string) $yaxis_title, $json_flags) ?>,
                    axisClass: 'ct-axis-title',
                    offset: {
                        x: 0,
                        y: 12
                    },
                    textAnchor: 'middle',
                    flipTitle: false
                }
            }),
            Chartist.plugins.ctPointLabels({
                textAnchor: 'middle',
                labelInterpolationFnc: function(value) {
                    <?php if ($show_currency): ?>
                        <?php if (is_right_side_currency_symbol()): ?>
                            return value + <?= json_encode((string) $config['currency_symbol'], $json_flags) ?>;
                        <?php else: ?>
                            return <?= json_encode((string) $config['currency_symbol'], $json_flags) ?> + value;
                        <?php endif; ?>
                    <?php else: ?>
                        return value;
                    <?php endif; ?>
                }
            }),
            Chartist.plugins.tooltip({
                pointClass: 'ct-tooltip-point',
                transformTooltipTextFnc: function(value) {
                    <?php if ($show_currency): ?>
                        <?php if (is_right_side_currency_symbol()): ?>
                            return value + <?= json_encode((string) $config['currency_symbol'], $json_flags) ?>;
                        <?php else: ?>
                            return <?= json_encode((string) $config['currency_symbol'], $json_flags) ?> + value;
                        <?php endif; ?>
                    <?php else: ?>
                        return value;
                    <?php endif; ?>
                }
            })
        ]
    };

    var <?= esc($chart_var, 'js') ?> = new Chartist.Line('#<?= esc($chart_id, 'js') ?>', data, options);

    <?= esc($chart_var, 'js') ?>.on('draw', function(ctx) {
        if (ctx.type === 'point') {
            var circle = new Chartist.Svg('circle', {
                cx: [ctx.x],
                cy: [ctx.y],
                r: [6],
                'ct:value': ctx.value.y,
                'ct:meta': ctx.meta,
                class: 'ct-tooltip-point'
            }, 'ct-area');
            ctx.element.replace(circle);
        }

        if (ctx.type === 'line' || ctx.type === 'area') {
            ctx.element.attr({
                style: 'stroke: #6366f1; stroke-width: 3px;'
            });
        }
    });
</script>
