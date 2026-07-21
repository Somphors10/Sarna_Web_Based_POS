<div class="login-pos-bg" aria-hidden="true">
    <div class="login-pos-receipt">
        <p class="login-pos-receipt__title">Receipt</p>
        <div class="login-pos-receipt__line"></div>
        <div class="login-pos-receipt__line login-pos-receipt__line--short"></div>
        <div class="login-pos-receipt__line"></div>
        <div class="login-pos-receipt__dash"></div>
        <div class="login-pos-receipt__total">
            <span>Total</span>
            <span>$24.50</span>
        </div>
    </div>

    <div class="login-pos-barcode">
        <?php for ($i = 0; $i < 18; $i++): ?>
            <span></span>
        <?php endfor; ?>
    </div>

    <div class="login-pos-tile">
        <div class="login-pos-tile__icon">$</div>
        <p class="login-pos-tile__name">Sale Item</p>
        <p class="login-pos-tile__price">$12.00</p>
    </div>

    <div class="login-pos-total">
        <p class="login-pos-total__label">Register Total</p>
        <p class="login-pos-total__amount">$0.00</p>
    </div>
</div>
