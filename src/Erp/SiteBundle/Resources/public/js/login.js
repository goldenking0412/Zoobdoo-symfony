$(document).ready(function () {
    $(document).find('#forgot-password-link').on('click', function (event) {
        event.preventDefault();
        var $this = $(this), url = this.href,
                modalId = $this.data('target'),
                $modal = $(modalId),
                originalHtml = $modal.html()
                ;
        $modal
                .on('hide.bs.modal.prevent', function (e) {
                    e.preventDefault();
                })
                .on('hidden.bs.modal', function (e) {
                    $modal.html(originalHtml);
                })
                ;
        $modal.find('.modal-dialog .loginmodal-container').html('<div class="loader"></div>');
        $.ajax({
            type: 'GET',
            cache: false,
            url: url,
            async: true,
            dataType: 'html',
            success: function (response) {
                $modal.html($(response).html());
                $modal.off('hide.bs.modal.prevent');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText);
            }
        });
    });

    $('#form-login').on('submit', function (event) {
        event.preventDefault();
        var _self = this, $_self = $(_self);
        $_self.find('input[type="submit"]').attr('disabled', 'disabled');
        $.ajax({
            type: 'POST',
            cache: false,
            url: _self.action,
            data: $_self.serialize(),
            async: true,
            dataType: 'json',
            success: function (response) {
                window.location.href = response.redirect;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var $alert = $('.alert.alert-dismissible.alert-danger'),
                        alertHtml = '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                        + jqXHR.responseText.replace(new RegExp('"', 'g'), '')
                        ;
                if ($alert.length) {
                    $alert.html(alertHtml);
                } else {
                    $alert = $('<div class="alert alert-dismissible alert-danger"></div>').html(alertHtml);
                    $_self.before($alert);
                }

                $_self.find('input[type="submit"]').removeAttr('disabled');
            }
        });
    });
});