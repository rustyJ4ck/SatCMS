/**
 * js/plugins/editor.js
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 */

define([

    'jquery',
    'app',
    'bootbox',
    'plugins/forms',
    'plugins/content',
    'plugins/i18n',

    'tinyMCE', // async delay sucks: export `tinymce`
    'select2',

    'jqueryTableDND',

    '../../../../vendor/jquery-icheck/icheck',
    '../../../../vendor/jquery-form/jquery.form',
    '../../../../vendor/jquery-maskedinput/src/jquery.maskedinput',

    "jqueryValidate",
    "bootstrapDateTime",

    '../../../../vendor/x-editable/dist/bootstrap3-editable/js/bootstrap-editable'

], function($, app, bootbox, formHandler, contentHandler, i18n) {

    'use strict';

    /** dom element, where events applied */
    var root;

    function deleteItem(item) {

        var $item = $(item);

        var title = $item.data('title') ? $item.data('title') : "Подтвердите удаление";

        bootbox.confirm(title, function(result) {

            if (result) {

                // remove TR
                // $(item).parents('tr').find('td').toggleClass('label-danger');

                if ($item.data('target')) {

                    // selector specified
                    $($item.data('target'))
                    .toggleClass('danger')
                        .animate(
                        {height:0, opacity:0}, 600, function(){
                            $(this).remove();
                        });
                }
                else {
                    $item.parents('tr')
                        .toggleClass('danger')
                        .animate(
                        {height:0, opacity:0}, 600, function(){
                            $(this).remove();
                        }
                    );
                }

                // actually, remove entity

                $.post(
                    $item.data('href')
                )
                .success(function(data) {
                    app.message(data.message !== undefined ? data.message : "Удаление подтверждено", !data.status);
                })
                .fail(function(data) {
                    app.message(data.message !== undefined ? data.message : "Ошибка при удалении", 1);
                })
                ;
            }
        });
    }

    /**
     * Delete checked items
     * @param item
     */
    function deleteSelectedItems(item) {

        var $item = $(item);
        var selector = $(item).data('source');
        var $items = $(selector);

        if (!$items.size()) {
            bootbox.alert('Ничего не выбрано');
            return;
        }

        bootbox.confirm("Подтвердите удаление " + $items.size() + " элементов", function(result) {

            if (result) {

                var ids = [];
                var $check;

                $items.each(function(k, v){

                    $check = $(v);

                    ids.push($check.val());

                    $check.parents('tr')
                        .toggleClass('danger')
                        .animate(
                        {height:0, opacity:0}, 600, function(){
                            $(this).remove();
                        }
                    );
                });

                console.log(ids);

                $.post(
                        $item.data('href'),
                        {ids: ids}
                    ).success(function(data) {
                        app.message(data.message !== undefined ? data.message : "Удаление подтверждено", !data.status);
                    });



            }
        });
    }

    /*
     * delete binding
     * use <a class="a-delete" data-href="" selector for ajax remove
     */

    function bindDeleteLinks() {

        console.log('bindDeleteLinks', root.find('.a-delete').size());

        root.find('.a-delete').on('click', function(e){

            e.preventDefault();
            deleteItem(this);
            return false;

        });

        root.find('.a-delete-selected').on('click', function(e){

            e.preventDefault();
            deleteSelectedItems(this);
            return false;

        });

    }

    /**
     * BindMasked
     */

    function bindMasked() {

        /*
         $("#date").mask("99/99/9999",{completed:function(){alert("completed!");}});
         $("#phone").mask("(999) 999-9999");
         $("#phoneExt").mask("(999) 999-9999? x99999");
         $("#iphone").mask("+33 999 999 999");
         $("#tin").mask("99-9999999");
         $("#ssn").mask("999-99-9999");
         $("#product").mask("a*-999-a999", { placeholder: " " });
         $("#eyescript").mask("~9.99 ~9.99 999");
         $("#po").mask("PO: aaa-999-***");
         $("#pct").mask("99%");
         */

        root.find('[data-mask]').each(function(){

            var $this = $(this);

            if ($this.data('mask-placeholder')) {
                $this.mask($this.data('mask'), {placeholder: $this.data('mask-placeholder')});
            } else {
                $this.mask($this.data('mask'));
            }

        });

    }

    /**
     * BindControls
     */

    function bindControls() {

        // checkbox

        // click = ifClicked
        root.find('input[type=checkbox].editable').on('ifClicked', function(){

            var $checkbox = $(this);
            var url = $(this).attr('href') + '&to=' + ($(this).is(':checked') ? 'false' : 'true');

            $.post(url, {
                field: $(this).data('field'),
                value: ($(this).is(':checked') ? '0' : '1')
            })
            .success(function(data){
                app.message(typeof data.message != 'undefined' ? data.message : 'Статус изменен', !data.status);

                if ($checkbox.data('callback')) {
                    var callback = new Function("data", $checkbox.data('callback'));
                    callback.call($checkbox, data);
                }
            });

            return false;
        });

        // editable

        /*
         <a href="#" class="editable" data-type="text"
         data-url="{$config.base_url}"
         data-pk="{$item.id}"
         data-params="{ id: {$item.id}, op:'change_field', field: 'value' }"
         >{$item.value}</a>
         */

        root.find('a.editable').editable({

            validate: function(value) {
                if($.trim(value) == '') {
                    return 'Заполните поле';
                }
            },

            success: function(response, newValue) {
                app.message(response.message, !response.status);
            },

            inputclass: 'editable-input-width'

        });

        // bs-popover

        root.find('[data-popover]').each(function(k, v){
            var $this = $(v);
            var options = $this.data();
            options.html = options.html || "html";
            options.trigger = options.trigger || "hover";
            options.placement = options.placement || "right";
            $this.popover(options);
        });

        // .btn-link with confirmation

        root.find('.btn-dlg-link').on('click', function(e){
            e.preventDefault();
            var $this = $(this);
            var url = $(this).attr('href');

            bootbox.confirm("Перейти по ссылке <a href='" + url + "'>" + url + "</a>" , function(result) {
                if (result) {
                    window.location.href = url;
                }
            });

            return false;
        });

        root.find('.a-confirm').on('click', function(e){

            e.preventDefault();
            var $this = $(this);
            var url = $(this).attr('href');

            var text = $this.data('content');
            text = text ? text : 'Подтвердите действие';

            bootbox.confirm(text , function(result) {
                if (result) {
                   app.go(url, 1);
                }
            });

            return false;
        });

        /**
         * attrs
         *
         * rel = id
         * [_value] = orig
         * [href] = base_url
         *
         * @returns {jQuery.prototype.init}
         */
        /*

         $('.quick_edit').bind('blur', function() {
            var $this = $(this);

            // orig value
            var _value = $this.attr('_value');

            if (_value == $this.val()) return;

            var _id  = $this.attr('rel');
            var _url = $this.attr('href'); // base url

            _url = _url ? _url : '/editor/index.php?m=' + ident_vars.m + '&c=' + ident_vars.c;
            _url += '&op=change_field&id=' + _id;

            $.ajax({
                type: "POST",
                url: _url,
                data: { field: $this.attr('name'), value: $this.val() },
                dataType: "json",
                success: function(response){
                    $.tf_message(response?response.message:'Параметр изменен');
                }
            });
        });

        */


    }

    /**
     * Flip item positions
     */

    function _flipPositions(url, ids, positions, src, dst) {
        $.post(url, {
                'op': 'flip',
                'src': src,
                'dst': dst,
                'ids[]': ids,
                'positions[]': positions
            }
            , function(data) {
                app.message(data.message, !data.status);
            }
        );
    }


    /**
     * .table-sortable
     */
    function bindTableSortable() {

        root.find("table.table-sortable").tableDnD({

            onDragClass: "drag-row",
            dragHandle: "drag-handle",

            /*
            onDrop: function(table, row) {
                alert($.tableDnD.serialize());
            },
            */

            onDrop: function(table, src, dst) {

                console.log('sortable-drop', table, src, dst);

                var rows = table.tBodies[0].rows;

                var positions = new Array();
                var ids = new Array();

                for (var i = 0; i < rows.length; i++) {
                    var row = $(rows[i]);
                    ids[i] = row.data('id');
                    positions[i] = row.data('position');
                }

                positions.sort(function (a, b) {return a - b;});

                var src_id = $(src).data('id');

                if (dst) {
                    var dst_id = $(dst).data('id');
                    $(table).find("tr[data-id="+ src_id +"]").fadeOut(300, function () { $(this).fadeIn(300); });
                    // send to server
                    _flipPositions($(table).data('base'), ids, positions, src_id, dst_id);
                }
            }
        });




    }


    /**
     * Forms
     */

    function bindForms() {

        //
        // validable form
        //

        root.find("form.validable").each(function(k, form){
            formHandler.bindValidatorAjax(form);
        });


        //
        // Validate without ajax
        //

        root.find("form._validable").each(function(k, form){
            formHandler.bindValidator(form);
        });

        //
        // Submit
        //

        root.find('form.submitable').each(function(k, form){
            formHandler.bindSubmitable(form);
        });

    }

    function bindUI() {

        // select2, ignore .system

        root.find('select[class!="system"]').select2({
            minimumResultsForSearch: $(this).data('no-search') ? -1 : 5
        });

        // checkboxes style

        root.find('input[type=checkbox], input[type=radio]').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            radioClass: 'iradio_flat-blue'
        }).on('ifClicked', function(){
            // console.log($(this).is(':checked'));
        });

        // ajax tabs

        root.find('a[data-toggle=tab][data-url]').on('click', function(){
            var $this = $(this);
            if (!$this.data('_loaded')) {
                var $target = $($this.attr('href'));
                app.blockUI(1);
                $target.load($(this).data('url'), function(){
                    _bindUI(this);
                    app.blockUI(0);
                });
                $this.data('_loaded', true);
            }
        });

        // a.bootbox data-content

        root.find('a.bootbox').on('click', function(){
            bootbox.alert({title: $(this).data('title') || 'Изображение', message: $(this).data('content')});
            return false;
        });

        // dates

        root.find('div.datetime').datetimepicker({
            // language: 'ru'
        });


        root.find('div.datetime > input')
            .prop('readonly', true)
            .on('click', function(){
                $(this).next('span').trigger('click');
            });

    }

    /**
     * Interactive remote areas
     */
    function bindWidgets() {

        root.find('.widget').each(function(){

            var $this = $(this);
            var url = $this.data('url') + 'embed/yes/';
            var $scope = null;

            if ($this.data('compilable')) {

                if ($this.data('isolated')) {
                    console.log('widget - isolate scope');
                    $scope = app.ngScope()/*app.ngInjector().get('$rootScope')*/.$new(true);
                } else {
                    $scope = app.ngScope();
                }
            }

            contentHandler.loadWidget($this, url, $scope);

        });

    }

    function elFinderBrowser (field_name, url, type, win) {
        tinymce.activeEditor.windowManager.open({
            file: '/editor/fm/?from=tinymce',
            title: 'Выберите файл для вставки',
            width: 940,
            height: 630,
            resizable: 'no'
        }, {
            setUrl: function (url) {
                win.document.getElementById(field_name).value = url;
            }
        });
        return false;
    }

    var wysiwygConfig = {

        //selector:'.wysiwyg',
        mode: 'exact',

        document_base_url : '/',
        convert_urls: true,
        relative_urls : false,

        file_browser_callback : elFinderBrowser,
        image_advtab: true,

        plugins: [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons template textcolor paste textcolor"
        ],

            toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | inserttime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

            menubar: false,
            toolbar_items_size: 'small',

    }

    var tinyMCE_initialized = false;

    /**
     * TinyMCE
     */
    function bindTextareas() {

        var $elms = root.find('.wysiwyg');

        if ($elms.size()) {

            /// require(['tinyMCE'], function(){
/*
                if (!tinyMCE_initialized) {
                    tinyMCE_initialized = true;
                    tinymce.init(wysiwygConfig);
                }
*/

                //$elms.tinymce();

                console.log('wysiwyg bindTextareas: ', $elms.size());

                var ids = [];

                $elms.each(function(){

                    var $elm = $(this);

                    // regenerate ID
                    var id = $elm.attr('id');

                    if (!id) {
                        $elm.attr('id', 'textarea' + Math.ceil(Math.random()*1000000));
                    }
                    else {
                        // fix rebind?
                        //tinymce.execCommand('mceRemoveControl', false, id);
                    }

                    id = $elm.attr('id');

                    //$elm.data('mce-id-binded', 1);

                    ids.push(id);

                    // tinymce.execCommand('mceAddControl', false, id);
                });

                // bind

                if (ids.count()) {

                    setTimeout(function(){

                        // tinymce.execCommand('mceAddControl', false, ids);

                        tinymce.init(//wysiwygConfig

                            $.extend(wysiwygConfig,
                                {
                                    // mode: 'exact',
                                    // selector: '#' + id
                                    // elements:  root.find('#'+id).get(0) //$elms.get()
                                    elements: ids
                                })
                        );

                    }, 200); // fuck you fucking mce!

                }



           /// });
        }

    }

    function cleanDOM(root) {

        console.log('cleanDOM', root);

        // clean selects
        root.find('select[class!="system"]').select2('destroy');

        root.find('.wysiwyg').each(function(k, v){
            // console.log('tinyMCE.execCommand(mceRemoveControl false, ', $(v).attr('id'));
            // tinymce.execCommand('mceRemoveControl', false, $(v).attr('id'));
        });

    }

    /*
     //selector: '.wysiwyg',
     plugins: [
     "advlist autolink lists link image charmap print preview anchor",
     "searchreplace visualblocks code fullscreen",
     "insertdatetime media table contextmenu paste"
     ],
     toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
     */

    var _modalTemplate = null;

    /**
     * Open modal
     * @param url
     * @private
     */
    function _openModal(url, params) {

        var title = params.title || 'Unnamed window';

        $.get(url, function(data){

            // error
            if (typeof data.status != 'undefined' && !data.status) {
                app.message(data.message, 1);
                return;
            }

            var tpl = _modalTemplate;

            tpl = tpl.replace('[[body]]', data);
            tpl = tpl.replace('[[title]]', title);

            var $tpl = $(tpl);

            if (params.dialog !== undefined) {
                $tpl.find('.modal-dialog').addClass(params.dialog);
            }

            _bindUI($tpl);

            // add angular
            ngCompile($tpl);

            $tpl.modal()

            .on('shown.bs.modal', function () {
                /*
                 app.message('Modal Show');
                 tinyMCE.execCommand('mceAddControl', false, 'mce-<?=$reply["id"]?>');
                 tinyMCE.execCommand('mceRemoveControl', false, 'mce-<?=$reply["id"]?>');
                 */
             })
            .on('hidden.bs.modal', function () {
                //$(this).data('bs.modal', null);
                cleanDOM($(this));
                $(this).remove();
            });

        })

        .fail(function(data){
            console.error('openModal failed', data);
            //bootbox.alert('openModal failed: ' + data.statusText + '(' + data.responseText + ')', true);
            app.message('openModal failed', 1);
            var tpl = _modalTemplate;
            tpl = tpl.replace('[[body]]', data.responseText);
            tpl = tpl.replace('[[title]]', data.statusText);
            $(tpl).modal();
        })
        ;

    }

    /**
     * Open modal
     * @param url
     * @param params
     */
    function openModal(url, params) {

        console.log('openModal',_modalTemplate);
        if (!_modalTemplate) {
            require(['text!/editor/templates/partials/modal.html'], function(data){
                _modalTemplate = data;
                _openModal(url, params);
            });
        }
        else {
            _openModal(url, params);
        }
    }

    /**
     * a[dialog], a[data-dialog]
     */
    function bindDialogs() {

        // prevent url trigger
        /*
        root.find('a[dialog], a[data-dialog]').each(function(elm){
            var $elm = $(elm);
            if ($elm.attr('href')) {
                $elm.data('href', $elm.attr('href'));
                $elm.removeAttr('href');
            }
        });
        */

        root.find('a[dialog], a[data-dialog]').on('click', function(e){
            e.preventDefault();
            //alert('bindDialogs');
            openModal($(this).attr('href'),
                $.extend({'dialog':$(this).attr('dialog')}, $(this).data())
            );
            return false;
        });

    }

    /**
     * .clickable
     */
    function bindLinks() {

        root.find('.clickable').on('click', function(){
            app.go($(this).attr('href'), 1);
            return false;
        });

    }

    function ngCompile(element, $scope) {

        console.log('ngCompile');

        var $compile = app.ngInjector().get('$compile');

        if (typeof $scope == 'undefined') {
            console.warn('ngCompile scope undefined');
            $scope = app.ngScope().$new(true);
        }

        if (element.find('.compilable').size()) {
            element.find('.compilable').each(function(){
                $compile(angular.element(this).contents())($scope);
            });

            // kick angular
            setTimeout(function(){
                $scope.$apply();
            }, 100);
        }

        //
    }

    /**
     * Build UI
     * @param elmSelector
     * @returns {boolean}
     * @private
     */

    function _bindUI(elmSelector) {

        root = $(elmSelector);

        bindDeleteLinks();
        bindTableSortable();
        bindForms();
        bindControls();
        bindMasked();
        bindUI();
        bindDialogs();
        bindLinks();
        bindTextareas();
        bindWidgets();

        console.log('Editor::Bind ', elmSelector);

        return true;
    }

    function ajaxCsrf() {

        $(document).ajaxSend(function(event, xhr, settings) {

            function sameOrigin(url) {
                // url could be relative or scheme relative or absolute
                var host = document.location.host; // host + port
                var protocol = document.location.protocol;
                var sr_origin = '//' + host;
                var origin = protocol + sr_origin;
                // Allow absolute or scheme relative URLs to same origin
                return (url == origin || url.slice(0, origin.length + 1) == origin + '/') ||
                    (url == sr_origin || url.slice(0, sr_origin.length + 1) == sr_origin + '/') ||
                    // or any other URL that isn't scheme relative or absolute i.e relative.
                    !(/^(\/\/|http:|https:).*/.test(url));
            }

            function safeMethod(method) {
                return (/^(GET|HEAD|OPTIONS|TRACE)$/.test(method));
            }

            if (!safeMethod(settings.type) && sameOrigin(settings.url)) {
                xhr.setRequestHeader("SC-CSRF-TOKEN", app.config.token);
            }

        });

    }

    /**
     * Constructor after loading
     */
    function initialize() {

        ajaxCsrf();

        contentHandler.initialize(this);

        // override modal backdrop 1040
        $.blockUI.defaults.baseZ = 1060;
        $.blockUI.defaults.overlayCSS.opacity = 0.3;

        // @todo fix r.js
        tinyMCE.baseURL = '/vendor/tinymce/';

        // fixme: tinymce modal focus bug
        $(document).on('focusin', function(e) {
            if ($(e.target).closest(".mce-window").length) {
                e.stopImmediatePropagation();
            }
        });

    }



    /**
     * Actual do
     */
    return {

        bindUI: _bindUI,

        initialize: initialize,

        ngCompile: ngCompile

    };

});