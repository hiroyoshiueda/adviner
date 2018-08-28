<?php
function smarty_modifier_username($user, $icon=false, $icon_size=32)
{
	$html = '';
	if (empty($user['user_id'])) {
		if ($icon) {
			$html = '<img src="/img/profile-default.png" width="'.$icon_size.'" height="'.$icon_size.'" class="icon-middle" alt="" /> ';
		}
		$html .= '<a class="username">';
		$html .= '退会ユーザー';
		$html .= '</a>';
	}
	else
	{
		if ($icon) {
			$html = '<img src="'.$user['profile_s_path'].'" width="'.$icon_size.'" height="'.$icon_size.'" class="icon-middle" alt="" /> ';
		}
		$nickname = htmlspecialchars($user['nickname'], ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
		$html .= '<a href="/profile/'.$user['user_id'].'/" title="'.$nickname.'のプロフィール" class="username">';
		$html .= $nickname;
		$html .= '</a>';
	}
	return $html;
}
?>