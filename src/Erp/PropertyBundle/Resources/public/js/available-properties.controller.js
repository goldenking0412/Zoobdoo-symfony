var AvailablePropertiesController = function () {
    this.selectControl = $('.select-control');
    this.selectArrow = $('.select2-selection__arrow');
    this.filterForm = $('form#property-search-form');
};

AvailablePropertiesController.prototype.initSelect = function () {
    var self = this, select2Opts;

    if (self.selectControl.length > 0) {
        select2Opts = {
            matcher: function (params, data) {
                // If there are no search terms, return all of the data
                if ($.trim(params.term) === '') {
                    return data;
                }

                // Skip if there is no 'children' property
                if (typeof data.children === 'undefined') {
                    return null;
                }

                // `data.children` contains the actual options that we are matching against
                var filteredChildren = [];
                $.each(data.children, function (idx, child) {
                    if (child.text.toUpperCase().indexOf(params.term.toUpperCase()) === 0) {
                        filteredChildren.push(child);
                    }
                });

                // If we matched any of the timezone group's children, then set the matched children on the group
                // and return the group object
                if (filteredChildren.length) {
                    var modifiedData = $.extend({}, data, true);
                    modifiedData.children = filteredChildren;

                    // You can return modified objects from here
                    // This includes matching the `children` how you want in nested data sets
                    return modifiedData;
                }

                // Return `null` if the term should not be displayed
                return null;
            },
            // minimumInputLength: 3, // only start searching when the user has input 3 or more characters
            maximumInputLength: 20, // only allow terms up to 20 characters long
            minimumResultsForSearch: 20 // at least 20 results must be displayed
        };

        self.selectControl.select2(select2Opts);
        self.selectArrow.hide();
        $(window).resize(function () {
            self.selectControl.select2(select2Opts);
            self.selectArrow.hide();
        }.bind(this));

        self.selectControl.on('select2:opening', function () {
            self.selectControl.on('select2:open', function () {
                if (this.classList.contains('select2-show-search-box')) {
                    $('.select2-dropdown .select2-search--dropdown').css('display', 'block');
                }
            });
        });
    }
};

AvailablePropertiesController.prototype.run = function () {
    this.initSelect();
};

$(function () {
    var controller = new AvailablePropertiesController();
    controller.run();
});
