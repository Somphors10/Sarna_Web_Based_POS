<?php
/**
 * @var object $admin
 */
?>

<div id="required_fields_message"><?= lang('Common.fields_required_message') ?></div>
<ul id="error_message_box" class="error_message_box"></ul>

<?= form_open('super-admin/changepassword', ['id' => 'employee_form', 'class' => 'form-horizontal']) ?>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="employee_login_info">
            <fieldset>

                <div class="form-group form-group-sm">
                    <?= form_label(lang('Employees.username'), 'username', ['class' => 'required control-label col-xs-3']) ?>
                    <div class="col-xs-8">
                        <div class="input-group">
                            <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-user"></span></span>
                            <?= form_input([
                                'name'     => 'username',
                                'id'       => 'username',
                                'class'    => 'form-control input-sm',
                                'value'    => $admin->username,
                                'readonly' => 'true'
                            ]) ?>
                        </div>
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <?= form_label(lang('Employees.current_password'), 'current_password', ['class' => 'required control-label col-xs-3']) ?>
                    <div class="col-xs-8">
                        <div class="input-group">
                            <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
                            <?= form_password([
                                'name'  => 'current_password',
                                'id'    => 'current_password',
                                'class' => 'form-control input-sm'
                            ]) ?>
                        </div>
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <?= form_label(lang('Employees.password'), 'password', ['class' => 'required control-label col-xs-3']) ?>
                    <div class="col-xs-8">
                        <div class="input-group">
                            <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
                            <?= form_password([
                                'name'  => 'password',
                                'id'    => 'password',
                                'class' => 'form-control input-sm'
                            ]) ?>
                        </div>
                        <p class="help-block" style="margin-top:6px;margin-bottom:0;"><?= lang('Common.password_strong_hint') ?></p>
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <?= form_label(lang('Employees.repeat_password'), 'repeat_password', ['class' => 'required control-label col-xs-3']) ?>
                    <div class="col-xs-8">
                        <div class="input-group">
                            <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-lock"></span></span>
                            <?= form_password([
                                'name'  => 'repeat_password',
                                'id'    => 'repeat_password',
                                'class' => 'form-control input-sm'
                            ]) ?>
                        </div>
                    </div>
                </div>

            </fieldset>
        </div>
    </div>
<?= form_close() ?>

<script type="text/javascript">
    window.WBPOS_STRONG_PASSWORD_MESSAGE = <?= json_encode(lang('Employees.password_strong')) ?>;
</script>
<script src="<?= base_url('js/password_strength.js?v=4') ?>"></script>
<script src="<?= base_url('js/strong_password.js?v=3') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $.validator.setDefaults({
            ignore: []
        });

        $.validator.addMethod("notEqualTo", function(value, element, param) {
            return this.optional(element) || value != $(param).val();
        }, '<?= lang('Employees.password_not_must_match') ?>');

        $('#employee_form').validate($.extend({
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    success: function(response) {
                        dialog_support.hide();
                        $.notify(response.message, {
                            type: response.success ? 'success' : 'danger'
                        });
                    },
                    dataType: 'json'
                });
            },

            rules: {
                current_password: {
                    required: true,
                    minlength: 8
                },
                password: {
                    required: true,
                    minlength: 8,
                    strongPassword: true,
                    notEqualTo: "#current_password"
                },
                repeat_password: {
                    equalTo: "#password"
                }
            },

            messages: {
                password: {
                    required: "<?= lang('Employees.password_required') ?>",
                    minlength: "<?= lang('Employees.password_minlength') ?>",
                    strongPassword: "<?= lang('Employees.password_strong') ?>"
                },
                repeat_password: {
                    equalTo: "<?= lang('Employees.password_must_match') ?>"
                }
            }
        }, form_support.error));
    });
</script>
