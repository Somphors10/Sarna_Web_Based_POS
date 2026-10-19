<br>
<div class="container-fluid">
    <ul class="nav nav-tabs" id="myTabs" data-toggle="tab">
        <li class="active"><a href="#system_tabs" data-toggle="tab" title="<?= lang('Config.system_conf') ?>"><?= lang('Config.system_conf') ?></a></li>
        <li><a href="#email_tabs" data-toggle="tab" title="<?= lang('Config.email_configuration') ?>"><?= lang('Config.email') ?></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="system_tabs"><?= view('configs/system_info') ?></div>
        <div class="tab-pane" id="email_tabs"><?= view('configs/email_config') ?></div>
    </div>
</div>
