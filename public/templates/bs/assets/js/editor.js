/**
 * Admin UI
 */
(function($, site){

    'use strict';
    console.log('editor.js');

    var editorBinded = false;

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

    var saveCallbackUrl = '/core/ctype/update/'

    function saveCallback() {
        //Save Object { id=4, field="title", ctype="sat.node"}
        var data = $(this.getElement()).data();

        console.log("Save", data);

        data.content = this.getContent();

        $.post(saveCallbackUrl, data, function(data){
           // console.log(data);
           tf.message(data.message, !data.status)
        });
    }



    // create panel

    $('body').append(
        '<div id="site-editable-panel" class="btn-group btn-group-xs">' +
            '<a id="site-enable-inline" class="btn btn-danger">Режим правки</a>' +
            '<a href="/editor/" class="btn btn-info">CPanel</a>' +
            '' +
            '' +
            '' +
            '</div>'
    );

    /**
     * Check cookie
     */

    if (parseInt($.cookie('inline-edit'))) {
        site.editable = true;
        bindEditorUI();
        $('#site-enable-inline').addClass('btn-success').removeClass('btn-danger');
    }

    /**
     * Button toggler
     */
    $('#site-enable-inline').on('click', function(){
        site.editable = site.editable ? 0 : 1;
        $.cookie('inline-edit', site.editable, {path:'/'});
        if (site.editable) {
            $('#site-enable-inline').addClass('btn-success').removeClass('btn-danger');
            bindEditorUI();
        }
        else {
            unbindEditorUI();
            $('#site-enable-inline').removeClass('btn-success').addClass('btn-danger');
        }
    });


    /*
    var tinyConfig = {
        plugins: "save",
        toolbar: "save",
        save_enablewhendirty: true,
        save_onsavecallback: saveCallback
    }
    */

    /**
     * Off
     */
    function unbindEditorUI() {
        if (!editorBinded) return;
        editorBinded = false;
        //$(".editable-inline, .editable-wysiwyg").tinymce().remove();
        if (tinymce.editors.length)
        for (var i in tinymce.editors) {
            tinymce.editors[i].remove();
        }

        undebugSatBlocks();
    }

    /**
     * On
     */
    function bindEditorUI() {

        if (editorBinded) return;

        editorBinded = true;

        tinymce.init({
            selector: ".editable-inline",
            inline: true,
            plugins: "save",
            toolbar: "save | undo redo",
            file_browser_callback : elFinderBrowser,
            menubar: false,
            forced_root_block : false,
            save_enablewhendirty: true,
            save_onsavecallback: saveCallback,
            // entity_encoding : "raw"
        });

        tinymce.init({
            selector: ".editable-wysiwyg",
            inline: true,
            menubar: false,
            file_browser_callback : elFinderBrowser,
            save_enablewhendirty: true,
            save_onsavecallback: saveCallback,
            plugins: [
                "save advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste"
            ],
            toolbar: "save | undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
        });

        debugSatBlocks();

    }

    function undebugSatBlocks() {
        $('.satblock-label').remove();
    }

    /**
     * debugSatBlocks
     */
    function debugSatBlocks() {

        $('satblock').each(function(k, elm){

            var $this = $(elm);

            if ($this.data())
            $.each($this.data(), function(k, v){
                $this.append('<span class="satblock-label"><i class="label-default label">' + k + ':' + v +'</i></span>');
            });
        });

    }


    /*
    $('.editable').each(function(){

        var $this = $(this);

        if ($this.is('.wysiwyg')) {
            console.log('editable-mce');
        } else {
            console.log('editable');
        }

    })
    */

})(this.jQuery, this.site);