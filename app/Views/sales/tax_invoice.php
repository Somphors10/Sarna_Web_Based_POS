<?php
/**
 * @var int $sale_id_num
 * @var bool $print_after_sale
 * @var string $customer_info
 * @var string $company_info
 * @var string $invoice_number
 * @var string $transaction_date
 * @var float $total
 * @var bool $include_hsn
 * @var string $discount
 * @var array $cart
 * @var float $subtotal
 * @var array $taxes
 * @var array $payments
 * @var string $amount_change
 * @var string $barcode
 * @var int $sale_id
 * @var array $config
 */

use App\Models\Employee;

?>

<?= view('partial/header') ?>

<link rel="stylesheet" href="css/invoice.css?v=4">

<?php
if (isset($error_message)) {
    echo '<div class="alert alert-dismissible alert-danger">' . esc($error_message) . '</div>';
    exit;
}
?>

<?= view('partial/print_receipt', ['print_after_sale' => $print_after_sale, 'selected_printer' => 'invoice_printer']) ?>

<section class="neo-module-page invoice-page">
    <header class="neo-module-header print_hide">
        <div>
            <h3 class="neo-module-title"><?= lang('Sales.tax_invoice') ?></h3>
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

    <div class="invoice-page__panel">
        <div id="page-wrap" class="invoice-document">
            <div class="invoice-doc-top">
                <div class="invoice-doc-brand">
                    <?php if ($config['company_logo'] != '') { ?>
                        <div class="invoice-doc-brand__logo">
                            <img id="image" src="<?= esc(base_url('uploads/' . $config['company_logo']), 'url') ?>" alt="company_logo">
                        </div>
                    <?php } ?>
                    <div class="invoice-doc-brand__info">
                        <?php if ($config['receipt_show_company_name']) { ?>
                            <div id="company_name" class="invoice-doc-brand__name"><?= esc($config['company']) ?></div>
                        <?php } ?>
                        <div id="company-title"><?= nl2br(esc($company_info)) ?></div>
                    </div>
                </div>

                <div class="invoice-doc-summary">
                    <div id="header" class="invoice-doc-summary__title"><?= lang('Sales.tax_invoice') ?></div>
                    <table id="meta" class="invoice-doc-summary__table">
                        <tr>
                            <td class="meta-head"><?= lang('Sales.invoice_number') ?></td>
                            <td><?= esc($invoice_number) ?></td>
                        </tr>
                        <tr>
                            <td class="meta-head"><?= lang('Common.date') ?></td>
                            <td><?= esc($transaction_date) ?></td>
                        </tr>
                        <tr>
                            <td class="meta-head"><?= lang('Sales.amount_due') ?></td>
                            <td class="invoice-doc-summary__total"><?= to_currency($total) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php if (isset($customer)) { ?>
                <div class="invoice-doc-billto">
                    <span class="invoice-doc-billto__label"><?= lang('Customers.customer') ?></span>
                    <div id="customer"><?= nl2br(esc($customer_info)) ?></div>
                </div>
            <?php } ?>

            <table id="items" class="invoice-doc-items">
                <thead>
                <tr>
                    <th><?= lang('Sales.item_number') ?></th>
                    <?php
                    $invoice_columns = 6;
                    if ($include_hsn) {
                        $invoice_columns += 1;
                        ?>
                        <th><?= lang('Sales.hsn') ?></th>
                    <?php } ?>
                    <th><?= lang('Sales.item_name') ?></th>
                    <th><?= lang('Sales.quantity') ?></th>
                    <th><?= lang('Sales.price') ?></th>
                    <th><?= lang('Sales.discount') ?></th>
                    <?php
                    if ($discount > 0) {
                        $invoice_columns += 1;
                        ?>
                        <th><?= lang('Sales.customer_discount') ?></th>
                    <?php } ?>
                    <th class="invoice-doc-items__total-col"><?= lang('Sales.total') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($cart as $line => $item) {
                    if ($item['print_option'] == PRINT_YES) {
                        ?>
                        <tr class="item-row">
                            <td><?= esc($item['item_number']) ?></td>
                            <?php if ($include_hsn): ?>
                                <td class="text-center"><?= esc($item['hsn_code']) ?></td>
                            <?php endif; ?>
                            <td class="item-name"><?= esc($item['name']) ?></td>
                            <td class="text-center"><?= to_quantity_decimals($item['quantity']) ?></td>
                            <td><?= to_currency($item['price']) ?></td>
                            <td class="text-center"><?= ($item['discount_type'] == FIXED) ? to_currency($item['discount']) : to_decimals($item['discount']) . '%' ?></td>
                            <?php if ($discount > 0): ?>
                                <td class="text-center"><?= to_currency($item['discounted_total'] / $item['quantity']) ?></td>
                            <?php endif; ?>
                            <td class="invoice-doc-items__amount"><?= to_currency($item['discounted_total']) ?></td>
                        </tr>
                        <?php if ($item['is_serialized'] || ($item['allow_alt_description'] && !empty($item['description']))) { ?>
                            <tr class="item-row item-row--serial">
                                <td><?= esc($item['hsn_code']) ?></td>
                                <td class="item-description" colspan="<?= $invoice_columns - 2 ?>"><?= esc($item['description']) ?></td>
                                <td class="text-center"><?= esc($item['serialnumber']) ?></td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="<?= $invoice_columns - 3 ?>" class="blank-bottom"></td>
                    <td colspan="2" class="total-line"><?= lang('Sales.sub_total') ?></td>
                    <td class="total-value" id="subtotal"><?= to_currency($subtotal) ?></td>
                </tr>

                <?php foreach ($taxes as $tax_group_index => $tax) { ?>
                    <tr>
                        <td colspan="<?= $invoice_columns - 3 ?>" class="blank"></td>
                        <td colspan="2" class="total-line"><?= (float)$tax['tax_rate'] . '% ' . esc($tax['tax_group']) ?></td>
                        <td class="total-value" id="taxes"><?= to_currency_tax($tax['sale_tax_amount']) ?></td>
                    </tr>
                <?php } ?>

                <tr class="invoice-doc-items__grand-total">
                    <td colspan="<?= $invoice_columns - 3 ?>" class="blank"></td>
                    <td colspan="2" class="total-line"><?= lang('Sales.total') ?></td>
                    <td class="total-value" id="total"><?= to_currency($total) ?></td>
                </tr>

                <?php
                $only_sale_check = false;
                $show_giftcard_remainder = false;
                foreach ($payments as $payment_id => $payment) {
                    $only_sale_check |= $payment['payment_type'] == lang('Sales.check');
                    $splitpayment = explode(':', $payment['payment_type']);
                    $show_giftcard_remainder |= $splitpayment[0] == lang('Sales.giftcard');
                    ?>
                    <tr>
                        <td colspan="<?= $invoice_columns - 3 ?>" class="blank"></td>
                        <td colspan="2" class="total-line"><?= esc($splitpayment[0]) ?></td>
                        <td class="total-value" id="paid"><?= to_currency($payment['payment_amount'] * -1) ?></td>
                    </tr>
                <?php } ?>

                <?php if (isset($cur_giftcard_value) && $show_giftcard_remainder) { ?>
                    <tr>
                        <td colspan="<?= $invoice_columns - 3 ?>" class="blank"></td>
                        <td colspan="2" class="total-line"><?= lang('Sales.giftcard_balance') ?></td>
                        <td class="total-value" id="giftcard"><?= to_currency($cur_giftcard_value) ?></td>
                    </tr>
                <?php } ?>

                <?php if (!empty($payments)) { ?>
                    <tr>
                        <td colspan="<?= $invoice_columns - 3 ?>"><?= lang('Sales.authorized_signature') ?>:</td>
                        <td colspan="2" class="total-line"><?= lang($amount_change >= 0 ? ($only_sale_check ? 'Sales.check_balance' : 'Sales.change_due') : 'Sales.amount_due') ?></td>
                        <td class="total-value" id="change"><?= to_currency($amount_change) ?></td>
                    </tr>
                <?php } ?>
                </tfoot>
            </table>

            <div id="terms" class="invoice-doc-footer">
                <div id="sale_return_policy">
                    <?php if (!empty($config['payment_message'])) { ?>
                        <p class="invoice-doc-footer__note"><?= nl2br(esc($config['payment_message'])) ?></p>
                    <?php } ?>
                    <?php if (!empty($comments) || !empty($config['invoice_default_comments'])) { ?>
                        <p class="invoice-doc-footer__note">
                            <?= empty($comments) ? nl2br(esc($config['invoice_default_comments'])) : esc(lang('Sales.comments')) . ': ' . nl2br(esc($comments)) ?>
                        </p>
                    <?php } ?>
                    <?php if (!empty($config['return_policy'])) { ?>
                        <p class="invoice-doc-footer__policy"><?= nl2br(esc($config['return_policy'])) ?></p>
                    <?php } ?>
                </div>
                <div id="barcode">
                    <img style="padding-top: 4%;" alt="<?= esc($barcode) ?>" src="data:image/png;base64,<?= esc($barcode) ?>"><br>
                    <?= esc($sale_id) ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(window).on("load", function() {
        if (window.jsPrintSetup) {
            <?php if (!$config['print_header']) { ?>
                jsPrintSetup.setOption('headerStrLeft', '');
                jsPrintSetup.setOption('headerStrCenter', '');
                jsPrintSetup.setOption('headerStrRight', '');
            <?php } ?>

            <?php if (!$config['print_footer']) { ?>
                jsPrintSetup.setOption('footerStrLeft', '');
                jsPrintSetup.setOption('footerStrCenter', '');
                jsPrintSetup.setOption('footerStrRight', '');
            <?php } ?>
        }
    });
</script>

<?= view('partial/footer') ?>
