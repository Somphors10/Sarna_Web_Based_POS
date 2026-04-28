<?= view('partial/header') ?>

<script type="text/javascript">
    dialog_support.init("a.modal-dlg");
</script>

<section class="neo-module-page">
    <header class="neo-module-header">
        <div>
            <h3 class="neo-module-title"><?= lang('Messages.sms_send') ?></h3>
            <p class="neo-module-subtitle">Send messages quickly to one or multiple recipients.</p>
        </div>
    </header>

    <div class="pos-form-shell" style="max-width: 760px; margin: 0 auto;">
        <?= form_open("messages/send/", ['id' => 'send_sms_form', 'enctype' => 'multipart/form-data', 'method' => 'post', 'class' => 'form-horizontal pos-modern-form']) ?>
            <div class="tab-content">
                <div class="form-group form-group-sm">
                    <label for="phone" class="col-xs-3 control-label"><?= lang('Messages.phone') ?></label>
                    <div class="col-xs-9">
                        <input class="form-control input-sm" type="text" name="phone" placeholder="<?= lang('Messages.phone_placeholder') ?>">
                        <span class="help-block" style="margin-top: 6px;"><?= lang('Messages.multiple_phones') ?></span>
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="message" class="col-xs-3 control-label"><?= lang('Messages.message') ?></label>
                    <div class="col-xs-9">
                        <textarea class="form-control input-sm" rows="4" id="message" name="message" placeholder="<?= lang('Messages.message_placeholder') ?>"></textarea>
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <div class="col-xs-offset-3 col-xs-9">
                        <?= form_submit([
                            'name'  => 'submit_form',
                            'id'    => 'submit_form',
                            'value' => lang('Common.submit'),
                            'class' => 'btn btn-primary btn-sm'
                        ]) ?>
                    </div>
                </div>
            </div>
        <?= form_close() ?>
    </div>
</section>

<?= view('partial/footer') ?>

<script type="text/javascript">
    // Validation and submit handling
    $(document).ready(function() {
        $('#send_sms_form').validate({
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    success: function(response) {
                        $.notify({
                            message: response.message
                        }, {
                            type: response.success ? 'success' : 'danger'
                        })
                    },
                    dataType: 'json'
                });
            }
        });
    });
</script>
