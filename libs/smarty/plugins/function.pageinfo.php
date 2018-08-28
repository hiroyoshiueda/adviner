<?php
/**
 * {pager total=$total limit=$limit}
 * @param $params
 * @param $smarty
 * @return unknown_type
 */
function smarty_function_pageinfo($params, &$smarty)
{
	if (isset($params['limit'])===false) $smarty->trigger_error('plugin "pager": missing or empty parameter: limit');
	if (isset($params['total'])===false) $smarty->trigger_error('plugin "pager": missing or empty parameter: total');

	$posvar = empty($params['posvar']) ? 'offset' : $params['posvar'];
	$limit = $params['limit'];
	$total = $params['total'];

	$offset = isset($_REQUEST[$posvar]) ? (int)$_REQUEST[$posvar] : 0;
	$format = empty($params['format']) ? '%d件中 %d件～%d件を表示' : $params['format'];
	$main_class = empty($params['main_class']) ? 'pageinfo' : $params['main_class'];

	$end = ($offset+$limit>$total) ? $total : $offset+$limit;
	$start = ($total>0) ? $offset+1 : 0;
	return '<div class="'.$main_class.'">'.sprintf($format, $total, $start, $end).'</div>';
}
?>