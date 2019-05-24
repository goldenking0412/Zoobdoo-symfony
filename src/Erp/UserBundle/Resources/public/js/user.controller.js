var UserController = function() {
    var name = $( '#name' ).val();
    this.init( { name: name ? name : null } );
};

UserController.prototype.init = function( options ) {
    this.options = {
        defaultName: 'defaultName'
    };
    $.extend( this.options, options );
    return this;
};

UserController.prototype.run = function() {
    alert( this.options.name ? this.options.name : this.options.defaultName );
};

$(function () {
    var controller = new UserController();
    controller.run();
});
