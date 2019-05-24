var PropertyEditController = function () {
    this.newImageId = 'new-image-';
    this.carouselId = $( '#carousel-id' ).val();
    this.addImageBtn = $( '#add-another-image' );
    this.imageCount = $( '#image-count' ).val();
    this.imageBlockPrefix = $( '#image-block-prefix' ).val();
    this.newSmallImagesBlock = $( '.preview-images' );
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

PropertyEditController.prototype.initImageUploader = function() {
    var self = this;

    self.addImageBtn.click(function (e) {
        e.preventDefault();

        var newWidget = self.imageDataPrototype.replace( /__name__/g, self.imageCount );
        newWidget = newWidget.replace( /__is_new__/g, 'yes' );
        var newLi = $( '<li id="' + self.imageFieldsId + self.imageCount + '"></li>' ).html( newWidget );
        newLi.appendTo( self.imagesFieldsList );

        self.imageCount++;

        self.renderImage();
    });
    self.removeLoadedImage();
};

PropertyEditController.prototype.inArray = function(val) {
    for(var i = 0, l = this.length; i < l; i++) {
        if(this[i] === val) {
            return true;
        }
    }
    return false;
};

PropertyEditController.prototype.renderImage = function() {
    var self = this,
        imageCount = self.imageCount - 1,
        fileBtn = $( 'div#' + self.imageBlockPrefix + '_' + imageCount ).find('input[ type="file" ]' ),
        itemsWrapper = this.newSmallImagesBlock,
        slideCurrent = this.newSmallImagesBlock.find('li.active'),
        slidesNumber = $( '.article-slide .item' ).length,
        bindFlag = true;
        error = $('.errors');

    fileBtn.bind( 'change keyup input', function() {
       
        if ( bindFlag ) {
            bindFlag = false;
            var reader = new FileReader(),
                file = this.files[0],
                allowedMimeTypes = fileBtn.data('mime-types').split(';'),
                allowedSize = fileBtn.data('max-file-size'),
                maxSizeMessage = fileBtn.data('max-file-size-message'),
                mimeTypesMessage = fileBtn.data('mime-types-message');

            error.html('');

            if ( file ) {
                if ( file.size > allowedSize ) {
                    error.html( maxSizeMessage );
                    self.imagesFieldsList.find('li:last').remove();
                } else if (!file.size) {
                    self.imagesFieldsList.find('li:last').remove();
                } else if ($.inArray(file.type, allowedMimeTypes) === -1) {
                    error.html(mimeTypesMessage);
                    self.imagesFieldsList.find('li:last').remove();
                } else {
                    reader.onload = function (e) {
                        var imgId = self.newImageId + imageCount,
                            bigBlockClass = imageCount > 1 ? '' : 'active',
                            smallLiClass = imageCount > 1 ? '' : 'active',
                            img = '<img id="' + imgId + '" src="' + e.target.result + '" class="" />';

                        var newBigImg = '<div class="item ' + bigBlockClass + '" data-target="#article-photo-carousel">' + img + '</div>',
                            newSmallImg = '<li class="' + smallLiClass + '" data-slide-to="' + ( imageCount ) + '" data-target="#' + self.carouselId + '">' +
                                '<a href="#" class="image-delete" data-image-attr-count="' + imageCount + '">&times;</a>' + img + '</li>',
                            firstImg = $('.first-screen-img');

                        firstImg.remove();
                        $('.cont-slider .item').removeClass('active');
                        self.newSmallImagesBlock.find('li').removeClass('active');
                        self.newBigImagesBlock.append(newBigImg);
                        self.newSmallImagesBlock.append(newSmallImg);

                        $('.cont-slider .item:last-child').addClass('active');
                        self.newSmallImagesBlock.find('li:last-child').addClass('active');
                        self.newBigImagesBlock.find('img').css({'width': 325, 'height': 300});
                        self.newSmallImagesBlock.find('img').css({'width': 100, 'height': 100});

                        // itemsWrapper.width(( slideCurrent.width() + parseInt(slideCurrent.css('margin-right')) + parseInt(slideCurrent.css('margin-left')) ) * ( slidesNumber + 1 ));
                        // ( itemsWrapper ).css('left', -(slideCurrent.width() + parseInt(slideCurrent.css('margin-right')) + parseInt(slideCurrent.css('margin-left')) ) * ( slidesNumber - 1 ));
                        
                        self.newSmallImagesBlock.find('li').each(function( index ) {
                            $( this ).attr( 'data-slide-number', index );
                        });
                    };

                    // read the image file as a data URL.
                    reader.readAsDataURL(this.files[0]);

                    // itemsWrapper.width(( slideCurrent.width() + parseInt(slideCurrent.css('margin-right')) + parseInt(slideCurrent.css('margin-left')) ) * ( slidesNumber + 1 ));
                    // ( itemsWrapper ).css('left', -(slideCurrent.width() + parseInt(slideCurrent.css('margin-right')) + parseInt(slideCurrent.css('margin-left')) ) * ( slidesNumber - 1 ));
                }
            } 
        } else {
            bindFlag = true;
        }
        
    });
    fileBtn.click();
};

PropertyEditController.prototype.removeLoadedImage = function() {
    var self = this,
        itemsWrapper = this.newSmallImagesBlock,
        slider = $( '.carousel' ),
        slideCurrent = this.newSmallImagesBlock.find('li.active'),        
        slidesNumber = $( '.article-slide .item' ).length,
        carouselItem = this.newSmallImagesBlock.find('li'),
        slidesBig = $( '.article-slide .item' );
        
    $( document ).on( 'click', '.image-delete', function ( event ) {
        event.preventDefault();
        event.stopPropagation();
        var imgCntId = $(this).attr( 'data-image-attr-count' ),
            imgNumberInList = $(this).parent().attr( 'data-slide-number' ),
            slider = $( '.carousel' ),
            imgId = self.newImageId + imgCntId;

        $( this ).parent().remove();

        self.newBigImagesBlock.find( '#' + imgId ).parent().remove();
        self.imagesFieldsList.find( '#' + self.imageFieldsId + imgNumberInList ).remove();

        var slidesImages = $( '.article-slide .item img' );

        if ( !self.newBigImagesBlock.children().length ) {
            self.newBigImagesBlock.html( '<div class="item active first-screen-img"><img alt="" title="" src="https://placehold.it/325x300"></div>' );
            self.imageCount = 1;
        }

        carouselItem.each(function( index ) {
            var dataSlideTo = $(this).attr( 'data-slide-to' );
            if (dataSlideTo > imgCntId ) {                
                $(this).attr( 'data-slide-to', ($(this).attr( 'data-slide-to' )-1) );
                $(this).find('a').attr( 'data-image-attr-count', ($(this).find('a').attr( 'data-image-attr-count' )-1) );
                $(this).find('img').attr( 'id', 'new-image-'+$(this).find('a').attr( 'data-image-attr-count' ) );
            }
        });        

        self.newBigImagesBlock.children().removeClass( 'active' );
        self.newBigImagesBlock.children().first().addClass( 'active' );

        slidesImages.each(function( index ) {
            $(this).attr( 'id', 'new-image-'+index );
        });

        itemsWrapper.width(( slideCurrent.width() + parseInt(slideCurrent.css('margin-right')) + parseInt(slideCurrent.css('margin-left')) ) * ( slidesNumber + 1));
        ( itemsWrapper ).css( 'left', '0px' );

        self.imageCount--;
        return false;
    }); 
};

PropertyEditController.prototype.newSlider = function() {
    this.slider = $( '.carousel' );

    this.slider.carousel({
        interval: false,
        cycle: false
    });

    var carouselThumbs = this.newSmallImagesBlock.find('li');

    carouselThumbs.each(function( index ) {
        $(this).attr( 'data-slide-number', index );
    });

    this.slider.on( 'slid.bs.carousel', function ( event ) {        
        var checkActiveInterval = setInterval(function() {
            if( this.newSmallImagesBlock.find('li.active').length) {
                var itemsWrapper = this.newSmallImagesBlock,
                    currentCarousel = $( event.currentTarget ),
                    slideCurrent = this.newSmallImagesBlock.find('li.active'),
                    slidesNumber = $( '.article-slide .item' ).length,
                    currentSlide = parseInt( slideCurrent.attr( 'data-slide-to' ) ) + 1,
                    itemsWrapperWidth = currentCarousel.find( ( slideCurrent ).width() + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-right' ) ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-left' ))) * currentCarousel.find( slidesNumber ) + 1;

                clearInterval(checkActiveInterval);
                if ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' )) !== 0 ) {
                    if ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' ) ) !== currentCarousel.find( slidesNumber ) - 1) {
                        currentCarousel.find( itemsWrapper ).css( 'left', - ( ( currentCarousel.find( slideCurrent ).width() ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-right' ) ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-left' ) ) ) * ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' ) ) - 1 ) );
                    }              
                } else {
                    currentCarousel.find( itemsWrapper ).css( 'left', - ( ( currentCarousel.find( slideCurrent ).width() ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-right' ) ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-left' ) ) ) * ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' ) ) ) );
                }
            }
        }, 100);        
    })
};

