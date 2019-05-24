(function ($) {
    $('#add-alert').click(function (e) {
        e.preventDefault();

        var parentEl = $('#alerts-block');
        var counter = $(this).data('widget-counter');
        var newWidget = $(this).data('prototype');

        newWidget = newWidget.replace(/__name__/g, counter);
        counter++;

        $(this).data('widget-counter', counter);
        $(newWidget).appendTo(parentEl);
    });
})(jQuery);