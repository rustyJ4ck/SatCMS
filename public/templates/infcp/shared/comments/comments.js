/**
 *  $Id: comments.js,v 1.2 2011/02/15 09:48:34 Vladimir Exp $
 */

(function () {

    'use strict';

    var commentsWidget = (function () {

        function hideForm() {

            var x = 5000;
            var y = parseInt($(window).height() / 2 - $('#comment-form-div').height() / 2);

            $('#comment-form-div').removeClass('in');

            // .animate({left: x, top: y}, 400);
            var tpid = $('input#comment-tpid').val();

            if (tpid) {
                $('div#comment-' + tpid).removeClass('comment-answer-this');
            }

           // tf.unblockUI();

        }

        function showForm() {

        }

        function addComment(tpid) {

            $('input#comment-tpid').val(tpid);
            // $('input#comment-pid').val(pid);
            // $('div#comment-' + pid).next('div.next').text()
            //$.modalBox($('div#comment-form').html());
            // сделать чтобы выезжало
            //alert($('div#comment-form').html());

        }

        function formCallback(data) {

            hideForm();

            // rem selected
            var tpid = $('input#comment-tpid').val();

            if (tpid) {
                $('div#comment-' + tpid).removeClass('comment-answer-this');
            }

            if (data.status) {
                $('div#comment-' + tpid).next('div.comment-next').append(data.data);
                // $('.branch-expand[rel=' + tpid + ']').click();
                $('#branch-' + tpid).show();

                // $('div.comment-last').corner('8px');
                $('div.comment-last').fadeOut(1000, function () {
                    $(this).fadeIn(2000);
                });

                $('.comments-list .no_comments').hide();

                $('form#comment-form').resetForm();
                $('div#comment-form-div').hide();

                tf.message(data.message);

                // scroll to it
                var targetOffset = ($('#comment-' + data.id).offset().top) - 30;
                $('html,body').animate({scrollTop: targetOffset}, 1000);
            }
            else {
                tf.message(data.message, true);
            }
        }

        /**
         *
         */
        function init() {

            $('#comment-form').on('submit', function () {
                if ($('#comment-text').val().length < 4) {
                    bootbox.alert('Слишком короткое сообщение');
                } else {
                    $(this).ajaxSubmit({
                        dataType: 'json',
                        success: commentsWidget.formCallback
                    });
                }

                return false;
            });


            // Submit
            // ---------------------------------------------------------

            $('#comment-submit-btn').on('click', function () {
                $('#comment-type').val('0');
                $('#comment-submit').click();
                return false;
            });

            // Close
            // ---------------------------------------------------------

            $('#comment-close').click(function () {

                commentsWidget.hideForm();
                return false;

            });

            // $('body').append('<div id="ttt">%%%</div>');
            // $('#ttt').html($('#ttt').html() + '<br/>' + $(this).attr('id') + ' : ' + branch);

            // Expand branch
            // ---------------------------------------------------------

            $('.branch-expand').on('click', function () {

                $('.branch-expand').removeClass('in');
                $(this).addClass('in');

                tf.message('branch-expand');

                var branch = 'branch-' + $(this).attr('rel');

                $(branch).toggle();
                var t = false;

                $('.comments-list .comment-next-root').each(function () {
                    if ($(this).attr('id') == branch)
                        t = $(this);
                    else
                        $(this).hide(300);
                });

                // show and scroll to it
                if (t) {
                    t.show(300);
                    var target_offset = ($(this).offset().top) - 200;
                    $('html,body').animate({scrollTop: target_offset}, 600);
                }
            });

            // Show answer btn
            // ---------------------------------------------------------

            /*
            $('.comment')
                .mouseover(function () {
                    $(this).find('.answer').show();
                })
                .mouseout(function () {
                    $(this).find('.answer').hide();
                });
            */

            // Answer btn click
            // ---------------------------------------------------------

            $('.answer').click(function () {

                var $this = $(this);

                // tf.blockUI(1);

                $('div.comment').removeClass('comment-answer-this');

                var tpid = $(this).attr('rel');
                $('input#comment-tpid').val(tpid);

                tpid = parseInt(tpid);

                if (tpid) {
                    $('div#comment-' + tpid).addClass('comment-answer-this');
                }

                var cmntBox = $('#comment-form-div');

                cmntBox.addClass('in');

                var x, y, width;

                // root level comments
                if ($this.attr('rel') == 0) {
                    var parent = $this.parents('.comments-widget');
                    width = parent.width() - 30;
                    y = $this.position().top - cmntBox.height() / 2;
                    x = 30;

                } else {
                    var $parent = $this.parents('.comment');
                    y = $parent.position().top + $parent.height() + 15;
                    x = $parent.position().left + 15;
                    width = $parent.width() + 3; // hack: 18 for padding+borders
                }

                console.log('comment', x, y, width);

                cmntBox
                    .animate({left: x, top: y, width: width}, 300,
                        function(){
                    //.fadeIn('fast', function(){
                        $('#comment-text').focus();
                    });


                // .center({horizontal: true, vertical: true})
                /*
                 var y = parseInt($(window).height() / 2 -cmnt.height() / 2);
                 var x = parseInt($(window).width() / 2 - cmnt.width() / 2);
                 root
                 .animate({left: x, top: y}, 0) //opacity: 1
                 .fadeIn('fast', function(){$('#comment-text').focus();});
                 */
                //

                return false;
            });


        }


        return {

            init: init,
            showForm: showForm,
            hideForm: hideForm,
            addComment: addComment,
            formCallback: formCallback

        }


    })();


    // DOM-Ready
    // ---------------------------------------------------------

    $(function () {

        commentsWidget.init();

        //
        // @todo cleanup and make it work
        // legacy code
        //

        // Rating
        // ---------------------------------------------------------

        $('.minus, .plus')
            .click(function () {
                var outter = $(this);
                var url = $(this).data('url');
                // if url not specified, this is disabled vote!
                if (url) {
                    $.post($(this).data('url'), function (data) {
                        if (data.status) {
                            outter.parent().html(data.data);
                        }
                    });
                }
            });

        // start rating
        $('.post_rating').click(function () {
            var outter = $(this);
            var url = $(this).data('url');
            // if url not specified, this is disabled vote!
            if (url) {
                $.post($(this).data('url'), function (data) {
                    if (data.status) {
                        $('#post_rating_ctx').html('Ваш голос учтен.');
                    }
                });
            }
        });

    });


})();