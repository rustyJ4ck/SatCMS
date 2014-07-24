<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty date_format_ex modifier plugin
 */
 
function smarty_modifier_date_diff_string($begin, $end = 0)
{ 
    return strings::date_diff($begin, $end);
}
