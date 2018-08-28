<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty capitalize modifier plugin
 *
 * Type:     modifier<br>
 * Name:     social_twitter<br>
 * Purpose:  Twitter button is displayed.
 *
 * {@internal {$url|social_twitter:$title:"horizontal":"advinercom":"advinercom"} is the fastest option for MBString enabled systems }}
 *
 * @param string $url
 * @param string $title
 * @param string $count none or horizontal or vertical
 * @param string $via
 * @param string $related
 */
function smarty_modifier_social_twitter($url, $title='', $count='horizontal', $via='advinercom', $related='advinercom')
{
	if ($count == 'vertical') {
		$width = '63';
		$height = '62';
		$count_class = 'twitter-count-vertical';
	} else {
		$width = '100';
		$height = '20';
		$count_class = 'twitter-count-horizontal';
	}
//	$html  = '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.htmlspecialchars($url,ENT_QUOTES).'"';
//	$html .= ' data-text="'.htmlspecialchars($title,ENT_QUOTES).'"';
//	$html .= ' 	data-count="'.$count.'" data-via="'.$via.'" data-related="'.$related.'" data-lang="en">Tweet</a>';
	$html  = '<iframe scrolling="no" frameborder="0" title="Twitterで共有する" style="width:'.$width.'px;height:'.$height.'px"';
	$html .= ' src="//platform.twitter.com/widgets/tweet_button.html?count='.$count;
	$html .= '&amp;lang=en';
	$html .= '&amp;text='.rawurlencode($title);
	$html .= '&amp;url='.rawurlencode($url);
	if ($via != '') $html .= '&amp;via='.$via;
	if ($related != '') $html .= '&amp;related='.$related;
	$html .= '" class="twitter-share-button '.$count_class.'" tabindex="0" allowtransparency="true"></iframe>';
	return $html;
}
?>