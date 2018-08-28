<?php
function smarty_modifier_join($var, $j='')
{
	if (is_array($var)===false) return $var;
	return join($j, $var);
}
?>