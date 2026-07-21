<?php
/**
 * @var int   $person_id
 * @var array $permission_ids
 * @var array $grants
 */

$detailed_reports = [
    'reports_sales'      => 'detailed',
    'reports_receivings' => 'detailed',
    'reports_customers'  => 'specific',
    'reports_discounts'  => 'specific',
    'reports_employees'  => 'specific',
    'reports_suppliers'  => 'specific',
];
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
            <p class="neo-module-subtitle"><?= lang('Common.welcome_message') ?></p>
        </div>
    </header>

    <?php if (isset($error)): ?>
        <div class="alert alert-dismissible alert-danger"><?= esc($error) ?></div>
    <?php endif; ?>

    <div class="neo-reports-grid">
        <div class="neo-report-card">
            <h4 class="neo-report-card__title"><?= lang('Reports.graphical_reports') ?></h4>
            <div class="neo-report-card__links">
                <?php foreach ($permission_ids as $permission_id) {
                    if (can_show_report($permission_id, ['inventory', 'receiving'])) {
                        $link = get_report_link($permission_id, 'graphical_summary');
                ?>
                    <a class="neo-report-link" href="<?= $link['path'] ?>"><?= $link['label'] ?></a>
                <?php
                    }
                } ?>
            </div>
        </div>

        <div class="neo-report-card">
            <h4 class="neo-report-card__title"><?= lang('Reports.summary_reports') ?></h4>
            <div class="neo-report-card__links">
                <?php foreach ($permission_ids as $permission_id) {
                    if (can_show_report($permission_id, ['inventory', 'receiving'])) {
                        $link = get_report_link($permission_id, 'summary');
                ?>
                    <a class="neo-report-link" href="<?= $link['path'] ?>"><?= $link['label'] ?></a>
                <?php
                    }
                } ?>
            </div>
        </div>

        <div class="neo-report-card">
            <h4 class="neo-report-card__title"><?= lang('Reports.detailed_reports') ?></h4>
            <div class="neo-report-card__links">
                <?php foreach ($detailed_reports as $report_name => $prefix) {
                    if (in_array($report_name, $permission_ids, true)) {
                        $link = get_report_link($report_name, $prefix);
                ?>
                    <a class="neo-report-link" href="<?= $link['path'] ?>"><?= $link['label'] ?></a>
                <?php
                    }
                } ?>
            </div>
        </div>

        <?php if (in_array('reports_inventory', $permission_ids, true)) { ?>
        <div class="neo-report-card">
            <h4 class="neo-report-card__title"><?= lang('Reports.inventory_reports') ?></h4>
            <div class="neo-report-card__links">
                <?php
                $inventory_low_report = get_report_link('reports_inventory_low');
                $inventory_summary_report = get_report_link('reports_inventory_summary');
                ?>
                <a class="neo-report-link" href="<?= $inventory_low_report['path'] ?>"><?= $inventory_low_report['label'] ?></a>
                <a class="neo-report-link" href="<?= $inventory_summary_report['path'] ?>"><?= $inventory_summary_report['label'] ?></a>
            </div>
        </div>
        <?php } ?>
    </div>
</section>

<?= view('partial/footer') ?>
