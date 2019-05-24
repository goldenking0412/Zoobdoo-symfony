var UploadController = function () {
};

UploadController.prototype.customUpload = function() {
    var multipleSupport = typeof $( '<input/>' )[0].multiple !== 'undefined',
        isIE = /msie/i.test( navigator.userAgent );
    $.fn.customFile = function() {
        return this.each(function() {
            var $file = $(this).addClass( 'custom-file-upload-hidden' ), 
                $wrap = $( '<div class="file-upload-wrapper">' ),
                $button = $( '<button type="button" class="btn-solid btn-solid-noborder file-upload-button">Select File</button>' ),
                $input = $( '.upload-input' ),
                $label = $( '<label class="btn-solid btn-solid-noborder file-upload-button" for="'+ $file[0].id +'">Select File</label>' );

                $file.css({
                    position: 'absolute',
                    left: '-9999px'
                });

                $wrap.insertAfter( $file )
                    .append( $file, ( isIE ? $label : $button ) );
                $file.attr( 'tabIndex', -1 );
                $button.attr( 'tabIndex', -1 );
                $button.click(function () {
                    $file.focus().click(); 
                });

            $file.change(function(event) {
                var files = [], fileArr, filename;

                if ( multipleSupport ) {
                  fileArr = $file[0].files;
                    for ( var i = 0, len = fileArr.length; i < len; i++ ) {
                        files.push( fileArr[i].name );
                    }
                    filename = files.join( ', ' );
                } else {
                    filename = $file.val().split( '\\' ).pop();
                } 

                $( event.currentTarget ).parents( '.doc-property-item' ).find( '.upload-input' ).val( filename ).attr( 'title', filename );
                $( event.currentTarget ).parents( '.upload-doc-col' ).find( '.upload-input' ).val( filename ).attr( 'title', filename );
            });
    });
};

$( '.file-upload-wrapper input[type=file]:last-child, .upload-doc-col input[type=file]' ).customFile();
};

UploadController.prototype.run = function() {
    this.customUpload();
};

$(function () {
    var controller = new UploadController();
    controller.run();
});
