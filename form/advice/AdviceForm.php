<?php
/**
 * 相談詳細(Form)
 * @author Hiroyoshi
 */
class AdviceForm extends BaseForm
{
	protected $validates = array(
		array(
			array('consult_body', '相談内容を入力してください。', 'required'),
			array('consult_body', '相談内容は1000文字以下で入力してください。', 'maxlengthZen', array(1000)),
			array('agree', '相談・アドバイスする時のガイドラインを読んで理解したらチェックを入れてください。', 'required')
//			array('public_flag', '非公開・公開を選択してください。', 'required'),
//			array('period_type', '回答への要望を選択してください。', 'required')
		)
	);

	protected $uniforms = array(
		array(
			'id' => 'int',
			'username' => 'aKV',
			'consult_body' => 'KV',
			'is_consult_form' => 'int',
			'public_flag' => 'int',
			'agree' => 'int'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
