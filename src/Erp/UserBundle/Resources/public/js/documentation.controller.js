var DocumentationController = function () {
    var fileStatus;
};

/**
 * 
 * @param {HTMLElement} $alert
 * @param {String} strClass
 * @returns {undefined}
 */
DocumentationController.prototype.showAlert = function ($alert, strClass) {
    $alert
            .removeClass('hidden alert-warning alert-success alert-danger')
            .addClass(strClass)
    ;
};

/**
 * 
 * @param {HTMLElement} $button
 * @returns {undefined}
 */
DocumentationController.prototype.disableButton = function ($button) {
    $button
            .addClass('disabled')
            .removeAttr('style')
            /*.siblings('.doc-delete').each(function (item) {
                $(item).addClass('disabled');
            })*/
    ;
};

/**
 * 
 * @param {String} route
 * @returns {String}
 */
DocumentationController.prototype.getUrlByRoute = function (route) {
    return $('input[name=route__' + route + ']').val();
};

/**
 * 
 * @param {Object} jsonData
 * @param {HTMLElement} $btn
 * @param {HTMLElement} $objErr
 * @param {HTMLElement} $objAlert
 * @param {String} btnDefaultValue
 * @returns {undefined}
 */
DocumentationController.prototype.helloSignEmbeddedSigning = function (jsonData, $btn, $objErr, $objAlert, btnDefaultValue) {
    var that = this;
    
    HelloSign.init(jsonData.CLIENT_ID);
    HelloSign.open({
        debug: jsonData.TEST_ENV,
        url: jsonData.SIGN_URL,
        uxVersion: 2,
        skipDomainVerification: jsonData.TEST_ENV,
        messageListener: function (eventData) {
            var text, alertClass, $btnsSignClass = $('.sign-btn');
            
            if (eventData.event === HelloSign.EVENT_SIGNED) {
                text = 'You successfully signed the document.';
                $.ajax({
                    url: $btn.data('doc'),
                    type: 'POST',
                    data: {},
                    dataType: 'json',
                    success: function (response) {
                        console.log(text);
                        
                        that.disableButton($btn);
                        that.disableButton($btn.siblings('.edit-btn'));
                        
                        alertClass = 'alert-success';
                        that.showAlert($objAlert, alertClass);
                        
                        $btn.closest('.doc-table-row').find('.status').children().each(function (item) {
                            var nextNode = this.nextSibling;
                            if (nextNode && nextNode.nodeType === 3) {
                                nextNode.nodeValue = response.status;
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(text + ' ' + textStatus);
                        
                        that.disableButton($btn);
                        that.disableButton($btn.siblings('.edit-btn'));
                        
                        alertClass = 'alert-warning';
                        that.showAlert($objAlert, alertClass);
                    }
                });
            } else if (eventData.event === HelloSign.EVENT_CANCELED) {
                text = 'You closed the document, please reopen to continue.';
                
                alertClass = 'alert-warning';
                that.showAlert($objAlert, alertClass);
                
                $btnsSignClass.removeClass('disabled');
                $btn.html(btnDefaultValue);
            } else if (eventData.event === HelloSign.EVENT_ERROR) {
                text = 'Uh oh something went wrong. Sorry about that!';
                
                alertClass = 'alert-error';
                that.showAlert($objAlert, alertClass);
                
                $btnsSignClass.removeClass('disabled');
                $btn.html(btnDefaultValue);
            } else if (eventData.event === HelloSign.EVENT_SENT) {
                // only used for embedded requesting
            }
            $objErr.html(text);
        }
    });
};

/**
 * 
 * @param {Object} jsonData
 * @param {HTMLElement} $btn
 * @param {String} btnDefaultValue
 * @returns {undefined}
 */
DocumentationController.prototype.helloSignEmbeddedTemplate = function (jsonData, $btn, btnDefaultValue) {
    var $btnsSignClass = $('.sign-btn'); // that = this;
    
    HelloSign.init(jsonData.CLIENT_ID);
    HelloSign.open({
        url: jsonData.TEMPLATE_URL,
        uxVersion: 2,
        skipDomainVerification: jsonData.TEST_ENV,
        messageListener: function (eventData) {
            var text;
            
            if (eventData.event === HelloSign.EVENT_TEMPLATE_CREATED) {
                // template created
                text = 'You successfully created the template.';
                console.log(text);
                
                $btnsSignClass.removeClass('disabled');
                $btn.html(btnDefaultValue);
            } else if (eventData.event === HelloSign.EVENT_CANCELED) {
                text = 'You closed the template, please reopen to continue.';

                $.ajax({
                    url: $btn.data('doc'),
                    type: 'POST',
                    data: {},
                    dataType: 'json',
                    success: function (response) {
                        console.log('success: ' + text + ' ' + response);
                        $btnsSignClass.removeClass('disabled');
                        $btn.html(btnDefaultValue);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('error: ' + text + ' ' + textStatus);
                        $btnsSignClass.removeClass('disabled');
                        $btn.html(btnDefaultValue);
                    }
                });
            } else if (eventData.event === HelloSign.EVENT_ERROR) {
                // nothing to do
            }
        }
    });
};

/**
 * 
 * @returns {undefined}
 */
DocumentationController.prototype.Edit = function () {
    var $this = this,
            docEdit = $('.doc-edit'),
            docDelete = $('.doc-delete'),
            docMenu = $('.dropdown-menu li'),
            okBtn = $('.ok-btn'),
            statusBtn = $('.btn-status')
    ;

    docEdit.on('click', function (e) {
        var currentRow = $(e.currentTarget).parents('.doc-table-row'),
                isTenant = $(this).data('is-tenant'),
                currentText = currentRow.find('.file-name').text().replace(/ /g, '');

        $(this).css('display', 'none');
        currentRow.css('background-color', '#f8f8f8');
        currentRow.find('.ok-btn').css('display', 'inline-block');
        if (!isTenant) {
            currentRow.find('.dropdown .btn').css('border', '1px solid #e3e1e1');
            currentRow.find('.dropdown-toggle').removeClass('disabled').append('<span class="select-container"></span>');
        }
        currentRow.find('.file-name-line').css('display', 'none');
        currentRow.find('.file-name-input').css('display', 'inline-block').val(currentText);
    });

    docMenu.on('click', function () {
        var selectedOption = $(this),
                dropdownBtn = $(this).parents('.dropdown').find('.btn');

        dropdownBtn.html('');
        selectedOption.clone().append('<span class="select-container"></span>').appendTo(dropdownBtn);
        dropdownBtn.val($(this).data('value'));
        DocumentationController.fileStatus = selectedOption.data('status');
    });

    okBtn.on('click', function (e) {
        var currentRow = $(e.currentTarget).parents('.doc-table-row'),
                fileId = currentRow.find('.btn-status').data('file-id'),
                fileName = currentRow.find('.file-name-input').val(),
                route = $this.getUrlByRoute('erp_user_document_update_ajax'),
                fileStatus = DocumentationController.fileStatus;

        $(this).hide();
        currentRow.find('.doc-edit').css('display', 'inline-block');
        if (currentRow.find('.dropdown .btn').text() !== '') {
            currentRow.find('.dropdown .btn').css('border', 'none');
            currentRow.find('.select-container').hide();
        }
        currentRow.find('.dropdown-toggle').addClass('disabled');
        currentRow.css('background-color', '#fff');
        currentRow.find('.file-name-line').css('display', 'block');
        currentRow.find('.file-name-input').hide();
        currentRow.find('.file-name-line a').text(fileName);

        route = route.replace('documentId', fileId);

        $.ajax({
            url: route,
            type: 'POST',
            data: {
                fileName: fileName,
                fileStatus: fileStatus
            },
            dataType: 'json',
            success: function (response) {
                if (response.errors) {
                    $('.errors').css({'opacity': 1}).html(response.errors).animate({'opacity': 0}, 3000);
                }
            }
        });
    });

    if (statusBtn.data('selected-file-status') !== ' ') {
        statusBtn.parents('.doc-table-row').find('.dropdown .btn').css('border', 'none');
    }
};

/**
 * 
 * @returns {undefined}
 */
DocumentationController.prototype.run = function () {
    this.Edit();

    var $this = this,
            $submit = $('button[type=submit]'),
            $file = $('._file'),
            $error = $('.errors'),
            maxFileSize = $file.data('max-file-size'),
            signBtn = $('.signing-btn'),
            editBtn = $('.edit-btn')
    ;
    
    /*
     * 
     */
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
    
    /*
     * 
     */
    $('.alert').on('click', '.close', function () {
        var $alert = $(this).closest('.alert');
        $alert.slideUp(400, function () {
            $alert
                    .addClass('hidden')
                    .removeAttr('style')
                    .removeClass('alert-warning alert-success alert-danger')
            ;
        });
    });
    
    /*
     * 
     */
    signBtn.on('click', function (event) {
        event.preventDefault();

        var $obj = $(this),
                $alertDiv = $obj.parent().siblings('.error-div'),
                $errorSpan = $alertDiv.find('.error'),
                label = $obj.html(),
                $btnsSignClass = $('.sign-btn')
        ;
        
        $alertDiv.removeClass('hidden').addClass('hidden');
        $errorSpan.html('');
        
        $btnsSignClass.addClass('disabled');
        $obj.html('Loading...');
        
        var url = this.href || this.getAttribute('href');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                var response;
                try {
                    response = JSON.parse(data);
                } catch (err) {
                    response = data;
                }
                if (response.SIGN_URL) {
                    $this.helloSignEmbeddedSigning(response, $obj, $errorSpan, $alertDiv, label);
                } else {
                    $btnsSignClass.removeClass('disabled');
                    $obj
                            .html(label)
                            .css('opacity', '1.0')
                    ;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                
                $this.showAlert($alertDiv, 'alert-danger');
                $errorSpan.html('Sorry, please try again later.');
                
                $btnsSignClass.removeClass('disabled');
                $obj
                        .html(label)
                        .css('opacity', '1.0')
                ;
            }
        });
    });
    
    /*
     * 
     */
    editBtn.on('click', function (event) {
        event.preventDefault();

        var $obj = $(this),
                $btnsSignClass = $('.sign-btn'),
                label = $obj.html()
        ;
        
        $btnsSignClass.addClass('disabled');
        $obj.html('Loading...');
        
        var url = this.href || this.getAttribute('href');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                var response;
                try {
                    response = JSON.parse(data);
                } catch (err) {
                    response = data;
                }
                if (response.TEMPLATE_URL) {
                    $this.helloSignEmbeddedTemplate(response, $obj, label);
                } else {
                    $btnsSignClass.removeClass('disabled');
                    $obj
                            .html(label)
                            .css('opacity', '1.0')
                    ;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                
                $btnsSignClass.removeClass('disabled');
                $obj
                        .html(label)
                        .css('opacity', '1.0')
                ;
            }
        });
    });
};

/**
 * 
 * @returns {undefined}
 */
$(function () {
    var controller = new DocumentationController();
    controller.run();
});
