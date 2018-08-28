<?php
function smarty_modifier_default($str, $default='')
{
	if ($str===null || $str=='') return $default;
    return $str;
}
?>