var ListingsController = function () {
    this.newImageId = 'new-image-';
    this.carouselId = $( '#carousel-id' ).val();
    this.addImageBtn = $( '#add-another-image' );
    this.imageCount = $( '#image-count' ).val();
    this.imageBlockPrefix = $( '#image-block-prefix' ).val();
    this.newSmallImagesBlock = $( '.carousel-indicators' );
    this.newBigImagesBlock = $( '.carousel-inner' );
    this.imageDataPrototype = $( '#image-data-prototype' ).val();
    this.imagesFieldsList = $( '#images-fields-list' );
    this.imageFieldsId = 'new-form-image-field-';
    this.documentsDataPrototype = $( '#document-data-prototype' ).val();
    this.addDocumentBtn = $( '#add-another-document' );
    this.documentCount = $( '#document-count' ).val();
    this.documentFieldsList = $( '#document-fields-list' );
    this.docFieldsId = 'new-form-doc-field-';
};

ListingsController.prototype.initImageUploader = function() {
    var self = this;

    self.addImageBtn.click(function (e) {
        e.preventDefault();
        var newWidget = self.imageDataPrototype.replace( /__name__/g, self.imageCount );

        var newLi = $( '<li id="' + self.imageFieldsId + self.imageCount + '"></li>' ).html( newWidget );
        newLi.appendTo( self.imagesFieldsList );

        self.imageCount++;
        self.renderImage();
    });
    self.removeLoadedImage();
};

ListingsController.prototype.inArray = function(val) {
    for(var i = 0, l = this.length; i < l; i++)	{
        if(this[i] == p_val) {
            return true;
        }
    }
    return false;
};

ListingsController.prototype.renderImage = function() {
    var self = this,
        imageCount = self.imageCount - 1,
        fileBtn = $( 'div#' + self.imageBlockPrefix + '_' + imageCount ).find('input[ type="file" ]' ),
        itemsWrapper = $( '.carousel-indicators' ),
        slideCurrent = $( '.carousel-indicators li.active' ),
        slidesNumber = $( '.article-slide .item' ).length,
        error = $('.errors.common-errors');

    fileBtn.click();

    fileBtn.bind('change keyup input', function() {
        var reader = new FileReader(),
            file = this.files[0],
            allowedMimeTypes = fileBtn.data('mime-types').split(';'),
            allowedSize = fileBtn.data('max-file-size'),
            maxSizeMessage = fileBtn.data('max-file-size-message'),
            mimeTypesMessage = fileBtn.data('mime-types-message');

        error.html('');

        if (file) {
            if (file.size > allowedSize) {
                error.html( maxSizeMessage );
                self.imagesFieldsList.find('li:last').remove();
            } else if ($.inArray(file.type, allowedMimeTypes) == -1) {
                error.html( mimeTypesMessage );
                self.imagesFieldsList.find('li:last').remove();
            } else {

                reader.onload = function (e) {

                    var imgId = self.newImageId + imageCount,
                        bigBlockClass = imageCount > 1 ? '' : 'active',
                        smallLiClass = imageCount > 1 ? '' : 'active',
                        img = '<img id="' + imgId + '" src="' + e.target.result + '" class="" />';

                    var newBigImg = '<div class="item ' + bigBlockClass + '">' + img + '</div>',
                        newSmallImg = '<li class="' + smallLiClass + '" data-slide-to="' + ( imageCount ) + '" data-target="#' + self.carouselId + '">' +
                            '<a href="#" class="image-delete" data-image-attr-count="' + imageCount + '">&times;</a>' + img + '</li>',
                        firstImg = $('.first-screen-img');

                    firstImg.remove();
                    $('.cont-slider .item, .carousel-indicators li').removeClass('active');
                    self.newBigImagesBlock.append(newBigImg);
                    self.newSmallImagesBlock.append(newSmallImg);

                    $('.cont-slider .item:last-child, .carousel-indicators li:last-child').addClass('active');
                    self.newBigImagesBlock.find('img').css({'width': 325, 'height': 300});
                    self.newSmallImagesBlock.find('img').css({'width': 75, 'height': 65});
                };

                // read the image file as a data URL.
                reader.readAsDataURL(this.files[0]);

                itemsWrapper.width(( slideCurrent.width() + parseInt(slideCurrent.css('margin-right')) + parseInt(slideCurrent.css('margin-left')) ) * ( slidesNumber + 1 ));
                ( itemsWrapper ).css('left', -(slideCurrent.width() + parseInt(slideCurrent.css('margin-right')) + parseInt(slideCurrent.css('margin-left')) ) * ( slidesNumber - 1 ));

            }
        }
    });
};

