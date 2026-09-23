<?php
/**
 * Shared person fields — Cambodia layout (employees, customers, suppliers).
 *
 * DB columns stay the same; labels map to KH address parts:
 * address_1 = House / Street, address_2 = Village, city = Commune,
 * state = District, zip = Province, country = Country.
 *
 * @var object $person_info
 * @var array $config
 */

$country_value = trim((string)($person_info->country ?? ''));
if ($country_value === '') {
    $country_value = 'Cambodia';
}
?>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.last_name'), 'last_name', ['class' => 'required control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <?= form_input([
            'name'        => 'last_name',
            'id'          => 'last_name',
            'class'       => 'form-control input-sm',
            'value'       => $person_info->last_name,
            'placeholder' => lang('Common.last_name_placeholder'),
        ]) ?>
    </div>
</div>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.first_name'), 'first_name', ['class' => 'required control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <?= form_input([
            'name'        => 'first_name',
            'id'          => 'first_name',
            'class'       => 'form-control input-sm',
            'value'       => $person_info->first_name,
            'placeholder' => lang('Common.first_name_placeholder'),
        ]) ?>
    </div>
</div>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.gender'), 'gender', !empty($basic_version) ? ['class' => 'required control-label col-xs-3'] : ['class' => 'control-label col-xs-3']) ?>
    <div class="col-xs-4">
        <label class="radio-inline">
            <?= form_radio([
                'name'    => 'gender',
                'type'    => 'radio',
                'id'      => 'gender',
                'value'   => 1,
                'checked' => (string) $person_info->gender === '1'
            ]) ?> <?= lang('Common.gender_male') ?>
        </label>
        <label class="radio-inline">
            <?= form_radio([
                'name'    => 'gender',
                'type'    => 'radio',
                'id'      => 'gender',
                'value'   => 0,
                'checked' => (string) $person_info->gender === '0'
            ]) ?> <?= lang('Common.gender_female') ?>
        </label>
    </div>
</div>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.email'), 'email', ['class' => 'control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <div class="input-group">
            <span class="input-group-addon input-sm"><span class="glyphicon glyphicon-envelope"></span></span>
            <?= form_input([
                'name'         => 'email',
                'id'           => 'email',
                'class'        => 'form-control input-sm',
                'value'        => $person_info->email,
                'placeholder'  => 'name@gmail.com',
                'autocomplete' => 'off',
                'readonly'     => 'readonly',
            ]) ?>
            <span class="input-group-btn">
                <a id="person_email_send_btn" class="btn btn-default btn-sm" href="#" title="<?= esc(lang('Common.email_send')) ?>" aria-label="<?= esc(lang('Common.email_send')) ?>">
                    <span class="glyphicon glyphicon-send"></span>
                </a>
            </span>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var $emailField = $('#email');
        var $emailSendBtn = $('#person_email_send_btn');

        if ($emailField.length === 0 || $emailSendBtn.length === 0) {
            return;
        }

        var syncEmailSendButton = function() {
            var address = ($emailField.val() || '').trim();
            if (address === '') {
                $emailSendBtn.attr('href', '#').addClass('disabled').attr('aria-disabled', 'true');
                return;
            }

            $emailSendBtn
                .attr('href', 'mailto:' + encodeURIComponent(address))
                .removeClass('disabled')
                .attr('aria-disabled', 'false');
        };

        var isValidEmail = function(value) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test((value || '').trim());
        };

        var clearAutofillIfInvalid = function() {
            var value = ($emailField.val() || '').trim();
            if (value !== '' && !isValidEmail(value)) {
                $emailField.val('');
            }
        };

        $emailField.on('focus', function() {
            $emailField.removeAttr('readonly');
        });

        $emailField.on('input change blur', syncEmailSendButton);
        clearAutofillIfInvalid();
        setTimeout(clearAutofillIfInvalid, 300);
        syncEmailSendButton();
    });
</script>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.phone_number'), 'phone_number', ['class' => 'control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <div class="input-group">
            <span class="input-group-addon input-sm">+855</span>
            <?= form_input([
                'name'        => 'phone_number',
                'id'          => 'phone_number',
                'class'       => 'form-control input-sm',
                'value'       => $person_info->phone_number,
                'placeholder' => lang('Common.phone_number_placeholder'),
            ]) ?>
        </div>
    </div>
</div>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.address_1'), 'address_1', ['class' => 'control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <?= form_input([
            'name'        => 'address_1',
            'id'          => 'address_1',
            'class'       => 'form-control input-sm',
            'value'       => $person_info->address_1,
            'placeholder' => lang('Common.address_1_placeholder'),
        ]) ?>
    </div>
</div>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.address_2'), 'address_2', ['class' => 'control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <?= form_input([
            'name'        => 'address_2',
            'id'          => 'address_2',
            'class'       => 'form-control input-sm',
            'value'       => $person_info->address_2,
            'placeholder' => lang('Common.address_2_placeholder'),
        ]) ?>
    </div>
</div>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.city'), 'city', ['class' => 'control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <?= form_input([
            'name'        => 'city',
            'id'          => 'city',
            'class'       => 'form-control input-sm',
            'value'       => $person_info->city,
            'placeholder' => lang('Common.city_placeholder'),
        ]) ?>
    </div>
</div>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.state'), 'state', ['class' => 'control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <?= form_input([
            'name'        => 'state',
            'id'          => 'state',
            'class'       => 'form-control input-sm',
            'value'       => $person_info->state,
            'placeholder' => lang('Common.state_placeholder'),
        ]) ?>
    </div>
</div>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.zip'), 'zip', ['class' => 'control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <?= form_input([
            'name'        => 'zip',
            'id'          => 'postcode',
            'class'       => 'form-control input-sm',
            'value'       => $person_info->zip,
            'placeholder' => lang('Common.zip_placeholder'),
        ]) ?>
    </div>
</div>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.country'), 'country', ['class' => 'control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <?= form_input([
            'name'  => 'country',
            'id'    => 'country',
            'class' => 'form-control input-sm',
            'value' => $country_value,
        ]) ?>
    </div>
</div>

<div class="form-group form-group-sm">
    <?= form_label(lang('Common.comments'), 'comments', ['class' => 'control-label col-xs-3']) ?>
    <div class="col-xs-8">
        <?= form_textarea([
            'name'  => 'comments',
            'id'    => 'comments',
            'class' => 'form-control input-sm',
            'value' => $person_info->comments
        ]) ?>
    </div>
</div>
