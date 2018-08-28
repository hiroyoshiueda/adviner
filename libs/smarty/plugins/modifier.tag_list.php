<?php
function smarty_modifier_tag_list($tag)
{
	if ($tag == '') return '';
	$html = '';
	$tags = mb_split('[ 　]+', $tag);
	if (is_array($tags)) {
		$html = '<ul class="tag-list">';
		foreach ($tags as $str) {
			if ($str != '') {
				$str = htmlspecialchars($str);
				$html .= '<li><a href="/search/t/'.$str.'">'.$str.'</a></li>';
			}
		}
		$html .= '</ul>';
		$html .= '<div class="clear"></div>';
	}
	return $html;
}
?>