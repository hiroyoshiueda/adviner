<?php
function smarty_modifier_social_c_fb_share($url)
{
	$href  = 'http://www.facebook.com/share.php?u='.rawurlencode($url);
	$html  = '<a href="'.$href.'" class="c_fb_share_button" title="Facebookで共有する"';
	$html .= ' onclick="window.open(this.href,\'share_facebook_window\',\'width=550,height=450,personalbar=0,toolbar=0,scrollbars=1,resizable=1\');return false;">Share</a>';
	return $html;
}
?>