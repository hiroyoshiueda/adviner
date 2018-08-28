<?php
function smarty_modifier_length($var)
{
	if (is_array($var)) {
		return count($var);
	} else {
		return mb_strlen($var);
	}
}
?>