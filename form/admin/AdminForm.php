<?php
/**
 * INDEX(Form)
 */
class AdminForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('adminlogin', 'ログインIDを入力してください。', 'required'),
			array('adminpassword', 'パスワードを入力してください。', 'required')
		)
	);

	protected $uniforms = array(
		array(
			'adminlogin' => 'aKV',
			'adminpassword' => 'aKV'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
