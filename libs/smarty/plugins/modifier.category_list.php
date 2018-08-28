<?php
function smarty_modifier_category_list($category_id, &$category_set, $href_target='')
{
	if (empty($category_id) || empty($category_set)) return '';

	if ($href_target!='') $href_target = ' target="'.$href_target.'"';

	$main_name = htmlspecialchars(AppConst::$mainCategorys[$category_set[$category_id]['main_category_id']], ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
	$sub_name  = htmlspecialchars($category_set[$category_id]['cname'], ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
	//$html  = '<span class="img-folder"></span>&nbsp;';
	$html  = '';
	$html .= '<a href="/category/'.$category_set[$category_id]['main_category_id'].'/" title="'.$main_name.'"'.$href_target.'>';
	$html .= $main_name;
	$html .= '</a>';
	$html .= '&nbsp;&gt;&nbsp;';
	$html .= '<a href="/category/'.$category_set[$category_id]['main_category_id'].'_'.$category_id.'/" title="'.$sub_name.'"'.$href_target.'>';
	$html .= $sub_name;
	$html .= '</a>';
	return $html;
}
?>