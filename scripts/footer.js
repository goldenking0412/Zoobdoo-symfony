var FooterController = function() {
};

FooterController.prototype.date = function() {
    var footerDate = $('.footer-date'),
        today = new Date(),
        year = today.getFullYear();

    footerDate.text( year );
};

FooterController.prototype.run = function() {
    this.date();
};

$( function () {
    var controller = new FooterController();
    controller.run();
});
