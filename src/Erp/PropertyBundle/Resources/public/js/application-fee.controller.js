var ApplicationFeeController = function () {
    this.noFeeSelector = '[name="erp_property_application_fee[noFee]"]';
    this.feeSelector = '[name="erp_property_application_fee[fee]"]';
};

ApplicationFeeController.prototype.listenNoFee = function () {
    var that = this;
    $(this.noFeeSelector).change(function () {
        var noFeeEl = $(this);
        var feeEl = $(that.feeSelector);
        var disabled = false;

        if (noFeeEl.is(':checked')) {
            disabled = true;
        }

        feeEl.prop('disabled', disabled);
    });
};

ApplicationFeeController.prototype.run = function() {
    this.listenNoFee();
};

$(function () {
    var controller = new ApplicationFeeController();
    controller.run();
});
