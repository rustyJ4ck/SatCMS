        
<form action="{$config.editor_url}" method="post" name="site_mod" id="frm_">


<div class="form">

    <div class="form-block">
        <label>{$lang._users.login}</label>
        <input type="text" name="login" size="40" value="{$return.form.login}"/>
    </div> 

    <div class="form-block_highlight">
        <label>{$lang._users.password}</label>
        <input type="text" name="password" size="40" value="{$return.form.password}"/>
    </div> 
    
    <div class="form-block">
        <label>{$lang._users.nick}</label>
        <input type="text" name="nick" size="40" value="{$return.form.nick}"/>
    </div>   
    
    <div class="form-block">
        <label>{$lang.email}</label>
        <input type="text" name="email" size="40" value="{$return.form.email}"/>
    </div>      
    
    <div class="form-block hidden">
        <label>{$lang._users.active}</label>
        <input type="checkbox" name="active" value="1" {if $return.form.active}checked="checked"{/if}/>
    </div>   
    
    <!-- level -->
    
    <div class="form-block">
    <label>{$lang._users.level}</label>
    <div>
        <select name="level">        
        {* sync with users_collection::$_levels *}
        <option value="1" {if $return.form.level == 1}selected="selected"{/if}>{$lang._users.level_user}</option>
        <option value="50" {if $return.form.level == 50}selected="selected"{/if}>{$lang._users.level_mod}</option>
        <option value="100" {if $return.form.level == 100}selected="selected"{/if}>{$lang._users.level_admin}</option>
        </select>
    </div>  
    </div> 
    
    <!-- extra data -->
    
    <br clear="all"/>      
              
    <div class="form-bottom">
    <input class="main" name="item_submit" type="submit" value="Сохранить"/>
    </div>
    
  
</div>
                
<input type="hidden" name="c" value="{$req.c}"/>
<input type="hidden" name="m" value="{$req.m}"/>

<input type="hidden" name="id" value="{$req.id}"/>

</form> 

 