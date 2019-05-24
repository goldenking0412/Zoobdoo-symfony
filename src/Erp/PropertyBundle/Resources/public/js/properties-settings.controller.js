//TODO Create React form instead
var PropertiesSettingsController = function () {
    this.nextSelector = 'button[role="next"]';
    this.backSelector = 'button[role="back"]';
    this.loadedSteps = [];
    this.currentStep = null;
    this.$settingsPopup = null;
};

//TODO Parse backend errors
PropertiesSettingsController.prototype.processResponse = function (response) {
    var that = this;

    if (!this.$settingsPopup.find('.modal-body').find('#' + that.currentStep).length) {
        this.$settingsPopup.find('.modal-body').append(response.html);
    }

    if (that.currentStep === 'properties-confirmation') {
        this.$settingsPopup.find('.modal-body').find('#' + that.currentStep).html(response.html);
    }

    this.$settingsPopup.find('.modal-title').html(response.modalTitle);
    $('body').trigger('popup-open');

    var modalBody = this.$settingsPopup.find('.modal-body');
    var form = modalBody.find('#' + that.currentStep + ' form');

    if (form) {
        form.submit(function (e) {
            var $form = $(this);
            var currentStep = $form.find('button[type=submit]').attr('step');
            var data = {};

            if (currentStep === 'properties-confirmation' || currentStep === 'save-settings') {
                data = {
                    erp_property_payment_settings: {
                        dayUntilDue: $('[name="erp_property_payment_settings[dayUntilDue]"]').val(),
                        paymentAmount: $('[name="erp_property_payment_settings[paymentAmount]"]').val()
                    }
                };

                if ($('[name="erp_property_payment_settings[allowPartialPayments]"]').is(":checked")) {
                    data.erp_property_payment_settings.allowPartialPayments = '1';
                }

                if ($('[name="erp_property_payment_settings[allowCreditCardPayments]"]').is(":checked")) {
                    data.erp_property_payment_settings.allowCreditCardPayments = '1';
                }

                if ($('[name="erp_property_payment_settings[allowAutoDraft]"]').is(":checked")) {
                    data.erp_property_payment_settings.allowAutoDraft = '1';
                }

            }

            $form.find('button[type=submit]').prop('disabled', true);

            if (currentStep === 'properties-confirmation') {
                var buff = [];
                $.each($form.serializeArray(), function (index, obj) {
                    buff.push(obj.value);
                });

                data['idx'] = buff;
            } else {
                $.each($form.serializeArray(), function (index, obj) {
                    data[obj.name] = obj.value;
                });
            }

            $.ajax({
                type: 'POST',
                cache: false,
                url: form.attr('action'),
                data: data,
                async: true,
                dataType: 'json',
                success: function (response) {
                    if (response.redirect) {
                        window.location = response.redirect;

                        return;
                    }

                    $form.find('button[type=submit]').prop('disabled', false);
                    $('#' + that.currentStep).hide();

                    that.loadedSteps.push(currentStep);
                    that.currentStep = currentStep;
                    $('#' + currentStep).show();
                    that.processResponse(response);
                }

            });
            return false;
        });

    }

    this.$settingsPopup.modal('show');
};

PropertiesSettingsController.prototype.initSettingsPopup = function () {
    if (!(this.$settingsPopup)) {
        var modalHtml =
                '<div class="modal fade" id="erp-settings-popup" tabindex="-1" role="dialog" aria-labelledby="my-modal-label" aria-hidden="true">'
                + '<div class="modal-dialog">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'
                + '<h4 class="modal-title"></h4>'
                + '</div>'
                + '<div class="modal-body"></div>'
                + '<div class="modal-footer"></div>'
                + '</div>';
        +'</div>';
        +'</div>';

        $(modalHtml).appendTo('body');
    }
    
    this.$settingsPopup = $(document).find('#erp-settings-popup');
    
    var that = this;
    this.$settingsPopup.on('shown.bs.modal', function (event) {
        that.$settingsPopup.find('.select-control').select2({
            dropdownParent: that.$settingsPopup.find('.modal-body')
        });
        that.$settingsPopup.find('.select2-selection__arrow').hide();

        $(window).resize(function () {
            that.$settingsPopup.find('.select-control').select2({
                dropdownParent: that.$settingsPopup.find('.modal-body')
            });
            that.$settingsPopup.find('.select2-selection__arrow').hide();
        }.bind(this));
    });
};

PropertiesSettingsController.prototype.listenNext = function () {
    var that = this;
    $('body').on('click', this.nextSelector, function (e) {
        e.preventDefault();
        var $this = $(this);
        var currentStep = $this.attr('step');

        $this.prop('disabled', true);

        if (-1 === $.inArray(currentStep, that.loadedSteps)) {
            $.ajax({
                type: 'GET',
                cache: false,
                url: $this.attr('url'),
                dataType: 'json',
                async: true,
                success: function (response) {
                    $('#' + that.currentStep).hide();

                    that.loadedSteps.push(currentStep);
                    that.currentStep = currentStep;

                    that.processResponse(response);
                    $this.prop('disabled', false);
                }
            });
        } else {
            $('#' + currentStep).show();
            $('#' + that.currentStep).hide();

            that.currentStep = currentStep;
            $this.prop('disabled', false);
        }
    });
};

PropertiesSettingsController.prototype.listenBack = function () {
    var that = this;
    $('body').on('click', this.backSelector, function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.prop('disabled', true);
        var nextStep = $this.attr('step');

        $('#' + that.currentStep).hide();
        $('#' + nextStep).show();

        that.currentStep = nextStep;

        $this.prop('disabled', false);
    });
};

PropertiesSettingsController.prototype.listenClosePopup = function () {
    var that = this;
    $('#erp-settings-popup').on('hide.bs.modal', function (e) {
        that.loadedSteps = [];
        that.currentStep = null;
        $('#erp-settings-popup').find('.modal-body').empty();
    });
};

PropertiesSettingsController.prototype.run = function () {
    this.initSettingsPopup();
    this.listenNext();
    this.listenBack();
    this.listenClosePopup();
};

$(document).ready(function () {
    var controller = new PropertiesSettingsController();
    controller.run();
});
