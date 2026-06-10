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
            showPoint: labelCount <= 14,
            showArea: true,
            lineSmooth: true,
            fullWidth: true,
            low: 0,
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
                    y: 10
                },
                labelInterpolationFnc: function (value, index) {
                    if (labelCount <= 10) {
                        return value;
                    }

                    return index % 2 === 0 ? value : null;
                }
            },
            axisY: {
                offset: 48,
                showGrid: true,
                onlyInteger: false,
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
                    anchorToPoint: true,
                    transformTooltipTextFnc: function (value, label, meta) {
                        var formatted;
                        <?php if ($show_currency && is_right_side_currency_symbol()): ?>
                            formatted = value + '<?= esc($config['currency_symbol'], 'js') ?>';
                        <?php elseif ($show_currency): ?>
                            formatted = '<?= esc($config['currency_symbol'], 'js') ?>' + value;
                        <?php else: ?>
                            formatted = value;
                        <?php endif; ?>

                        return meta ? meta + ': ' + formatted : formatted;
                    }
                })
            ]
        };

        var <?= esc($chart_var, 'js') ?> = new Chartist.Line('#<?= esc($chart_id, 'js') ?>', data, options);

        <?= esc($chart_var, 'js') ?>.on('draw', function (ctx) {
            if (ctx.type === 'line') {
                ctx.element.attr({
                    style: 'stroke: url(#neo-line-gradient); stroke-width: 3px; stroke-linecap: round;'
                });
                ctx.element.animate({
                    opacity: {
                        begin: 200,
                        dur: 900,
                        from: 0,
                        to: 1
                    }
                });
            }

            if (ctx.type === 'point') {
                ctx.element.attr({
                    style: 'stroke: #6366f1; stroke-width: 10px; fill: #ffffff;'
                });
                ctx.element.animate({
                    opacity: {
                        begin: ctx.index * 60 + 300,
                        dur: 400,
                        from: 0,
                        to: 1
                    }
                });
            }

            if (ctx.type === 'area') {
                ctx.element.attr({
                    style: 'fill: url(#neo-area-gradient);'
                });
                ctx.element.animate({
                    opacity: {
                        begin: 100,
                        dur: 1000,
                        from: 0,
                        to: 1
                    }
                });
            }

            if (ctx.type === 'grid' && ctx.axis.units.pos === 'y') {
                ctx.element.attr({
                    style: 'stroke: #f1f5f9; stroke-width: 1px;'
                });
            }
        });
    })();
</script>
<svg width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="neo-line-gradient" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0%" stop-color="#6366f1"></stop>
            <stop offset="100%" stop-color="#22d3ee"></stop>
        </linearGradient>
        <linearGradient id="neo-area-gradient" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="rgba(99, 102, 241, 0.22)"></stop>
            <stop offset="100%" stop-color="rgba(99, 102, 241, 0.01)"></stop>
        </linearGradient>
    </defs>
</svg>
