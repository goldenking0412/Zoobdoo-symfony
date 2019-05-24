$(document).ready(function () {
    var templateIdInputAddress = '#erp_property_edit_form_address',
            templateIdInputZip = '#erp_property_edit_form_zip',
            templateIdSelect2City = '#select2-erp_property_edit_form_city_{property-id}-container'
            ;

    $(document).on('change', 'select[data-class="states"]', function () {
        var $el = $(this), $form = $el.closest('form'),
                dataId = ($form.data('id')) ? ('_' + $form.data('id')) : ''
        ;

        var $inputAddress = $(templateIdInputAddress + dataId),
                $inputZip = $(templateIdInputZip + dataId),
                $select2City = $(templateIdSelect2City.replace('_{property-id}', dataId)),
                stateCode = $el.val(),
                route = baseRoute.replace('{stateCode}', stateCode),
                $citiesEl = $('select[data-class="cities"]')
        ;

        $citiesEl.empty();
        $citiesEl.attr('disabled', 'disabled');
        $select2City.addClass('hide');
        $inputAddress.val('');
        $inputZip.val('');
        if (stateCode) {
            $.ajax({
                url: route,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    $citiesEl.append('<option value=""></option>');
                    $.each(response, function (key, city) {
                        $citiesEl.append('<option value="' + city.id + '" data-postal-code="">' + city.name + '</option>');
                    });
                    $citiesEl.removeAttr('disabled');
                }
            });
        }
    });
    
    $(document).on('change', 'select[data-class="cities"]', function () {
        var $el = $(this), $form = $el.closest('form'),
                dataId = ($form.data('id')) ? ('_' + $form.data('id')) : ''
        ;

        var $inputAddress = $(templateIdInputAddress + dataId),
                $inputZip = $(templateIdInputZip + dataId),
                $select2City = $(templateIdSelect2City.replace('_{property-id}', dataId))
        ;

        $inputAddress.val('');
        $inputZip.val('');
        $select2City.removeClass('hide');
    });


    var prevAmountInputValue = '';
    var paymentAmount = $('.paymentAmount');
    if (paymentAmount.length > 0) {
        formatPaymentAmount(paymentAmount[0]);
    }
    $(document).on('input', '.paymentAmount', function () {
        formatPaymentAmount(this);
    });
    
    function formatPaymentAmount(paymentAmountField) {
        var amount = paymentAmountField.value;

        if (amount.length > 0) {
            amount = amount.replace('$', '');
            amount = amount.replace('.', '');

            if ($.isNumeric(amount)) {
                prevAmountInputValue = amount;
            } else {
                amount = prevAmountInputValue;
            }

            var amountInt = parseInt(amount);
            if (0 === amountInt) {
                paymentAmountField.value = '$0.00';
            } else if (0 < amountInt && amountInt < 10) {
                paymentAmountField.value = '$' + amountInt/100;
            } else if (9 < amountInt && amountInt < 100 && 0 === (amountInt % 10)) {
                paymentAmountField.value = '$' + amountInt/100 + '0';
            } else if (9 < amountInt && amountInt < 100 && 0 !== (amountInt % 10)) {
                paymentAmountField.value = '$' + amountInt/100;
            } else {
                paymentAmountField.value = '$' + amountInt.toString().substring(0, amount.length - 2) + '.' + amountInt.toString().substring(amount.length - 2);
            }
        }
    }

    $(document).on('click', '.property-details-submit', function (e) {
        e.preventDefault();

        var form = $(this).closest('form');

        if (paymentAmount.length > 0) {
            var amount = paymentAmount[0].value;
            amount = amount.replace('$', '');
            paymentAmount[0].value = amount;
        }

        form.submit();

        if (paymentAmount.length > 0) {
            paymentAmount[0].value = '$' + amount;
        }
    });
});