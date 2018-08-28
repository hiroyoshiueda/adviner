<?php
/**
 * {google_analytics}
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_google_analytics($params, &$smarty)
{
	$html = '';

	if (app_is_notip() || constant('APP_GA_ACOUNT')=='') return $html;

	$ga_acount = constant('APP_GA_ACOUNT');

	//  _gaq.push(['_setDomainName', '.adviner.com']);
	$html = <<<STR
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '{$ga_acount}']);
_gaq.push(['_trackPageview']);
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
STR;
	return $html;
}
?>