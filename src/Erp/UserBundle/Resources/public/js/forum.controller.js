var ForumController = function() {
};

ForumController.prototype.addMessage = function() {
    var commentCreate = $( '.create-comment' );

    $( document ).on( 'click', '.reply-btn', function (e) {
        var replyName = $( this ).data( 'reply-name' ),
            formText = $( 'form.create-comment .field-text' ),
            pageWrappers = $( 'html, body' );

        event.preventDefault();

        formText.val(replyName + ', ').focus();

        pageWrappers.animate({
            scrollTop: $( '#messsage-add-row' ).offset().top - 140
        }, 300);
    });

    $( document ).on( 'submit', '.create-comment', function() {
        var form = $(this),
            createCommentUrl = form.attr( 'action' ),
            fieldText = form.find( 'input.field-text' ).val();

        $.post(createCommentUrl, { 'text':fieldText }, function(response) {
            response = $.parseJSON(response);
            if ( response.status === 'ok' ) {
                $( '#messsage-add-row' ).after( response.html );
                form.trigger( 'reset' );

                $( '.not-comments' ).remove();
            } else {
                $( '#messsage-add-row .errors' ).css( { 'opacity': 1 } ).html(response.errors).animate( { 'opacity': 0 }, 3000 );
            }

            form.find('button[type=submit]').prop('disabled', false);
        });

        return false;
    });

    commentCreate.validate({
        success: function() {
            commentCreate.find('button[type=submit]').prop('disabled', false);
        },
        errorPlacement: function( error, element ) {
            return false;
        }
    });

    $( document ).on( 'click', '.more-comments', function() {
        var $this = $( this ),
            nextUrl = $( this ).attr( 'href' );

        $.post( nextUrl, {}, function( response ) {
            response = $.parseJSON( response );
            if ( response.status == 'ok' ) {
                var commets = $( response.html ).find( '.comment-block '),
                    pagination = $( response.html ).find( 'a.more-comments' );

                if ( commets.length ) {
                    commets.each(function ( indx, el ) {
                        $( '.forum-message-container .comment-block:last' ).after( el );
                    });

                }

                if ( pagination.length ) {
                    $('a.more-comments').replaceWith(pagination);
                } else {
                    $this.remove();
                }
            }
        });

        return false;
    });
};

ForumController.prototype.run = function() {
    this.addMessage();
};

$( function () {
    var controller = new ForumController();
    controller.run();
});
