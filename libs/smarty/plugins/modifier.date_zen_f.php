<?php
function smarty_modifier_date_zen_f($date, $wname=true)
{
	if (empty($date)) return '';
	$d = preg_split('/[\/\- :]+/', $date);
	if ($wname) {
		$t = mktime(0,0,0,$d[1],$d[2],$d[0]);
		$days = array ("日", "月", "火", "水", "木", "金", "土");
		return sprintf('%04d年%d月%d日（%s）',$d[0],$d[1],$d[2],$days[date('w', $t)]);
	} else {
		return sprintf('%04d年%d月%d日',$d[0],$d[1],$d[2]);
	}
}
?>