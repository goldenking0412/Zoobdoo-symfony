var ConstructorController = function() {
};

ConstructorController.prototype.editSection = function() {

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

        $.post( updateUrl, {'name':name}, function( response ) {
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

        currentSection.find( '.section-footer-input, .btn-footer' ).hide();
    });
};

ConstructorController.prototype.changeSection = function () {
    var addSection = $( '.add-section' ),
        footerBtn = $( '.btn-footer' );

    addSection.on( 'click', function ( event ) {
        event.preventDefault();

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

ConstructorController.prototype.contractEdit = function () {

    $( document ).on( 'click', '.edit-block-btn', function ( event ) {
        var saveSection = $( '.save-block-btn' ),
            currentSection = $( event.currentTarget ).parents( '.section-block' ),
            currentSectionData = currentSection.attr('data-attribute');

        event.preventDefault();

        var contractEditorSelector = '.section-block[data-attribute="' + currentSectionData + '"] .contract-edit-form';
        tinymce.init({
            setup: function ( editor ) {
                    editor.on('init',function(e) {
                        setModalEvents(editor);
                    });
            },
            selector: contractEditorSelector,
            plugins: ['autoresize', 'table', 'contextmenu', 'paste','code'],
            toolbar: 'bold italic underline ' +
            '| alignleft aligncenter alignright alignjustify ' +
            '| inserttable tableprops deletetable ' +
            '| cell row column ' +
            '| bullist numlist' + '| undo redo',
            statusbar: false,
            table_advtab: false,
            content_css : '/assets/styles/style.min.css',
            body_class: 'editor',
            menubar: false,
            table_cell_advtab: false,
            table_row_advtab: false,
            visual: false,
        });

        function setModalEvents ( editor ) {
            editor.windowManager.oldOpen = editor.windowManager.open;
            editor.windowManager.open = function( t,r ) {

                var modal = this.oldOpen.apply( this, [t,r] );
                setTimeout( function () { 
                    $( '.mce-floatpanel.mce-in' ).addClass( 'mce-big-dialog' );
                    $( '.mce-big-dialog, .mce-big-dialog .mce-container, .mce-big-dialog .mce-container-body' ).css( 'width', '450px' );
                    $( '.mce-widget.mce-listbox' ).css( 'width', '120px' );
                    $( '.mce-label' ).css( 'left', '26px' );
                }, 10);
            };
        }

        $(this).hide();
        currentSection.find( saveSection).show();
        currentSection.find( '.section-content').removeClass( 'editor' );
    });

    $( document ).on( 'click', '.save-block-btn', function ( event ) {
        var $this = $( this),
            currentSection = $( event.currentTarget ).parents( '.section-block' ),
            updateUrl = $this.attr( 'data-href' ),
            content = tinyMCE.activeEditor.getContent(),
            contractEditorSelectorId = currentSection.find('.contract-edit-form').attr('id');

        event.preventDefault();

        $.post( updateUrl, {'content':content}, function( response ) {
            if (response.status) {
                tinyMCE.execCommand("mceRemoveEditor", true, contractEditorSelectorId);
                currentSection.find( '.contract-complete-form').show();
                $this.hide();
                currentSection.find( '.edit-block-btn' ).show();
                currentSection.find( '.section-content').addClass( 'editor' );
            }
        });
    });

    $( document ).on( 'change', 'input[name=isPublished]', function ( event ) {
        var $this = $( this),
            updateUrl = $this.attr( 'data-href'),
            isPublished = $this.is(':checked') ? 1 : 0;

        event.preventDefault();

        $.post( updateUrl, {'isPublished':isPublished}, function( response ) {});
    });
};

ConstructorController.prototype.run = function() {
    this.editSection();
    this.changeSection();
    this.contractEdit();
};

$( function () {
    var controller = new ConstructorController();
    controller.run();
});