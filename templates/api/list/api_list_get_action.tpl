{foreach item=d from=$consult_list name="action_list"}
{include file="_parts/consult_thread/consult_thread.tpl" consult=$d user_set=$form.user_set reply_list=$form.reply_set[$d.consult_id] review_list=$form.review_set[$d.consult_id] is_first=$smarty.foreach.action_list.first}
{/foreach}
