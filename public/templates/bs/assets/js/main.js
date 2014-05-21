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

        $('#sidebar').affix({
            offset: {
                top: 150
            }
        });

        var $body = $(document.body);
        var navHeight = $('.navbar').outerHeight(true) + 10;

        $body.scrollspy({
            target: '#leftCol',
            offset: navHeight
        });

        var preload_data = [
            //   { id: 'user0', text: 'Disabled User', locked: true}
        ];

        /*
         $.get('/assets/search.json', function(data){
         $("#searchBox").typeahead({ source:["local", "remote", "cancel"] });
         },'json');
         */

        // constructs the suggestion engine
        var suggests = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,

            // prefetch
            // local: $.map(_states, function(state) { return { value: state }; })
            // remote

            prefetch: {
                //url: '/assets/' + /*site.domain*/ 'localhost-' +  'search.json'
                url: '/sat/search/tree/'
            }

        });

        // kicks off the loading/processing of `local` and `prefetch`
        suggests.initialize();

        $('#searchBox').typeahead({
                hint: true,
                highlight: true,
                minLength: 1,
                limit: 10
            },
            {
                name: 'suggests',
                displayKey: 'title',

                source: suggests.ttAdapter()
            })
            .bind('typeahead:selected', function (obj, data, name) {

                if (data.url) {
                    window.location.href = data.url;
                }
                else
                    return false;

            });


        /*

         // ADD SLIDEDOWN ANIMATION TO DROPDOWN //
         $('.dropdown').on('show.bs.dropdown', function(e){
         $(this).find('.dropdown-menu').first().stop(true, true).fadeIn(300);
         });

         // ADD SLIDEUP ANIMATION TO DROPDOWN //
         $('.dropdown').on('hide.bs.dropdown', function(e){
         $(this).find('.dropdown-menu').first().stop(true, true).fadeOut(300);
         });

         */

        $('#contact-form').on('mouseenter', function(){

            var $this = $(this);
            if (!$this.data('loaded')) {
                $(this).find('.panel-body').load(
                    '/templates/bs/partials/contact-form.html?1',
                    function(){
                        $this.data('loaded', 1);
                        tf.bindUI($this);
                    }
                );
            }

        });

        tf.bindUI($body);

    }


})(this.jQuery, this.tf);