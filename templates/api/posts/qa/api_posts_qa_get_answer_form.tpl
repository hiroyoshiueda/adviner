<div id="answer-post-form-{$question.question_id}" class="answer-post-form">
	<textarea id="answer-form-textarea-{$question.question_id}" name="answer_form_input[]" class="input_autosize"></textarea>
	<div class="answer-post-form-btn">
		<ul>
			<li><a href="#{$question.question_id}" class="small-btn green-btn answer-post-btn">回答する</a></li>
			<li><a href="#{$question.question_id}" class="answer-cancel-btn">キャンセル</a></li>
		</ul>
		<div class="clear"></div>
	</div>
</div>
