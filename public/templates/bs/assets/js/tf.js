/**
 * TwoFaced.js UI
 */
(function (w, $) {

    'use strict';

    var tf;

    if (typeof w.tf != 'undefined') {
        throw 'TF already loaded!';
    }

    if (typeof tf == 'undefined') {
        tf = {};
        // create ns
        tf.site = {}
    }

    // UI bindings
    tf.bindings = {};

    /**
     * bind events handlers
     * called onload
     */

    tf.bindUI = function (root) {
        for (var key in tf.bindings) {
            if (tf.bindings.hasOwnProperty(key)) {
                console.log('tf.bindUI: ' + key);
                tf.bindings[key].call(root);
            }
        }
    }

    tf.embed = function () {}

    tf._includes = {
        js: {},
        css: {},
        js_wait: 0
    }

    /**
     * Require JS
     * @param path
     * @param async
     */

    tf.script = function (path, callback) {

        path += '.js';

        if (tf._includes.js[path]) {
            return;
        }

        console.log('[@js] ' + path);

        tf._includes.js[path] = 1;

        if (typeof head != 'undefined') {
            if (typeof callback == 'undefined')
                head.load(path); //, typeof callback == 'undefined' ? false : callback );
            else
                head.load(path, callback);
            return true;
        }

    }

    /**
     * Require css
     * @param url
     */

    tf.style = function (path, callback) {

        path += '.css';

        if (tf._includes.css[path]) {
            return;
        }

        console.log('[@css] ' + path);

        tf._includes.css[path] = 1;

        if (typeof head != 'undefined') {
            if (typeof callback == 'undefined')
                head.load(path); //, typeof callback == 'undefined' ? false : callback );
            else
                head.load(path, callback);
            return true;
        }
    }

    // some legacy code

    /**
     * Route to base
     */

    tf.to_asset = function (url) {
        return config.assets_url + url;
    }

    /**
     * Route to base
     */

    tf.to_base = function (url) {
        return config.site_url + url;
    }

    /**
     * From submit handler
     */

    /*
    tf.submit_handler = function ($form, data, statusText, xhr) {

        var response = data ? data.message : null;

        if (!response || statusText == 'error') {
            tf.message('BAD RESPONSE: '
                + (xhr.statusText == 'OK'
                ? data
                : xhr.statusText)
            );
            return false;
        }

        if (response.message && response.message.length) {
            tf.message(response.message, !response.status);
        }

        if (response.status) {

            if (typeof response.redirect != 'undefined' && response.redirect.length) {
                tf.redirect(response.redirect);
            }

            // call on success
            if ($form.data('success')) {
                window[$form.data('success')]($form, data, statusText, xhr);
            }

        }
        else {

            // call on fail
            if ($form.data('fail')) {
                window[$form.data('fail')]($form, data, statusText, xhr);
            }

            return tf.validator($form, response.validator);
        }
    }
    */

    /**
     * user login
     */

    tf.user_login = function (data) {
        if (data.status) {
            //$('#user_cp').html(data.data);
            if (data.redirect) {
                tf.redirect(data.redirect)
            }
            else {
                w.location.reload();
            }
        }
        else {
            tf.message(data.message, true);
        }
    }


    tf.redirect = function (data) {
        if (!data) return;
        w.location.href = data;
        if (w.navigator.userAgent.toString().indexOf("Chrome/2") != -1) {
            w.open(data, '_self');
        } else {
            w.location.href = data;
        }
    }

    /**
     * user logout
     */

    tf.user_logout = function (data) {
        tf.redirect('/');
    }

    /**
     *  loginbox
     *  @todo make it langness
     */

    tf.login_box = function (redirect) {

        if (!redirect) redirect = window.location.href;
        if ($('#fl_l').size()) {
            $('#fl_l').focus();
            tf.message('Пожалуйста, войдите в систему!', true);
            if (!$('#frm_login').find('input[name=redirect]').size()) $('#frm_login').append('<input type="hidden" name="redirect" value=""/>');
            $('#frm_login').find('input[name=redirect]').val(redirect);
        }
        else {
            tf.redirect(redirect);
        }

        return false;
    }

    /**
     * Create login box
     */

    tf._create_login_box = function () {

        if ($('#loginbox_w').size()) return;

        $('body').append('\
            <div id="loginbox_w" class="well">                                                     \
            <div id="loginbox_title"><h1>Просмотр данного раздела ограничен</h1><br/>        \
            Для продолжения работы вам необходимо авторизироваться или <a href="/user/register/">зарегистрироваться</a>.</div> \
            \
            <form class="validable" id="loginbox_w_f" rel="" method="post" action="/user/login/" >       \
            \
            <div id="loginbox_fields"> \
            <div><span class="span4">Логин</span>  <input required="required" data-rule-minlength="4" data-rule-email="1" class="span3" onchange="$(\'#loginbox_msg\').empty();" id="loginbox_fields_login" name="email" type="text" value="" validate="{minlen:5,required:1}" /> <label for="loginbox_fields_login" class="error">Логин?</label>  </div>             \
            <div><span class="span4">Пароль</span> <input required="required" data-rule-minlength="4" class="span3" onchange="$(\'#loginbox_msg\').empty();" id="loginbox_fields_pw" name="password" type="password" value="" validate="{minlen:4,required:1}"/>            <label for="loginbox_fields_pw" class="error">Пароль?</label>      </div>      \
            </div> \
                                                                                                                     \
            <div id="loginbox_panel" class="clearfix">                \
            <input type="submit" value="Войти" class="hidden" />      \
                                                                                     \
            <a id="loginbox_submit" href="#" class="btn btn-primary" onclick="$(\'#loginbox_w_f\').trigger(\'submit\');return false;">Войти</a>           \
            <a href="#" class="btn btn-danger" onclick="$(\'#loginbox_w\').modal(\'hide\');return false;">Отмена</a>          \
                                                                                     \
                                                                                     \
            <!--input type="button" value="Отмена" onclick="tf.unblockUI();"/--></div>       \
            <div id="loginbox_msg"></div>                                         \
            </form>                                                           \
            </div>                                                                  \
            ');
    }


    /**
     * Login pop
     */

    tf.login_box_popup = function (redirect) {

        var lb = $('#loginbox_w');

        if (typeof redirect == 'undefined') redirect = '';
        $('#loginbox_w_f').attr('rel', redirect);

        if (!lb.size()) {
            tf._create_login_box();
        }

        //tf.blockUI(0, function(){$('#loginbox_w').show()});

        $('#loginbox_w').modal();

        $("#loginbox_w_f").validate({

            submitHandler: function (form) {

                console.log('login-submit');

                // onsubmit
                $(form).ajaxSubmit({dataType: "json", success: function (data) {
                    if (!data.message.status) $('#loginbox_msg').html(data.message.message); else {
                        tf.message('Вы успешно авторизированы в системе');
                        var url = $('#loginbox_w_f').attr('rel');
                        if (!url) url = window.location.href;
                        // tf.message('done:' + url);
                        tf.redirect(url);

                        // $('#loginbox_w').modal('hide');
                        // tf.unblockUI();
                    }
                }
                });
                return false;


            }, highlight: false
        });

        $('#loginbox_w_f').attr('rel', redirect);

    }

    /**
     * Message
     */

    tf.message = function (message, is_error) {

        if (is_error)
            toastr.error(message);
        else
            toastr.success(message);

        /*
         $.bootstrapGrowl(message, {
         type: (typeof is_error == "undefined" || !is_error) ? 'info' : 'error',
         align: 'right',
         width: 'auto',
         stackup_spacing: 10
         });
         */
    }

    /**
     * BlockUI
     * @param level 0-low ,1-high
     * @param callback
     */

    tf.blockUI = function (level, callback) {

        if (typeof level !== 'undefined') {
            if (level == 0) return tf.unblockUI();
        }

        level = (typeof level == 'undefined' ) ? 19 : 1030;

        if (!$('#blockUI').size()) {
            $('body').append('<div id="blockUI" style="z-index:' + level + '"></div>');
            $('#blockUI').on('click', function () {
                if (19 == $(this).data('level')) {
                    tf.unblockUI();
                }
            })
        }

        $('#blockUI').html();
        $('#blockUI').data('level', level);

        $('#blockUI').animate({opacity: 'show'}, 500, function () {
            if (typeof callback === 'function') {
                callback($('#blockUI'));
            }
        });
    }

    /**
     * UnblockUI
     */

    tf.unblockUI = function () {
        if ($('#blockUI').size()) $('#blockUI').hide().remove();
    }

    // export
    w.tf = tf;

})(this, this.jQuery);