PropertyEditController.prototype.initDocumentUploader = function() {
    var self = this;

    self.addDocumentBtn.click(function ( e ) {
        e.preventDefault();
        var newWidget = self.documentsDataPrototype.replace( /__name__/g, self.documentCount );
        var docDeleteBtn = '<div class="doc-delete-block" style="float: left; font-size: 2em; color: #f00; cursor: pointer;"><span class="doc-delete" aria-hidden="true">&times;</span></div>';
        var newLi = $( '<li class="doc-property-item"></li>' ).html( newWidget ).append( docDeleteBtn );

        self.documentFieldsList.append(newLi);
        self.documentCount++;

    });

    $( '#add-another-document' ).on( 'click', function () {
        $( 'input[type=file]:last-child' ).customFile();
    });
};

PropertyEditController.prototype.removeLoadedDocument = function() {
    var self = this;

    $( document ).on( 'touchstart click', '.doc-delete', function( event ) {
        var docCntId = $( this ).attr( 'data-doc-attr-count' ),
            docId = self.docFieldsId + docCntId,
            $error = $('.errors');

        $( event.currentTarget ).closest('.doc-property-item').remove();
        $( '#'+ docId ).remove();

        $error.html('To save your changes please click Submit button');
    });
};

PropertyEditController.prototype.resizeDropdown = function() {
    $( '.dropdown' ).on( 'click', function () {
        $( '.select2-results' ).css( 'width', '100%' );
    });
};

PropertyEditController.prototype.validateDocuments = function() {
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

PropertyEditController.prototype.run = function() {
    var self = this;

    if ( window.File && window.FileReader && window.FileList && window.Blob ) {
        this.newSlider();
        this.initImageUploader();
        this.initDocumentUploader();
        this.removeLoadedDocument();
        this.validateDocuments();

        var $submit = $('button[type=submit]'),
            $importFile = $('._import_file'),
            $error = $('.errors'),
            maxFileSize = $importFile.data('max-file-size');

        $('form[name=erp_property_image_edit_form]').submit(function() {

            $inputs = self.imagesFieldsList.find('input[data-is-new=yes]');

            $inputs.each(function(indx, el) {
                if (!$(el).val()) {
                    $(el).parents('li').remove();
                }
            });
        });

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
    var controller = new PropertyEditController();
    controller.run();
});
