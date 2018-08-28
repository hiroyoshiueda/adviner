{foreach from=$footer_scripts item=d}
<script src="{$d.src}{$d.ver}">{$d.code nofilter}</script>
{/foreach}
