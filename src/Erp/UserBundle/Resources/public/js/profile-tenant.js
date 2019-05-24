var ProfileController = function () {
    this.dateRange = $('.date');
};

ProfileController.prototype.newTitle = function () {
    var itemText = $('.menu-item.current a').text(),
            pageTitle = $('.page-title');

    pageTitle.html(itemText);
};

ProfileController.prototype.datePicker = function () {
    var date = new Date();
    date.setDate(date.getDate());

    if (this.dateRange.length > 0) {
        this.dateRange.datepicker({
            startDate: new Date(),
            minDate: new Date(),
            autoclose: true
        });
    }
};

ProfileController.prototype.initPayRentWidget = function () {
    var propertyPrice = $('#property-price').val(),
            checkRecurring = $('#ps_pay_rent_form_isRecurring'),
            amountInput = $('#ps_pay_rent_form_amount');

    checkRecurring.click(function () {
        if ($(this).is(':checked')) {
            amountInput.val(propertyPrice).attr('readonly', true);
        } else {
            amountInput.removeAttr('readonly');
        }
    });
};

ProfileController.prototype.run = function () {
    this.newTitle();
    this.datePicker();
    this.initPayRentWidget();
};

$(function () {
    var controller = new ProfileController();
    controller.run();
});
