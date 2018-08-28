<?php
/**
 * {pager total=$total limit=$limit}
 * @param $params
 * @param $smarty
 * @return unknown_type
 */
function smarty_function_pager3($params, &$smarty)
{
	if (isset($params['limit'])===false) $smarty->trigger_error('plugin "pager": missing or empty parameter: limit');
	if (isset($params['total'])===false) $smarty->trigger_error('plugin "pager": missing or empty parameter: total');

	$posvar = empty($params['posvar']) ? 'pagenum' : $params['posvar'];
	$limit = $params['limit'];
	$total = $params['total'];

	$offset = isset($_REQUEST[$posvar]) ? (int)$_REQUEST[$posvar] : 0;

	if ($total == 0) return '';

	$prev_format = empty($params['prev_format']) ? '&laquo; 前の%d件' : $params['prev_format'];
	$next_format = empty($params['next_format']) ? '次の%d件 &raquo;' : $params['next_format'];
	$main_class = empty($params['main_class']) ? 'pagination' : $params['main_class'];
	if (empty($params['url'])) {
		$base_url = $_SERVER['REQUEST_URI'];
		$removeVars = array($posvar);
		foreach($removeVars as $tmp)	{
			$base_url = preg_replace('/(&|\?)'.$tmp.'\=[^&]*/', '', $base_url);
		}
	} else {
		$base_url = $params['url'];
	}
	$pmts = null;
	if (is_array($_POST) && count($_POST)>0) $pmts = $_POST;
	if (is_array($params['params'])) $pmts = $pmts + $params['params'];
	$show_disable = empty($params['show_disable']) ? 1 : $params['show_disable'];
	$max_page = empty($params['max_page']) ? 10 : $params['max_page'];

	$total_page = (int)ceil($total / $limit);
	$current_page = ($offset<1) ? 1 : $offset;
//	if ($total_page == 1) return '';

	$start = ($current_page <= $max_page) ? 1 : $current_page - ($max_page/2);
	$end = $start + $max_page;
	if ($end > $total_page) $end = $total_page;

	if (is_array($pmts)) {
		$arr = array();
		foreach ($pmts as $k => $v) {
			if ($k == $posvar) continue;
			$arr[] = $k . '=' . $v;
		}
		$base_url .= (strpos($base_url, '?')===false) ? '?' : '&';
		$base_url .= implode('&', $arr);
	}
	$sep = (strpos($base_url, '?')===false) ? '?' : '&';
	$base_url .= $sep.$posvar.'=%d';

	$html = '<div class="'.$main_class.'"><ul>';

	if ($start>1 || $current_page>1) {
		$off = $current_page -1;
		$url = $base_url;
		if ($off>0) $url = sprintf($base_url, $off);
		$html .= '<li><a href="'.$url.'" class="prev">'.sprintf($prev_format, $limit).'</a></li>';
	} else {
		if ($show_disable>0) $html .= '<li class="disabled"><a>'.sprintf($prev_format, $limit).'</a></li>';
	}

	if ($start < $end) {
		while ($start<=$end) {
			$off = $start;
			$url = $base_url;
			if ($off>0) $url = sprintf($base_url, $off);
			$selected = ($start == $current_page) ? ' class="active"' : '';
			$html .= '<li'.$selected.'><a href="'.$url.'">' . $start . '</a></li>';
			$start++;
		}
	} else {
		return '';
	}

	if ($current_page<$total_page) {
		$url = sprintf($base_url, $current_page + 1);
		$html .= '<li><a href="'.$url.'" class="next">'.sprintf($next_format, $limit).'</a></li>';
	} else {
		if ($show_disable>0) $html .= '<li class="disabled"><a>'.sprintf($next_format, $limit).'</a></li>';
	}

	$html .= '</ul></div>';

	return $html;
}
?>