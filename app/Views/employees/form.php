<?php
/**
 * @var string $controller_name
 * @var object $person_info
 * @var array $all_modules
 * @var array $all_subpermissions
 * @var int $employee_id
 */
?>

<div class="pos-form-shell">
<div id="required_fields_message" class="pos-form-required"><?= lang('Common.fields_required_message') ?></div>
<ul id="error_message_box" class="error_message_box"></ul>

<?= form_open("$controller_name/save/$person_info->person_id", ['id' => 'employee_form', 'class' => 'form-horizontal pos-modern-form', 'autocomplete' => 'off']) ?>

    <p class="employee-form-steps-hint"><?= lang('Employees.form_steps_hint') ?></p>
    <ul class="nav nav-tabs nav-justified pos-form-tabs employee-form-tabs" data-tabs="tabs">
        <li class="active" role="presentation">
            <a data-toggle="tab" href="#employee_basic_info">1. <?= lang('Employees.basic_information') ?></a>
        </li>
        <li role="presentation">
            <a data-toggle="tab" href="#employee_login_info">2. <?= lang('Employees.login_info') ?></a>
        </li>
        <li role="presentation">
            <a data-toggle="tab" href="#employee_permission_info">3. <?= lang('Employees.permission_info') ?></a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade in active" id="employee_basic_info">
            <fieldset>
                <?= view('people/form_basic_info') ?>
            </fieldset>
        </div>

        <div class="tab-pane" id="employee_login_info">
            <fieldset>
                <div class="form-group form-group-sm">
                    <?= form_label(lang('Employees.username'), 'username', ['class' => 'required control-label col-xs-3']) ?>
                    <div class="col-xs-8">
                        <div class="input-group">
                            <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
                            <?= form_input([
                                'name'         => 'username',
                                'id'           => 'username',
                                'class'        => 'form-control input-sm',
                                'value'        => $person_info->username,
                                'autocomplete' => 'off',
                            ]) ?>
                        </div>
                    </div>
                </div>

                <?php $password_label_attributes = $person_info->person_id == "" ? ['class' => 'required'] : []; ?>

                <div class="form-group form-group-sm">
                    <?= form_label(lang('Employees.password'), 'password', array_merge($password_label_attributes, ['class' => 'control-label col-xs-3'])) ?>
                    <div class="col-xs-8">
                        <div class="input-group">
                            <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
                            <?= form_password([
                                'name'         => 'password',
                                'id'           => 'password',
                                'class'        => 'form-control input-sm',
                                'autocomplete' => 'new-password',
                            ]) ?>
                        </div>
                        <p class="help-block" style="margin-top:6px;margin-bottom:0;"><?= lang('Common.password_strong_hint') ?></p>
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <?= form_label(lang('Employees.repeat_password'), 'repeat_password', array_merge($password_label_attributes, ['class' => 'control-label col-xs-3'])) ?>
                    <div class="col-xs-8">
                        <div class="input-group">
                            <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
                            <?= form_password([
                                'name'         => 'repeat_password',
                                'id'           => 'repeat_password',
                                'class'        => 'form-control input-sm',
                                'autocomplete' => 'new-password',
                            ]) ?>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="tab-pane" id="employee_permission_info">
            <fieldset>
                <p><?= lang('Employees.permission_desc') ?></p>
                <input type="hidden" name="permission_check" id="permission_check" value="1">

                <ul id="permission_list" class="employee-permission-list">
                    <?php foreach ($all_modules as $module) { ?>
                        <li class="employee-permission-item">
                            <label class="employee-permission-module">
                                <?= form_checkbox("grant_$module->module_id", $module->module_id, $module->grant == 1, 'class="module"') ?>
                                <span>
                                    <span class="employee-permission-title"><?= lang("Module.$module->module_id") ?></span>
                                    <span class="employee-permission-desc"><?= lang("Module.$module->module_id" . '_desc') ?></span>
                                </span>
                            </label>
                            <?= form_dropdown(
                                "menu_group_$module->module_id",
                                [
                                    'home'   => lang('Module.home'),
                                    'office' => lang('Module.office'),
                                    'both'   => lang('Module.both')
                                ],
                                $module->menu_group,
                                'class="module-menu-group form-control input-sm"'
                            ) ?>
                            <?php
                            foreach ($all_subpermissions as $permission) {
                                $exploded_permission = explode('_', $permission->permission_id, 2);
                                if ($permission->module_id == $module->module_id) {
                                    $lang_key = $module->module_id . '.' . $exploded_permission[1];
                                    $lang_line = lang(ucfirst($lang_key));
                                    $lang_line = (lang(ucfirst($lang_key)) == $lang_line) ? ucwords(str_replace("_", " ", $exploded_permission[1])) : $lang_line;
                                    if (!empty($lang_line)) {
                            ?>
                                        <ul>
                                            <li>
                                                <label>
                                                    <?= form_checkbox("grant_$permission->permission_id", $permission->permission_id, $permission->grant == 1) ?>
                                                    <?= form_hidden("menu_group_$permission->permission_id", "--") ?>
                                                    <span><?= $lang_line ?></span>
                                                </label>
                                            </li>
                                        </ul>
                            <?php
                                    }
                                }
                            }
                            ?>
                        </li>
                    <?php } ?>
                </ul>
            </fieldset>
        </div>
    </div>

