<?php
/**
 * 設定 - 銀口座設定(Form)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserSettingAccountForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('bank_name', '銀行名を入力してください。', 'required'),
			array('bank_name', '銀行名は100文字以下で入力してください。', 'maxlengthZen', array(100)),
			array('bank_code', '銀行コードを入力してください。', 'required'),
			array('bank_code', '銀行コードは半角数字で入力してください。', 'number'),
			array('bank_code', '銀行コードは4桁以下で入力してください。', 'maxlength', array(4)),
			array('branch_name', '支店名を入力してください。', 'required'),
			array('branch_name', '支店名は100文字以下で入力してください。', 'maxlengthZen', array(100)),
			array('branch_code', '支店コードを入力してください。', 'required'),
			array('branch_code', '支店コードは半角数字で入力してください。', 'number'),
			array('branch_code', '支店コードは3桁以下で入力してください。', 'maxlength', array(4)),
			array('deposit_type', '口座の種類を選択してください。', 'required'),
			array('bank_number', '口座番号を入力してください。', 'required'),
			array('bank_number', '口座番号は半角数字で入力してください。', 'number'),
			array('bank_number', '口座番号は7桁以下で入力してください。', 'maxlength', array(7)),
			array('bank_holder', '口座名義を入力してください。', 'required'),
			array('bank_holder', '口座名義は100文字以下で入力してください。', 'maxlengthZen', array(100))
		)
	);

	protected $uniforms = array(
		array(
			'bank_name' => 'KV',
			'bank_code' => 'a',
			'branch_name' => 'KV',
			'branch_code' => 'a',
			'deposit_type' => 'int',
			'bank_number' => 'a',
			'bank_holder' => 'SCKV'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
