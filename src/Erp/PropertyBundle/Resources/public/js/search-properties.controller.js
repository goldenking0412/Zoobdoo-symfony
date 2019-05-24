var SearchPropertiesController = function () {
    this.filterForm = $( 'form#property-search-form' );
    this.selectControl = $( '.select-control' );
    this.selectArrow = $( '.select2-selection__arrow' );
    this.selectedFilter = $( '.close-selected-filter' );
    this.inputFilter = $( '.close-input-filter' );
    this.linkToPage = $( '.pagination .to-page' );
    this.propertiesPage = $( '#properties_page' );
};

SearchPropertiesController.prototype.initSelect = function() {
    var self = this;

    self.filterForm.find( 'select' ).change(function() {
        self.filterForm.submit();
    } );

    this.selectControl.select2();
    this.selectArrow.hide();
    $( window ).resize(function() {
        this.selectControl.select2();
        this.selectArrow.hide();
    }.bind( this ) );
};

SearchPropertiesController.prototype.closeFilters = function() {
    var self = this;

    self.selectedFilter.click( function () {
        var elId = $( this ).data( 'el-id' ),
            selEl = '#' + elId,
            selectControl = $( '.select-control' );
            
        $( selEl + ' option:selected' ).removeAttr( 'selected' );
        $( selEl + ' option:first' ).attr( 'selected','selected' );
        selectControl.select2();
        $( this ).parent().hide();

        $( selEl ).change();
    });

    self.inputFilter.click( function () {
        var elId = $( this ).data( 'el-id' ),
            inpEl = '#' + elId;
        $( inpEl ).val('');

        self.filterForm.submit();
    });
};

SearchPropertiesController.prototype.initPaging = function() {
    var self = this;
    var isPageClick = false;

    self.linkToPage.click( function () {
        var pageNum = $( this ).data( 'page-num' );
        isPageClick = true;
        self.propertiesPage.val( pageNum );
        self.filterForm.submit();
    });

    $('form[name=properties]').submit(function(){
        if (!isPageClick) {
            self.propertiesPage.val(1)
        }
    });
};

SearchPropertiesController.prototype.run = function() {
    this.initSelect();
    this.closeFilters();
    this.initPaging();
};

$( function () {
    var controller = new SearchPropertiesController();
    controller.run();
});
