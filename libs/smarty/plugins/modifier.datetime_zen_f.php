<?php
function smarty_modifier_datetime_zen_f($datetime)
{
	if (empty($datetime)) return '';
	$d = preg_split('/[\/\- :]+/', $datetime);
	$t = mktime($d[3],$d[4],0,$d[1],$d[2],$d[0]);
	$days = array ("日", "月", "火", "水", "木", "金", "土");
	if (strlen($datetime)>10) {
		return sprintf('%04d年%d月%d日（%s）%02d:%02d',$d[0],$d[1],$d[2],$days[date('w', $t)],$d[3],$d[4]);
	} else {
		return sprintf('%04d年%d月%d日（%s）',$d[0],$d[1],$d[2],$days[date('w', $t)]);
	}
}
?>