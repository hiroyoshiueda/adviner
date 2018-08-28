<?php
/**
 * API - 相談返信(Form)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class ApiResponseForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('reply_body', '送信内容を入力してください。', 'required'),
			array('reply_body', '一度の送信内容は全角2000文字以下で入力してください。', 'maxlengthZen', array(2000))
		),
		array(
			array('evaluate_type', '評価を選択してください。', 'required'),
			array('review_body', '評価コメントを入力してください。', 'required'),
			array('review_body', '評価コメントは全角2000文字以下で入力してください。', 'maxlengthZen', array(2000))
		),
		array(
			array('please_body', '相談内容を入力してください。', 'required'),
			array('please_body', '相談内容は全角800文字以下で入力してください。', 'maxlengthZen', array(800))
		),
	);

	protected $uniforms = array(
		array(
			'id' => 'int',
			'reply_opt' => 'int',
			'reply_body' => 'KV',
			'review_body' => 'KV',
			'secret_flag' => 'int',
			'review_share' => 'int',
			'evaluate_type' => 'int',
			'review_public_flag' => 'int',
			'please_body' => 'KV',
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
