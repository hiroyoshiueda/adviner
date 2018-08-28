<?php
function smarty_modifier_date_f($date)
{
	$date_f = '';
	if (empty($date)) return '';
	$d = preg_split('/[\/\- :]+/', $date);
	if (empty($d[2]))
	{
		$date_f = sprintf('%04d/%d',$d[0],$d[1]);
	} else {
		$date_f = sprintf('%04d/%d/%d',$d[0],$d[1],$d[2]);
	}
	return $date_f;
}
?>