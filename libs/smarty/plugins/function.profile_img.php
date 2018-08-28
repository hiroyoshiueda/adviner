<?php
/**
 * {profile_img user=$user size=70 is_href=true is_blank=true}
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_profile_img($params, &$smarty)
{
//	if (isset($params['user'])===false) $smarty->trigger_error('plugin "profile_img": missing or empty parameter: user');
//	if (isset($params['size'])===false) $smarty->trigger_error('plugin "profile_img": missing or empty parameter: size');

	$user = $params['user'];
	$size = $params['size'];
	// default
	$is_href = isset($params['is_href']) ? $params['is_href'] : true;
	$is_blank = isset($params['is_blank']) ? $params['is_blank'] : false;
	$img_url = constant('APP_CONST_IMG_URL');

	$nickname = htmlspecialchars($user['nickname'], ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);

	if (empty($user))
	{
		$is_href = false;
		$html = '<img src="'.$img_url.'/img/profile-default.png" width="'.$size.'" height="'.$size.'" alt="　" />';
	}
	else if ($size > 0 && $size <= 50)
	{
		$html = '<img src="'._change_protocol_profile_img($user['profile_s_path']).'" width="'.$size.'" height="'.$size.'" alt="'.$nickname.' さん" />';
	}
	else if ($size > 100)
	{
		$html = '<img src="'._change_protocol_profile_img($user['profile_b_path']).'" width="'.$size.'" height="'.$size.'" alt="'.$nickname.' さん" />';
	}
	else
	{
		$html = '<img src="'.$img_url.'/img/spacer.gif" style="background-image:url(\''._change_protocol_profile_img($user['profile_path']).'\');width:'.$size.'px;height:'.$size.'px;" width="'.$size.'" height="'.$size.'" class="profile-normal" alt="'.$nickname.' さん" />';
	}

	if ($is_href)
	{
		$attr_target = $is_blank ? ' target="_blank"' : '';
		$html = '<a href="/profile/'.$user['user_id'].'/" title="'.$nickname.' さん"'.$attr_target.'>'.$html.'</a>';
	}

	return $html;
}
function _change_protocol_profile_img($src_url)
{
	if (empty($_SERVER['HTTPS'])) {
		$src_url = str_replace('https://', 'http://', $src_url);
	}
	return $src_url;
}
?>