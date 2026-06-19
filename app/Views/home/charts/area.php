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
        var peak = Math.max.apply(null, values.concat([0]));

        function buildChart() {
            var chartBody = canvas.closest('.neo-chart-card__body');
            var bodyHeight = chartBody ? chartBody.clientHeight : 168;
            var gradient = ctx.createLinearGradient(0, 0, 0, bodyHeight);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.01)');

            window.<?= esc($chart_var, 'js') ?> = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: values,
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2,
                    pointRadius: labels.length <= 14 ? 3 : 0,
                    pointHoverRadius: 5,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 4,
                        bottom: 0,
                        left: 0,
                        right: 4
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#94a3b8',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: function (items) {
                                return items.length ? items[0].label : '';
                            },
                            label: function (item) {
                                return 'Revenue: ' + formatMoney(item.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 11
                            },
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 6
                        }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: peak > 0 ? peak * 1.15 : undefined,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.15)',
                            drawTicks: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 10
                            },
                            padding: 6,
                            callback: function (value) {
                                return formatMoney(value);
                            },
                            maxTicksLimit: 4
                        }
                    }
                }
            }
        });
        }

        var ctx = canvas.getContext('2d');
        requestAnimationFrame(function () {
            requestAnimationFrame(buildChart);
        });
    })();
</script>
