<?php
function smarty_modifier_number_format($str)
{
	if ($str === null || $str == '') $str = '0';
    return number_format($str,0);
}
?>