var RemittanceController = function () {
    this.selectControlClass = '.select-control';
    this.selectArrowClass = '.select2-selection__arrow';
    this.$container = $('#register-form-remittances');
    this.selectControl = $(this.selectControlClass);
    this.selectArrow = $(this.selectArrowClass);
    this.fileStatus = null;
    this.$form = $('#form-remittance');
};

/**
 * 
 * @returns {undefined}
 */
RemittanceController.prototype.initSelect = function () {
    this.selectControl.select2();
    this.selectArrow.hide();
    $(window).resize(function () {
        this.selectControl.select2();
        this.selectArrow.hide();
    }.bind(this));
};

/**
 * 
 * @returns {undefined}
 */
RemittanceController.prototype.customUpload = function (inputCustom) {
    var multipleSupport = typeof $('<input/>')[0].multiple !== 'undefined',
            isIE = /msie/i.test(navigator.userAgent);

    var that = this;
    $.fn.customFile = function () {
        return this.each(function () {
            var $file = $(this).addClass('custom-file-upload-hidden'),
                    $wrap = $('<div class="file-upload-wrapper">'),
                    $button = $('<button type="button" class="btn-solid btn-solid-noborder file-upload-button">Select File</button>'),
                    $input = that.$container.find('.upload-input'),
                    $label = $('<label class="btn-solid btn-solid-noborder file-upload-button" for="' + $file[0].id + '">Select File</label>');

            $file.css({
                position: 'absolute',
                left: '-9999px'
            });

            $wrap.insertAfter($file)
                    .append($file, (isIE ? $label : $button));
            $file.attr('tabIndex', -1);
            $button.attr('tabIndex', -1);
            $button.click(function () {
                $file.focus().click();
            });

            $file.change(function () {
                var files = [],
                        fileArr, filename;

                if (multipleSupport) {
                    fileArr = $file[0].files;
                    for (var i = 0, len = fileArr.length; i < len; i++) {
                        files.push(fileArr[i].name);
                    }
                    filename = files.join(', ');
                } else {
                    filename = $file.val().split('\\').pop();
                }

                $input.val(filename).attr('title', filename);
            });

            $input.on({
                blur: function () {
                    $file.trigger('blur');
                },
                keydown: function (e) {
                    if (e.which === 13) {
                        if (!isIE) {
                            $file.trigger('click');
                        }
                    } else if (e.which === 8 || e.which === 46) {
                        $file.replaceWith($file = $file.clone(true));
                        $file.trigger('change');
                        $input.val('');
                    } else if (e.which === 9) {
                        return this;
                    } else {
                        return false;
                    }
                }
            });
        });
    };

    inputCustom.customFile();
};

/**
 * 
 * @returns {undefined}
 */
RemittanceController.prototype.run = function () {
    var $submit = this.$container.find('button[type=submit]'),
            $file = this.$container.find('._file'),
            $error = this.$container.find('#form-remittance-document-errors'),
            maxFileSize = $file.data('max-file-size')
            ;

    this.customUpload($file);

    $file.fileValidator({
        onValidation: function (files) {
            $submit.removeAttr('disabled');
            $error.html('');
        },
        onInvalid: function (validationType, file) {
            $submit.attr('disabled', 'disabled');
            $error.html($file.data('max-file-size-message'));
        },
        maxSize: maxFileSize
    });
    
    var _self = this;
    this.$form.on('submit', function (event) {
        event.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', 'disabled');
        $.ajax({
            type: this.method,
            url: this.action,
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'html',
            success: function (response) {
                _self.$container.html(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var response;
                try {
                    response = JSON.parse(jqXHR.responseText);
                } catch (err) {
                    response = jqXHR.responseText;
                }
                console.log(response);
            }
        });
    });
    
    // this.initSelect();
};

$(function () {
    var controller = new RemittanceController();
    controller.run();
});