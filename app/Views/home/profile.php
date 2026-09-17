<?php
$profile_card = pos_profile_card_context($user_info);
$profile_initials = '';
foreach (preg_split('/\s+/', (string)$profile_card['display_name']) ?: [] as $part) {
    if ($part !== '') {
        $profile_initials .= strtoupper(substr($part, 0, 1));
    }
    if (strlen($profile_initials) >= 2) {
        break;
    }
}
if ($profile_initials === '') {
    $profile_initials = strtoupper(
        substr((string)($user_info->first_name ?? ''), 0, 1)
        . substr((string)($user_info->last_name ?? ''), 0, 1)
    );
}
?>

<?= view('partial/header') ?>

<script type="text/javascript">
    dialog_support.init("a.modal-dlg");
</script>

<style>
.pos-plan-page { padding: 8px 0 48px; }
.pos-plan-page__head { text-align: center; max-width: 640px; margin: 8px auto 28px; }
.pos-plan-page__head h1 {
    margin: 0;
    color: #0f172a !important;
    font-size: 34px;
    font-weight: 800;
    letter-spacing: -0.03em;
}
.pos-plan-page__head p { margin: 10px 0 0; color: #64748b; font-size: 15px; }
.pos-plan-grid { display: flex; justify-content: center; }
.pos-plan-card {
    width: 100%;
    max-width: 400px;
    background: #fff;
    border: 2px solid #2563eb;
    border-radius: 18px;
    padding: 28px 24px 24px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
    box-sizing: border-box;
}
.pos-profile-card-page .sa-profile-card { padding: 0 0 18px; border-bottom: 1px solid #f1f5f9; margin-bottom: 18px; }
.pos-plan-card__cta {
    display: block;
    padding: 12px 16px;
    border-radius: 999px;
    background: #0f172a;
    color: #fff !important;
    font-size: 15px;
    font-weight: 700;
    text-align: center;
    text-decoration: none !important;
}
</style>

<div class="col-xs-12">
    <section class="pos-plan-page pos-profile-page">
    <header class="pos-plan-page__head">
        <h1><?= esc(lang('Common.profile')) ?></h1>
        <p><?= esc(lang('Login.account_profile_subtitle')) ?></p>
    </header>

    <div class="pos-plan-grid">
        <article class="pos-plan-card pos-profile-card-page">
            <div class="sa-profile-card">
                <span class="sa-profile-card__avatar" aria-hidden="true"><?= esc($profile_initials ?? '') ?></span>
                <div class="sa-profile-card__copy">
                    <strong><?= esc($profile_card['display_name'] ?? '') ?></strong>
                    <?php if (!empty($profile_card['email'])): ?>
                        <span><?= esc($profile_card['email']) ?></span>
                    <?php endif; ?>
                    <span class="sa-profile-card__role"><?= esc($profile_card['role_label'] ?? '') ?></span>
                </div>
            </div>
            <?= anchor(
                'home/changepassword/' . (int)($user_info->person_id ?? 0),
                lang('Employees.change_password'),
                [
                    'class'           => 'pos-plan-card__cta pos-plan-card__cta--secondary modal-dlg',
                    'data-btn-submit' => lang('Common.submit'),
                    'title'           => lang('Employees.change_password'),
                ]
            ) ?>
        </article>
    </div>
    </section>
</div>

<?= view('partial/footer') ?>
