<?php
function smarty_modifier_datetime_f($date)
{
	if (empty($date)) return '';
	$d = preg_split('/[\/\- :]+/', $date);
	return sprintf('%04d/%d/%d %02d:%02d',$d[0],$d[1],$d[2],$d[3],$d[4]);
}
?>