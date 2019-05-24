var ApplicationController = function() {
};

ApplicationController.prototype.checkboxValidation = function() {
    var checkbox = $( '.agreement-block .checkbox-btn' ),
        checkLabel =  $( '.agreement-block .agreement-check' );

    $( '.application-submit' ).on( 'click', function ( event ) {
        if ( !checkbox.prop( 'checked' ) ) {
            checkLabel.addClass('notchecked');
        }
    });

    checkbox.on( 'click', function ( event ) {
        if ( checkbox.prop( 'checked' ) ) {
            checkLabel.removeClass('notchecked');
        }
    });
};

ApplicationController.prototype.run = function() {
    this.checkboxValidation();

    $('.custom-file-upload-hidden').on('change', function (){
        var fileName = this.files[0].name;

        $(this).parent().parent().find('.upload-filename').text(fileName);
    });

    $('.select-control').select2({'width': '95%'});

    $('.tab-next').on('click', function () {
        $('.nav-tabs > .active').next('li').find('a').trigger('click');

        $("html, body").animate({ scrollTop: 550 }, "slow");

        return false;
    });

    $('.tab-prev').on('click', function () {
        $('.nav-tabs > .active').prev('li').find('a').trigger('click');

        $("html, body").animate({ scrollTop: 550 }, "slow");

        return false;
    });
};


$( function () {
    var controller = new ApplicationController();
    controller.run();
});
