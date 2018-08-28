<?php
/**
 * 設定変更(Form)
 * @author Hiroyoshi
 */
class UserSettingForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('profile_msg', '自己紹介は4000文字以下で入力してください。', 'maxlengthZen', array(4000))
		)
	);

	protected $uniforms = array(
		array(
			'profile_msg' => 'KV',
			'is_edit_profile_msg' => 'int',
			'consult_mail_to' => 'int',
			'consult_reply_to' => 'int',
			'advice_reply_to' => 'int',
			'consult_review_to' => 'int',
			'is_edit_mail_to' => 'int'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
