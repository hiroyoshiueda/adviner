<?php
function smarty_modifier_social_fb_like($url, $width='105')
{
//	$html  = '<iframe src="http://www.facebook.com/plugins/like.php?href='.rawurlencode($url);
//	$html .= '&amp;layout=button_count&amp;show_faces=false&amp;width='.$width.'&amp;action=like&amp;colorscheme=light&amp;height=21&amp;appId='.constant('APP_CONST_FACEBOOK_OAUTH_CONSUMER_KEY').'"';
//	$html .= ' scrolling="no" frameborder="0" style="border:none;overflow:hidden;width:'.$width.'px;height:21px;" allowTransparency="true"></iframe>';
//	$html = '<fb:like href="'.htmlspecialchars($url,ENT_QUOTES).'" send="false" layout="button_count" width="'.$width.'" show_faces="false" action="like" font="lucida+grande"></fb:like>';
	$html = '<div class="fb-like" data-href="'.htmlspecialchars($url,ENT_QUOTES).'" data-send="false" data-layout="button_count" data-width="'.$width.'" data-show-faces="false" data-font="lucida grande"></div>';
	return $html;
}
?>