ListingsController.prototype.removeLoadedImage = function() {
    var self = this,
        itemsWrapper = $( '.carousel-indicators' );

    $( document ).on( 'click', '.image-delete', function ( e ) {
        e.preventDefault();
        var imgCntId = $(this).attr( 'data-image-attr-count' );
        var imgId = self.newImageId + imgCntId;

        $( this ).parent().remove();
        self.newBigImagesBlock.find( '#' + imgId ).parent().remove();
        self.imagesFieldsList.find( '#' + self.imageFieldsId + imgCntId ).remove();

        if ( !self.newBigImagesBlock.children().length ) {
            self.newBigImagesBlock.html( '<div class="item active first-screen-img"><img alt="" title="" src="https://placehold.it/325x300"></div>' );
            self.imageCount = 1;
        }

        self.newBigImagesBlock.children().removeClass( 'active' );
        self.newBigImagesBlock.children().first().addClass( 'active' );

        ( itemsWrapper ).css( 'left', '0' );
    });
};

ListingsController.prototype.initDocumentUploader = function() {
    var self = this;

    self.addDocumentBtn.click(function ( e ) {
        e.preventDefault();
        var newWidget = self.documentsDataPrototype.replace( /__name__/g, self.documentCount );
        alert(newWidget);
        var docDeleteBtn = '<div class="doc-delete-block"><span class="doc-delete">X</span></div>';
        var newLi = $( '<li class="doc-property-item"></li>' ).html( newWidget ).append( docDeleteBtn );

        self.documentFieldsList.append(newLi);
        self.documentCount++;

    });

    $( '#add-another-document' ).on( 'click', function () {
        $( 'input[type=file]:last-child' ).customFile();
    });
};

ListingsController.prototype.removeLoadedDocument = function() {
    var self = this;

    $( document ).on( 'touchstart click', '.doc-delete', function( event ) {
        var docCntId = $( this ).attr( 'data-doc-attr-count' ),
            docId = self.docFieldsId + docCntId;

        $( event.currentTarget ).closest('.doc-property-item').remove();
        $( '#'+ docId ).remove();
    });
};

ListingsController.prototype.resizeDropdown = function() {
    $( '.dropdown' ).on( 'click', function () {
        $( '.select2-results' ).css( 'width', '100%' );
    });
};

ListingsController.prototype.validateDocuments = function() {
    var self = this;

    $(document).on('change', '._file', function() {
        var file = this.files[0],
            allowedMimeTypes = $(this).data('mime-types').split(';'),
            allowedSize = $(this).data('max-file-size'),
            $error = $('.errors.documentation-errors');

        if (file.size > allowedSize) {
            $error.html( $(this).data('max-file-size-message') );
            self.documentFieldsList.find('li:last').remove();
        } else if ($.inArray(file.type, allowedMimeTypes) == -1) {
            $error.html( $(this).data('mime-types-message') );
            self.documentFieldsList.find('li:last').remove();
        } else {
            $error.html( '' );
        }
    });
};

ListingsController.prototype.run = function() {
    this.resizeDropdown();
    if ( window.File && window.FileReader && window.FileList && window.Blob ) {
        this.initImageUploader();
        this.initDocumentUploader();
        this.removeLoadedDocument();
        this.validateDocuments();


        var $submit = $('button[type=submit]'),
            $importFile = $('._import_file'),
            $error = $('.errors'),
            maxFileSize = $importFile.data('max-file-size');

        $importFile.fileValidator({
            onValidation: function(files) {
                $submit.removeAttr('disabled');
                $error.html('');
            },
            onInvalid:    function(validationType, file) {
                $submit.attr('disabled', 'disabled');
                switch (validationType) {
                    case 'maxSize':
                        $error.html('The file is too large. Allowed maximum size is ' + (maxFileSize / 1024) + ' Kb');
                        break;
                    default:
                        $error.html('Unknown error');
                        break;
                }
            },
            maxSize: maxFileSize
        });
    } else {
        alert( 'The File APIs are not fully supported in this browser.' );
    }
};

$(function () {
    var controller = new ListingsController();
    controller.run();
});
