<?php
/**
 * Receipt fragment for Sales → Manage modal (no header, sidebar, or footer).
 *
 * @var array $config
 */

if (isset($error_message)) {
    echo '<div class="alert alert-danger">' . esc($error_message) . '</div>';
    return;
}
?>

<div class="receipt-modal-shell receipt-page">
    <div class="receipt-page__panel">
        <?= view('sales/' . $config['receipt_template']) ?>
    </div>
</div>
