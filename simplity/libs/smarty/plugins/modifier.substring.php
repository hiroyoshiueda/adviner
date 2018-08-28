<?php
function smarty_modifier_substring($str, $start, $length)
{
    return mb_substr($str, $start, $length);
}
?>