<?php
/**
 * Shop-focused reports hub: primary Cambodia retail reports first, advanced last.
 *
 * @var int   $person_id
 * @var array $permission_ids
 * @var array $grants
 */

$permission_ids = $permission_ids ?? [];

$report_text = static function (string $key, string $fallback): string {
    $value = lang('Reports.' . $key);
    if ($value === '' || $value === 'Reports.' . $key) {
        return $fallback;
    }

    return $value;
};

$render_links = static function (array $items) use ($permission_ids, $report_text): void {
    $label_overrides = [
        'reports_sales' => $report_text('sales', 'Sales'),
    ];

    foreach ($items as $item) {
        $permission = $item['permission'];
        if (!in_array($permission, $permission_ids, true)) {
            continue;
        }

        $link = get_report_link($permission, $item['prefix'] ?? '', $item['lang'] ?? '');
        $label = $label_overrides[$permission] ?? $link['label'];
        if ($label === '' || str_starts_with((string) $label, 'Reports.')) {
            $label = $link['label'] !== '' ? $link['label'] : $permission;
        }
        ?>
        <a class="neo-report-link" href="<?= esc($link['path'], 'attr') ?>"><?= esc($label) ?></a>
        <?php
    }
};

$chart_reports = [
    ['permission' => 'reports_payments', 'prefix' => 'graphical_summary'],
    ['permission' => 'reports_sales', 'prefix' => 'graphical_summary'],
    ['permission' => 'reports_categories', 'prefix' => 'graphical_summary'],
    ['permission' => 'reports_items', 'prefix' => 'graphical_summary'],
];

$summary_reports = [
    ['permission' => 'reports_payments', 'prefix' => 'summary'],
    ['permission' => 'reports_sales', 'prefix' => 'summary'],
    ['permission' => 'reports_categories', 'prefix' => 'summary'],
    ['permission' => 'reports_items', 'prefix' => 'summary'],
];

$history_reports = [
    ['permission' => 'reports_sales', 'prefix' => 'detailed'],
    ['permission' => 'reports_receivings', 'prefix' => 'detailed'],
    ['permission' => 'reports_customers', 'prefix' => 'specific'],
];

$advanced_reports = [
    ['permission' => 'reports_suppliers', 'prefix' => 'summary'],
    ['permission' => 'reports_employees', 'prefix' => 'summary'],
    ['permission' => 'reports_discounts', 'prefix' => 'summary'],
    ['permission' => 'reports_expenses_categories', 'prefix' => 'summary'],
    ['permission' => 'reports_taxes', 'prefix' => 'summary'],
    ['permission' => 'reports_sales_taxes', 'prefix' => 'summary'],
];

$has_any = static function (array $items) use ($permission_ids): bool {
    foreach ($items as $item) {
        if (in_array($item['permission'], $permission_ids, true)) {
            return true;
        }
    }

    return false;
};

$show_charts = $has_any($chart_reports);
$show_summary = $has_any($summary_reports);
$show_history = $has_any($history_reports);
$show_inventory = in_array('reports_inventory', $permission_ids, true);
$show_advanced = $has_any($advanced_reports);
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    dialog_support.init("a.modal-dlg");

    $(document).ready(function() {
        const storageKey = 'reports:last-link';

        $('.neo-report-link').on('click', function() {
            const href = $(this).attr('href');
            if (href) {
                localStorage.setItem(storageKey, href);
            }
        });

        const lastHref = localStorage.getItem(storageKey);
        if (lastHref) {
            $('.neo-report-link').each(function() {
                if ($(this).attr('href') === lastHref) {
                    $(this).addClass('is-active-report');
                }
            });
        }
    });
</script>

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= lang('Module.reports') ?></h3>
            <p class="neo-module-subtitle"><?= esc($report_text('hub_subtitle', 'Start with payments, sales, items, and stock. Use More reports only when needed.')) ?></p>
        </div>
    </header>

    <?php if (isset($error)): ?>
        <div class="alert alert-dismissible alert-danger"><?= esc($error) ?></div>
    <?php endif; ?>

    <div class="neo-reports-grid">
        <?php if ($show_charts): ?>
        <div class="neo-report-card">
            <h4 class="neo-report-card__title"><?= esc($report_text('graphical_reports', 'Charts')) ?></h4>
            <p class="neo-report-card__hint"><?= esc($report_text('hub_charts_hint', 'Easy visual view of money and products.')) ?></p>
            <div class="neo-report-card__links">
                <?php $render_links($chart_reports); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($show_summary): ?>
        <div class="neo-report-card">
            <h4 class="neo-report-card__title"><?= esc($report_text('summary_reports', 'Summary tables')) ?></h4>
            <p class="neo-report-card__hint"><?= esc($report_text('hub_summary_hint', 'Same data as tables for printing or checking totals.')) ?></p>
            <div class="neo-report-card__links">
                <?php $render_links($summary_reports); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($show_history): ?>
        <div class="neo-report-card">
            <h4 class="neo-report-card__title"><?= esc($report_text('detailed_reports', 'Sale & receiving history')) ?></h4>
            <p class="neo-report-card__hint"><?= esc($report_text('hub_history_hint', 'Each sale or receiving, one by one.')) ?></p>
            <div class="neo-report-card__links">
                <?php $render_links($history_reports); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($show_inventory): ?>
        <div class="neo-report-card">
            <h4 class="neo-report-card__title"><?= esc($report_text('inventory_reports', 'Stock')) ?></h4>
            <p class="neo-report-card__hint"><?= esc($report_text('hub_stock_hint', 'What is low and what you have in store.')) ?></p>
            <div class="neo-report-card__links">
                <?php
                $inventory_low_report = get_report_link('reports_inventory_low');
                $inventory_summary_report = get_report_link('reports_inventory_summary');
                ?>
                <a class="neo-report-link" href="<?= esc($inventory_low_report['path'], 'attr') ?>"><?= esc($inventory_low_report['label']) ?></a>
                <a class="neo-report-link" href="<?= esc($inventory_summary_report['path'], 'attr') ?>"><?= esc($inventory_summary_report['label']) ?></a>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($show_advanced): ?>
        <div class="neo-report-card neo-report-card--advanced">
            <h4 class="neo-report-card__title"><?= esc($report_text('advanced_reports', 'More reports')) ?></h4>
            <p class="neo-report-card__hint"><?= esc($report_text('hub_advanced_hint', 'Taxes, staff, discounts, suppliers, expenses.')) ?></p>
            <div class="neo-report-card__links neo-report-card__links--grid">
                <?php $render_links($advanced_reports); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?= view('partial/footer') ?>
