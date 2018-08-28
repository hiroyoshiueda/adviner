{foreach item=d from=$list name="qa_list"}
{assign var="user" value=$form.user_set[$d.question_user_id]}
<div class="qa-items"{if $smarty.foreach.qa_list.first && $is_top} style="border:none;"{/if}>
	<div class="qa-user">
		<span class="profile-frame-50">{profile_img user=$user size=50}</span>
		<div class="username">{$user.nickname}</div>
	</div>
	<div class="qa-data">
		<p class="qa-body"><a>{$d.question_body|makebody:false nofilter}</a></p>
		<div class="qa-tool">
			<ul>
				<li><a href="#{$d.question_id}" class="answer-form-btn" title="回答する">回答する</a></li>
				<li>・</li>
				<li>{$d.createdate|datetime_t}</li>
			</ul>
			<div class="clear"></div>
		</div>
	</div>
	<div class="clear"></div>
</div>
{/foreach}