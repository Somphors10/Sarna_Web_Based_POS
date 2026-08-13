<?php
/**
 * @var array $tax_categories
 */

if (empty($tax_categories)) {
    $tax_categories = [
        [
            'tax_category_id'    => -1,
            'tax_category'       => '',
            'tax_group_sequence' => '',
        ],
    ];
}

$i = 0;
?>

<div class="tax-repeat-list tax-repeat-list--categories">
    <div class="tax-repeat-head tax-repeat-head--categories" aria-hidden="true">
        <span class="tax-repeat-head__label"></span>
        <span class="tax-repeat-head__col"><?= lang('Taxes.tax_category_name') ?></span>
        <span class="tax-repeat-head__col tax-repeat-head__col--narrow"><?= lang('Taxes.sequence') ?></span>
        <span class="tax-repeat-head__actions"></span>
    </div>

    <?php foreach ($tax_categories as $key => $category) {
        $tax_category_id = $category['tax_category_id'];
        $tax_category = $category['tax_category'];
        $tax_group_sequence = $category['tax_group_sequence'];
        ++$i;
        ?>

    <div class="tax-repeat-row form-group form-group-sm">
        <div class="tax-repeat-row__label">
            <?= form_label(lang('Taxes.tax_category') . ' ' . $i, 'tax_category_' . $i) ?>
        </div>
        <div class="tax-repeat-row__grid tax-repeat-row__grid--categories">
            <div class="tax-repeat-row__field">
                <?= form_input([
                    'name'        => 'tax_category[]',
                    'id'          => 'tax_category_' . $i,
                    'class'       => 'valid_chars form-control input-sm',
                    'placeholder' => lang('Taxes.tax_category_name'),
                    'value'       => $tax_category,
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
        </div>
        <div class="tax-repeat-row__actions">
            <button type="button" class="tax-repeat-btn tax-repeat-btn--add add_tax_category" title="Add row" aria-label="Add row">
                <span aria-hidden="true">+</span>
            </button>
            <button type="button" class="tax-repeat-btn tax-repeat-btn--remove remove_tax_category" title="<?= lang('Common.delete') ?>" aria-label="<?= lang('Common.delete') ?>">
                <span aria-hidden="true">−</span>
            </button>
        </div>
        <?= form_hidden('tax_category_id[]', (string)$tax_category_id) ?>
    </div>

    <?php } ?>
</div>
