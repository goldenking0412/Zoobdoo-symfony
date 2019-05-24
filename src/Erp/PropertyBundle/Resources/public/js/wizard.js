$(document).ready(function () {
    var nodeFormSettingsError = $('#settings-error');
    
    var $collectionHolder;
    var $addTagLink = $('<div class="clearfix"></div><div><a href="#" data-href="#" class="add-another-collection-widget btn-add pull-right" style="margin-top: 10px; margin-bottom: 50px;"><span class="img">+</span><span class="text">Add Tenant</span></a></div>');

    /**
     * 
     * @param {type} $collectionHolder
     * @returns {addTagForm}
     */
    function addTagForm($collectionHolder) {
        var prototype = $collectionHolder.data('prototype'),
                index = $collectionHolder.data('index')
        ;
        var newForm = prototype;
        newForm = newForm.replace(/__name__/g, index);
        $collectionHolder.data('index', index + 1);
        $collectionHolder.append(newForm);
        addTagFormDeleteLink();
        $('#erp_invite_user_form_invitedUsers_' + index + '_birthdate').datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true
        }).on('hide', function (event) {
            event.preventDefault();
            event.stopPropagation();
        });
    }

    /**
     * 
     * @returns {undefined}
     */
    function addTagFormDeleteLink() {
        var $removeFormA = $('<div class="clearfix"></div><div><a href="#" data-href="#" class="add-another-collection-widget btn-add red" style="margin-bottom: 10px;"><span class="img"><i class="icon icon-close"></i></span><span class="text">Remove</span></a></div>');
        var $tagFormLi = $collectionHolder.find('li').last();
        $tagFormLi.append($removeFormA);
        $removeFormA.on('click', function (e) {
            e.preventDefault();
            $tagFormLi.remove();
        });
    }
    
    // Get the ul that holds the collection of tags
    $collectionHolder = $('ul.tags');
    $collectionHolder.parent().append($addTagLink);
    $collectionHolder.find('li').each(function () {
        addTagFormDeleteLink();
    });
    $collectionHolder.data('index', $collectionHolder.find('li').length);
    $addTagLink.on('click', function (e) {
        e.preventDefault();
        addTagForm($collectionHolder);
    });
    $('#erp_invite_user_form_invitedUsers_0_birthdate').datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true
        }).on('hide', function (event) {
            event.preventDefault();
            event.stopPropagation();
        });

    $('#form-settings').on('submit', function (event) {
        event.preventDefault();
        var $this = $(this), url = this.action, method = this.method, self = this, $self = $this;
        $.ajax({
            url: url,
            method: method,
            dataType: 'json',
            data: $this.serialize(),
            success: function (data) {
                self.elements['second_email'] = data.second_email;
                $.each(data.settings, function (index, item) {
                    $self.find('#' . item).attr('checked', 'checked');
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText);
                
                if (nodeFormSettingsError.length === 0) {
                    $self.append(nodeFormSettingsError);
                    nodeFormSettingsError = $('#settings-error');
                }
                nodeFormSettingsError
                        .toggleClass('hidden')
                        .find('.message')
                        .html('Something went wrong when saving these settings. Please try again later')
                ;
                self.reset();
            }
        });
    });
    
    var f_erp_property_payment_settings_termLease = function () {
        if ($('#erp_property_payment_settings_termLease').is(':checked')) {
            $('#erp_property_payment_settings_leaseEnd').parent().parent().fadeIn();
            $('#erp_property_payment_settings_atWill').attr('disabled', 'disabled');
            $('#erp_property_payment_settings_atWill').attr('checked', false);
        } else {
            $('#erp_property_payment_settings_leaseEnd').parent().parent().fadeOut();
            $('#erp_property_payment_settings_atWill').attr('disabled', false);
        }
    };
    
    f_erp_property_payment_settings_termLease();
    
    $('#erp_property_payment_settings_atWill').click(function () {
        if ($('#erp_property_payment_settings_atWill').is(':checked')) {
            $('#erp_property_payment_settings_termLease').attr('checked', false);
            $('#erp_property_payment_settings_termLease').attr('disabled', 'disabled');
            f_erp_property_payment_settings_termLease();
        } else {
            $('#erp_property_payment_settings_termLease').attr('checked', false);
            $('#erp_property_payment_settings_termLease').attr('disabled', false);
        }
    });
    
    $('#erp_property_payment_settings_termLease').click(f_erp_property_payment_settings_termLease);

    var $datePickers = $('#erp_property_payment_settings_moveInDate, #erp_property_payment_settings_leaseEnd, .datewidget'),
            select2 = $('#filter-all-landlords, #filter-all-managers')
    ;

    if ($datePickers.length > 0) {
        $datePickers.datepicker({
            format: 'mm/dd/yyyy',
        }).on('hide', function (event) {
            event.preventDefault();
            event.stopPropagation();
        });
    }

    if (select2.length > 0) {
        $('#filter-all-landlords, #filter-all-managers').select2({
            placeholder: 'Select one',
            showSearchBox: true
        });

        $('#filter-all-landlords, #filter-all-managers').on('select2:open', function () {
            $('.select2-dropdown')
                    .css('border', '1px solid #d6d6d6')
                    .css('border-radius', '10px 10px 0px 0px')
                    ;
            $('.select2-dropdown .select2-search--dropdown').css('display', 'block');
        });
    }

    $('#filter-all-landlords, #filter-all-managers').on('change', function (event) {
        var $this = $(this), values = ($(this).val()).split('&'),
                $form = $this.closest('form'), formName = $form.attr('name'),
                form = $form.get(0)
                ;

        values.forEach(function (currentValue, index, arr) {
            var pair = currentValue.split('='), key = pair[0],
                    value = decodeURIComponent(pair[1]);
            form.elements[formName + '[' + key + ']'].value = value;
            // form.elements[formName + '[' + key + ']'].setAttribute('disabled', 'disabled');
            form.elements[formName + '[' + key + ']'].setAttribute('style', 'pointer-events: none; cursor: not-allowed;');
        });
    });
    
    $('#landlord-btn-reset').on('click', function (event) {
        var form = event.target.form, elements = form.elements;
        for (var i = 0; i < elements.length; i++) {
            elements[i].removeAttribute('disabled');
            elements[i].removeAttribute('style');
        }
    });

    $('.contact-email-wizard').first().attr('required', 'required');
    $('.contact-email-wizard:gt(0)').parent().parent().hide();
});