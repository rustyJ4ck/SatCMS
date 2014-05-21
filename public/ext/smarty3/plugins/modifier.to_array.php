<?php
/**
 * Smarty plugin
 * $Id: modifier.i18n.php,v 1.1.2.2 2012/09/20 06:28:32 Vova Exp $
 * @package Smarty
 * @subpackage plugins
 */


/**
 * JSON Encode []
 *
 * {"id: '1', title: '2'"|array}
 * {"new: 1, dd: \'2 1\', ddd: 3"|to_array|json_encode
 *
 * Type:     modifier<br>
 * Name:     lower<br>
 * Purpose:  convert string to lowercase
 *
 * @param string
 * @return string
 */
function smarty_modifier_to_array($value)
{
    // already array
    if (is_array($value)) return $value;

    // $value = "first: 'firstValue', second: 'secondValue'";
    // $value = "first: firstValue, second: 'secondValue', third: thirdvalue";

    $matches = preg_match_all('@(?P<id>[a-z]+)\s*\:\s*([\"\'])?(?P<value>[^\,\'\"]+)((\s*[\"\']\s*,\s*)?)@i', $value, $m, PREG_SET_ORDER);

    if (!$matches) {
        return array();
    }

    $result = array();

    foreach ($m as $v) {
        $result [$v['id']] = trim($v['value']);

        // values yes|no casts to int(bool)
        $result [$v['id']] === 'yes' && $result [$v['id']] = 1;
        $result [$v['id']] === 'no'  && $result [$v['id']] = 0;
    }

    return $result;
}
