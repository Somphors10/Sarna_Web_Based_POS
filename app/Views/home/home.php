<?php
/**
 * @var array $allowed_modules
 */
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    dialog_support.init("a.modal-dlg");

    $(document).ready(function() {
        const $search = $('#neo_module_search');
        const $cards = $('.neo-module-card');

        $search.on('input', function() {
            const q = $(this).val().toLowerCase().trim();

            $cards.each(function() {
                const text = ($(this).data('search') || '').toLowerCase();
                $(this).toggle(text.indexOf(q) !== -1);
            });
        });
    });
</script>

<section class="neo-home">
    <div class="neo-main">
        <header class="neo-main-header">
            <div>
                <p class="neo-main-eyebrow">POS Dashboard</p>
                <h2><?= lang('Common.welcome_message') ?></h2>
                <p class="neo-main-subtitle">Run sales quickly, manage inventory, and track daily operations in one place.</p>
            </div>
            <div class="neo-search-wrap">
                <input id="neo_module_search" type="text" class="form-control" placeholder="<?= lang('Common.search') ?> modules">
            </div>
        </header>

        <?php
            // For assignment/demo: hide only a few low-priority modules.
            $hidden_dashboard_modules = ['messages', 'migrate', 'giftcards', 'cashups'];
            $visible_modules = array_values(array_filter($allowed_modules, static fn($module) => !in_array($module->module_id, $hidden_dashboard_modules, true)));
            $visible_module_ids = array_map(static fn($module) => $module->module_id, $visible_modules);
        ?>

        <div class="neo-kpi-grid">
            <div class="neo-kpi-card">
                <span class="neo-kpi-label">Active Modules</span>
                <strong class="neo-kpi-value"><?= count($visible_modules) ?></strong>
            </div>
            <div class="neo-kpi-card">
                <span class="neo-kpi-label">Sales Shortcuts</span>
                <strong class="neo-kpi-value"><?= in_array('sales', $visible_module_ids, true) ? 'Ready' : 'N/A' ?></strong>
            </div>
            <div class="neo-kpi-card">
                <span class="neo-kpi-label">Inventory Access</span>
                <strong class="neo-kpi-value"><?= in_array('items', $visible_module_ids, true) ? 'Enabled' : 'N/A' ?></strong>
            </div>
            <div class="neo-kpi-card">
                <span class="neo-kpi-label">Receivings Access</span>
                <strong class="neo-kpi-value"><?= in_array('receivings', $visible_module_ids, true) ? 'Enabled' : 'N/A' ?></strong>
            </div>
        </div>

        <div class="neo-info-grid">
            <?php foreach(array_slice($visible_modules, 0, 3) as $module) { ?>
                <a class="neo-info-card" href="<?= base_url($module->module_id) ?>">
                    <img src="<?= base_url("images/menubar/$module->module_id.svg") ?>" alt="<?= lang("Module.$module->module_id") ?>">
                    <div>
                        <strong><?= lang("Module.$module->module_id") ?></strong>
                        <p><?= lang("Module.$module->module_id" . '_desc') ?></p>
                    </div>
                </a>
            <?php } ?>
        </div>

        <h3 class="neo-section-title">Quick Access</h3>
        <div class="neo-module-grid">
            <?php foreach($visible_modules as $module) { ?>
                <a class="neo-module-card"
                   href="<?= base_url($module->module_id) ?>"
                   title="<?= lang("Module.$module->module_id" . '_desc') ?>"
                   data-search="<?= esc(lang("Module.$module->module_id") . ' ' . lang("Module.$module->module_id" . '_desc')) ?>">
                    <img class="neo-module-icon" src="<?= base_url("images/menubar/$module->module_id.svg") ?>" alt="<?= lang("Module.$module->module_id") ?>">
                    <span class="neo-module-title"><?= lang("Module.$module->module_id") ?></span>
                    <span class="neo-module-subtitle"><?= lang("Module.$module->module_id" . '_desc') ?></span>
                </a>
            <?php } ?>
        </div>
    </div>
</section>

<?= view('partial/footer') ?>
