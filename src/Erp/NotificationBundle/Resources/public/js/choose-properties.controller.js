(function ($) {
    var checkboxes = $('form[name="erp_notification_user_notification"] tbody input[name="idx[]"]');
    var allElementsInput = $('form[name="erp_notification_user_notification"] input[name="all_elements"]');
    var button = $('form[name="erp_notification_user_notification"] #erp_notification_user_notification_submit');

    allElementsInput.click(function (e) {
        checkboxes.prop('checked', $(this).prop('checked'));
        toggleButtonState();
    });

    function checkAllInputs() {
        if (checkboxes.filter(':checked').length === 0 ||
                checkboxes.filter(':checked').length !== checkboxes.length) {
            allElementsInput.removeAttr('checked');
        } else if (checkboxes.filter(':checked').length === checkboxes.length) {
            allElementsInput.prop('checked', 'checked');
        }
    }

    function toggleButtonState() {
        if (checkboxes.filter(':checked').length) {
            button.removeAttr('disabled');
        } else {
            button.attr('disabled', 'disabled');
        }
        checkAllInputs();
    }

    toggleButtonState();
    checkboxes.click(toggleButtonState);
})(jQuery);
