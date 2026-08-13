<?php
/**
 * @var array $tax_codes
 */

if (empty($tax_codes)) {
    $tax_codes = [
        [
            'tax_code_id'   => -1,
            'tax_code'      => '',
            'tax_code_name' => '',
            'city'          => '',
            'state'         => '',
            'deleted'       => 0,
        ],
    ];
}

$i = 0;
?>

<div class="tax-repeat-list tax-repeat-list--codes">
    <div class="tax-repeat-head tax-repeat-head--codes" aria-hidden="true">
        <span class="tax-repeat-head__label"></span>
        <span class="tax-repeat-head__col"><?= lang('Taxes.code') ?></span>
        <span class="tax-repeat-head__col"><?= lang('Taxes.name') ?></span>
        <span class="tax-repeat-head__col"><?= lang('Taxes.city') ?></span>
        <span class="tax-repeat-head__col"><?= lang('Taxes.state') ?></span>
        <span class="tax-repeat-head__actions"></span>
    </div>

    <?php foreach ($tax_codes as $tax_code => $tax_code_data) {
        $tax_code_id = $tax_code_data['tax_code_id'];
        $tax_code = $tax_code_data['tax_code'];
        $tax_code_name = $tax_code_data['tax_code_name'];
        $city = $tax_code_data['city'];
        $state = $tax_code_data['state'];
        ++$i;
        $row_hidden = !empty($tax_code_data['deleted']);
        ?>

    <div class="tax-repeat-row form-group form-group-sm"<?= $row_hidden ? ' style="display:none;"' : '' ?>>
        <div class="tax-repeat-row__label">
            <?= form_label(lang('Taxes.tax_code') . ' ' . $i, 'tax_code_' . $i) ?>
        </div>
        <div class="tax-repeat-row__grid tax-repeat-row__grid--codes">
            <div class="tax-repeat-row__field">
                <?= form_input([
                    'name'        => 'tax_code[]',
                    'id'          => 'tax_code_' . $i,
                    'class'       => 'valid_chars text-uppercase form-control input-sm',
                    'placeholder' => lang('Taxes.code'),
                    'value'       => $tax_code,
                ]) ?>
            </div>
            <div class="tax-repeat-row__field">
                <?= form_input([
                    'name'        => 'tax_code_name[]',
                    'class'       => 'valid_chars form-control input-sm',
                    'placeholder' => lang('Taxes.name'),
                    'value'       => $tax_code_name,
                ]) ?>
            </div>
            <div class="tax-repeat-row__field">
                <?= form_input([
                    'name'        => 'city[]',
                    'class'       => 'valid_chars form-control input-sm',
                    'placeholder' => lang('Taxes.city'),
                    'value'       => $city,
                ]) ?>
            </div>
            <div class="tax-repeat-row__field">
                <?= form_input([
                    'name'        => 'state[]',
                    'class'       => 'valid_chars form-control input-sm',
                    'placeholder' => lang('Taxes.state'),
                    'value'       => $state,
                ]) ?>
            </div>
        </div>
        <div class="tax-repeat-row__actions">
            <button type="button" class="tax-repeat-btn tax-repeat-btn--add add_tax_code" title="Add row" aria-label="Add row">
                <span aria-hidden="true">+</span>
            </button>
            <button type="button" class="tax-repeat-btn tax-repeat-btn--remove remove_tax_code" title="<?= lang('Common.delete') ?>" aria-label="<?= lang('Common.delete') ?>">
                <span aria-hidden="true">−</span>
            </button>
        </div>
        <?= form_hidden('tax_code_id[]', (string)$tax_code_id) ?>
    </div>

    <?php } ?>
</div>
