<?php
/**
 * {tag type="checkbox" name="" value="" checked="" label=""}
 * {tag type="select" name="" options="" selected=""}
 * @param $params
 * @param $smarty
 * @return unknown_type
 */
function smarty_function_tag($params, &$smarty)
{
	require_once(SMARTY_PLUGINS_DIR . 'shared.escape_special_chars.php');

	if (isset($params['name']) === false) {
		$smarty->trigger_error('tag: the "name" attribute not found.', E_USER_WARNING);
	}

	$extra = '';
	$p = array();
	$opt = array();
	$opt_group = array();
	$tag = '';
	$sub = '';
	$is_label = false;

	if (strpos($params['type'], ':') !== false) {
		list($tag, $sub) = explode(':', $params['type']);
	} else {
		$tag = $params['type'];
	}

//	if (isset($params['id']) === false) $params['id'] = $params['name'].'_id';

	foreach($params as $key => $value) {
		switch ($key) {
			case 'type':
				if (strpos($value, 'select') !== false) {
					$p['size'] = '1';
					break;
				}
				$p[$key] = $value;
				break;
			case 'name':
			case 'id':
			case 'class':
			case 'style':
			case 'maxlength':
			case 'size':
			case 'onchange':
			case 'onclick':
			case 'tabindex':
				$p[$key] = $value;
				break;
			case 'value':
				$p[$key] = smarty_function_escape_special_chars($value);
				break;
			case 'selected':
			case 'checked':
				if ($value === null) $value = '';
				if ($params['value'] == (string)$value) $p[$key] = $key;
				break;
			case 'checks':
				if (is_array($value)) {
					foreach ($value as $str) {
						if ($params['value'] == $str) {
							$p['checked'] = 'checked';
							break;
						}
					}
				}
				break;
			case 'label':
				$is_label = true;
//				$extra = '<label for="'.$params['id'].'">'.smarty_function_escape_special_chars($value).'</label>';
//				$extra = '&nbsp;'.smarty_function_escape_special_chars($value);
				$extra = smarty_function_escape_special_chars($value);
				break;
			case 'options':
				$opt = $value;
				break;
			case 'kvoptions':
				$opt = array();
				if (is_array($value)) {
					foreach ($value as $val => $txt) {
						$opt[] = array('value'=>$val, 'text'=>$txt);
					}
				}
				break;
			case 'groups':
				$opt_group = $value;
				break;
		}
	}

	if ($sub == 'year') {
		$dt = _smarty_function_tag_split_date($params['selected']);
	} else if ($sub == 'month') {
		$params['from'] = 1;
		$params['to'] = 12;
		$dt = _smarty_function_tag_split_date($params['selected']);
	} else if ($sub == 'day') {
		$params['from'] = 1;
		$params['to'] = 31;
		$dt = _smarty_function_tag_split_date($params['selected']);
	}

//	if ($params['blank'] == 'on') {
//		if (is_array($opt)) {
//			array_unshift($opt, array('value'=>'', 'text'=>''));
//		} else {
//			$opt = array(array('value'=>'', 'text'=>''));
//		}
//	} else if ($params['blank'] != '') {
//		if (is_array($opt)) {
//			array_unshift($opt, array('value'=>'', 'text'=>$params['blank']));
//		} else {
//			$opt = array(array('value'=>'', 'text'=>$params['blank']));
//		}
//	}

	if ($params['from'] != '' && $params['to'] != '') {
		$from = (int)$params['from'];
		$to = (int)$params['to'];
		for ($i=$from; $i<=$to; $i++) {
			if ($params['format'] != '') $text = sprintf($params['format'], $i);
			else $text = $i;
			$arr = array('value'=>$i, 'text'=>$text);
			$opt[] = $arr;
		}
	}

	if ($is_label && isset($p['id'])===false) $p['id'] = $p['name'];

	$html = '';
	if ($is_label) $html .= '<label class="radio" style="cursor:pointer;">';
	if ($tag == 'select' || $tag == 'select-group') {
		if ($sub == '') {
			$selected = $params['selected'];
		} else {
			$dt = _smarty_function_tag_split_date($params['selected']);
			$selected = $dt[$sub];
		}
		unset($p['selected']);
		if ($tag == 'select') {
			$html .= _smarty_function_tag_select($p, $opt, $selected, $params['blank']);
		} else {
			$html .= _smarty_function_tag_select_group($p, $opt, $opt_group, $selected, $params['blank']);
		}
	} else {
		$html .= _smarty_function_tag_input($p);
	}
//	if ($is_label) $html .= '<label for="'.$p['id'].'" style="cursor:pointer;">';
	$html .= $extra;
	if ($is_label) $html .= '</label>';
	return $html;
}

