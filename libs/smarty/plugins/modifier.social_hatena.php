<?php
/**
 * @param string $url
 * @param string $title
 * @param string $layout simple or standard or vertical
 * @return string
 */
function smarty_modifier_social_hatena($url, $title, $layout='standard')
{
	$html = '';
	if (empty($_SERVER['HTTPS'])) {
		$html  = '<a href="http://b.hatena.ne.jp/entry/'.htmlspecialchars($url,ENT_QUOTES).'" class="hatena-bookmark-button" data-hatena-bookmark-layout="'.$layout.'"';
		$html .= ' title="'.htmlspecialchars($title).'をはてなブックマークに追加"><img src="http://b.st-hatena.com/images/entry-button/button-only.gif" alt="" width="20" height="20" style="border:none;" /></a>';
		//$html .= '<script type="text/javascript" src="http://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script>';
	}
	return $html;
}
?>