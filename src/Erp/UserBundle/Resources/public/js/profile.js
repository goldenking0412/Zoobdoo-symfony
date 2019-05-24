var ProfileController = function () {
    this.dateRange = $('.date');
};

ProfileController.prototype.newTitle = function () {
    var itemText = $('.menu-item.current a').text(),
            pageTitle = $('.page-title');

    pageTitle.html(itemText);
};

ProfileController.prototype.datePicker = function () {
    if (this.dateRange.length > 0) {
        this.dateRange.datepicker({
            autoclose: true
        });
    }
};

ProfileController.prototype.run = function () {
    var widgetMessage = $('.widget-message'),
            widgetRequest = $('.widget-service-request'),
            widgetPayRent = $('form[name="ps_pay_rent_form"]'),
            widgetAskPro = $('form[name="erp_users_ask_pro_form"]'),
            widgetCheckEmail = $('form[name="sm_email_form"]');

    this.newTitle();
    this.datePicker();

    if (widgetMessage.length > 0) {
        widgetMessage.validate({
            success: function () {
                widgetMessage.find('button[type=submit]').prop('disabled', false);
            },
            errorPlacement: function (error, element) {
                return false;
            }
        });
    }

    if (widgetRequest.length > 0) {
        widgetRequest.validate({
            success: function () {
                widgetRequest.find('button[type=submit]').prop('disabled', false);
            },
            errorPlacement: function (error, element) {
                return false;
            }
        });
    }
    if (widgetPayRent.length > 0) {
        widgetPayRent.validate({
            success: function () {
                widgetPayRent.find('button[type=submit]').prop('disabled', false);
            },
            errorPlacement: function (error, element) {
                return false;
            }
        });
    }

    if (widgetAskPro.length > 0) {
        widgetAskPro.validate({
            success: function () {
                widgetAskPro.find('button[type=submit]').prop('disabled', false);
            },
            errorPlacement: function (error, element) {
                return false;
            }
        });
    }

    if (widgetAskPro.length > 0) {
        widgetCheckEmail.validate({
            success: function () {
                widgetCheckEmail.find('button[type=submit]').prop('disabled', false);
            },
            errorPlacement: function (error, element) {
                return false;
            }
        });
    }
};

$(function () {
    var controller = new ProfileController();
    controller.run();
});
