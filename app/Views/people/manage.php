<?php
/**
 * @var string $controller_name
 * @var string $table_headers
 * @var array $config
 */

$show_email_button = !in_array($controller_name, ['customers', 'employees', 'suppliers'], true);
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    $(document).ready(function() {
        // Ensure action buttons (e.g. New Customer) work even when table init fails.
        dialog_support.init("a.modal-dlg, button.modal-dlg");

        // Hard fallback: if modal lib fails for any reason, still navigate.
        $('#new_customer_btn').on('click', function(event) {
            if (typeof BootstrapDialog === 'undefined') {
                event.preventDefault();
                window.location.href = '<?= "$controller_name/view" ?>';
            }
        });

        <?= view('partial/bootstrap_tables_locale') ?>
        <?php
        $employee = model(\App\Models\Employee::class);
        ?>

        table_support.init({
            employee_id: <?= (int) $employee->get_logged_in_employee_info()->person_id ?>,
            resource: '<?= esc($controller_name) ?>',
            headers: <?= $table_headers ?>,
            pageSize: <?= table_page_size($config['lines_per_page']) ?>,
            uniqueId: '<?= $controller_name === 'suppliers' ? 'person_id_key' : 'people.person_id' ?>',
            enableActions: <?= $show_email_button ? 'function() {
                var selectedRows = $("#table td input:checkbox:checked").parents("tr");
                var mailtoLinks = selectedRows.find("td a[href^=\'mailto:\']");
                $("#email").prop(\'disabled\', mailtoLinks.length === 0);
            }' : 'undefined' ?>
        });

        <?php if ($show_email_button): ?>
        $("#email").click(function(event) {
            event.preventDefault();

            var recipients = [];
            $("#table td input:checkbox:checked").parents("tr").find("td a[href^='mailto:']").each(function() {
                var address = ($(this).attr('href') || '').replace(/^mailto:/i, '').trim();
                if (address !== '' && recipients.indexOf(address) === -1) {
                    recipients.push(address);
                }
            });

            if (recipients.length === 0) {
                $.notify(<?= json_encode(lang('Common.email_no_recipients')) ?>, { type: 'warning' });
                return false;
            }

            window.location.href = 'mailto:' + recipients.join(',');
            return false;
        });
        <?php endif; ?>
    });
</script>

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= ucfirst($controller_name) ?></h3>
            <p class="neo-module-subtitle"><?= lang('Common.welcome_message') ?></p>
        </div>
        <div id="title_bar" class="btn-toolbar neo-module-actions">
            <?php if ($controller_name === 'customers') { ?>
                <a id="csv_import_btn" class="btn btn-default btn-sm modal-dlg" data-btn-submit="<?= lang('Common.submit') ?>" href="<?= "$controller_name/csvImport" ?>" data-href="<?= "$controller_name/csvImport" ?>" title="<?= lang(ucfirst($controller_name) . '.import_items_csv') ?>">
                    <span class="glyphicon glyphicon-import">&nbsp;</span><?= lang('Common.import_csv') ?>
                </a>
            <?php } ?>
            <a id="new_customer_btn" class="btn btn-primary btn-sm modal-dlg" data-btn-submit="<?= lang('Common.submit') ?>" href="<?= "$controller_name/view" ?>" data-href="<?= "$controller_name/view" ?>" title="<?= lang(ucfirst($controller_name) . '.new') ?>">
                <span class="glyphicon glyphicon-user">&nbsp;</span><?= lang(ucfirst($controller_name) . '.new') ?>
            </a>
        </div>
    </header>

    <div id="toolbar" class="neo-table-toolbar">
        <div class="btn-toolbar">
            <button id="delete" class="btn btn-default btn-sm">
                <span class="glyphicon glyphicon-trash">&nbsp;</span><?= lang('Common.delete') ?>
            </button>
            <?php if ($show_email_button): ?>
            <button id="email" class="btn btn-default btn-sm">
                <span class="glyphicon glyphicon-envelope">&nbsp;</span><?= lang('Common.email') ?>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div id="table_holder" class="neo-table-holder">
        <table id="table"></table>
    </div>
</section>

<?= view('partial/footer') ?>
