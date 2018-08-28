<?php
/**
 * @param string $url
 * @param string $title
 * @param string $count none or horizontal or vertical
 */
function smarty_modifier_social_c_twitter($url, $title='', $count='horizontal', $via='advinercom')
{
	$href  = 'http://twitter.com/share?count='.$count.'&amp;original_referer='.rawurlencode($url).'&amp;text='.rawurlencode($title).'&amp;url='.rawurlencode($url).'&amp;via='.$via.'&amp;related='.$via;
	$html  = '<a href="'.$href.'" class="c_twitter_button" title="Twitterで共有する"';
	$html .= ' onclick="window.open(this.href,\'share_twitter_window\',\'width=550,height=450,personalbar=0,toolbar=0,scrollbars=1,resizable=1\');return false;">Share</a>';
	return $html;
}
?>