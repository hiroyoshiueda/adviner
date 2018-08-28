<?php
/**
 * Enter description here...
 *
 */
class UtilFile {

	/**
	 * Enter description here...
	 *
	 * @param String $filePath
	 * @return boolean
	 */
	public static function isFile($filePath) {
		$pathBuilder = new PathBuilder($filePath, false);
		return $pathBuilder->isFile();
	}

	/**
	 * Enter description here...
	 *
	 * @param String $filePath
	 * @return String
	 */
	public static function readTextFile($filePath) {
		$text='';
		if (file_exists($filePath) === false) return $text;
		$fp = fopen($filePath, 'rb');
		if ($fp === false) return $text;
		$text = fread($fp, filesize($filePath));
		fclose($fp);
		return $text;
	}

	/**
	 * Enter description here...
	 *
	 * @param String $str
	 * @return boolean
	 */
	public static function isEmpty($str) {
		return ($str == null || $str == '');
	}

	/**
	 * Enter description here...
	 *
	 * @param String $filePath
	 * @return Array
	 */
	public function readConf($filePath) {
		$map = array();
		if (file_exists($filePath) === false) return $map;
		$lines = files($filePath);
		foreach ($lines as $line) {
			$line = ltrim($line);
			if ($line == '') continue;
			if (substr($line, 0, 1) == '#') continue;
			list($key, $val) = explode('=', $line, 2);
			$key = ltrim($key);
			$val = ltrim($val);
			$map[$key] = $val;
		}
		return $map;
	}

	public static function readDirFile(&$map, $dir) {
		if (substr($dir, -1) == "/") $dir = substr($dir, 0, strlen($dir) - 1);
		if (file_exists($dir) === false) return;
		if (is_dir($dir)) {
			if ($handle = opendir($dir)) {
				while (($file = readdir($handle)) !== false) {
					if ($file == "." || $file == "..") continue;
					self::readDirFile($map, $dir."/".$file);
        		}
        		closedir($handle);
			}
		} else if (is_file($dir)) {
			$map[] = $dir;
		}
		return;
	}

	public static function removeDir($dir)
	{
		//@error_log("check: ".$dir."\n", 3, APP_DIR."/logs/errors.log");
		if (file_exists($dir) === false) return 0;
		$ret = 0;
		if (is_dir($dir)) {
			if ($handle = opendir($dir)) {
				while (($file = readdir($handle)) !== false) {
					if ($file == "." || $file == "..") continue;
					$ret += self::removeDir($dir.DIRECTORY_SEPARATOR.$file);
				}
				closedir($handle);
				if (@rmdir($dir)) $ret++;
				//else @error_log("rmdir(): ".$dir."\n", 3, APP_DIR."/logs/errors.log");
			}
		} else if (is_file($dir)) {
			if (@unlink($dir)) $ret++;
			//else @error_log("unlink(): ".$dir."\n", 3, APP_DIR."/logs/errors.log");
		}
		return $ret;
	}

	/**
	 * アップロードファイルのチェック
	 * @param array $file $_FILES['xxx']
	 * @param string $errmsg
	 * @return boolean
	 */
	public static function uploadFileCheck(&$file, &$errmsg)
	{
		if ($file['error'] != UPLOAD_ERR_OK) {
			switch ($file['error']) {
				case UPLOAD_ERR_INI_SIZE:
					$errmsg = sprintf('ファイルサイズが最大サイズ（%s）を超えています。', ini_get('upload_max_filesize'));
					break;
				case UPLOAD_ERR_FORM_SIZE:
					if ($_POST['MAX_FILE_SIZE']>=1048576) {
						$size_str = number_format($_POST['MAX_FILE_SIZE']/1048576).' MB';
					} else if ($_POST['MAX_FILE_SIZE']>=1024) {
						$size_str = number_format($_POST['MAX_FILE_SIZE']/1024).' KB';
					} else {
						$size_str = number_format($_POST['MAX_FILE_SIZE']).' B';
					}
					$errmsg = sprintf('ファイルサイズが最大サイズ（%s）を超えています。', $size_str);
					break;
				case UPLOAD_ERR_PARTIAL:
					$errmsg = 'アップロードされたファイルは一部のみしかアップロードされていません。[UPLOAD_ERR_PARTIAL]';
					break;
				case UPLOAD_ERR_NO_FILE:
					$errmsg = 'ファイルはアップロードされませんでした。[UPLOAD_ERR_NO_FILE]';
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$errmsg = 'テンポラリフォルダがありません。[UPLOAD_ERR_NO_TMP_DIR]';
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$errmsg = 'ディスクへの書き込みに失敗しました。[UPLOAD_ERR_CANT_WRITE]';
					break;
				case UPLOAD_ERR_EXTENSION:
					$errmsg = 'ファイルのアップロードが拡張モジュールによって停止されました。[UPLOAD_ERR_EXTENSION]';
					break;
			}
			return false;
		} else {
			return true;
		}
	}

	/**
	 * ファイルポインタから行を取得し、CSVフィールドを処理する
	 * fgetcsv()関数の文字化け対策
	 * @param resource handle
	 * @param int length
	 * @param string delimiter
	 * @param string enclosure
	 * @return ファイルの終端に達した場合を含み、エラー時にFALSEを返します。
	 */
	public static function mb_fgetcsv(&$handle, $length = null, $d = ',', $e = '"')
	{
		$d = preg_quote($d);
		$e = preg_quote($e);
		$_line = '';
		while ($eof != true) {
			$_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
			$itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
			if ($itemcnt % 2 == 0) $eof = true;
		}
		$_csv_line = preg_replace('/(?:\r\n|[\r\n])?$/', $d, trim($_line));
		$_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
		preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
		$_csv_data = $_csv_matches[1];
		for ($_csv_i=0; $_csv_i<count($_csv_data); $_csv_i++) {
			$_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
			$_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
		}
		return empty($_line) ? false : $_csv_data;
	}

	public static function rename($oldname, $newname)
	{
		if (file_exists($newname)) @unlink($newname);
		return @rename($oldname, $newname);
	}

	public static function removeFile($file)
	{
		if (file_exists($file) && is_file($file)) {
			@unlink($file);
		}
		return;
	}
}
?>