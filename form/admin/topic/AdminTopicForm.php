<?php
/**
 * INDEX(Form)
 */
class AdminTopicForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('date', '日付を選択してください。', 'required'),
			array('title', 'タイトルを入力してください。', 'required')
		)
	);
	protected $uniforms = array(
		array(
			'id' => 'int',
			'display_flag' => 'int',
			'date' => 'aKV',
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
