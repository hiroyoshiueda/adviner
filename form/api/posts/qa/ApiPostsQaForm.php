<?php
/**
 * Q&A投稿API(Form)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiPostsQaForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('question_body', '質問内容を入力してください。', 'required'),
			array('question_body', '質問内容は全角800文字以下で入力してください。', 'maxlengthZen', array(800))
		)
	);

	protected $uniforms = array(
		array(
			'question_body' => 'KV',
			'is_fb_share' => 'int'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
