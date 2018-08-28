<?php
function smarty_modifier_datetime_t($date)
{
	if (empty($date)) return '';
	$d = preg_split('/[\/\- :]+/', $date);
	$current_ts = time();
	$display_ts = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
	$difference = $current_ts - $display_ts;
	$text = '';
	if ($difference < 60) {
		$text = $difference.'秒前';
	} else if ($difference < 3600) {
		$text = number_format(($difference / 60)).'分前';
	} else if ($difference < 86400) {
		$text = number_format(($difference / 3600)).'時間前';
	} else if ($difference < 604800) {
		$text = number_format(($difference / 86400)).'日前';
	} else {
		$text = sprintf('%04d/%d/%d %02d:%02d',$d[0],$d[1],$d[2],$d[3],$d[4]);
	}
	return $text;
}
?>