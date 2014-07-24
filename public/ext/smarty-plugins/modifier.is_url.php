<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: modifier.is_url.php,v 1.1.2.2 2012/09/20 12:16:26 Vova Exp $
 */


/**
 * Smarty is_url modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */
function smarty_modifier_is_url($url, $origurl = null)
{
    if (empty($url)) {
        return false;
    }

    if (!isset($origurl)) {
        $origurl = urldecode($_SERVER['REQUEST_URI']);
    }

    return (strings::strpos($origurl, $url) === 0) ? true : false;
}

