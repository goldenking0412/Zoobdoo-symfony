var PropertyDetailsController = function() {
    this.slider = $( '.carousel' );
    this.accordionHead = $( '.panel-heading' );
    this.accordionMinus = $( '.accordion-minus' );
    this.accordionPlus = $( '.accordion-plus' );
    this.slidesNum = $( '.slides-num' );
    this.slidesNumCurrent = $( '.current-slide-num' );
    this.slidesItem = $( '.article-slide .item' );
    this.slideCurrent = $( '.carousel-indicators li.active') ;
    this.slidesNumber = this.slidesItem.length;
    this.currentSlide = parseInt( $( '.carousel-indicators li.active' ).attr( 'data-slide-to' ) ) + 1;
    this.itemsWrapper = $( '.carousel-indicators' );
    this.itemsWrapperWidth = ( this.slideCurrent.width() + parseInt( this.slideCurrent.css( 'margin-right' ) ) + parseInt( this.slideCurrent.css( 'margin-left' ) ) ) * this.slidesNumber;
};

PropertyDetailsController.prototype.newSlider = function() {
    this.slider.carousel({
        interval: false,
        cycle: false
    });

    this.itemsWrapper.width(this.itemsWrapperWidth);

    this.slider.swiperight(function() {  
        $( this ).carousel( 'prev' );  
    });  
    this.slider.swipeleft(function() {
        $( this ).carousel( 'next' );  
    });

    this.slider.on( 'slid.bs.carousel', function ( event ) {        
        var checkActiveInterval = setInterval(function() {
            if( $( '.carousel-indicators li.active' ).length) {
                var itemsWrapper = $( '.carousel-indicators' ),
                    currentCarousel = $( event.currentTarget ),
                    slideCurrent = $( '.carousel-indicators li.active' ),
                    slidesNumber = $( '.article-slide .item' ).length,
                    currentSlide = parseInt( slideCurrent.attr( 'data-slide-to' ) ) + 1,
                    itemsWrapperWidth = currentCarousel.find( ( slideCurrent ).width() + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-right' ) ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-left' ))) * currentCarousel.find( slidesNumber ) + 1;

                clearInterval(checkActiveInterval);
                $( '.current-slide-num' ).text( parseInt( slideCurrent.attr( 'data-slide-to' ) ) + 1 );
                if ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' )) !== 0 ) {
                    if ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' ) ) !== currentCarousel.find( slidesNumber ) - 1) {
                        currentCarousel.find( itemsWrapper ).css( 'left', - ( ( currentCarousel.find( slideCurrent ).width() ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-right' ) ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-left' ) ) ) * ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' ) ) - 1) );
                    }              
                } else {
                    currentCarousel.find( itemsWrapper ).css( 'left', - ( ( currentCarousel.find( slideCurrent ).width() ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-right' ) ) + parseInt( currentCarousel.find( slideCurrent ).css( 'margin-left' ) ) ) * ( parseInt( currentCarousel.find( slideCurrent ).attr( 'data-slide-to' ) ) ) );
                }
            }
        }, 100);        
    })

    if (this.slidesNumber !== 0) {
        this.slidesNum.text( this.slidesNumber );
        this.slidesNumCurrent.text( this.currentSlide );
    } else {
        this.slidesNum.text( '0' );
    }
};

PropertyDetailsController.prototype.propertyAccordion = function() {
    var that = this;

    this.accordionHead.on( 'click', function (e) {
        that.accordionPlus.show();
        that.accordionMinus.hide();
        if ( $( e.currentTarget ).parent( '.accordion-top' ).hasClass( 'collapsed' ) ) {
            $( this ).find( that.accordionMinus ).show();
            $( this ).find( that.accordionPlus ).hide();
        } else {
            $( this ).find( that.accordionPlus ).show();
            $( this ).find( that.accordionMinus ).hide();
        }
    });
};

PropertyDetailsController.prototype.modalWidth = function() {
    var modal = $( '.modal' );

    modal.on( 'show.bs.modal', function () {
        $( this ).find( '.modal-dialog' ).css({
            width: '900px'
        });
    });
};

PropertyDetailsController.prototype.run = function () {
    this.newSlider();
    this.propertyAccordion();
    this.modalWidth();
};

$( function () {
    var controller = new PropertyDetailsController();
    controller.run();
});

