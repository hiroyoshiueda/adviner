<?php
/**
 * 相談窓口登録フォーム(Form)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserAdviceContactForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('category_id', 'アドバイスできる分野を選択してください。', 'required'),
			array('advice_title', 'アドバイスできることを入力してください。', 'required'),
			array('advice_title', 'アドバイスできることは全角40文字以下で入力してください。', 'maxlengthZen', array(40)),
			array('advice_body', 'アドバイスできる詳細を入力してください。', 'required'),
			array('advice_body', 'アドバイスできる詳細は全角800文字以下で入力してください。', 'maxlengthZen', array(800)),
			array('advice_tag', 'キーワードは全角50文字以下で入力してください。', 'maxlengthZen', array(50)),
			array('agree', '相談・アドバイスする時のガイドラインを読んで理解したらチェックを入れてください。', 'required')
		)
	);

	protected $uniforms = array(
		array(
			'id' => 'int',
			'category_id' => 'int',
			'advice_title' => 'KV',
			'advice_body' => 'KV',
			'advice_tag' => 'aKVS',
			'public_type' => 'int',
			'comment_status' => 'int',
			'charge_flag' => 'int',
			'charge_price' => 'int',
			'charge_count' => 'int',
			'charge_body' => 'KV',
			'default_category_id' => 'int'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
