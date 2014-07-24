<?php
/**
 * Smarty plugin
 * $Id: modifier.i18n.php,v 1.1.2.2 2012/09/20 06:28:32 Vova Exp $
 * @package Smarty
 * @subpackage plugins
 */


/**
 * I18N string
 * 
 * {"cars2\\body_type_id"|i18n}
 * {"Параметр"|i18n:'module.prefix':params}
 *
 * Type:     modifier<br>
 * Name:     lower<br>
 * Purpose:  convert string to lowercase
 * 
 * @param string
 * @return string
 */
function smarty_modifier_i18n($id, $prefix = '', $params = null)
{

    if ($prefix) {
        $id = $prefix . '.' . $id;
    }

    return core::lib('i18n')->T($id, $params);
}
