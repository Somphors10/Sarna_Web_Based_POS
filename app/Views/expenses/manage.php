<?php
/**
 * @var string $controller_name
 * @var string $table_headers
 * @var array $filters
 * @var array $config
 */
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#filters').on('change', function() {
            table_support.refresh();
        });
        $('#filters').on('hidden.bs.select', function(e) {
            table_support.refresh();
        });

        <?= view('partial/daterangepicker') ?>

        var picker = $('#daterangepicker').data('daterangepicker');
        if (picker) {
            var allTimeStart = moment('2010-01-01 00:00:00');
            var allTimeEnd = moment();

            if (picker.timePicker) {
                start_date = allTimeStart.format('YYYY-MM-DD HH:mm:ss');
                end_date = allTimeEnd.endOf('day').format('YYYY-MM-DD HH:mm:ss');
            } else {
                start_date = allTimeStart.format('YYYY-MM-DD');
                end_date = allTimeEnd.format('YYYY-MM-DD');
            }

            picker.setStartDate(allTimeStart);
            picker.setEndDate(allTimeEnd);
            $('#daterangepicker').val(
                picker.startDate.format(picker.locale.format) + ' - ' + picker.endDate.format(picker.locale.format)
            );
        }

        $("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {
            table_support.refresh();
        });

        <?= view('partial/soft_delete_list_script') ?>
        <?= view('partial/bootstrap_tables_locale') ?>

        table_support.init({
            resource: '<?= esc($controller_name) ?>',
            headers: <?= $table_headers ?>,
            pageSize: <?= table_page_size($config['lines_per_page']) ?>,
            uniqueId: 'expense_id_key',
            onLoadSuccess: function(response) {
                if ($("#table tbody tr").length > 1) {
                    $("#payment_summary").html(response.payment_summary);
                    $("#table tbody tr:last td:first").html("");
                    $("#table tbody tr:last").css('font-weight', 'bold');
                }
            },
            queryParams: function() {
                var selectedFilters = mergeDeletedTableFilters($("#filters").val() || []);

                return $.extend(arguments[0], {
                    "start_date": start_date,
                    "end_date": end_date,
                    "filters": selectedFilters
                });
            }
        });
    });
</script>

<?= view('partial/print_receipt', ['print_after_sale' => false, 'selected_printer' => 'takings_printer']) ?>

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= ucfirst($controller_name) ?></h3>
        </div>
        <div id="title_bar" class="print_hide btn-toolbar neo-module-actions">
            <button onclick="javascript:printdoc()" class="btn btn-info btn-sm">
                <span class="glyphicon glyphicon-print">&nbsp;</span><?= lang('Common.print') ?>
            </button>
            <button class="btn btn-primary btn-sm modal-dlg" data-btn-submit="<?= lang('Common.submit') ?>" data-href="<?= "$controller_name/view" ?>" title="<?= lang(ucfirst($controller_name) . '.new') ?>">
                <span class="glyphicon glyphicon-tags">&nbsp;</span><?= lang(ucfirst($controller_name) . '.new') ?>
            </button>
        </div>
    </header>

    <?= view('partial/list_tabs') ?>

    <div id="toolbar" class="neo-table-toolbar">
        <div class="form-inline" role="toolbar">
            <button id="delete" class="btn btn-default btn-sm print_hide">
                <span class="glyphicon glyphicon-trash">&nbsp;</span><?= lang('Common.delete') ?>
            </button>
            <button id="restore" class="btn btn-default btn-sm print_hide hidden">
                <span class="glyphicon glyphicon-repeat">&nbsp;</span><?= lang('Common.restore') ?>
            </button>
            <?= form_input(['name' => 'daterangepicker', 'class' => 'form-control input-sm', 'id' => 'daterangepicker']) ?>
            <?= form_multiselect('filters[]', esc($filters), [''], [
                'id'                        => 'filters',
                'class'                     => 'form-control input-sm pos-items-filter-select',
                'size'                      => 1
            ]) ?>
        </div>
    </div>

    <div id="table_holder" class="neo-table-holder">
        <table id="table"></table>
    </div>

    <div id="payment_summary"></div>
</section>

<?= view('partial/footer') ?>
