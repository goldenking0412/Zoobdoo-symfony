var SMPersonalFormController = function () {
    this.dateRange = $('.date-birth');
    this.routeCheckRenterPaid = $('#route-check-renter-paid').val();
    this.routeReportPaid = $('#route-report-paid').val();
};

SMPersonalFormController.prototype.datePicker = function () {
    if (this.dateRange.length > 0) {
        this.dateRange.datepicker({
            startDate: new Date(1880, 01, 01),
            minDate: new Date(1880, 01, 01),
            maxDate: new Date(),
            autoclose: true
        });
    }

    function getInternetExplorerVersion() {
        var rv = -1;
        if (navigator.appName == 'Microsoft Internet Explorer') {
            var ua = navigator.userAgent;
            var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
            if (re.exec(ua) != null)
                rv = parseFloat(RegExp.$1);
        } else if (navigator.appName == 'Netscape')
        {
            var ua = navigator.userAgent;
            var re = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
            if (re.exec(ua) != null)
                rv = parseFloat(RegExp.$1);
        }
        return rv;
    }

    if (getInternetExplorerVersion() !== -1) {
        $('.date-birth').bind('invalid', function () {
            event.preventDefault();
        }, false);

        $('#sm-personal-form-submit').on('click', function () {
            $('.personal-info-form input:invalid').addClass('required-control');
        });
    }
};

SMPersonalFormController.prototype.initFCRAAgreement = function () {
    var checkFCRA = $('#sm_personal_info_form_FcraAgreementAccepted');

    if (checkFCRA.is(':checked')) {
        $('#sm-personal-form-submit').removeAttr('disabled');
    }

    checkFCRA.click(function () {
        if ($(this).is(':checked')) {
            $('#sm-personal-form-submit').removeAttr('disabled');
        } else {
            $('#sm-personal-form-submit').attr('disabled', 'disabled');
        }
    });
};

SMPersonalFormController.prototype.initTenantScreeningWidget = function () {
    var emailReportSelect = $('#sm_get_reports_form_smRenters'),
            getReportBtn = $('#sm_get_reports_form_submit'),
            getReportForm = $('form[name="sm_get_reports_form"]'),
            self = this,
            isPayed = false;


    emailReportSelect.change(function () {
        var selValue = this.value;
        $.ajax({
            url: self.routeCheckRenterPaid + '/' + selValue,
            type: 'POST',
            success: function (response) {
                if (response.status !== 'undefined') {
                    isPayed = response.status;
                    if (isPayed) {
                        getReportBtn.removeAttr('disabled');
                        $('.paid-report').remove();
                        getReportBtn.show();
                    } else {
                        getReportBtn.hide();
                        $('.paid-report').remove();
                        getReportForm.find('.with-submit-btn').append('<a role="popup" href="' + self.routeReportPaid + '/' + selValue + '" class="btn edit-btn paid-report lightbox-ajax">GET REPORT</a>');
                        initLightbox();
                        //$('#managers-profile').erpPopup();
                    }
                }
            }
        });
    });
}

SMPersonalFormController.prototype.run = function () {
    this.datePicker();
    this.initFCRAAgreement();
    this.initTenantScreeningWidget();
};

$(function () {
    var controller = new SMPersonalFormController();
    controller.run();
});
