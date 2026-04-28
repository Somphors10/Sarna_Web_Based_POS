<?php
/**
 * @var string $controller_name
 * @var string $table_headers
 * @var array $filters
 * @var array $stock_locations
 * @var int $stock_location
 * @var array $config
 */

use App\Models\Employee;
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    $(document).ready(function() {
        var init_select_controls = function() {
            if ($.fn.selectpicker) {
                $('.selectpicker').selectpicker();
                $('#toolbar').addClass('items-selectpicker-ready');
            } else {
                $('#toolbar').removeClass('items-selectpicker-ready');
            }
        };

        init_select_controls();

        $('#generate_barcodes').click(function() {
            window.open(
                'index.php/items/generateBarcodes/' + table_support.selected_ids().join(':'),
                '_blank'
            );
        });

        // When filter selection changes, refresh table.
        $('#filters').on('change', function() {
            table_support.refresh();
        });
        // Bootstrap-select compatibility (if plugin is active elsewhere)
        $('#filters').on('hidden.bs.select', function(e) {
            table_support.refresh();
        });

        // Load the preset daterange picker
        <?= view('partial/daterangepicker') ?>
        // Set the beginning of time as starting date
        $('#daterangepicker').data('daterangepicker').setStartDate("<?= date($config['dateformat'], mktime(0, 0, 0, 01, 01, 2010)) ?>");
        // Update the hidden inputs with the selected dates before submitting the search data
        var start_date = "<?= date('Y-m-d', mktime(0, 0, 0, 01, 01, 2010)) ?>";
        $("#daterangepicker").on('apply.daterangepicker', function(ev, picker) {
            table_support.refresh();
        });

        $("#stock_location").change(function() {
            table_support.refresh();
        });

        <?php
        echo view('partial/bootstrap_tables_locale');
        $employee = model(Employee::class);
        ?>

        table_support.init({
            employee_id: <?= $employee->get_logged_in_employee_info()->person_id ?>,
            resource: '<?= esc($controller_name) ?>',
            headers: <?= $table_headers ?>,
            pageSize: <?= $config['lines_per_page'] ?>,
            uniqueId: 'items.item_id',
            queryParams: function() {
                return $.extend(arguments[0], {
                    "start_date": start_date,
                    "end_date": end_date,
                    "stock_location": $("#stock_location").val(),
                    "filters": $("#filters").val()
                });
            },
            onLoadSuccess: function(response) {
                $('a.rollover').imgPreview({
                    imgCSS: {
                        width: 200
                    },
                    distanceFromCursor: {
                        top: 10,
                        left: -210
                    }
                })
            }
        });
    });
</script>

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= ucfirst($controller_name) ?></h3>
            <p class="neo-module-subtitle"><?= lang('Common.welcome_message') ?></p>
        </div>
        <div id="title_bar" class="btn-toolbar neo-module-actions print_hide">
            <button class="btn btn-info btn-sm modal-dlg" data-btn-submit="<?= lang('Common.submit') ?>" data-href="<?= "$controller_name/csvImport" ?>" title="<?= lang('Items.import_items_csv') ?>">
                <span class="glyphicon glyphicon-import">&nbsp;</span><?= lang('Common.import_csv') ?>
            </button>
            <button class="btn btn-primary btn-sm modal-dlg" data-btn-new="<?= lang('Common.new') ?>" data-btn-submit="<?= lang('Common.submit') ?>" data-href="<?= "$controller_name/view" ?>" title="<?= lang(ucfirst($controller_name) . '.new') ?>">
                <span class="glyphicon glyphicon-tag">&nbsp;</span><?= lang(ucfirst($controller_name) . '.new') ?>
            </button>
        </div>
    </header>

    <div id="toolbar" class="neo-table-toolbar">
        <div class="form-inline" role="toolbar">
            <button id="delete" class="btn btn-default btn-sm print_hide">
                <span class="glyphicon glyphicon-trash">&nbsp;</span><?= lang('Common.delete') ?>
            </button>
            <button id="bulk_edit" class="btn btn-default btn-sm modal-dlg print_hide" data-btn-submit="<?= lang('Common.submit') ?>" data-href="<?= "items/bulkEdit" ?>" title="<?= lang('Items.edit_multiple_items') ?>">
                <span class="glyphicon glyphicon-edit">&nbsp;</span><?= lang('Items.bulk_edit') ?>
            </button>
            <button id="generate_barcodes" class="btn btn-default btn-sm print_hide" data-href="<?= "$controller_name/generateBarcodes" ?>" title="<?= lang('Items.generate_barcodes') ?>">
                <span class="glyphicon glyphicon-barcode">&nbsp;</span><?= lang('Items.generate_barcodes') ?>
            </button>
            <?= form_input(['name' => 'daterangepicker', 'class' => 'form-control input-sm', 'id' => 'daterangepicker']) ?>
            <?= form_multiselect('filters[]', $filters, [''], [
                'id'                        => 'filters',
                'class'                     => 'form-control input-sm pos-items-filter-select',
                'size'                      => 1
            ]) ?>
            <?php
            if (count($stock_locations) > 1) {
                echo form_dropdown(
                    'stock_location',
                    $stock_locations,
                    $stock_location,
                    [
                        'id'         => 'stock_location',
                        'class'      => 'form-control input-sm'
                    ]
                );
            }
            ?>
        </div>
    </div>

    <div id="table_holder" class="neo-table-holder">
        <table id="table"></table>
    </div>
</section>

<?= view('partial/footer') ?>
