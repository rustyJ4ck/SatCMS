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
function smarty_modifier_ngUrl($url)
{
    static $ie_version;

    if (!isset($ie_version)) {
        preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
        $ie_version = @$matches[1] ?: false;
    }

    if ($ie_version && $ie_version <= 9) {
        $url = '/editor/#!' . $url;
    }

/*
Edit: IE11 will change the user agent syntax to prevent all previous useragent sniffing from working.
Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko
*/


    return $url;
}
