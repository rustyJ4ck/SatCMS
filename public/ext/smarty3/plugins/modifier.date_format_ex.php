<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty date_format_ex modifier plugin
 */
 
function smarty_modifier_date_format_ex($in, $format = 'd.m.Y H:i')
{ 
  $in = empty($in) ? time() : $in;  
  if (!ctype_digit($in)) $in = strtotime($in);
  $out = date($format, $in);
  return $out;
}

