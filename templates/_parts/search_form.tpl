{if $userInfo}
<div class="bottom15">
<div id="search-frame{$prefix}">
<div id="search-sort-area">
<ul id="search-sort-list">
<li><a href="{$base_url}"{if $form.sort==""} class="sort_selected"{/if}>人気順</a></li>
<li class="sort_last"><a href="{$base_url}?sort=created"{if $form.sort=="created"} class="sort_selected"{/if}>新着順</a></li>
</ul>
<div class="clear"></div>
</div>
{if $show_search_category!==false}
<div id="search-keyword-area">
{tag type="select" id="search-category" name="search_category" kvoptions=$AppConst.mainCategorys blank="すべて" selected=$form.search_category onchange="Adviner.postSearch();"}
</div>
{/if}
<div class="clear"></div>
</div>
</div>
{/if}
