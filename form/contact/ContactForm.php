<?php
/**
 * お問い合わせ(Form)
 * @author Hiroyoshi
 */
class ContactForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('subject', '件名を入力してください。', 'required'),
			array('subject', '件名は30文字以下で入力してください。', 'maxlengthZen', array(30)),
			array('body', '問い合わせ内容を入力してください。', 'required'),
			array('body', '問い合わせ内容は400文字以下で入力してください。', 'maxlengthZen', array(400)),
			array('username', 'お名前を入力してください。', 'required'),
			array('username', 'お名前は20文字以下で入力してください。', 'maxlengthZen', array(20)),
			array('useremail', 'メールアドレスを入力してください。', 'required'),
			array('useremail', '正しいメールアドレスの書式で入力してください。', 'email'),
			array('useremail', 'メールアドレスは200文字以下で入力してください。', 'maxlength', array(200)),
		),
		array(
			array('body', 'ご意見を入力してください。', 'required'),
			array('body', 'ご意見は400文字以下で入力してください。', 'maxlengthZen', array(400)),
		)
	);
	protected $uniforms = array(
		array(
			'subject' => 'KV',
			'body' => 'KV',
			'username' => 'KV',
			'useremail' => 'aKV'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
