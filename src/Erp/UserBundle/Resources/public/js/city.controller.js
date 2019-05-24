var CityController = function () {
};

CityController.prototype.init = function(options) {
    return this;
};

CityController.prototype.run = function() {
    this.ajax();
};

CityController.prototype.getUrlByRoute = function(route) {
    return $( 'input[ name=route__' + route + ' ]' ).val();
};

CityController.prototype.ajax = function() {

    var $this = this;

    $( document ).on( 'change', 'select[data-class="states"]', function() {
        var $el = $( this ),
            stateCode = $el.val(),
            route = $this.getUrlByRoute( 'erp_core_get_cities_by_state_code' ),
            citiesEl = $( 'select[data-class="cities"]' ),
            selectProperties = $( '#select2-erp_users_manager_form_registration_city-container, #select2-properties_cityId-container, #select2-erp_property_edit_form_city-container' ),
            selectPropertiesContainer = $( '#select2-erp_users_tenant_contact_info_city-container, #select2-erp_users_form_address_details_city-container' );

        citiesEl.empty();

        citiesEl.attr( 'disabled', 'disabled' );
        selectProperties.addClass( 'hide' );
        selectPropertiesContainer.addClass( 'hide' );

        if ( stateCode ) {
            $.ajax({
                url: route + '/' + stateCode,
                type: 'GET',
                dataType: 'json',
                success: function ( response ) {
                    citiesEl.append( '<option value=""></option>' );
                    $.each(response, function ( key, city ) {
                        citiesEl.append( '<option value="' + city.id + '" data-postal-code="">' + city.name + '</option>' );
                    });
                    citiesEl.removeAttr( 'disabled' );
                }
            });
        }
    });

    $( document ).on( 'change', 'select[data-class="cities"]', function() {
        var selectContainers =  $( '#select2-erp_users_manager_form_registration_city-container, #select2-properties_cityId-container' ),
            citySelectContainers = $( '#select2-erp_users_tenant_contact_info_city-container, #select2-erp_users_form_address_details_city-container' ),
            editSelectContainer = $( '#select2-erp_property_edit_form_city-container' );

        selectContainers.removeClass( 'hide' );
        citySelectContainers.removeClass( 'hide' );
        editSelectContainer.removeClass( 'hide' );
    });
};

$(function() {
    var controller = new CityController();
    controller.run();
});
