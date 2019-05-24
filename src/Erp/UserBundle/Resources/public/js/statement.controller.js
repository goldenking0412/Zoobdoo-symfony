var StatementController = function () {
    this.$form = $('#form-statement');
    this.$nodeError = $('#form-statement-error');
    this.$nodeErrorParent = this.$nodeError.parent();
    this.actHtmlError = this.$nodeError.html();
};

StatementController.prototype.showStatement = function () {
    if (this.$form) {
        var that = this;

        this.$form.on('submit', function (event) {
            event.preventDefault();
            var $form = $(this);

            var action = $form.attr('action'),
                    data = $form.serialize();

            if (
                    ($form.find('#form-statement-landlord').val() !== '')
                    && ($form.find('#form-statement-month').val() === '')
            ) {
                if ($form.find('#' + that.$nodeError.attr('id')).length === 0) {
                    that.$nodeErrorParent.append(that.$nodeError);
                    that.$nodeError = that.$nodeErrorParent.children().first();
                }

                that.$nodeError
                        .html(that.actHtmlError + ' Error: if you select a landlord, you must select a month')
                        .removeClass('hidden')
                        ;

                $form.find('button[type=submit]').removeAttr('disabled');
            } else {
                $.fancybox.showLoading();
                // $.fancybox.helpers.overlay.open();
                $.get(action, data, function (response) {
                    var data;
                    try {
                        data = JSON.parse(response);
                    } catch (err) {
                        data = response;
                    }
                    
                    $form.get(0).reset();
                    
                    $.fancybox.open(data, {
                        type: 'inline',
                        hideOnContentClick: false,
                        showCloseButton: false,
                        closeBtn: false,
                        autoSize: false,
                        closeClick: false,
                        width: 900,
                        height: 500,
                        helpers: {
                            overlay: {
                                css: {
                                    background: 'rgba(0, 0, 0, 0.65)',
                                    closeClick: false
                                }
                            }
                        },
                        afterLoad: function (current, previous) {
                            // handle custom close button in inline modal
                            if (current.href.indexOf('#') === 0) {
                                jQuery(current.href).find('a.close').off('click.fb').on('click.fb', function (e) {
                                    e.preventDefault();
                                    jQuery.fancybox.close();
                                });
                            }
                        },
                    });
                });
            }
        });
    }
};

StatementController.prototype.run = function () {
    this.showStatement();
};

$(function () {
    var controller = new StatementController();
    controller.run();
});