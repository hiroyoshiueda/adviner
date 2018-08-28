<?php
function smarty_modifier_list_message($message, $limit=0, $is_escape=true)
{
	$message = preg_replace('/[　\s\r\n\t]+/u', '', $message);
	if ($limit>0) {
		$s = mb_strlen($message)>$limit ? '...' : '';
		$message = mb_substr($message, 0, $limit) . $s;
	}
	if ($is_escape) $message = htmlspecialchars($message, ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
	return $message;
}
?>