function _smarty_function_tag_input(&$p)
{
	$html = '<input';
	foreach($p as $key => $val) {
		if ($key == '') continue;
		$html .= ' '.$key.'="'.$val.'"';
	}
	$html .= ' />';
	return $html;
}
function _smarty_function_tag_select(&$p, &$opt, $selected, $blank)
{
	$html = '<select';
	foreach($p as $key => $val) {
		if ($key == '') continue;
		$html .= ' '.$key.'="'.$val.'"';
	}
	$html .= ">\n";
	if (count($opt)>0) {
		if (isset($blank) && $blank != '') {
			if ($blank == 'on') $blank = '';
			$html .= "<option value=\"\"";
			if ((string)$selected === '') $html .= ' selected="selected"';
			$html .= ">".htmlspecialchars($blank)."</option>\n";
		}
		foreach ($opt as $ary) {
			$html .= '<option value="'.htmlspecialchars($ary['value']).'"';
			if ((string)$ary['value'] === (string)$selected) $html .= ' selected="selected"';
			if (isset($ary['class'])) $html .= ' class="'.$ary['class'].'"';
			$html .= '>';
			$html .= htmlspecialchars($ary['text']);
			$html .= "</option>\n";
		}
	}
	$html .= "</select>\n";
	return $html;
}
function _smarty_function_tag_select_group(&$p, &$opt, &$opt_group, $selected, $blank)
{
	$html = '<select';
	foreach($p as $key => $val) {
		if ($key == '') continue;
		$html .= ' '.$key.'="'.$val.'"';
	}
	$html .= ">\n";
	if (count($opt)>0) {
		if (isset($blank) && $blank != '') {
			if ($blank == 'on') $blank = '';
			$html .= "<option value=\"\"";
			if ((string)$selected === '') $html .= ' selected="selected"';
			$html .= ">".htmlspecialchars($blank)."</option>\n";
		}
		foreach ($opt as $gid => $arr) {
			$is_group = false;
			if (isset($opt_group[$gid]) && $opt_group[$gid] != '') {
				$html .= "<optgroup label=\"".htmlspecialchars($opt_group[$gid])."\">\n";
				$is_group = true;
			}
			foreach ($arr as $ary) {
				$html .= '<option value="'.htmlspecialchars($ary['value']).'"';
				if ((string)$ary['value'] === (string)$selected) $html .= ' selected="selected"';
				if (isset($ary['class'])) $html .= ' class="'.$ary['class'].'"';
				$html .= '>';
				$html .= htmlspecialchars($ary['text']);
				$html .= "</option>\n";
			}
			if ($is_group) {
				$html .= "</optgroup>\n";
			}
		}
	}
	$html .= "</select>\n";
	return $html;
}
function _smarty_function_tag_split_date($date)
{
	$arr = array('year'=>'','month'=>'','day'=>'');
	if ($date == '') return $arr;
	if (preg_match("|(\d{2,4})[/\-\.]{1}(\d{1,2})[/\-\.]{1}(\d{1,2})|i", $date, $m)) {
		$arr['year'] = (int)$m[1];
		$arr['month'] = (int)$m[2];
		$arr['day'] = (int)$m[3];
	}
	return $arr;
}
?>