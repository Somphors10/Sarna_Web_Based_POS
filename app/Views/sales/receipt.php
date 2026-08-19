<?php
/**
 * @var int $sale_id_num
 * @var bool $print_after_sale
 * @var array $config
 */

use App\Models\Employee;

?>

<?= view('partial/header') ?>

<?php
if (isset($error_message)) {
    echo '<div class="alert alert-dismissible alert-danger">' . $error_message . '</div>';
    exit;
}
?>

<?= view('partial/print_receipt', ['print_after_sale' => $print_after_sale, 'selected_printer' => 'receipt_printer']) ?>

<section class="neo-module-page receipt-page">
    <header class="neo-module-header print_hide">
        <div>
            <h3 class="neo-module-title"><?= lang('Sales.receipt') ?></h3>
            <p class="neo-module-subtitle"><?= esc($config['company']) ?></p>
        </div>
        <div id="control_buttons" class="neo-module-actions btn-toolbar">
            <button type="button" class="btn btn-info btn-sm" id="show_print_button" onclick="printdoc();">
                <span class="glyphicon glyphicon-print"></span><?= lang('Common.print') ?>
            </button>
            <?= anchor('sales', '<span class="glyphicon glyphicon-shopping-cart"></span>' . lang('Sales.register'), ['class' => 'btn btn-info btn-sm', 'id' => 'show_sales_button']) ?>
            <?php
            $employee = model(Employee::class);
            if ($employee->has_grant('reports_sales', session('person_id'))): ?>
                <?= anchor('sales/manage', '<span class="glyphicon glyphicon-list-alt"></span>' . lang('Sales.takings'), ['class' => 'btn btn-info btn-sm', 'id' => 'show_takings_button']) ?>
            <?php endif; ?>
        </div>
    </header>

    <div class="receipt-page__panel">
        <?= view('sales/' . $config['receipt_template']) ?>
    </div>
</section>

<?= view('partial/footer') ?>
