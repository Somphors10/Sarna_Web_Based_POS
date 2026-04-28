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
        <link rel="stylesheet" href="css/tailwind.css">
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
</head>

<body>
    <div class="wrapper">
        <div class="neo-layout">
            <aside class="neo-global-sidebar">
                <a class="neo-global-brand" href="<?= site_url() ?>">OSPOS</a>
                <div class="neo-global-sidebar-body">
                    <nav class="neo-global-menu">
                        <?php foreach ($allowed_modules as $module): ?>
                            <a class="neo-global-menu-item <?= $module->module_id == $request->getUri()->getSegment(1) ? 'is-active' : '' ?>" href="<?= base_url($module->module_id) ?>" title="<?= lang("Module.$module->module_id") ?>">
                                <img src="<?= base_url("images/menubar/$module->module_id.svg") ?>" alt="<?= lang("Module.$module->module_id") ?>">
                                <span><?= lang('Module.' . $module->module_id) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </aside>

            <main class="neo-global-content">
                <div class="topbar pos-topbar">
                    <div class="container pos-topbar-inner">
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
