<?php
function smarty_modifier_auto_filesize($size, $decimals=0)
{
	$unit = ' B';
	if ($size>0) {
		// MB
		if ($size >= 1048576) {
			$size = $size / 1048576;
			$unit = ' MB';
		// KB
		} else if ($size >= 1024) {
			$size = $size / 1024;
			$unit = ' KB';
		}
	}
	return number_format($size, $decimals) . $unit;
}
?>