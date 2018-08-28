<?php
function smarty_modifier_errorclass($errors)
{
	if (empty($errors)) return '';
	return ' error';
}
?>