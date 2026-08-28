<?php
/**
 * Super Admin sidebar: subscriptions plus every POS module Admin can use.
 *
 * @var string $sa_active
 * @var array $pos_modules
 */
$sa_active = $sa_active ?? '';
$pos_modules = $pos_modules ?? super_admin_pos_nav_modules();
?>
<p class="sa-nav-group">Subscriptions</p>
<a class="neo-global-menu-item <?= $sa_active === 'overview' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/overview') ?>" title="Overview">
    <span class="sa-nav-icon"><img class="neo-nav__icon" src="<?= base_url('images/super-admin/overview.svg') ?>" alt=""></span>
    <span>Overview</span>
</a>
<a class="neo-global-menu-item <?= $sa_active === 'businesses' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/businesses') ?>" title="Businesses">
    <span class="sa-nav-icon"><img class="neo-nav__icon" src="<?= base_url('images/super-admin/businesses.svg') ?>" alt=""></span>
    <span>Businesses</span>
</a>
<a class="neo-global-menu-item <?= $sa_active === 'requests' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/requests') ?>" title="Pending Requests">
    <span class="sa-nav-icon"><img class="neo-nav__icon" src="<?= base_url('images/super-admin/pending.svg') ?>" alt=""></span>
    <span>Requests</span>
</a>
<a class="neo-global-menu-item <?= $sa_active === 'history' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/history') ?>" title="Request History">
    <span class="sa-nav-icon"><img class="neo-nav__icon" src="<?= base_url('images/super-admin/overview.svg') ?>" alt=""></span>
    <span>History</span>
</a>
<a class="neo-global-menu-item <?= $sa_active === 'admins' ? 'is-active' : '' ?>" href="<?= site_url('super-admin/admins') ?>" title="Platform Admins">
    <span class="sa-nav-icon"><img class="neo-nav__icon" src="<?= base_url('images/super-admin/admins.svg') ?>" alt=""></span>
    <span>Platform Admins</span>
</a>
<a class="neo-global-menu-item <?= in_array($sa_active, ['features', 'plans', 'feature'], true) ? 'is-active' : '' ?>" href="<?= site_url('super-admin/plans') ?>" title="Plans &amp; Template">
    <span class="sa-nav-icon"><img class="neo-nav__icon" src="<?= base_url('images/nav/config.svg') ?>" alt=""></span>
    <span>Plans &amp; Sync</span>
</a>

<p class="sa-nav-group">POS Features</p>
<?php foreach ($pos_modules as $nav_feature): ?>
    <a class="neo-global-menu-item <?= $sa_active === $nav_feature['id'] ? 'is-active' : '' ?>" href="<?= esc($nav_feature['url'], 'attr') ?>" title="<?= esc($nav_feature['label'], 'attr') ?>">
        <span class="sa-nav-icon"><img class="neo-nav__icon" src="<?= base_url($nav_feature['icon']) ?>" alt=""></span>
        <span><?= esc($nav_feature['label']) ?></span>
    </a>
<?php endforeach; ?>
