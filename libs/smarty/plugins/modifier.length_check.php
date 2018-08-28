<?php
function smarty_modifier_length_check($str, $length)
{
	if (empty($str)) return $length;
	else return $length - mb_strlen($str);
}
?>