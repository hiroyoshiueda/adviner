<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-last" href="{$base_url}" title="{$form.htitle}">{$form.htitle}</a>
</div>

<div id="main-content">
<div id="help" class="normal-frame">

{*<ul class="tabs bottom25">
<li><a href="/service/" title="Advinerについて">Advinerについて</a></li>
<li><a href="/service/charge" title="有料アドバイスについて">有料アドバイスについて</a></li>
<li><a href="/service/pay" title="有料相談する方法">有料相談する方法</a></li>
<li class="active"><a href="/help/" title="よくある質問">よくある質問</a></li>
</ul>*}

<div class="htitle_with_bar bottom20">
<h1>{$form.htitle}</h1>
</div>

{foreach item=d from=$form.faq_category_list name="faq_category_list"}
<h1 class="ctitle">{$d.title}</h1>
	{foreach item=fd from=$form.faq_data[$d.faq_category_id] name="faq_list"}
<a name="faq{$fd.faq_id}"></a>
<div class="help_item bottom15">
	<div class="question">
		<h2><span class="number">{$smarty.foreach.faq_list.iteration}</span>{$fd.question}</h2>
	</div>
	<div class="answer">
		<p>{$fd.answer|nl2br nofilter}</p>
	</div>
</div>
	{/foreach}
{/foreach}

</div>
</div>

<div id="side-content">

{include file="_parts/ad/ad_side.tpl"}

{include file="_parts/side_feedback_form.tpl"}

{include file="_parts/ad/ad_side_text.tpl"}

</div>