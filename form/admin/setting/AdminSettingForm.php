<?php
/**
 * INDEX(Form)
 */
class AdminSettingForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('login', 'ログインIDを入力してください。', 'required'),
			array('password', 'パスワードを入力してください。', 'required')
		)
	);
	protected $uniforms = array(
		array(
			'login' => 'aKV',
			'password' => 'aKV'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
