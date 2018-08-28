<?php
/**
 * サインアップ(Form)
 *
 * Copyright(c) 2011 Esora Inc. All Rights Reserved.
 * http://www.e-sora.co.jp/
 *
 * @author Esora Inc.
 */
class UserSignupForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('profile_msg', '自己紹介は4000文字以下で入力してください。', 'maxlengthZen', array(4000)),
//			array('agree', '「Adviner 利用規約」及び「相談・アドバイスする時のガイドライン」に同意の上、チェックボックスにチェックを入れてください。', 'required')
		)
	);

	protected $uniforms = array(
		array(
			'profile_msg' => 'KV',
			'consult_mail_to' => 'int',
			'consult_reply_to' => 'int',
			'advice_reply_to' => 'int',
			'consult_review_to' => 'int',
			'agree' => 'int',
			'is_edit_profile_msg' => 'int',
			'is_edit_mail_to' => 'int',
			'signup_fb_share' => 'int'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
