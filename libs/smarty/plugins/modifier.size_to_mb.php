<?php
function smarty_modifier_size_to_mb($size, $decimals=0)
{
	if ($size>0) {
		$size = $size / (1024 * 1024);
	}
	return number_format($size, $decimals);
}
?>