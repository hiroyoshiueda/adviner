<?php
/**
 * @param unknown_type $url
 * @param unknown_type $size small(15px) or medium(20px) or standard(24px) or tall(60px)
 * @param unknown_type $count
 */
function smarty_modifier_social_g_plusone($url, $size='medium', $count='false')
{
	if ($size == 'tall') $count = 'true';
	$html = '<div class="g-plusone" data-size="'.$size.'" data-count="'.$count.'" data-href="'.htmlspecialchars($url,ENT_QUOTES).'"></div>';
//	$html = '<g:plusone size="medium" count="false" href="'.$url.'"></g:plusone>';
	return $html;
}
?>