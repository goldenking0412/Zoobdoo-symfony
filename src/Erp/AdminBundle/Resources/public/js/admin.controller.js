var AdminController = function() {
};

AdminController.prototype.run = function() {
    $(document).on('change', 'input[type=file]', function() {
        var filename = this.files[0].name;
        $(this).parent().parent().parent().find('.form-group input[type=text]').val(filename);
    });
};

$(function() {
    var controller = new AdminController();
    controller.run();
});
