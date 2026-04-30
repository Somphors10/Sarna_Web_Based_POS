<?php
/**
 * @var string $controller_name
 * @var string $table_headers
 * @var array $config
 */
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    $(document).ready(function() {
        <?= view('partial/bootstrap_tables_locale') ?>

        table_support.init({
            resource: '<?= esc($controller_name) ?>',
            headers: <?= $table_headers ?>,
            pageSize: <?= $config['lines_per_page'] ?>,
            uniqueId: 'definition_id_key'
        });
    });
</script>

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= ucfirst($controller_name) ?></h3>
            <p class="neo-module-subtitle"><?= lang('Common.welcome_message') ?></p>
        </div>
        <div id="title_bar" class="btn-toolbar neo-module-actions print_hide">
            <button class="btn btn-primary btn-sm modal-dlg" data-btn-submit="<?= lang('Common.submit') ?>" data-href="<?= esc("$controller_name/view") ?>" title="<?= lang(ucfirst($controller_name) . ".new") ?>">
                <span class="glyphicon glyphicon-star">&nbsp;</span><?= lang(ucfirst($controller_name) . ".new") ?>
            </button>
        </div>
    </header>

    <div id="toolbar" class="neo-table-toolbar">
        <div class="form-inline" role="toolbar">
            <button id="delete" class="btn btn-default btn-sm print_hide">
                <span class="glyphicon glyphicon-trash">&nbsp;</span><?= lang('Common.delete') ?>
            </button>
        </div>
    </div>

    <div id="table_holder" class="neo-table-holder">
        <table id="table"></table>
    </div>
</section>

<?= view('partial/footer') ?>
