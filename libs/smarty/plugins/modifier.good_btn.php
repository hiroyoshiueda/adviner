<?php
function smarty_modifier_good_btn($permalink, $good, $is_login, $is_frame=true)
{
	$good_count = empty($good['good_count'][$permalink]) ? 0 : $good['good_count'][$permalink];
	$user_count = empty($good['user_count'][$permalink]) ? 0 : $good['user_count'][$permalink];

	if ($is_login && $user_count > 0)
	{
		$btn = '<span class="ad_good_button_text">Cancel</span>';
		$evt_class = 'ad_good_cancel ad_good_ok';
		$title = 'Goodを取り消す';
	}
	else
	{
		$btn = '<span class="ad_good_button_text">Good</span>';
		if ($is_login) {
			$evt_class = 'ad_good_send';
			$title = 'Goodを押す';
		} else {
			$evt_class = 'ad_good_login';
			$title = 'Goodを押すにはログインしてください';
		}
	}
	$btn = '<a href="'.htmlspecialchars($permalink, ENT_QUOTES).'" class="ad_good_button_face '.$evt_class.'" title="'.$title.'">'.$btn.'</a>';
	$btn = '<span class="ad_good_button">'.$btn.'</span>';

	$cnt = '<a class="ad_good_count_face"><span class="ad_good_count_text">'.$good_count.'</span></a>';
	$cnt = '<span class="ad_good_count">'.$cnt.'</span>';

	$btn .= $cnt;

	if ($is_frame) {
		$btn = '<div class="ad_good" data-href="'.htmlspecialchars($permalink, ENT_QUOTES).'">'.$btn.'<div style="clear:left;"></div></div>';
	}

	return $btn;
}
//function smarty_modifier_good_btn($permalink, $good_count, $user_count, $is_login, $is_frame=true)
//{
//	$good_count = empty($good_count) ? 0 : $good_count;
//	$user_count = empty($user_count) ? 0 : $user_count;
//
//	$btn = '<span class="ad_good_button_count">'.$good_count.'</span>';
//	if ($user_count > 0)
//	{
//		$btn .= '<span class="ad_good_button_text">GOOD!</span>';
//		$evt_class = 'ad_good_cancel ad_good_ok';
//	}
//	else
//	{
//		$btn .= '<span class="ad_good_button_text">GOOD!</span>';
//		if ($is_login) {
//			$evt_class = 'ad_good_send';
//		} else {
//			$evt_class = 'ad_good_login';
//		}
//	}
//	$btn = '<span class="ad_good_button_context">'.$btn.'</span>';
//	$btn = '<a href="'.htmlspecialchars($permalink, ENT_QUOTES).'" class="ad_good_button_face '.$evt_class.'">'.$btn.'<span class="ad_good_cancel_text" style="display:none;">取り消す</span></a>';
//	if ($is_frame) {
//		$btn = '<div class="ad_good" data-href="'.htmlspecialchars($permalink, ENT_QUOTES).'">'.$btn.'</div>';
//	}
//	return $btn;
//}
?>