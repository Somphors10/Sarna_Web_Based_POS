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
        var palette = ['#60a5fa', '#fbbf24', '#fb7185', '#c4b5fd', '#34d399', '#38bdf8', '#f97316', '#94a3b8'];

        var data = {
            labels: <?= json_encode($labels_1, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
            series: <?= json_encode($series_data_1, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
        };

        var options = {
            width: '100%',
            height: '100%',
            donut: false,
            startAngle: 270,
            chartPadding: 8,
            labelPosition: 'outside',
            labelInterpolationFnc: function () {
                return '';
            },
            plugins: [
                Chartist.plugins.tooltip({
                    transformTooltipTextFnc: function (value, label, meta) {
                        var formatted;
                        <?php if ($show_currency && is_right_side_currency_symbol()): ?>
                            formatted = value + '<?= esc($config['currency_symbol'], 'js') ?>';
                        <?php elseif ($show_currency): ?>
                            formatted = '<?= esc($config['currency_symbol'], 'js') ?>' + value;
                        <?php else: ?>
                            formatted = value;
                        <?php endif; ?>

                        return meta ? meta + ' · ' + formatted : formatted;
                    }
                })
            ]
        };

        var <?= esc($chart_var, 'js') ?> = new Chartist.Pie('#<?= esc($chart_id, 'js') ?>', data, options);

        <?= esc($chart_var, 'js') ?>.on('draw', function (ctx) {
            if (ctx.type === 'slice') {
                var color = palette[ctx.index % palette.length];

                ctx.element.attr({
                    style: 'fill: ' + color + '; stroke: ' + color + '; stroke-width: 1px;'
                });

                ctx.element.animate({
                    opacity: {
                        begin: ctx.index * 80 + 150,
                        dur: 500,
                        from: 0,
                        to: 1
                    }
                });
            }
        });
    })();
</script>
