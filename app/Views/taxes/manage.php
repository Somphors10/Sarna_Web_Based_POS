<?php
/**
 * @var string $controller_name
 */
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    dialog_support.init("a.modal-dlg");
</script>

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= lang('Module.taxes') ?></h3>
            <p class="neo-module-subtitle"><?= lang('Common.welcome_message') ?></p>
        </div>
    </header>

    <div class="pos-form-shell">
        <ul class="nav nav-tabs pos-form-tabs" data-tabs="tabs">
            <li class="active" role="presentation">
                <a data-toggle="tab" href="#tax_codes_tab" title="<?= lang(ucfirst($controller_name) . '.tax_codes_configuration') ?>">
                    <?= lang(ucfirst($controller_name) . '.tax_codes') ?>
                </a>
            </li>
            <li role="presentation">
                <a data-toggle="tab" href="#tax_jurisdictions_tab" title="<?= lang(ucfirst($controller_name) . '.tax_jurisdictions_configuration') ?>">
                    <?= lang(ucfirst($controller_name) . '.tax_jurisdictions') ?>
                </a>
            </li>
            <li role="presentation">
                <a data-toggle="tab" href="#tax_categories_tab" title="<?= lang(ucfirst($controller_name) . '.tax_categories_configuration') ?>">
                    <?= lang(ucfirst($controller_name) . '.tax_categories') ?>
                </a>
            </li>
            <li role="presentation">
                <a data-toggle="tab" href="#tax_rates_tab" title="<?= lang(ucfirst($controller_name) . '.tax_rate_configuration') ?>">
                    <?= lang(ucfirst($controller_name) . '.tax_rates') ?>
                </a>
            </li>
        </ul>

        <div class="tab-content pos-modern-form">
            <div class="tab-pane fade in active" id="tax_codes_tab">
                <?= view('taxes/tax_codes') ?>
            </div>
            <div class="tab-pane" id="tax_jurisdictions_tab">
                <?= view('taxes/tax_jurisdictions') ?>
            </div>
            <div class="tab-pane" id="tax_categories_tab">
                <?= view('taxes/tax_categories') ?>
            </div>
            <div class="tab-pane" id="tax_rates_tab">
                <?= view('taxes/tax_rates') ?>
            </div>
        </div>
    </div>
</section>

<?= view('partial/footer') ?>
