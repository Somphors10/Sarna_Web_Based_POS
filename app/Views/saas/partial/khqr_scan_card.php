<?php
/**
 * KHQR scan card — red bar, dashed line, QR code only (no merchant name on the picture).
 *
 * @var string $qr_url
 * @var string $wrapper_class
 */
$qr_url = (string)($qr_url ?? '');
$wrapper_class = trim((string)($wrapper_class ?? ''));
?>
<div class="khqr-scan-card<?= $wrapper_class !== '' ? ' ' . esc($wrapper_class, 'attr') : '' ?>">
    <div class="khqr-scan-card__bar">KHQR</div>
    <div class="khqr-scan-card__rule" aria-hidden="true"></div>
    <div class="khqr-scan-card__code">
        <img src="<?= esc($qr_url, 'attr') ?>" alt="ABA KHQR" width="240" height="240" loading="lazy">
    </div>
</div>
