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
            labels: <?= json_encode(esc($labels_1, 'js')) ?>,
            series: [{
                data: <?= json_encode(esc($series_data_1, 'js')) ?>
            }]
        };

        var options = {
            width: '100%',
            height: '220px',
            showPoint: <?= count($series_data_1) <= 14 ? 'true' : 'false' ?>,
            showArea: true,
            lineSmooth: true,
            fullWidth: true,
            chartPadding: {
                top: 16,
                right: 16,
                bottom: 8,
                left: 8
            },
            axisX: {
                offset: 24,
                showGrid: false,
                labelOffset: {
                    x: 0,
                    y: 6
                }
            },
            axisY: {
                offset: 48,
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
                    style: 'stroke: #4f46e5; stroke-width: 2px;'
                });
            }

            if (ctx.type === 'point') {
                var circle = new Chartist.Svg('circle', {
                    cx: [ctx.x],
                    cy: [ctx.y],
                    r: [4],
                    'ct:value': ctx.value.y,
                    'ct:meta': ctx.meta,
                    class: 'ct-tooltip-point'
                }, 'ct-area');

                ctx.element.replace(circle);
                ctx.element.attr({ style: 'fill: #4f46e5;' });
            }

            if (ctx.type === 'area') {
                ctx.element.attr({
                    style: 'fill: rgba(79, 70, 229, 0.08);'
                });
            }
        });
    })();
</script>
