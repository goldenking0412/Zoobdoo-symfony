var ConstructorController = function() {
    this.fieldType = '';
    this.fieldName = '';
    this.fieldData = [];
};

ConstructorController.prototype.getUrlByRoute = function( route ) {
    return $( 'input[name=route__' + route + ']' ).val();
};

ConstructorController.prototype.editSection = function() {

    var self = this;

    function resetFormFields() {
        self.fieldType = '';
        self.fieldName = '';
        self.fieldData = [];
    };

    $( document ).on( 'change', '.select-field-type', function ( event ) {
        var currentSection = $( event.currentTarget ).parents( '.section-block' ),
            currentFieldType = this.options[this.selectedIndex].value;

        // set field type
        self.fieldType = currentFieldType;

        if ( currentFieldType !== 'radio' ) {
            currentSection.find( '.input-col, .btn-col, .edit-col' ).show();
            currentSection.find( '.option-col, .radio-btn-col' ).hide();
        } else {
            currentSection.find( '.input-col' ).hide();
            currentSection.find( '.option-col, .btn-col, .radio-btn-col, .edit-col' ).show();
        }
    });

    $( document ).on( 'click', '.add-option-btn', function ( event ) {
        var currentSection = $( event.currentTarget ).parents( '.section-block' );

        event.preventDefault();

        if ( currentSection.find( '.option-col' ).length <= 4 ) {
            currentSection.find( '.option-col:last' ).clone().appendTo( currentSection.find( '.dropdown-col' ) );
            currentSection.find( '.option-col:last input' ).val('');
            currentSection.find( '.option-col' ).show();
            if (  currentSection.find( '.option-counter' ).length >= 1 ) {
                currentSection.find( '.option-counter:last' ).html( parseInt( currentSection.find( '.option-counter:last' ).text() ) + 1 );
                currentSection.find( '.option-col .errors' ).hide();
            }
        } else {
            $( '.add-option-btn' ).hide();
        }
    });

    $( '.edit-section-btn' ).on( 'click', function ( event ) {
        var currentSection = $( event.currentTarget ).parents( '.section-block' );

        currentSection.find( '.section-title-input' ).show().val( currentSection.find( '.section-title' ).text() );
        currentSection.find( '.section-title' ).hide();
        currentSection.find( '.block-title .ok-btn, .block-title .cancel-btn' ).show();
    });

    $( document ).on( 'click', '.ok-btn.title-btn', function ( event ) {
        var currentSection = $( event.currentTarget ).parents( '.section-block' ),
            updateUrl = $( this ).attr( 'data-href' ),
            name = currentSection.find( 'input.section-title-input' ).val();

        event.preventDefault();

        currentSection.find( '.section-title' ).text(name);

        $.post( updateUrl, {'name':name}, function(response ) {
            if ( response.status ) {
                currentSection.find( '.section-title-input, .ok-btn.title-btn, .cancel-btn.title-btn' ).hide();
                currentSection.find( '.section-title' ).show();
            }
        });
    });

    $( document ).on( 'click', '.cancel-btn.title-btn', function ( event ) {
        var currentSection = $( event.currentTarget ).parents( '.section-block' );

        event.preventDefault();

        currentSection.find( '.section-title-input, .ok-btn.title-btn, .cancel-btn.title-btn' ).hide();
        currentSection.find( '.section-title' ).show();
    });

    $( document ).on( 'click', '.form-footer .cancel', function ( event ) {
        var  currentSection = $( event.currentTarget ).parents( '.section-block' );
        event.preventDefault();

        $('.additional-actions').show();
        currentSection.find( '.section-footer-input, .btn-footer' ).hide();
    });


    $( document ).on( 'click', '.constructor-btn', function ( event ) {
        var currentSection = $( event.currentTarget ).parents( '.section-block' ),
            fieldName = currentSection.find( 'input.field-title-input' ).val(),
            fieldLabel = currentSection.find( 'input.field-title-input' ),
            fieldData = currentSection.find( 'input.option-title-input' ),
            addFieldUrl = $( this ).data( 'href' ),
            errorBlock = $('.option-col .errors');

        event.preventDefault();

        if( $( this ).hasClass( 'ok-btn' ) ) {

            self.fieldData = [];

            if ( self.fieldType == 'radio') {
                // set radio data
                fieldData.each(function() {
                    radioValue = $( this ).val();
                    if ( radioValue.length  && fieldData.length >= 2 ) {
                        self.fieldData.push( radioValue );
                        $( this ).css( 'border', '1px solid #a8a4a4' );                        
                    } else if ( !radioValue.length  && fieldData.length >= 2 ) {
                        $( this ).css( 'border', '1px solid #ca171b' );
                        return false;
                    } else if ( !errorBlock.length && fieldData.length < 2 ){
                        $( '.option-col:first' ).append( '<span class="errors">You should add at least 2 options for radio button</span>' );
                        $( '.option-col:first .errors' ).show();
                    }
                });
            } else {
                // set field name
                self.fieldName = fieldName;
            }
            if ( ( self.fieldType == 'radio' && self.fieldData.length && radioValue.length ) || self.fieldName.length ) {
                $.post(addFieldUrl, {
                        'name': self.fieldName,
                        'type': self.fieldType,
                        'data': self.fieldData
                    },
                    function( response ) {
                        if ( response.status !== 'undefined' && response.status === 'ok' ) {
                            $( '.file-upload-button' ).remove();
                            currentSection.replaceWith( response.html );
                            $( '.upload-doc-col input[type=file]' ).customFile();
                            $( '#rental-form-constructor' ).erpPopup();
                        } else {
                            $( 'div.errors' ).css( { 'opacity': 1 } ).html( response.error ).animate( { 'opacity': 0} , 3000 );
                        }
                    }
                );
                
            } else {
                fieldLabel.css( 'border', '1px solid #ca171b' );
            }
            if ( fieldName.length ) {
                dropdownBtn = $( '.dropdown-menu li' ).parents( '.dropdown' ).find( '.btn' );
                dropdownBtn.html( '<span class="select-container"></span>' );
                currentSection.find( '.option-col' ).not( ':first' ).remove();
                currentSection.find( '.constructor-col, .edit-col, .option-col' ).hide();
                resetFormFields();
            }
        } else {
            dropdownBtn = $( '.dropdown-menu li' ).parents( '.dropdown' ).find( '.btn' );
            dropdownBtn.html( '<span class="select-container"></span>' );
            currentSection.find( '.option-col' ).not( ':first' ).remove();
            currentSection.find( '.constructor-col, .edit-col, .option-col' ).hide();
        }
    });

    $( document ).on( 'click touchstart', '.hide-field', function ( event ) {
        var currentGroup = $( event.currentTarget ).parents( '.form-fields' ),
            removeFileUrl = $( this ).data( 'href' );

        event.preventDefault();

        $.post( removeFileUrl, {},
            function( response ) {
                if ( response.status !== 'undefined' && response.status ) {
                    currentGroup.remove();
                } else {
                    $( 'div.errors' ).сss( { 'opacity': 1 } ).html( response.error ).animate( { 'opacity': 0 }, 3000 );
                }
            }
        );

    });
};

ConstructorController.prototype.changeSection = function () {
    var addSection = $( '.add-section' ),
        footerBtn = $( '.btn-footer' );

    addSection.on( 'click', function ( event ) {
        event.preventDefault();
        $( '.additional-actions').hide();
        $( '.section-footer-input, .btn-footer' ).css( 'display', 'inline-block' );
    });

    footerBtn.on( 'click', function ( event ) {
        $( '.section-footer-input, .btn-footer' ).hide();
    });

    $( document ).on( 'click', '.add-field-btn', function ( event ) {
        var currentSection = $( event.currentTarget ).parents( '.section-block' ),
            optionBtn = $( '.add-option-btn' );
            
        event.preventDefault();

        optionBtn.show();
        currentSection.find( '.constructor-col' ).css( 'display', 'inline-block' );
    });
};

ConstructorController.prototype.run = function() {
    this.editSection();
    this.changeSection();
};

$( function () {
    var controller = new ConstructorController();
    controller.run();
});
