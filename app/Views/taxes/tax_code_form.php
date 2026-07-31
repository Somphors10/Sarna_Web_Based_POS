<?php
/**
 * @var object $tax_code_info
 * @var array $tax_rates
 */
$tax_code_id = $tax_code_info->tax_code_id ?? NEW_ENTRY;
$tax_rates = $tax_rates ?? [];
?>

<div id="required_fields_message"><?= lang('Common.fields_required_message') ?></div>
<ul id="error_message_box" class="error_message_box"></ul>

<?= form_open('tax_codes/save/' . $tax_code_id, ['id' => 'tax_code_form', 'class' => 'form-horizontal']) ?>
    <fieldset id="tax_code_basic_info">
        <div class="form-group form-group-sm">
            <?= form_label(lang('Taxes.tax_code'), 'tax_code', ['class' => 'required control-label col-xs-3']) ?>
            <div class="col-xs-4">
                <?= form_input([
                    'name'  => 'tax_code',
                    'id'    => 'tax_code',
                    'class' => 'form-control input-sm text-uppercase',
                    'value' => $tax_code_info->tax_code ?? '',
                ]) ?>
            </div>
        </div>

        <div class="form-group form-group-sm">
            <?= form_label(lang('Taxes.tax_code_name'), 'tax_code_name', ['class' => 'required control-label col-xs-3']) ?>
            <div class="col-xs-8">
                <?= form_input([
                    'name'  => 'tax_code_name',
                    'id'    => 'tax_code_name',
                    'class' => 'form-control input-sm',
                    'value' => $tax_code_info->tax_code_name ?? '',
                ]) ?>
            </div>
        </div>

        <div class="form-group form-group-sm">
            <?= form_label(lang('Common.city'), 'city', ['class' => 'control-label col-xs-3']) ?>
            <div class="col-xs-8">
                <?= form_input([
                    'name'  => 'city',
                    'id'    => 'city',
                    'class' => 'form-control input-sm',
                    'value' => $tax_code_info->city ?? '',
                ]) ?>
            </div>
        </div>

        <div class="form-group form-group-sm">
            <?= form_label(lang('Common.state'), 'state', ['class' => 'control-label col-xs-3']) ?>
            <div class="col-xs-8">
                <?= form_input([
                    'name'  => 'state',
                    'id'    => 'state',
                    'class' => 'form-control input-sm',
                    'value' => $tax_code_info->state ?? '',
                ]) ?>
            </div>
        </div>

        <?php if (!empty($tax_rates)) { ?>
            <div class="form-group form-group-sm">
                <?= form_label(lang('Taxes.tax_rate'), '', ['class' => 'control-label col-xs-3']) ?>
                <div class="col-xs-8">
                    <table class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th><?= lang('Taxes.tax_category') ?></th>
                            <th><?= lang('Taxes.tax_rate') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($tax_rates as $tax_rate_row) { ?>
                            <tr>
                                <td><?= esc($tax_rate_row['tax_category']) ?></td>
                                <td><?= esc(to_tax_decimals($tax_rate_row['tax_rate'])) ?>%</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    </fieldset>
<?= form_close() ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#tax_code_form').validate($.extend(form_support.handler, {
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    success: function(response) {
                        dialog_support.hide();
                        table_support.handle_submit('tax_codes', response);
                    },
                    dataType: 'json'
                });
            },
            rules: {
                tax_code: {
                    required: true,
                    minlength: 1
                },
                tax_code_name: 'required'
            },
            messages: {
                tax_code: {
                    required: <?= json_encode(lang('Taxes.tax_code_required')) ?>,
                    minlength: <?= json_encode(lang('Taxes.tax_code_required')) ?>
                },
                tax_code_name: <?= json_encode(lang('Taxes.tax_code_required')) ?>
            }
        }));
    });
</script>
