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
        var palette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#64748b'];

        var data = {
            labels: <?= json_encode(esc($labels_1, 'js')) ?>,
            series: <?= json_encode(esc($series_data_1, 'js')) ?>
        };

        var options = {
            width: '100%',
            height: '220px',
            donut: true,
            donutWidth: 52,
            chartPadding: 10,
            labelPosition: 'outside',
            plugins: [
                Chartist.plugins.tooltip({
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

        var <?= esc($chart_var, 'js') ?> = new Chartist.Pie('#<?= esc($chart_id, 'js') ?>', data, options);

        <?= esc($chart_var, 'js') ?>.on('draw', function (ctx) {
            if (ctx.type === 'slice') {
                ctx.element.attr({
                    style: 'fill: ' + palette[ctx.index % palette.length]
                });
            }

            if (ctx.type === 'label') {
                ctx.element.attr({
                    style: 'fill: #475569; font-size: 11px;'
                });
            }
        });
    })();
</script>
