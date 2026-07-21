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

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= lang('Customers.customer') ?></h3>
            <p class="neo-module-subtitle"><?= lang('Common.welcome_message') ?></p>
        </div>
    </header>

    <?= view('customers/form') ?>
</section>

<?= view('partial/footer') ?>
