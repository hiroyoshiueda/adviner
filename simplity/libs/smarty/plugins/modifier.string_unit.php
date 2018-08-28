<?php
function smarty_modifier_string_unit($str, $unit, $empty='')
{
	if ($str==$empty) return '';
    return $str . $unit;
}
?>