<?php
function smarty_modifier_nolink($html)
{
	return preg_replace('/(<a[^>]+)href="([^"]+)"/i', '$1href="#"', $html);
}
?>