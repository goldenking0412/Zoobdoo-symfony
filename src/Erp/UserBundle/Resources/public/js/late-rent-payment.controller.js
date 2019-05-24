var LateRentPaymentController = function () {
    this.allowRentPaymentForm = '[name="erp_user_user_late_rent_payment"]';
};

LateRentPaymentController.prototype.listenCheckboxesChange = function () {
    var form = $(this.allowRentPaymentForm);
    var url = form.attr('action');

    form.find('input:checkbox').change(function () {
        if (form.find('input:checkbox:checked').length === 0) {
            form.append("<input type='hidden' name='" + form.attr('name') + "' />");
        }

        var data = form.serialize();
        form.find('input[type="hidden"][name="' + form.attr('name') + '"]').remove();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (response) {}
        });
    });
};

LateRentPaymentController.prototype.run = function() {
    this.listenCheckboxesChange();
};

$(function() {
    var controller = new LateRentPaymentController();
    controller.run();
});
