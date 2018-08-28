<?php
/**
 * 管理画面／よくある質問(Form)
 */
class AdminContentFaqForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('question', '質問を入力してください。', 'required'),
			array('answer', '回答を入力してください。', 'required')
		)
	);
	protected $uniforms = array(
		array(
			'id' => 'int',
			'display_flag' => 'int',
			'faq_category_id' => 'int',
			'order_num' => 'int',
			'question' => 'KV',
			'answer' => 'KV'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
