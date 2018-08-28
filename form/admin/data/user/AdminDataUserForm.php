<?php
/**
 * 管理画面／ユーザー(Form)
 */
class AdminDataUserForm extends BaseForm
{
	/** @var array */
	protected $validates = array(
		array(
			array('nickname', 'ニックネームを入力してください。', 'required'),
			array('nickname', 'ニックネームは3文字以上10文字以内で入力してください。', 'length_range_zen', array(3,10)),
			array('email', 'メールアドレスを入力してください。', 'required'),
			array('email', 'メールアドレスを正しい書式で入力してください。', 'email'),
			array('email', 'メールアドレスは100文字以下で入力してください。', 'maxlength', array(100)),
			array('password', 'パスワードは6文字以上で入力してください。', 'minlength', array(6)),
			array('password', 'パスワードは半角英数記号( . @ # % $ = _ * & - )を入力してください。', 'password'),
			array('name_sei', '氏名（姓）を入力してください。', 'required'),
			array('name_mei', '氏名（名）を入力してください。', 'required'),
			array('name', '氏名は100文字以下で入力してください。', 'maxlengthZen', array(100)),
			array('kana_sei', '氏名フリガナ（姓）を入力してください。', 'required'),
			array('kana_mei', '氏名フリガナ（名）を入力してください。', 'required'),
			array('kana', '氏名フリガナは100文字以下で入力してください。', 'maxlengthZen', array(100)),
			array('zip', '郵便番号を入力してください。', 'required'),
			array('zip', '郵便番号を正しい書式で入力してください。', 'match', array('^[0-9]{3}-[0-9]{4}$')),
			array('area', '都道府県を選択してください。', 'required'),
			array('addr1', '市区町村を入力してください。', 'required'),
			array('addr2', '番地・建物名を入力してください。', 'required'),
			array('tel', '電話番号を入力してください。', 'required'),
			array('tel', '電話番号は20文字以下で入力してください。', 'maxlength', array(20)),
			array('fax', 'FAX番号は20文字以下で入力してください。', 'maxlength', array(20)),
			array('mobile_tel', '緊急連絡先を入力してください。', 'required'),
			array('mobile_tel', '緊急連絡先は20文字以下で入力してください。', 'maxlength', array(20)),
			array('url', 'ホームページURLを正しい書式で入力してください。', 'match', array('^https?://.*')),
			array('career', 'キャリアを入力してください。', 'required'),
			array('forte', '得意分野を入力してください。', 'required'),
			array('production_env', '制作環境を入力してください。', 'required'),
			array('coverage', '対応範囲を入力してください。', 'required'),
			array('business_hours', '営業時間を入力してください。', 'required'),
			array('meeting_method', '打ち合わせ方法を選択してください。', 'required'),
			array('mailmaga_flag', 'メール配信の受信可否を選択してください。', 'required')
		)
	);
	protected $uniforms = array(
		array(
			'id' => 'int',
			'password_confirm' => 'aKV',
			'key' => 'md5',
			'user_id' => 'int',
			'display_flag' => 'int',
			'email' => 'aKV',
			'password' => 'aKV',
			'nickname' => 'KV',
			'name' => 'KV',
			'name_sei' => 'KV',
			'name_mei' => 'KV',
			'kana' => 'KV',
			'kana_sei' => 'KV',
			'kana_mei' => 'KV',
			'zip' => 'aKV',
			'area' => 'KV',
			'addr1' => 'aKV',
			'addr2' => 'aKV',
			'tel' => 'aKV',
			'fax' => 'aKV',
			'mobile_tel' => 'aKV',
			'url' => 'aKV',
			'image_file' => 'KV',
			'image_path' => 'aKV',
			'image_size' => 'int',
			'career ' => 'KV',
			'forte' => 'KV',
			'production_env' => 'KV',
			'business_hours' => 'KV',
			'meeting_method' => 'KV',
			'meeting_other1' => 'KV',
			'meeting_other2' => 'KV',
			'mailmaga_flag' => 'int',
			'temp_key' => 'md5'
		)
	);

	function __construct()
	{
		parent::__construct();
		$this->uniform($this->uniforms[0]);
	}
}
?>
