<div id="user_cp" style="text-align:center">
    <div id="user_cp_wrap">


        {if $user.logged_in}

            <p>You are already logged in.</p>
            <p>Proceed to user <a href="/users/cp/" class="btn btn-xs btn-primary">control panel</a>.</p>

        {else}


        <form id="frm-login"
              method="post"
              action="/users/login/"
                >

            <h3>Панель управления</h3><br/>

            <div class="input-group-lg">
                <input required="required" style="width:300px;"
                       class="form-control"
                       name="login" type="text" value=""  placeholder="Логин"
                       onfocus="this.value = '';"
                        />   <br/>
                <input required="required" style="width:300px"
                       class="form-control"
                       name="password" type="password" value="" placeholder="Пароль"
                       onfocus="this.value = '';"
                        /> <br/>
            </div>

            {csrf_token}

            <input type="hidden" name="redirect" value="/editor/"/>
            <input type="submit" class="btn btn-primary btn-lg" value="Войти" />
        </form>


        {/if}


    </div>
</div>

<script>
    {literal}

    site.ready.push(function(){

        var lastAction = 0;
        var delay = 5000;

        $('#frm-login').on('submit', function(){

            var timer = (new Date()).getTime();

            if ($('input[name=login]').val().length < 3 || $('input[name=password]').val().length < 3) {
                toastr.error('Заполните поле логин+пароль');
                return false;
            }

            if (timer > (lastAction + delay)) {

                $('input[type=submit]').prop('disabled', true);
                lastAction = (new Date()).getTime();

                $(this).ajaxSubmit({dataType : 'json', success: function(data){
                    $('input[type=submit]').prop('disabled', false);
                if (data.status) {
                    toastr.success(data.message);
                    window.location.href = data.redirect;
                } else {
                    toastr.error(data.message);
                }
            }});

        return false;
        } else {
            toastr.error('Подождите ' + Math.ceil((delay + lastAction - timer) / 1000) + ' секунд');
        }

        return false;
        });

    });

    {/literal}
</script>
