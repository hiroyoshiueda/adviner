<?php
function smarty_modifier_array_to_string($array, $join="<br />", $array_data=null)
{
	if (is_array($array)) {
		if ($array_data === null) {
			return join($join, $array);
		}
		$new_arr = array();
		foreach ($array as $val) {
			if ($val!='' && isset($array_data[$val]) && $array_data[$val]!='') {
				$new_arr[] = $array_data[$val];
			}
		}
		return join($join, $new_arr);
	}
	return $array;
}
?>