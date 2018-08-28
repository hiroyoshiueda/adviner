<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty escape modifier plugin
 *
 * Type:     modifier<br>
 * Name:     sp_escape<br>
 * Purpose:  Escape the string according to escapement type
 * @param string
 * @return string
 */
function smarty_modifier_sp_escape($string)
{
	if (is_array($string)) return $string;
	return htmlspecialchars($string, ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
}
?>