<?= form_close() ?>
</div>

<script type="text/javascript">
    window.WBPOS_STRONG_PASSWORD_MESSAGE = <?= json_encode(lang('Employees.password_strong')) ?>;
</script>
<script src="<?= base_url('js/password_strength.js?v=4') ?>"></script>
<script src="<?= base_url('js/strong_password.js?v=3') ?>"></script>
<script type="text/javascript">
    // Validation and submit handling
    $(document).ready(function() {
        var $email = $('#email');
        var savedEmail = <?= json_encode((string) ($person_info->email ?? '')) ?>;
        var stripBadEmail = function() {
            var value = ($email.val() || '').trim();
            if (value !== '' && value.indexOf('@') === -1 && savedEmail === '') {
                $email.val('');
            }
        };
        stripBadEmail();
        setTimeout(stripBadEmail, 400);

        $.validator.setDefaults({
            ignore: []
        });

        $.validator.addMethod('hasModuleGrant', function() {
            return $('#permission_list input.module:checked').length > 0;
        }, "<?= lang('Employees.subpermission_required') ?>");

        $('#permission_list > li').each(function() {
            var $li = $(this);
            var $module = $li.find('input.module').first();
            var $children = $li.find('ul input[type="checkbox"]');
            var $menu = $li.find('select.module-menu-group');

            var updateEnabled = function(checked) {
                $children.prop('disabled', !checked);
                $menu.prop('disabled', !checked);
                if (!checked) {
                    $children.prop('checked', false);
                }
            };

            $module.on('change', function() {
                var checked = $module.is(':checked');
                updateEnabled(checked);
                if (checked) {
                    $children.prop('checked', true);
                }
            });

            updateEnabled($module.is(':checked'));
        });

        $('#employee_form').validate($.extend({
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    success: function(response) {
                        dialog_support.hide();
                        table_support.handle_submit("<?= esc($controller_name) ?>", response);
                    },
                    dataType: 'json'
                });
            },

            errorLabelContainer: '#error_message_box',

            invalidHandler: function(event, validator) {
                if (!validator.numberOfInvalids()) {
                    return;
                }

                var $first = $(validator.errorList[0].element);
                var $pane = $first.closest('.tab-pane');
                if ($pane.length && !$pane.hasClass('active')) {
                    $('.employee-form-tabs a[href="#' + $pane.attr('id') + '"]').tab('show');
                }
            },

            rules: {
                first_name: 'required',
                last_name: 'required',
                username: {
                    required: true,
                    minlength: 5,
                    remote: '<?= esc("$controller_name/checkUsername/$employee_id") ?>'
                },
                password: {
                    <?php if ($person_info->person_id == '') { ?>
                        required: true,
                    <?php } ?>
                    minlength: 8,
                    strongPassword: true
                },
                repeat_password: {
                    equalTo: '#password'
                },
                email: {
                    email: true
                },
                permission_check: {
                    hasModuleGrant: true
                }
            },

            messages: {
                first_name: "<?= lang('Common.first_name_required') ?>",
                last_name: "<?= lang('Common.last_name_required') ?>",
                username: {
                    required: "<?= lang('Employees.username_required') ?>",
                    minlength: "<?= lang('Employees.username_minlength') ?>",
                    remote: "<?= lang('Employees.username_duplicate') ?>"
                },
                password: {
                    <?php if ($person_info->person_id == "") { ?>
                        required: "<?= lang('Employees.password_required') ?>",
                    <?php } ?>
                    minlength: "<?= lang('Employees.password_minlength') ?>",
                    strongPassword: "<?= lang('Employees.password_strong') ?>"
                },
                repeat_password: {
                    equalTo: "<?= lang('Employees.password_must_match') ?>"
                },
                email: "<?= lang('Common.email_invalid_format') ?>",
                permission_check: "<?= lang('Employees.subpermission_required') ?>"
            }
        }, form_support.error));
    });
</script>
