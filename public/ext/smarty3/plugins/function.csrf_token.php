<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * CSRF Token
 */
function smarty_function_csrf_token()
{
    return '<input type="hidden" name="x_token" value="' . core::lib('auth')->token() . '" />';
}