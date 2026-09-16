<?php
/**
 * @var object $user_info
 * @var array $allowed_modules
 * @var CodeIgniter\HTTP\IncomingRequest $request
 * @var array $config
 */

use Config\Services;

$request = Services::request();
helper('locale');
$html_lang = current_language_code();
$is_sa_pos_shell = is_platform_super_admin();
?>

<!doctype html>
<html lang="<?= esc($html_lang) ?>" class="lang-<?= esc($html_lang) ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <base href="<?= base_url() ?>">
    <title><?= esc($config['company']) . ' | ' . lang('Common.powered_by') . ' ' . lang('Common.software_short') . ' ' . esc(config('App')->application_version) ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="<?= 'resources/bootswatch/' . (empty($config['theme']) ? 'flatly' : esc($config['theme'])) . '/bootstrap.min.css' ?>">

    <?php $assets_injected = false; ?>
    <?php if (ENVIRONMENT == 'development' || get_cookie('debug') == 'true' || $request->getGet('debug') == 'true') : ?>
        <!-- inject:debug:css -->
        <link rel="stylesheet" href="resources/css/jquery-ui-fe010342cb.css">
        <link rel="stylesheet" href="resources/css/bootstrap-dialog-1716ef6e7c.css">
        <link rel="stylesheet" href="resources/css/jasny-bootstrap-40bf85f3ed.css">
        <link rel="stylesheet" href="resources/css/bootstrap-datetimepicker-66374fba71.css">
        <link rel="stylesheet" href="resources/css/bootstrap-select-66d5473b84.css">
        <link rel="stylesheet" href="resources/css/bootstrap-table-ed9d1a3360.css">
        <link rel="stylesheet" href="resources/css/bootstrap-table-sticky-header-07d65e7533.css">
        <link rel="stylesheet" href="resources/css/daterangepicker-85523b7dfe.css">
        <link rel="stylesheet" href="resources/css/chartist-c19aedb81a.css">
        <link rel="stylesheet" href="resources/css/chartist-plugin-tooltip-2e0ec92e60.css">
        <link rel="stylesheet" href="resources/css/bootstrap-tagsinput-5a6d46a06c.css">
        <link rel="stylesheet" href="resources/css/bootstrap-toggle-e12db6c1f3.css">
        <link rel="stylesheet" href="resources/css/bootstrap-292fc0ad3b.autocomplete.css">
        <link rel="stylesheet" href="resources/css/invoice-e9f26a7ebc.css">
        <link rel="stylesheet" href="resources/css/ospos_print-2ba645b044.css">
        <link rel="stylesheet" href="resources/css/ospos-73edad0b33.css">
        <link rel="stylesheet" href="resources/css/popupbox-7b616030b0.css">
        <link rel="stylesheet" href="resources/css/receipt-59573bfc05.css">
        <link rel="stylesheet" href="resources/css/register-57e3f53225.css">
        <link rel="stylesheet" href="resources/css/reports-38f70509fb.css">
        <!-- endinject -->
        <link rel="stylesheet" href="css/dashboard.css?v=117">
        <link rel="stylesheet" href="css/theme/topbar-footer.css?v=68">
        <link rel="stylesheet" href="css/forms.css?v=11">
        <link rel="stylesheet" href="css/password-toggle.css?v=4">
        <?php if ($is_sa_pos_shell): ?>
        <link rel="stylesheet" href="css/theme/tokens.css">
        <link rel="stylesheet" href="css/theme/layout-sidebar.css">
        <link rel="stylesheet" href="css/theme/responsive.css">
        <link rel="stylesheet" href="css/theme/super-admin.css?v=62">
        <?php endif; ?>
        <?php if ($config['theme'] != 'flatly' && file_exists($_SERVER['DOCUMENT_ROOT'] . '/public/css/' . esc($config['theme']) . '.css')) { ?>
            <link rel="stylesheet" href="<?= 'css/' . esc($config['theme']) . '.css' ?>">
        <?php } ?>
        <link rel="stylesheet" href="css/theme/profile-menu.css?v=5">
        <link rel="stylesheet" href="css/theme/modals.css?v=1">
        <!-- inject:debug:js -->
        <script src="resources/js/jquery-12e87d2f3a.js"></script>
        <script src="resources/js/jquery-4fa896f615.form.js"></script>
        <script src="resources/js/jquery-a0350e8820.validate.js"></script>
        <script src="resources/js/jquery-ui-cbc65ff85e.js"></script>
        <script src="resources/js/bootstrap-894d79839f.js"></script>
        <script src="resources/js/bootstrap-dialog-27123abb65.js"></script>
        <script src="resources/js/jasny-bootstrap-7c6d7b8adf.js"></script>
        <script src="resources/js/bootstrap-datetimepicker-25e39b7ef8.js"></script>
        <script src="resources/js/bootstrap-select-b01896a67b.js"></script>
        <script src="resources/js/bootstrap-table-bdb06552ea.js"></script>
        <script src="resources/js/bootstrap-table-export-6389dc2aa5.js"></script>
        <script src="resources/js/bootstrap-table-mobile-fc655b68ab.js"></script>
        <script src="resources/js/bootstrap-table-sticky-header-cb4d83d172.js"></script>
        <script src="resources/js/moment-d65dc6d2e6.min.js"></script>
        <script src="resources/js/daterangepicker-048c56a690.js"></script>
        <script src="resources/js/es6-promise-855125e6f5.js"></script>
        <script src="resources/js/FileSaver-e73b1946e8.js"></script>
        <script src="resources/js/html2canvas-e1d3a8d7cd.js"></script>
        <script src="resources/js/jspdf-4bad9ca248.umd.js"></script>
        <script src="resources/js/purify-5fa80c50ad.js"></script>
        <script src="resources/js/jspdf-4f52bd767f.plugin.autotable.js"></script>
        <script src="resources/js/tableExport-3d506dfa61.min.js"></script>
        <script src="resources/js/chartist-8a7ecb4445.js"></script>
        <script src="resources/js/chartist-plugin-pointlabels-0a1ab6aa4e.js"></script>
        <script src="resources/js/chartist-plugin-tooltip-116cb48831.js"></script>
        <script src="resources/js/chartist-plugin-axistitle-80a1198058.js"></script>
        <script src="resources/js/chartist-plugin-barlabels-4165273742.js"></script>
        <script src="resources/js/bootstrap-notify-376bc6eb87.js"></script>
        <script src="resources/js/bootstrap-tagsinput-855a7c7670.js"></script>
        <script src="resources/js/bootstrap-toggle-1c7a19a049.js"></script>
        <script src="resources/js/clipboard-908af414ab.js"></script>
        <script src="resources/js/imgpreview-62e42c15a0.full.jquery.js"></script>
        <script src="resources/js/manage_tables-8df051b53c.js"></script>
        <script src="resources/js/nominatim-599d9d6f9c.autocomplete.js"></script>
        <!-- endinject -->
        <?php $assets_injected = true; ?>
    <?php else : ?>
        <!--inject:prod:css -->
        <link rel="stylesheet" href="resources/opensourcepos-5bd11d6cca.min.css">
        <!-- endinject -->
        <link rel="stylesheet" href="css/dashboard.css?v=117">
        <link rel="stylesheet" href="css/theme/topbar-footer.css?v=68">
        <link rel="stylesheet" href="css/forms.css?v=11">
        <link rel="stylesheet" href="css/password-toggle.css?v=4">
        <?php if ($is_sa_pos_shell): ?>
        <link rel="stylesheet" href="css/theme/tokens.css">
        <link rel="stylesheet" href="css/theme/layout-sidebar.css">
        <link rel="stylesheet" href="css/theme/responsive.css">
        <link rel="stylesheet" href="css/theme/super-admin.css?v=62">
        <?php endif; ?>

        <!-- Tweaks to the UI for a particular theme should drop here  -->
        <?php if ($config['theme'] != 'flatly' && file_exists($_SERVER['DOCUMENT_ROOT'] . '/public/css/' . esc($config['theme']) . '.css')) { ?>
            <link rel="stylesheet" href="<?= 'css/' . esc($config['theme']) . '.css' ?>">
        <?php } ?>
        <link rel="stylesheet" href="css/theme/profile-menu.css?v=5">
        <link rel="stylesheet" href="css/theme/modals.css?v=1">
        <!-- inject:prod:js -->
        <script src="resources/jquery-2c872dbe60.min.js"></script>
        <script src="resources/opensourcepos-04102e27be.min.js"></script>
        <!-- endinject -->
        <?php $assets_injected = true; ?>
    <?php endif; ?>

    <?php
    // Fallback only when gulp inject tags are not populated.
    if (!$assets_injected) {
        $jquery_bundle = glob(FCPATH . 'resources/jquery-*.min.js');
        $ospos_bundle = glob(FCPATH . 'resources/opensourcepos-*.min.js');
        if (!empty($jquery_bundle)) { ?>
            <script src="<?= 'resources/' . basename($jquery_bundle[0]) ?>"></script>
        <?php }
        if (!empty($ospos_bundle)) { ?>
            <script src="<?= 'resources/' . basename($ospos_bundle[0]) ?>"></script>
        <?php }
    } ?>

    <?= view('partial/header_js') ?>
    <script src="js/password_toggle.js?v=3"></script>
    <script src="js/pos_select.js?v=5"></script>
    <?= view('partial/lang_lines') ?>

    <link rel="stylesheet" href="css/theme/pos-notify.css?v=6">

    <style>
        html {
            overflow: auto;
        }

        /* Force shop bell = avatar size (36px) */
        .pos-notify-btn {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            min-height: 36px !important;
        }
        .pos-notify-btn svg {
            width: 26px !important;
            height: 26px !important;
        }
        .pos-notify-badge {
            min-width: 20px !important;
            height: 20px !important;
            font-size: 12px !important;
        }
    </style>
    <script>
        (function() {
            const key = 'ospos_sidebar_collapsed';
            const isCollapsed = localStorage.getItem(key) === '1';
            if (window.innerWidth > 992 && isCollapsed) {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
    </script>
</head>

<?php
    $subscription_view_only = !empty($subscription_view_only) || (bool)session()->get('subscription_view_only');
    $body_classes = [];
    if ($is_sa_pos_shell) {
        $body_classes[] = 'sa-dashboard';
    }
    if (!$is_sa_pos_shell && $subscription_view_only) {
        $body_classes[] = 'pos-view-only';
    }
?>
<body<?= $body_classes !== [] ? ' class="' . esc(implode(' ', $body_classes)) . '"' : '' ?>>
    <script>
        (function() {
            const key = 'ospos_sidebar_collapsed';

            const syncForViewport = function() {
                if (window.innerWidth <= 992) {
                    document.documentElement.classList.remove('sidebar-collapsed');
                } else if (localStorage.getItem(key) === '1') {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
                const toggle = document.getElementById('neo_sidebar_toggle');
                const mobileToggle = document.getElementById('neo_mobile_sidebar_toggle');
                const mobileBackdrop = document.getElementById('neo_sidebar_backdrop');
                if (!toggle) {
                    return;
                }

                const setExpandedState = function() {
                    toggle.setAttribute('aria-expanded', (!document.documentElement.classList.contains('sidebar-collapsed')).toString());
                };

                toggle.addEventListener('click', function() {
                    if (window.innerWidth <= 992) {
                        return;
                    }

                    document.documentElement.classList.toggle('sidebar-collapsed');
                    localStorage.setItem(key, document.documentElement.classList.contains('sidebar-collapsed') ? '1' : '0');
                    setExpandedState();
                });

                const closeMobileSidebar = function() {
                    document.documentElement.classList.remove('mobile-sidebar-open');
                };

                if (mobileToggle) {
                    mobileToggle.addEventListener('click', function() {
                        if (window.innerWidth > 992) {
                            return;
                        }
                        document.documentElement.classList.toggle('mobile-sidebar-open');
                    });
                }

                if (mobileBackdrop) {
                    mobileBackdrop.addEventListener('click', closeMobileSidebar);
                }

                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape' && window.innerWidth <= 992) {
                        closeMobileSidebar();
                    }
                });

                document.querySelectorAll('.neo-global-menu-item, .neo-sidebar-logout').forEach(function(link) {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 992) {
                            closeMobileSidebar();
                        }
                    });
                });

                window.addEventListener('resize', function() {
                    syncForViewport();
                    setExpandedState();
                    if (window.innerWidth > 992) {
                        document.documentElement.classList.remove('mobile-sidebar-open');
                    }
                });

                syncForViewport();
                setExpandedState();
            });

            document.addEventListener('DOMContentLoaded', function() {
                const profileBtn = document.getElementById('pos_profile_btn');
                const profileDropdown = document.getElementById('pos_profile_dropdown');
                let openDropdown = null;

                const closeDropdown = function() {
                    if (openDropdown) {
                        openDropdown.hidden = true;
                        openDropdown = null;
                    }
                    if (profileBtn) {
                        profileBtn.setAttribute('aria-expanded', 'false');
                    }
                };

                if (!profileBtn || !profileDropdown) {
                    return;
                }

                profileBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    const willOpen = profileDropdown.hasAttribute('hidden');
                    closeDropdown();
                    if (willOpen) {
                        profileDropdown.removeAttribute('hidden');
                        profileBtn.setAttribute('aria-expanded', 'true');
                        openDropdown = profileDropdown;
                    }
                });

                document.addEventListener('click', function(event) {
                    if (event.target.closest('.pos-profile-dropdown-wrap, .sa-dropdown-wrap')) {
                        return;
                    }
                    closeDropdown();
                });

                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeDropdown();
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const notifyBtn = document.getElementById('pos_notify_btn');
                const notifyPanel = document.getElementById('pos_notify_panel');
                const notifyBackdrop = document.getElementById('pos_notify_backdrop');
                const notifyClose = document.getElementById('pos_notify_close');
                if (!notifyBtn || !notifyPanel) {
                    return;
                }

                const setOpen = function(open) {
                    notifyPanel.classList.toggle('is-open', open);
                    notifyPanel.setAttribute('aria-hidden', open ? 'false' : 'true');
                    notifyBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                    document.body.classList.toggle('pos-notify-open', open);
                    if (notifyBackdrop) {
                        notifyBackdrop.hidden = !open;
                        notifyBackdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
                    }
                };

                notifyBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    setOpen(!notifyPanel.classList.contains('is-open'));
                });

                if (notifyClose) {
                    notifyClose.addEventListener('click', function() {
                        setOpen(false);
                    });
                }
                if (notifyBackdrop) {
                    notifyBackdrop.addEventListener('click', function() {
                        setOpen(false);
                    });
                }
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        setOpen(false);
                    }
                });
            });

            // View-only: block New/Delete/edit actions in the UI (sidebar stays for viewing).
            document.addEventListener('DOMContentLoaded', function() {
                if (!document.body.classList.contains('pos-view-only')) {
                    return;
                }

                const allowSel = [
                    '.pos-view-only-allow',
                    '.neo-global-menu-item',
                    '.neo-global-brand',
                    '.neo-sidebar-toggle',
                    '.neo-mobile-menu-toggle',
                    '.neo-sidebar-backdrop',
                    '.pos-notify-btn',
                    '.pos-notify-panel',
                    '.pos-notify-backdrop',
                    '.sa-profile-btn',
                    '.sa-dropdown__menu-item',
                    '.js-pos-logout',
                    '.pos-logout-link',
                    '.neo-sidebar-logout',
                    'a[href*="saas/"]',
                    '.pagination a',
                    '.fixed-table-pagination a',
                    '.search-input',
                    'input[type="search"]',
                    '.form-control',
                    '.list-tabs a',
                    '.nav-tabs a'
                ].join(',');

                const blockSel = [
                    'a.btn',
                    'button.btn',
                    '.modal-dlg',
                    '#delete',
                    '#restore',
                    '#email',
                    '#bulk_edit',
                    '#new_customer_btn',
                    'a[data-href]',
                    'button[data-href]',
                    '#register button',
                    '#register input[type="submit"]',
                    '.btn-primary',
                    '.btn-danger',
                    '.btn-success',
                    '.btn-warning'
                ].join(',');

                const flash = function() {
                    let tip = document.getElementById('pos_view_only_toast');
                    if (!tip) {
                        tip = document.createElement('div');
                        tip.id = 'pos_view_only_toast';
                        tip.className = 'pos-view-only-toast';
                        tip.setAttribute('role', 'status');
                        tip.textContent = <?= json_encode(lang('Login.subscription_view_only')) ?>;
                        document.body.appendChild(tip);
                    }
                    tip.classList.add('is-visible');
                    clearTimeout(tip._hideTimer);
                    tip._hideTimer = setTimeout(function() {
                        tip.classList.remove('is-visible');
                    }, 3200);
                };

                document.addEventListener('click', function(event) {
                    if (event.target.closest(allowSel)) {
                        return;
                    }
                    const blocked = event.target.closest(blockSel);
                    if (!blocked) {
                        return;
                    }
                    event.preventDefault();
                    event.stopPropagation();
                    flash();
                }, true);
            });
        })();
    </script>
    <?php if (!$is_sa_pos_shell): ?><div class="wrapper"><?php endif; ?>
        <div class="neo-layout<?= $is_sa_pos_shell ? ' sa-layout' : '' ?>">
            <aside class="neo-global-sidebar<?= $is_sa_pos_shell ? ' sa-sidebar' : '' ?>">
                <div class="neo-global-brand-row">
                    <a class="neo-global-brand" href="<?= site_url(is_platform_super_admin() ? 'super-admin/overview' : 'home') ?>">
                        <span class="neo-global-brand-full"><?= lang('Common.software_short') ?></span>
                    </a>
                    <button id="neo_sidebar_toggle" class="neo-sidebar-toggle" type="button" aria-label="Toggle sidebar" aria-expanded="true">
                        <svg class="neo-sidebar-toggle__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                </div>
                <div class="neo-global-sidebar-body">
                    <nav class="neo-global-menu">
                        <?php if (is_platform_super_admin()): ?>
                            <?= view('partial/super_admin_nav', [
                                'sa_active' => (string) $request->getUri()->getSegment(1),
                                'pos_modules' => super_admin_pos_nav_modules(),
                            ]) ?>
                        <?php else: ?>
                        <?php
                            // Keep the menu mostly complete, hide only low-priority modules.
                            $hidden_sidebar_modules = hidden_ui_module_ids();
                            $sidebar_modules = array_values(array_filter($allowed_modules ?? [], static fn($module) => !in_array($module->module_id, $hidden_sidebar_modules, true)));
                        ?>
                        <?php foreach ($sidebar_modules as $module): ?>
                            <a class="neo-global-menu-item <?= $module->module_id == $request->getUri()->getSegment(1) ? 'is-active' : '' ?>" href="<?= base_url($module->module_id) ?>" title="<?= lang("Module.$module->module_id") ?>">
                                <img class="neo-nav__icon" src="<?= base_url(pos_module_nav_icon((string) $module->module_id)) ?>" alt="">
                                <span><?= lang('Module.' . $module->module_id) ?></span>
                            </a>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </nav>
                    <div class="neo-sidebar-footer">
                        <?php
                            $header_logout_url = is_platform_super_admin()
                                ? site_url('super-admin/logout')
                                : site_url('home/logout');
                        ?>
                        <a
                            class="neo-sidebar-logout <?= is_platform_super_admin() ? 'js-super-admin-logout' : 'pos-logout-link' ?>"
                            href="<?= $header_logout_url ?>"
                            data-logout-url="<?= $header_logout_url ?>"
                            title="<?= lang('Login.logout') ?>"
                            onclick="return typeof window.osposConfirmLogout === 'function' ? window.osposConfirmLogout(this) : true;"
                        >
                            <span class="sa-nav-icon"><img class="neo-nav__icon" src="<?= base_url('images/super-admin/logout.svg') ?>" alt=""></span>
                            <span><?= lang('Login.logout') ?></span>
                        </a>
                    </div>
                </div>
            </aside>
            <div id="neo_sidebar_backdrop" class="neo-sidebar-backdrop"></div>

            <main class="neo-global-content<?= $is_sa_pos_shell ? ' sa-main' : '' ?>">
                <?php
                    helper('platform_features');
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
                    if ($profile_initials === '' && is_platform_super_admin()) {
                        $profile_initials = 'SA';
                    }
                    $label_or = static function (string $key, string $fallback): string {
                        $line = lang($key);
                        return ($line === $key || $line === '') ? $fallback : $line;
                    };
                    $label_profile = $label_or('Common.profile', 'Profile');
                    $label_change_password = $label_or('Employees.change_password', 'Change Password');
                    $ui_language = current_language_code();

                    $sa_notification_items = [];
                    if ($is_sa_pos_shell && function_exists('saas_build_super_admin_notification_items')) {
                        $sa_notification_items = saas_build_super_admin_notification_items();
                    }

                    $pos_notify_items = [];
                    $subscription_banner = null;
                    $subscription_banner_tone = 'warning';
                    $shop_company = (string)($config['company'] ?? 'Shop');
                    if (!$is_sa_pos_shell) {
                        $banner_tenant_id = (int)(session()->get('tenant_id') ?? 0);
                        if ($banner_tenant_id > 0 && function_exists('saas_tenant_subscription_info')) {
                            $sub_info = saas_tenant_subscription_info($banner_tenant_id);
                            $period_label = !empty($sub_info['period_end'])
                                ? saas_format_period_end($sub_info['period_end'])
                                : '';
                            if (!empty($sub_info['is_expired'])) {
                                $subscription_banner_tone = 'expired';
                                $subscription_banner = lang(
                                    'Login.subscription_expired_banner',
                                    [$period_label !== '' ? $period_label : '—']
                                );
                                $pos_notify_items[] = [
                                    'type'     => 'expired',
                                    'title'    => lang('Login.subscription_notify_expired_title'),
                                    'subtitle' => $shop_company,
                                    'body'     => $subscription_banner,
                                    'link'     => site_url('saas/checkout'),
                                    'link_label' => lang('Login.subscription_notify_renew'),
                                ];
                            } elseif (!empty($sub_info['is_warning']) && !empty($sub_info['period_end'])) {
                                $subscription_banner = lang(
                                    'Login.subscription_warning',
                                    [
                                        $period_label,
                                        (string)max(0, (int)$sub_info['days_left']),
                                    ]
                                );
                                $pos_notify_items[] = [
                                    'type'     => 'warning',
                                    'title'    => lang('Login.subscription_notify_warning_title'),
                                    'subtitle' => $shop_company,
                                    'body'     => $subscription_banner,
                                    'link'     => site_url('saas/checkout'),
                                    'link_label' => lang('Login.subscription_notify_renew'),
                                ];
                            } elseif (!empty($sub_info['has_period']) && !empty($sub_info['period_end'])) {
                                $pos_notify_items[] = [
                                    'type'     => 'active',
                                    'title'    => lang('Login.subscription_notify_active_title'),
                                    'subtitle' => $shop_company,
                                    'body'     => lang(
                                        'Login.subscription_notify_active_body',
                                        [
                                            $period_label,
                                            (string)max(0, (int)$sub_info['days_left']),
                                        ]
                                    ),
                                    'link'     => site_url('saas/checkout'),
                                    'link_label' => lang('Login.subscription_notify_view'),
                                ];
                            }
                        }
                    }
                    $pos_notify_count = count($pos_notify_items);
                ?>
                <?php if ($is_sa_pos_shell): ?>
                <header class="sa-top-navbar">
                    <div class="sa-top-navbar__start">
                        <button id="neo_mobile_sidebar_toggle" class="neo-mobile-menu-toggle sa-top-navbar__menu" type="button" aria-label="Open menu" aria-expanded="false">
                            <span class="neo-hamburger-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="sa-top-navbar__end">
                        <div class="sa-top-navbar__search">
                            <div class="sa-search-wrap sa-search-wrap--pill">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7"></circle>
                                    <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
                                </svg>
                                <input type="text" id="sa_navbar_search" class="sa-input sa-input--pill" placeholder="Search businesses, admins, requests..." autocomplete="off">
                            </div>
                        </div>
                        <div class="sa-top-navbar__actions">
                            <?= view('partial/super_admin_notify', [
                                'notification_items' => $sa_notification_items,
                                'sa_notify_mode'     => 'button',
                            ]) ?>
                            <div class="sa-dropdown-wrap pos-profile-dropdown-wrap">
                                <button
                                    type="button"
                                    class="sa-profile-btn"
                                    id="pos_profile_btn"
                                    aria-label="<?= esc($label_profile) ?>"
                                    aria-expanded="false"
                                    aria-haspopup="true"
                                >
                                    <span class="sa-profile-avatar" aria-hidden="true"><?= esc($profile_initials) ?></span>
                                </button>
                                <div class="sa-dropdown sa-dropdown--profile" id="pos_profile_dropdown" hidden>
                                    <div class="sa-profile-card">
                                        <span class="sa-profile-card__avatar" aria-hidden="true"><?= esc($profile_initials) ?></span>
                                        <div class="sa-profile-card__copy">
                                            <strong><?= esc($profile_card['display_name']) ?></strong>
                                            <?php if (!empty($profile_card['username'])): ?>
                                                <span>@<?= esc($profile_card['username']) ?></span>
                                            <?php endif; ?>
                                            <?php if ($profile_card['email'] !== ''): ?>
                                                <span><?= esc($profile_card['email']) ?></span>
                                            <?php endif; ?>
                                            <span class="sa-profile-card__role"><?= esc($profile_card['role_label']) ?></span>
                                        </div>
                                    </div>
                                    <div class="sa-dropdown__menu">
                                        <?= anchor(
                                            'super-admin/changepassword',
                                            $label_change_password,
                                            [
                                                'class'           => 'sa-dropdown__menu-item modal-dlg',
                                                'data-btn-submit' => lang('Common.submit'),
                                                'title'           => $label_change_password,
                                            ]
                                        ) ?>
                                        <a class="sa-dropdown__menu-item" href="<?= site_url('super-admin/admins') ?>">Platform Admins</a>
                                        <a
                                            class="sa-dropdown__menu-item js-super-admin-logout"
                                            href="<?= site_url('super-admin/logout') ?>"
                                            data-logout-url="<?= site_url('super-admin/logout') ?>"
                                            onclick="return typeof window.osposConfirmLogout === 'function' ? window.osposConfirmLogout(this) : true;"
                                        ><?= lang('Login.logout') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var searchInput = document.getElementById('sa_navbar_search');
                    if (!searchInput) {
                        return;
                    }
                    searchInput.addEventListener('keydown', function(event) {
                        if (event.key !== 'Enter') {
                            return;
                        }
                        event.preventDefault();
                        var q = (searchInput.value || '').trim();
                        var url = <?= json_encode(site_url('super-admin/businesses')) ?>;
                        if (q !== '') {
                            url += (url.indexOf('?') >= 0 ? '&' : '?') + 'q=' + encodeURIComponent(q);
                        }
                        window.location.href = url;
                    });
                });
                </script>
                <?php else: ?>
                <div class="topbar pos-topbar">
                    <div class="container pos-topbar-inner">
                        <button id="neo_mobile_sidebar_toggle" class="neo-mobile-menu-toggle" type="button" aria-label="Open menu">
                            <span class="neo-hamburger-icon" aria-hidden="true"></span>
                        </button>
                        <div class="navbar-left pos-topbar-clock">
                            <div id="liveclock"><?= date($config['dateformat'] . ' ' . $config['timeformat']) ?></div>
                        </div>

                        <div class="navbar-center pos-topbar-company">
                            <strong><?= esc($config['company']) ?></strong>
                        </div>

                        <div class="navbar-right pos-topbar-user">
                            <button
                                type="button"
                                class="pos-notify-btn"
                                id="pos_notify_btn"
                                aria-label="<?= esc(lang('Login.subscription_notifications')) ?>"
                                aria-expanded="false"
                                aria-controls="pos_notify_panel"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                <span class="pos-notify-badge<?= $pos_notify_count > 0 ? '' : ' pos-notify-badge--hidden' ?>"><?= $pos_notify_count ?></span>
                            </button>
                            <div class="sa-dropdown-wrap pos-profile-dropdown-wrap">
                                <button
                                    type="button"
                                    class="sa-profile-btn"
                                    id="pos_profile_btn"
                                    aria-label="<?= esc($label_profile) ?>"
                                    aria-expanded="false"
                                    aria-haspopup="true"
                                >
                                    <span class="sa-profile-avatar" aria-hidden="true"><?= esc($profile_initials) ?></span>
                                </button>
                                <div class="sa-dropdown sa-dropdown--profile" id="pos_profile_dropdown" hidden>
                                    <div class="sa-profile-card">
                                        <span class="sa-profile-card__avatar" aria-hidden="true"><?= esc($profile_initials) ?></span>
                                        <div class="sa-profile-card__copy">
                                            <strong><?= esc($profile_card['display_name']) ?></strong>
                                            <?php if ($profile_card['email'] !== ''): ?>
                                                <span><?= esc($profile_card['email']) ?></span>
                                            <?php endif; ?>
                                            <span class="sa-profile-card__role"><?= esc($profile_card['role_label']) ?></span>
                                        </div>
                                    </div>
                                    <div class="sa-dropdown__menu">
                                        <a
                                            class="sa-dropdown__menu-item<?= $ui_language === 'en' ? ' is-active' : '' ?>"
                                            href="<?= site_url('home/language/en') ?>"
                                        ><?= esc($label_or('Common.language_english', 'English')) ?></a>
                                        <a
                                            class="sa-dropdown__menu-item<?= $ui_language === 'km' ? ' is-active' : '' ?>"
                                            href="<?= site_url('home/language/km') ?>"
                                        ><?= esc($label_or('Common.language_khmer', 'Khmer')) ?></a>
                                        <?= anchor(
                                            "home/changepassword/$user_info->person_id",
                                            $label_change_password,
                                            [
                                                'class'           => 'sa-dropdown__menu-item modal-dlg',
                                                'data-btn-submit' => lang('Common.submit'),
                                                'title'           => $label_change_password,
                                            ]
                                        ) ?>
                                        <a
                                            class="sa-dropdown__menu-item js-pos-logout"
                                            href="<?= site_url('home/logout') ?>"
                                            data-logout-url="<?= site_url('home/logout') ?>"
                                            onclick="return typeof window.osposConfirmLogout === 'function' ? window.osposConfirmLogout(this) : true;"
                                        ><?= lang('Login.logout') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($subscription_banner !== null): ?>
                <div class="pos-subscription-alert<?= $subscription_banner_tone === 'expired' ? ' pos-subscription-alert--expired' : '' ?>" role="status">
                    <div class="pos-subscription-alert__inner">
                        <span class="pos-subscription-alert__icon" aria-hidden="true">
                            <?php if ($subscription_banner_tone === 'expired'): ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6"></path><circle cx="12" cy="16.5" r="1" fill="currentColor" stroke="none"></circle></svg>
                            <?php else: ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.3 3.3 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.3a2 2 0 0 0-3.4 0Z"></path><path d="M12 9v4"></path><circle cx="12" cy="16.5" r="1" fill="currentColor" stroke="none"></circle></svg>
                            <?php endif; ?>
                        </span>
                        <span class="pos-subscription-alert__text"><?= esc($subscription_banner) ?></span>
                        <a class="pos-subscription-alert__link pos-view-only-allow" href="<?= site_url('saas/checkout') ?>"><?= esc(lang('Login.subscription_renew')) ?></a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!$is_sa_pos_shell && $request->getGet('view_only') === '1' && $subscription_banner === null): ?>
                <div class="pos-subscription-alert pos-subscription-alert--expired" role="alert">
                    <div class="pos-subscription-alert__inner">
                        <span class="pos-subscription-alert__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6"></path><circle cx="12" cy="16.5" r="1" fill="currentColor" stroke="none"></circle></svg>
                        </span>
                        <span class="pos-subscription-alert__text"><?= esc(lang('Login.subscription_view_only')) ?></span>
                        <a class="pos-subscription-alert__link pos-view-only-allow" href="<?= site_url('saas/checkout') ?>"><?= esc(lang('Login.subscription_renew')) ?></a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($is_sa_pos_shell): ?>
                    <?= view('partial/super_admin_notify', [
                        'notification_items' => $sa_notification_items ?? [],
                        'sa_notify_mode'     => 'panel',
                    ]) ?>
                <?php endif; ?>
                <?php if (!$is_sa_pos_shell): ?>
                <div id="pos_notify_backdrop" class="pos-notify-backdrop" hidden aria-hidden="true"></div>
                <aside id="pos_notify_panel" class="pos-notify-panel" aria-hidden="true" aria-labelledby="pos_notify_panel_title">
                    <div class="pos-notify-panel__head">
                        <div class="pos-notify-panel__title-row">
                            <div class="pos-notify-panel__title-wrap">
                                <h2 id="pos_notify_panel_title"><?= esc(lang('Login.subscription_notifications')) ?></h2>
                                <span class="pos-notify-panel__count<?= $pos_notify_count > 0 ? '' : ' pos-notify-panel__count--hidden' ?>"><?= $pos_notify_count ?></span>
                            </div>
                            <button type="button" class="pos-notify-panel__close" id="pos_notify_close" aria-label="Close">&times;</button>
                        </div>
                    </div>
                    <div class="pos-notify-panel__body">
                        <ul class="pos-notify-panel__list<?= $pos_notify_count > 0 ? '' : ' pos-notify-panel__list--empty' ?>">
                            <?php foreach ($pos_notify_items as $item): ?>
                                <li class="pos-notify-card">
                                    <div class="pos-notify-card__row">
                                        <span class="pos-notify-card__icon pos-notify-card__icon--<?= esc($item['type']) ?>" aria-hidden="true">
                                            <?php if ($item['type'] === 'expired'): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6"></path><circle cx="12" cy="16.5" r="1" fill="currentColor" stroke="none"></circle></svg>
                                            <?php elseif ($item['type'] === 'warning'): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.3 3.3 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.3a2 2 0 0 0-3.4 0Z"></path><path d="M12 9v4"></path><circle cx="12" cy="16.5" r="1" fill="currentColor" stroke="none"></circle></svg>
                                            <?php else: ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"></path><path d="M5 21V7l8-4v18"></path><path d="M19 21V11l-6-4"></path></svg>
                                            <?php endif; ?>
                                        </span>
                                        <div class="pos-notify-card__head">
                                            <strong><?= esc($item['title']) ?></strong>
                                        </div>
                                    </div>
                                    <?php if (!empty($item['subtitle'])): ?>
                                        <p class="pos-notify-card__subtitle"><?= esc($item['subtitle']) ?></p>
                                    <?php endif; ?>
                                    <p class="pos-notify-card__body"><?= esc($item['body']) ?></p>
                                    <a class="pos-notify-card__link pos-view-only-allow" href="<?= esc($item['link'] ?? site_url('saas/checkout'), 'attr') ?>"><?= esc($item['link_label'] ?? lang('Login.subscription_notify_renew')) ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="pos-notify-panel__empty<?= $pos_notify_count > 0 ? ' pos-notify-panel__empty--hidden' : '' ?>"><?= esc(lang('Login.subscription_notify_empty')) ?></p>
                    </div>
                </aside>
                <?php endif; ?>
                <?php if ($is_sa_pos_shell): ?>
                <div class="sa-main-body sa-pos-content">
                <?php else: ?>
                <div class="container">
                    <div class="row">
                <?php endif; ?>
