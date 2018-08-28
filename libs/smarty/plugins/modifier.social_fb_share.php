<?php
function smarty_modifier_social_fb_share($url, $type='button_count')
{
	$html = '';
	if (empty($_SERVER['HTTPS'])) {
		$html = '<a name="fb_share" type="'.$type.'" share_url="'.rawurlencode($url).'" href="http://www.facebook.com/sharer.php">Share</a>';
	}
	return $html;
}
?>