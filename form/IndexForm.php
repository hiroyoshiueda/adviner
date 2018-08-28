<?php
/**
 * INDEX(Form)
 */
class IndexForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		'login' => array(
			array('username', 'メールアドレスを入力してください。', 'required'),
			array('username', 'メールアドレスは200文字以内で入力してください。', 'maxlength', array(200)),
			array('password', 'パスワードを入力してください。', 'required'),
			array('password', 'パスワードは6文字以上20文字以内で入力してください。', 'length_range', array(6, 20)),
			array('password', 'パスワードは半角英数記号( . @ # % $ = _ * & - )を入力してください。', 'password')
		)
	);

	protected $uniforms = array(
		array(
			'username' => 'aKV',
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
