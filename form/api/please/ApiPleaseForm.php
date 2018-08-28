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
			array('reply_body', 'アドバイスを入力してください。', 'required'),
			array('reply_body', 'アドバイスは全角2000文字以下で入力してください。', 'maxlengthZen', array(2000))
		)
	);

	protected $uniforms = array(
		array(
			'consult_id' => 'int',
			'advice_id' => 'int',
			'reply_body' => 'KV'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
