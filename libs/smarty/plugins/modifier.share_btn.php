<?php
function smarty_modifier_share_btn($permalink, $pagetitle, $pagebody='')
{
	if ($pagebody != '') {
		$pagetitle = $pagebody;
	}
//	$btn  = '<div class="ad_share" data-href="'.htmlspecialchars($permalink, ENT_QUOTES).'" data-title="'.htmlspecialchars($pagetitle, ENT_QUOTES).'">';
//	$btn .= '<a class="ad_share_button" onclick="Adviner.openShareBox(this);" title="ソーシャルメディアで共有する"><span>Share</span></a>';
//	$btn .= '</div>';
	$btn  = '';
	return $btn;
}
?>