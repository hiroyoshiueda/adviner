<?php
/**
 * プロフィール画像の生成
 * @author Hiroyoshi
 */
class ProfileImage
{
	public $biggerPath = '';
	public $normalPath = '';
	public $smallPath = '';

	public $biggerSize = 0;
	public $normalSize = 0;
	public $smallSize = 0;

	private $logger = null;

	public function __construct(&$logger)
	{
		$this->logger = $logger;
	}

	/**
	 * 48px 73px 128px サイズ画像の生成
	 * @param unknown_type $imagePath
	 * @param unknown_type $publicPath
	 */
	public function create($imagePath, $publicPath)
	{
		if ($this->_createPicture($imagePath, 'biggerPath', APP_CONST_PROFILE_IMAGE_BIGGER_SIZE, 'bigger') === false) {
			throw new SpException('画像(bigger)の生成に失敗しました。');
		}
		if ($this->_createPicture($imagePath, 'normalPath', APP_CONST_PROFILE_IMAGE_NORMAL_SIZE, 'normal') === false) {
			throw new SpException('画像(normal)の生成に失敗しました。');
		}
		if ($this->_createPicture($imagePath, 'smallPath', APP_CONST_PROFILE_IMAGE_SMALL, 'small') === false) {
			throw new SpException('画像(small)の生成に失敗しました。');
		}

		@mkdir($publicPath, 0705, true);

		// 公開ディレクトリにコピー
		$bigger_tmp = APP_CONST_PROFILE_IMAGE_TMP_DIR.'/'.$this->biggerPath;
		$bigger_new = $publicPath.'/'.$this->biggerPath;
		if (UtilFile::rename($bigger_tmp, $bigger_new) === false) {
			$this->logger->error('画像(bigger)のコピーに失敗しました。'.$bigger_tmp);
			throw new SpException('画像(bigger)のコピーに失敗しました。');
		}

		$normal_tmp = APP_CONST_PROFILE_IMAGE_TMP_DIR.'/'.$this->normalPath;
		$normal_new = $publicPath.'/'.$this->normalPath;
		if (UtilFile::rename($normal_tmp, $normal_new) === false) {
			$this->logger->error('画像(normal)のコピーに失敗しました。'.$normal_tmp);
			throw new SpException('画像(normal)のコピーに失敗しました。');
		}

		$small_tmp = APP_CONST_PROFILE_IMAGE_TMP_DIR.'/'.$this->smallPath;
		$small_new = $publicPath.'/'.$this->smallPath;
		if (UtilFile::rename($small_tmp, $small_new) === false) {
			$this->logger->error('画像(small)のコピーに失敗しました。'.$small_tmp);
			throw new SpException('画像(small)のコピーに失敗しました。');
		}

		$this->biggerSize = filesize($bigger_new);
		$this->normalSize = filesize($normal_new);
		$this->smallSize = filesize($small_new);
	}

	/**
	 * 画像のリサイズ
	 * @param unknown_type $upload_path
	 * @param unknown_type $create_prop
	 * @param unknown_type $size
	 * @param unknown_type $prefix
	 */
	private function _createPicture($upload_path, $create_prop, $size, $prefix)
	{
		$create_path = preg_replace('/\.[a-z]+$/i', '_'.$prefix.'.jpg', $upload_path);
		$this->$create_prop = $create_path;
		$upload_img = APP_CONST_PROFILE_IMAGE_TMP_DIR.'/'.$upload_path;
		$create_img = APP_CONST_PROFILE_IMAGE_TMP_DIR.'/'.$create_path;
		if (Util::resizeImage($upload_img, $create_img, $size, $size, 'jpg', &$errmsg, 1) === false) {
			$this->logger->error($errmsg);
			return false;
		} else {
			return true;
		}
	}
}
?>
