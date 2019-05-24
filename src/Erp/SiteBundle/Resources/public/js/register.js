var PageController = function () {
    this.dropdownItem = $('.dropdown-menu li');
};

PageController.prototype.dropdown = function () {
    this.dropdownItem.on('click', function () {
        var selText = $(this).text();
        $(this).parents('.dropdown').find('.dropdown-toggle').html(selText + '<span class="fa fa-chevron-down"></span>');
    });
};

PageController.prototype.terms = function () {
    $('.checkbox .terms-link').on('click', function () {
        $('.terms-text').toggle('slow', 'swing');
    });

    $(document).ready(function () {
        $('.checkbox input').prop('checked', false);
    });

    $(document).on('change touchstart', '.checkbox input', function (event) {
        if ($('.checkbox input').prop('checked')) {
            $('#erp_users_manager_form_registration_save').prop("disabled", false);
        } else {
            $('#erp_users_manager_form_registration_save').prop("disabled", true);
        }
    });
};

PageController.prototype.customUpload = function () {
    var multipleSupport = typeof $('<input/>')[0].multiple !== 'undefined',
            isIE = /msie/i.test(navigator.userAgent),
            inputCustom = $('input[type=file]');

    $.fn.customFile = function () {
        return this.each(function () {
            var $file = $(this).addClass('custom-file-upload-hidden'),
                    $wrap = $('<div class="file-upload-wrapper">'),
                    $button = $('<button type="button" class="btn-solid btn-solid-noborder file-upload-button">Select File</button>'),
                    $input = $('<textarea class="file-upload-input" disabled="disabled"></textarea>'),
                    $label = $('<label class="btn-solid btn-solid-noborder file-upload-button" for="' + $file[0].id + '">Select File</label>');

            $file.css({
                position: 'absolute',
                left: '-9999px'
            });

            $wrap.insertAfter($file)
                    .append($file, (isIE ? $label : $button), $input);
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

                $input.val(filename).attr('title', filename)
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

PageController.prototype.run = function () {
    // this.customUpload();
    this.dropdown();
    this.terms();

    var $submit = $('button[type=submit]'),
            $image = $('._image');

    $image.each(function ($item) {
        $item.fileValidator({
            onValidation: function (files) {
                $item.closest('.profile-picture-upload').find('.errors').html('');
            },
            onInvalid: function (validationType, file) {
                $item.val('');
                var $error = $item.closest('.profile-picture-upload').find('.errors');
                switch (validationType) {
                    case 'type':
                        $error.html($item.data('mime-types-message'));
                        break;
                    case 'maxSize':
                        $error.html($item.data('max-file-size-message'));
                        break;
                    default:
                        $error.html('Unknown error');
                        break;
                }
            },
            maxSize: $item.data('max-file-size'),
            type: 'image'
        });
    });
};

$(function () {
    var controller = new PageController();
    controller.run();
});
