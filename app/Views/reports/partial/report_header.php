<?php
/**
 * Shared report page header: Reports / Title + subtitle.
 *
 * @var string $title
 * @var string|null $subtitle
 */
$title = $title ?? '';
$subtitle = $subtitle ?? '';
?>
<header class="neo-module-header neo-report-page-header">
    <div>
        <div class="neo-report-breadcrumb" aria-label="breadcrumb">
            <a href="<?= site_url('reports') ?>"><?= lang('Module.reports') ?></a>
            <span class="neo-report-breadcrumb-sep">/</span>
            <span class="neo-report-breadcrumb-current"><?= esc($title) ?></span>
        </div>
        <h3 class="neo-module-title"><?= esc($title) ?></h3>
        <?php if ($subtitle !== '' && $subtitle !== null): ?>
            <p class="neo-module-subtitle"><?= esc($subtitle) ?></p>
        <?php endif; ?>
    </div>
</header>
