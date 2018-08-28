<?php
function smarty_modifier_size_to_kb($size)
{
	if ($size>0) {
		$size = $size / 1024;
	}
	return number_format($size);
}
?>