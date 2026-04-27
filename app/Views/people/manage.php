<?php
/**
 * @var string $controller_name
 * @var string $table_headers
 * @var array $config
 */
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

        table_support.init({
            resource: '<?= esc($controller_name) ?>',
            headers: <?= $table_headers ?>,
            pageSize: <?= $config['lines_per_page'] ?>,
            uniqueId: 'people.person_id',
            enableActions: function() {
                var email_disabled = $("td input:checkbox:checked").parents("tr").find("td a[href^='mailto:']").length == 0;
                $("#email").prop('disabled', email_disabled);
            }
        });

        $("#email").click(function(event) {
            var recipients = $.map($("tr.selected a[href^='mailto:']"), function(element) {
                return $(element).attr('href').replace(/^mailto:/, '');
            });
            location.href = "mailto:" + recipients.join(",");
        });
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
            <button id="email" class="btn btn-default btn-sm">
                <span class="glyphicon glyphicon-envelope">&nbsp;</span><?= lang('Common.email') ?>
            </button>
        </div>
    </div>

    <div id="table_holder" class="neo-table-holder">
        <table id="table"></table>
    </div>
</section>

<?= view('partial/footer') ?>
