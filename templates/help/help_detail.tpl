<div id="main-content">
<div class="page-frame">
<div id="help">

<h1 class="htitle_with_line bottom20">{$form.htitle}</h1>

{foreach item=d from=$form.faq_list name="faq_list"}
<a name="faq{$d.faq_id}"></a>
<div class="help_item">
<div class="question">
	<h2><span class="number">{$smarty.foreach.faq_list.iteration}</span>{$d.question}</h2>
</div>
<div class="answer">
	<p>{$d.answer|nl2br nofilter}</p>
</div>
</div>
{/foreach}

</div>
</div>
</div>

<div id="side-content">

{include file="_parts/side_faq_menu.tpl"}

{include file="_parts/side_feedback_form.tpl"}

</div>
