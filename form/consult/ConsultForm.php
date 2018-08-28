<?php
/**
 * 相談詳細(Form)
 * @author Hiroyoshi
 */
class ConsultForm extends BaseForm
{
	protected $validates = array(
		array(
			array('consult_body', '相談内容を入力してください。', 'required'),
			array('consult_body', '相談内容は400文字以下で入力してください。', 'maxlengthZen', array(400)),
//			array('public_flag', '非公開・公開を選択してください。', 'required'),
//			array('period_type', '回答への要望を選択してください。', 'required')
		)
	);

	protected $uniforms = array(
		array(
			'id' => 'int',
			'username' => 'aKV',
			'consult_body' => 'KV',
			'public_flag' => 'int',
//			'period_type' => 'int'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
