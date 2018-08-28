<?php
/**
 * Q&A質問登録(Form)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class QaQuestionForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('question_title', '質問タイトルを入力してください。', 'required'),
			array('question_title', '質問タイトルは全角40文字以下で入力してください。', 'maxlengthZen', array(40)),
			array('question_body', '質問内容を入力してください。', 'required'),
			array('question_body', '質問内容は全角800文字以下で入力してください。', 'maxlengthZen', array(800)),
			array('category_id', 'カテゴリーを選択してください。', 'required'),
			//array('agree', '相談・アドバイスする時のガイドラインを読んで理解したらチェックを入れてください。', 'required')
		)
	);

	protected $uniforms = array(
		array(
			'id' => 'int',
			'question_id' => 'int',
			'question_title' => 'KV',
			'question_body' => 'KV',
			'category_id' => 'int',
			'limit_type' => 'int'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
