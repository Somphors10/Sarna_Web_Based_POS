<?php
/**
 * @var array $allowed_modules
 */
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    dialog_support.init("a.modal-dlg");
</script>

<section class="neo-home">
    <div class="neo-main">
        <header class="neo-main-header">
            <h2><?= lang('Common.welcome_message') ?></h2>
            <div class="neo-search-wrap">
                <input type="text" class="form-control" placeholder="<?= lang('Common.search') ?>">
            </div>
        </header>

        <div class="neo-info-grid">
            <?php foreach(array_slice($allowed_modules, 0, 4) as $module) { ?>
                <a class="neo-info-card" href="<?= base_url($module->module_id) ?>">
                    <img src="<?= base_url("images/menubar/$module->module_id.svg") ?>" alt="<?= lang("Module.$module->module_id") ?>">
                    <div>
                        <strong><?= lang("Module.$module->module_id") ?></strong>
                        <p><?= lang("Module.$module->module_id" . '_desc') ?></p>
                    </div>
                </a>
            <?php } ?>
        </div>

        <div class="neo-module-grid">
            <?php foreach($allowed_modules as $module) { ?>
                <a class="neo-module-card" href="<?= base_url($module->module_id) ?>" title="<?= lang("Module.$module->module_id" . '_desc') ?>">
                    <img class="neo-module-icon" src="<?= base_url("images/menubar/$module->module_id.svg") ?>" alt="<?= lang("Module.$module->module_id") ?>">
                    <span class="neo-module-title"><?= lang("Module.$module->module_id") ?></span>
                </a>
            <?php } ?>
        </div>
    </div>
</section>

<?= view('partial/footer') ?>
