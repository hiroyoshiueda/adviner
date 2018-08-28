<?php
function smarty_modifier_in_array($search, $arr)
{
	if (is_array($arr)===false) return false;
	return in_array($search, $arr);
}
?>