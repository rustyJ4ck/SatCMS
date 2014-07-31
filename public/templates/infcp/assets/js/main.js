// if console is not available -> mock it to prevent errors
try {
    var c = console;
}
catch (e) {
    console = {};
    console.log = function () { };
    console.debug = function () { };
    console.warn = function () { };
    console.error = function () { };
    console.dir = function () { };
}

/**
 * Common init routines
 */
(function ($, tf) {

    'use strict';

    console.log(site);

    // site.styles

    if (site.styles.length) {
        $(site.styles).each(function (k, v) {
            tf.style(v);
        });
    }

    // site.scripts

    if (site.scripts.length) {
        $(site.scripts).each(function (k, v) {
            tf.script(v);
        });
    }

    // Init

    tf.bindings.bindForms = function() {

        this.find("form.validable").each(function(k, form){
            tf.forms.bindValidatorAjax(form);
        });

    }

    /**
     * a.bootbox
     */

    tf.bindings.aBootbox = function () {

        this.find('a.bootbox').on('click', function () {
            bootbox.alert({title: $(this).data('title') || 'Изображение', message: $(this).data('content')});
            return false;
        });

    }

    //
    // Dependencies
    //

    /*
     <link href="/vendor/select2/select2.css" rel="stylesheet" type="text/css" />
     <link href="/vendor/toastr/toastr.css" rel="stylesheet" type="text/css" />
     tf.style('/vendor/toastr/toastr');
     tf.script('/vendor/toastr/toastr');
     tf.script('/vendor/bootbox/bootbox');
     */

    head.load([
        '/vendor/toastr/toastr.css',
        '/vendor/toastr/toastr.js',
        '/vendor/bootbox/bootbox.js',
        '/vendor/jquery.validation/dist/jquery.validate.js',
        '/vendor/blockui/jquery.blockUI.js',
        '/vendor/jquery-form/jquery.form.js',
        site.urls.template + 'assets/js/forms.js'
    ], function () {

        $.blockUI.defaults.message = '';
        $.blockUI.defaults.zIndex = 1032;

        domLoaded();

    });

    //
    // Build UI
    //

    //$(function () {

    function domLoaded() {

        setTimeout(function () {
            $('.cloak:not(.in)').toggleClass('in');
        }, 500);

        var $body = $(document.body);
        var navHeight = $('.navbar').outerHeight(true) + 10;

        /**
         * Contact form
         */
        $('#contact-form').on('mouseenter', function(){

            var $this = $(this);

            if ($this.hasClass('c-visible')) {
                return false;
            }

            if (!$this.data('loaded')) {
                $(this).find('.panel-body').load(
                    '/templates/bs/partials/contact-form.html?1',
                    function(){
                        $this.data('loaded', 1);
                        tf.bindUI($this);
                    }
                );
            }

            $('#contact-form').removeClass('c-hidden').addClass('c-visible');

        })

        .on('mouseleave', function(e){

           var triggered = 0;

           var $target = $(e.target);
           var validElm = $target.hasClass('panel-body') || $target.hasClass('panel') || $target.hasClass('panel-heading');

           if (!triggered && validElm) {
               triggered = 1;
               setTimeout(function(){
                   $('#contact-form').addClass('c-hidden').removeClass('c-visible');
                   setTimeout(function(){triggered = 0;}, 600);
               }, 500);
           }
           else {
               return false;
           }

        });

        // ready scripts
        if (site.ready.length) {
            for (var i in site.ready) {
                site.ready[i].call();
            }
        }

        // Initial ui setup
        tf.bindUI($body);

    }


})(this.jQuery, this.tf);