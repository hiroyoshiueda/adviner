<?php
function smarty_modifier_more_message($message, $limit=100, $is_makelink=true, $is_br=true)
{
	$html = '';
	if (mb_strlen($message)>$limit) {
		$id_str = sprintf("more-message-%s", uniqid(rand()));
		$html  = '<span>';
		$html .= htmlspecialchars(mb_substr($message, 0, $limit), ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
		//$html .= mb_substr($message, 0, $limit);
		$html .= '</span>';
		$html .= '<span style="display:none;" id="'.$id_str.'">';
		$html .= htmlspecialchars(mb_substr($message, $limit), ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
		//$html .= mb_substr($message, $limit);
		$html .= '</span>';
		$html .= '<a href="#" onclick="$(\'#'.$id_str.'\').show();$(this).hide();return false;" style="display:block;">すべてを読む</a>';
	} else {
		$html = htmlspecialchars($message, ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
		//$html = $message;
	}
	if ($is_makelink) $html = app_create_link($html);
	if ($is_br) $html = nl2br($html);
	return $html;
}
?>