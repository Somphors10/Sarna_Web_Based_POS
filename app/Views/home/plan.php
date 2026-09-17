<?php
/**
 * @var array $plan
 * @var string $pay_url
 * @var string $shop_name
 */
$status = (string)($plan['status'] ?? 'none');
$show_renew = !empty($plan['show_renew']);
$status_label = [
    'active'  => lang('Login.subscription_status_active'),
    'warning' => lang('Login.subscription_status_warning'),
    'expired' => lang('Login.subscription_status_expired'),
][$status] ?? '';
$price = (string)($plan['price_label'] ?? '$20 / month');
?>

<?= view('partial/header') ?>

<style>
.pos-plan-page { padding: 8px 0 48px; }
.pos-plan-page__head { text-align: center; max-width: 640px; margin: 8px auto 28px; }
.pos-plan-page__head h1 {
    margin: 0;
    color: #0f172a !important;
    font-size: 34px;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.2;
}
.pos-plan-page__head p { margin: 10px 0 0; color: #64748b; font-size: 15px; line-height: 1.5; }
.pos-plan-grid { display: flex; justify-content: center; }
.pos-plan-card {
    width: 100%;
    max-width: 400px;
    background: #fff;
    border: 2px solid <?= $show_renew ? '#d97706' : '#2563eb' ?>;
    border-radius: 18px;
    padding: 28px 24px 24px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
    box-sizing: border-box;
}
.pos-plan-card__badge {
    display: inline-block;
    margin-bottom: 10px;
    color: <?= $show_renew ? '#d97706' : '#2563eb' ?>;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.pos-plan-card h2 {
    margin: 0 0 4px;
    font-size: 28px;
    font-weight: 800;
    color: #0f172a !important;
}
.pos-plan-card__shop { margin: 0 0 16px; color: #64748b; font-size: 14px; }
.pos-plan-card__price { margin: 0 0 18px; color: #15803d; font-size: 32px; font-weight: 800; line-height: 1; }
.pos-plan-card__price span { font-size: 16px; font-weight: 600; color: #64748b; }
.pos-plan-card__list { margin: 0 0 20px; padding: 0; list-style: none !important; }
.pos-plan-card__list li {
    list-style: none !important;
    position: relative;
    padding: 8px 0 8px 26px;
    color: #334155;
    font-size: 14px;
}
.pos-plan-card__list li::before {
    content: "✓";
    position: absolute;
    left: 0;
    top: 8px;
    color: #2563eb;
    font-weight: 800;
}
.pos-plan-card__meta {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 8px;
    padding-top: 12px;
    border-top: 1px solid #eef2f7;
    color: #64748b;
    font-size: 13px;
}
.pos-plan-card__meta strong { color: #0f172a; }
.pos-plan-card__status {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: <?= $show_renew ? '16px' : '0' ?>;
    color: <?= $status === 'expired' ? '#dc2626' : ($status === 'warning' ? '#d97706' : '#059669') ?>;
}
.pos-plan-card__cta {
    display: block;
    padding: 12px 16px;
    border-radius: 999px;
    background: #2563eb;
    color: #fff !important;
    font-size: 15px;
    font-weight: 700;
    text-align: center;
    text-decoration: none !important;
}
.pos-plan-card__cta:hover { background: #1d4ed8; color: #fff !important; }
</style>

<div class="col-xs-12">
    <section class="pos-plan-page">
        <header class="pos-plan-page__head">
            <h1><?= esc(lang('Login.account_plan_title')) ?></h1>
            <p><?= esc(lang('Login.account_plan_subtitle')) ?></p>
        </header>

        <div class="pos-plan-grid">
            <article class="pos-plan-card">
                <span class="pos-plan-card__badge"><?= esc(lang('Login.account_plan_current')) ?></span>
                <h2><?= esc((string)($plan['plan_name'] ?? 'WBPOS')) ?></h2>
                <p class="pos-plan-card__shop"><?= esc($shop_name) ?></p>
                <div class="pos-plan-card__price">
                    $20 <span>/ month</span>
                </div>
                <ul class="pos-plan-card__list">
                    <li><?= esc(lang('Login.account_plan_feature_sales')) ?></li>
                    <li><?= esc(lang('Login.account_plan_feature_reports')) ?></li>
                    <li><?= esc(lang('Login.account_plan_feature_staff')) ?></li>
                    <li><?= esc(lang('Login.account_plan_feature_private')) ?></li>
                </ul>
                <div class="pos-plan-card__meta">
                    <span><?= esc(lang('Login.subscription_paid_until')) ?></span>
                    <strong><?= esc((string)($plan['paid_until'] ?? '—')) ?></strong>
                </div>
                <?php if ($status_label !== ''): ?>
                    <div class="pos-plan-card__status"><?= esc($status_label) ?></div>
                <?php endif; ?>
                <?php if ($show_renew): ?>
                    <a class="pos-plan-card__cta pos-view-only-allow" href="<?= esc($pay_url, 'attr') ?>"><?= esc(lang('Login.subscription_notify_renew')) ?></a>
                <?php endif; ?>
            </article>
        </div>
    </section>
</div>

<?= view('partial/footer') ?>
