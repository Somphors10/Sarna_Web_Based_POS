<?php
/**
 * @var array $allowed_modules
 */

$office_cards = function_exists('rbac_office_display_ids')
    ? rbac_office_display_ids()
    : ['employees', 'roles', 'expenses_categories', 'config'];
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    dialog_support.init("a.modal-dlg");
</script>

<section class="neo-home">
    <div class="neo-main">
        <header class="neo-main-header">
            <h2><?= esc(lang('Module.office')) ?></h2>
        </header>

        <div class="neo-module-grid">
            <?php foreach ($office_cards as $module_id): ?>
                <a class="neo-module-card" href="<?= base_url($module_id) ?>" title="<?= lang("Module.$module_id" . '_desc') ?>">
                    <img class="neo-module-icon" src="<?= base_url(pos_module_nav_icon($module_id)) ?>" alt="<?= lang("Module.$module_id") ?>">
                    <span class="neo-module-title"><?= lang("Module.$module_id") ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?= view('partial/footer') ?>
