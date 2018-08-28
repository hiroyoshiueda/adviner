<?php
function smarty_modifier_string_width($str, $start, $length, $marker)
{
    return mb_strimwidth($str, $start, $length, $marker);
}
?>