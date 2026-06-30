<?php
/**
 * @var object $user_info
 * @var array $allowed_modules
 * @var CodeIgniter\HTTP\IncomingRequest $request
 * @var array $config
 */

use Config\Services;

$request = Services::request();
?>

<!doctype html>
<html lang="<?= $request->getLocale() ?>">

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
        <link rel="stylesheet" href="resources/css/invoice-1eae5e39b9.css">
        <link rel="stylesheet" href="resources/css/ospos_print-2ba645b044.css">
        <link rel="stylesheet" href="resources/css/ospos-b70a42bf24.css">
        <link rel="stylesheet" href="resources/css/popupbox-7b616030b0.css">
        <link rel="stylesheet" href="resources/css/receipt-a171207d8e.css">
        <link rel="stylesheet" href="resources/css/register-58be93b261.css">
        <link rel="stylesheet" href="resources/css/reports-407b727797.css">
        <!-- endinject -->
        <link rel="stylesheet" href="css/dashboard.css?v=19">
        <link rel="stylesheet" href="css/forms.css">
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
        <script src="resources/js/manage_tables-70efc473d8.js"></script>
        <script src="resources/js/nominatim-599d9d6f9c.autocomplete.js"></script>
        <!-- endinject -->
        <?php $assets_injected = true; ?>
    <?php else : ?>
        <!--inject:prod:css -->
        <link rel="stylesheet" href="resources/opensourcepos-5f093a9a3a.min.css">
        <!-- endinject -->
        <link rel="stylesheet" href="css/dashboard.css?v=19">
        <link rel="stylesheet" href="css/forms.css">

        <!-- Tweaks to the UI for a particular theme should drop here  -->
        <?php if ($config['theme'] != 'flatly' && file_exists($_SERVER['DOCUMENT_ROOT'] . '/public/css/' . esc($config['theme']) . '.css')) { ?>
            <link rel="stylesheet" href="<?= 'css/' . esc($config['theme']) . '.css' ?>">
        <?php } ?>
        <!-- inject:prod:js -->
        <script src="resources/jquery-2c872dbe60.min.js"></script>
        <script src="resources/opensourcepos-d58256b84d.min.js"></script>
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
    <?= view('partial/lang_lines') ?>

    <style>
        html {
            overflow: auto;
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

<body>
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

                document.querySelectorAll('.neo-global-menu-item').forEach(function(link) {
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
        })();
    </script>
    <div class="wrapper">
        <div class="neo-layout">
            <aside class="neo-global-sidebar">
                <div class="neo-global-brand-row">
                    <a class="neo-global-brand" href="<?= site_url('home') ?>">
                        <span class="neo-global-brand-full"><?= lang('Common.software_short') ?></span>
                        <span class="neo-global-brand-mini">W</span>
                    </a>
                    <button id="neo_sidebar_toggle" class="neo-sidebar-toggle" type="button" aria-label="Toggle sidebar" aria-expanded="true">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </button>
                </div>
                <div class="neo-global-sidebar-body">
                    <nav class="neo-global-menu">
                        <?php
                            // Keep the menu mostly complete, hide only low-priority modules.
                            $hidden_sidebar_modules = hidden_ui_module_ids();
                            $sidebar_modules = array_values(array_filter($allowed_modules, static fn($module) => !in_array($module->module_id, $hidden_sidebar_modules, true)));
                        ?>
                        <?php foreach ($sidebar_modules as $module): ?>
                            <?php
                                $nav_icon_file = 'images/nav/' . $module->module_id . '.svg';
                                $nav_icon_url = is_file(FCPATH . $nav_icon_file)
                                    ? base_url($nav_icon_file)
                                    : base_url('images/menubar/' . $module->module_id . '.svg');
                            ?>
                            <a class="neo-global-menu-item <?= $module->module_id == $request->getUri()->getSegment(1) ? 'is-active' : '' ?>" href="<?= base_url($module->module_id) ?>" title="<?= lang("Module.$module->module_id") ?>">
                                <img class="neo-nav__icon" src="<?= $nav_icon_url ?>" alt="">
                                <span><?= lang('Module.' . $module->module_id) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </aside>
            <div id="neo_sidebar_backdrop" class="neo-sidebar-backdrop"></div>

            <main class="neo-global-content">
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
                            <?= anchor("home/changePassword/$user_info->person_id", "$user_info->first_name $user_info->last_name", ['class' => 'modal-dlg pos-user-link', 'data-btn-submit' => lang('Common.submit'), 'title' => lang('Employees.change_password')]) ?>
                            <?= anchor('home/logout', lang('Login.logout'), [
                                'class' => 'pos-logout-link',
                                'data-logout-url' => site_url('home/logout'),
                                'onclick' => 'return typeof window.osposConfirmLogout === "function" ? window.osposConfirmLogout(this) : true;',
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
