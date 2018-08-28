<?php
/**
 * 管理画面／よくある質問カテゴリー(Form)
 */
class AdminContentFaqcategoryForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('title', 'カテゴリー名を入力してください。', 'required')
		)
	);
	protected $uniforms = array(
		array(
			'id' => 'int',
			'display_flag' => 'int',
			'order_num' => 'int',
			'title' => 'KV'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
