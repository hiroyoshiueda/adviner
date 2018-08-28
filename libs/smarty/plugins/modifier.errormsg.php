<?php
function smarty_modifier_errormsg($errors)
{
	if (empty($errors)) return '';
	$errors = (is_array($errors)) ? join("\n", $errors) : $errors;
	$errors = htmlspecialchars($errors, ENT_QUOTES);
	return '<p class="errormsg-text">'.nl2br($errors).'</p>';
}
?>