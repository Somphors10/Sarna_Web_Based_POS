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
?>

<script type="text/javascript">
    (function () {
        var data = {
            labels: <?= json_encode($labels_1, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
            series: [{
                data: <?= json_encode($series_data_1, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
            }]
        };

        var labelCount = data.labels.length;

        var options = {
            width: '100%',
            height: '100%',
            showPoint: labelCount <= 12,
            showArea: true,
            lineSmooth: true,
            fullWidth: true,
            chartPadding: {
                top: 12,
                right: 12,
                bottom: 4,
                left: 4
            },
            axisX: {
                offset: 28,
                showGrid: false,
                labelOffset: {
                    x: 0,
                    y: 8
                },
                labelInterpolationFnc: function (value, index) {
                    if (labelCount <= 8) {
                        return value;
                    }

                    return index % 2 === 0 ? value : null;
                }
            },
            axisY: {
                offset: 42,
                showGrid: true,
                labelInterpolationFnc: function (value) {
                    <?php if ($show_currency && is_right_side_currency_symbol()): ?>
                        return value + '<?= esc($config['currency_symbol'], 'js') ?>';
                    <?php elseif ($show_currency): ?>
                        return '<?= esc($config['currency_symbol'], 'js') ?>' + value;
                    <?php else: ?>
                        return value;
                    <?php endif; ?>
                }
            },
            plugins: [
                Chartist.plugins.tooltip({
                    pointClass: 'ct-tooltip-point',
                    transformTooltipTextFnc: function (value) {
                        <?php if ($show_currency && is_right_side_currency_symbol()): ?>
                            return value + '<?= esc($config['currency_symbol'], 'js') ?>';
                        <?php elseif ($show_currency): ?>
                            return '<?= esc($config['currency_symbol'], 'js') ?>' + value;
                        <?php else: ?>
                            return value;
                        <?php endif; ?>
                    }
                })
            ]
        };

        var <?= esc($chart_var, 'js') ?> = new Chartist.Line('#<?= esc($chart_id, 'js') ?>', data, options);

        <?= esc($chart_var, 'js') ?>.on('draw', function (ctx) {
            if (ctx.type === 'line') {
                ctx.element.attr({
                    style: 'stroke: #4f46e5; stroke-width: 2.5px;'
                });
            }

            if (ctx.type === 'point') {
                ctx.element.attr({
                    style: 'stroke: #4f46e5; stroke-width: 8px; fill: #ffffff;'
                });
            }

            if (ctx.type === 'area') {
                ctx.element.attr({
                    style: 'fill: url(#<?= esc($chart_id, 'js') ?>-gradient);'
                });
            }
        });
    })();
</script>
<svg width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="<?= esc($chart_id) ?>-gradient" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="rgba(79, 70, 229, 0.28)"></stop>
            <stop offset="100%" stop-color="rgba(79, 70, 229, 0.02)"></stop>
        </linearGradient>
    </defs>
</svg>
