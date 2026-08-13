<?php
/**
 * @var array $tax_jurisdictions
 * @var array $tax_types
 */

if (empty($tax_jurisdictions)) {
    $tax_jurisdictions = [
        [
            'jurisdiction_id'      => -1,
            'jurisdiction_name'    => '',
            'tax_group'            => '',
            'reporting_authority'  => '',
            'tax_type'             => '',
            'tax_group_sequence'   => '',
            'cascade_sequence'     => '',
        ],
    ];
}

$i = 0;
?>

<div class="tax-repeat-list tax-repeat-list--jurisdictions">
    <div class="tax-repeat-head tax-repeat-head--jurisdictions" aria-hidden="true">
        <span class="tax-repeat-head__label"></span>
        <span class="tax-repeat-head__col"><?= lang('Taxes.jurisdiction_name') ?></span>
        <span class="tax-repeat-head__col tax-repeat-head__col--narrow"><?= lang('Taxes.tax_group') ?></span>
        <span class="tax-repeat-head__col"><?= lang('Taxes.tax_type') ?></span>
        <span class="tax-repeat-head__col"><?= lang('Taxes.reporting_authority') ?></span>
        <span class="tax-repeat-head__col tax-repeat-head__col--narrow"><?= lang('Taxes.sequence') ?></span>
        <span class="tax-repeat-head__col tax-repeat-head__col--narrow"><?= lang('Taxes.cascade_sequence') ?></span>
        <span class="tax-repeat-head__actions"></span>
    </div>

    <?php foreach ($tax_jurisdictions as $tax_jurisdiction => $jurisdiction) {
        $jurisdiction_id = $jurisdiction['jurisdiction_id'];
        $jurisdiction_name = $jurisdiction['jurisdiction_name'];
        $tax_group = $jurisdiction['tax_group'];
        $reporting_authority = $jurisdiction['reporting_authority'];
        $tax_type = $jurisdiction['tax_type'];
        $tax_group_sequence = $jurisdiction['tax_group_sequence'];
        $cascade_sequence = $jurisdiction['cascade_sequence'];
        ++$i;
        ?>

    <div class="tax-repeat-row form-group form-group-sm">
        <div class="tax-repeat-row__label">
            <?= form_label(lang('Taxes.tax_jurisdiction') . ' ' . $i, 'jurisdiction_name_' . $i) ?>
        </div>
        <div class="tax-repeat-row__grid tax-repeat-row__grid--jurisdictions">
            <div class="tax-repeat-row__field">
                <?= form_input([
                    'name'        => 'jurisdiction_name[]',
                    'id'          => 'jurisdiction_name_' . $i,
                    'class'       => 'valid_chars form-control input-sm',
                    'placeholder' => lang('Taxes.jurisdiction_name'),
                    'value'       => $jurisdiction_name,
                ]) ?>
            </div>
            <div class="tax-repeat-row__field tax-repeat-row__field--narrow">
                <?= form_input([
                    'name'        => 'tax_group[]',
                    'class'       => 'valid_chars form-control input-sm',
                    'placeholder' => lang('Taxes.tax_group'),
                    'value'       => $tax_group,
                ]) ?>
            </div>
            <div class="tax-repeat-row__field">
                <?= form_dropdown('tax_type[]' . $i, $tax_types, $tax_type, ['class' => 'form-control input-sm']) ?>
            </div>
            <div class="tax-repeat-row__field">
                <?= form_input([
                    'name'        => 'reporting_authority[]',
                    'class'       => 'valid_chars form-control input-sm',
                    'placeholder' => lang('Taxes.reporting_authority'),
                    'value'       => $reporting_authority,
                ]) ?>
            </div>
            <div class="tax-repeat-row__field tax-repeat-row__field--narrow">
                <?= form_input([
                    'name'        => 'tax_group_sequence[]',
                    'class'       => 'valid_chars form-control input-sm',
                    'placeholder' => lang('Taxes.sequence'),
                    'value'       => $tax_group_sequence,
                ]) ?>
            </div>
            <div class="tax-repeat-row__field tax-repeat-row__field--narrow">
                <?= form_input([
                    'name'        => 'cascade_sequence[]',
                    'class'       => 'valid_chars form-control input-sm',
                    'placeholder' => lang('Taxes.cascade_sequence'),
                    'value'       => $cascade_sequence,
                ]) ?>
            </div>
        </div>
        <div class="tax-repeat-row__actions">
            <button type="button" class="tax-repeat-btn tax-repeat-btn--add add_tax_jurisdiction" title="Add row" aria-label="Add row">
                <span aria-hidden="true">+</span>
            </button>
            <button type="button" class="tax-repeat-btn tax-repeat-btn--remove remove_tax_jurisdiction" title="<?= lang('Common.delete') ?>" aria-label="<?= lang('Common.delete') ?>">
                <span aria-hidden="true">−</span>
            </button>
        </div>
        <?= form_hidden('jurisdiction_id[]', (string)$jurisdiction_id) ?>
    </div>

    <?php } ?>
</div>
