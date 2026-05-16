<?php
/**
 * @var string $controller_name
 * @var object $person_info
 * @var array $packages
 * @var int $selected_package
 * @var bool $use_destination_based_tax
 * @var string $sales_tax_code_label
 * @var string $employee
 * @var array $config
 */
?>

<?= view('partial/header') ?>

<div class="panel panel-default" style="margin: 16px 0;">
    <div class="panel-body">
    <?= view('customers/form') ?>
    </div>
</div>

<?= view('partial/footer') ?>
