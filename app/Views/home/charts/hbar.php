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

$values = array_map(static fn ($item) => (float) ($item['value'] ?? 0), $series_data_1);
$total = array_sum($values);
$palette = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#14b8a6', '#64748b'];
$colors = [];

foreach ($values as $index => $value) {
    $colors[] = $palette[$index % count($palette)];
}

$currency_symbol = $config['currency_symbol'] ?? '$';
$currency_side = ($show_currency && function_exists('is_right_side_currency_symbol') && is_right_side_currency_symbol()) ? 'right' : 'left';
?>

<script type="text/javascript">
    (function () {
        if (typeof Chart === 'undefined') {
            return;
        }

        var canvas = document.getElementById('<?= esc($chart_id, 'js') ?>');
        if (!canvas) {
            return;
        }

        var currencySymbol = <?= json_encode($currency_symbol, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var currencySide = <?= json_encode($currency_side) ?>;
        var showCurrency = <?= $show_currency ? 'true' : 'false' ?>;
        var total = <?= json_encode($total) ?>;

        function formatMoney(value) {
            var amount = Number(value || 0).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            if (!showCurrency) {
                return amount;
            }

            return currencySide === 'right' ? amount + currencySymbol : currencySymbol + amount;
        }

        var labels = <?= json_encode($labels_1, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var values = <?= json_encode($values, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var colors = <?= json_encode($colors, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var barThickness = labels.length <= 2 ? 26 : (labels.length <= 4 ? 20 : 16);

        window.<?= esc($chart_var, 'js') ?> = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: barThickness,
                    maxBarThickness: Math.max(barThickness + 4, 24)
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 2,
                        bottom: 2,
                        right: 8
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#cbd5e1',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function (item) {
                                var pct = total > 0 ? ((item.raw / total) * 100).toFixed(1) : '0.0';

                                return formatMoney(item.raw) + ' (' + pct + '%)';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: '#eef2f7'
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11
                            },
                            callback: function (value) {
                                return formatMoney(value);
                            },
                            maxTicksLimit: 5
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#334155',
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            autoSkip: false
                        }
                    }
                }
            }
        });
    })();
</script>
