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
    <title><?= esc($config['company']) . ' | ' . lang('Common.powered_by') . ' OSPOS ' . esc(config('App')->application_version) ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="<?= 'resources/bootswatch/' . (empty($config['theme']) ? 'flatly' : esc($config['theme'])) . '/bootstrap.min.css' ?>">

    <?php if (ENVIRONMENT == 'development' || get_cookie('debug') == 'true' || $request->getGet('debug') == 'true') : ?>
        <!-- inject:debug:css -->
        <!-- endinject -->
        <!-- inject:debug:js -->
        <!-- endinject -->
    <?php else : ?>
        <!--inject:prod:css -->
        <!-- endinject -->
        <link rel="stylesheet" href="css/dashboard.css">
        <link rel="stylesheet" href="css/forms.css">

        <!-- Tweaks to the UI for a particular theme should drop here  -->
        <?php if ($config['theme'] != 'flatly' && file_exists($_SERVER['DOCUMENT_ROOT'] . '/public/css/' . esc($config['theme']) . '.css')) { ?>
            <link rel="stylesheet" href="<?= 'css/' . esc($config['theme']) . '.css' ?>">
        <?php } ?>
        <!-- inject:prod:js -->
        <!-- endinject -->
    <?php endif; ?>

    <?php
    // Fallback for setups where gulp inject tags are not populated.
    $jquery_bundle = glob(FCPATH . 'resources/jquery-*.min.js');
    $ospos_bundle = glob(FCPATH . 'resources/opensourcepos-*.min.js');
    if (!empty($jquery_bundle)) { ?>
        <script src="<?= 'resources/' . basename($jquery_bundle[0]) ?>"></script>
    <?php }
    if (!empty($ospos_bundle)) { ?>
        <script src="<?= 'resources/' . basename($ospos_bundle[0]) ?>"></script>
    <?php } ?>

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
                    <a class="neo-global-brand" href="<?= site_url() ?>">
                        <span class="neo-global-brand-full">OSPOS</span>
                        <span class="neo-global-brand-mini">O</span>
                    </a>
                    <button id="neo_sidebar_toggle" class="neo-sidebar-toggle" type="button" aria-label="Toggle sidebar" aria-expanded="true">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </button>
                </div>
                <div class="neo-global-sidebar-body">
                    <nav class="neo-global-menu">
                        <?php
                            // Keep the menu mostly complete, hide only low-priority modules.
                            $hidden_sidebar_modules = ['messages', 'migrate', 'giftcards', 'cashups'];
                            $sidebar_modules = array_values(array_filter($allowed_modules, static fn($module) => !in_array($module->module_id, $hidden_sidebar_modules, true)));
                        ?>
                        <?php foreach ($sidebar_modules as $module): ?>
                            <a class="neo-global-menu-item <?= $module->module_id == $request->getUri()->getSegment(1) ? 'is-active' : '' ?>" href="<?= base_url($module->module_id) ?>" title="<?= lang("Module.$module->module_id") ?>">
                                <span class="neo-global-menu-icon">
                                    <img src="<?= base_url("images/menubar/$module->module_id.svg") ?>" alt="<?= lang("Module.$module->module_id") ?>">
                                </span>
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
                            <?= anchor('home/logout', lang('Login.logout'), ['class' => 'pos-logout-link']) ?>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
