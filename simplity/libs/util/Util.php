<?php
class Util
{
	public static function toString($str, $def='')
	{
		if ($str === null) return $def;
		return (string)$str;
	}
	public static function toInt($str, $def=0)
	{
		if ($str === null || $str == '') return $def;
		return (int)self::toNumber($str);
	}
	public static function toNumber($str, $def='0')
	{
		if (preg_match("/^([0-9]+)/", $str, $m)) {
			return $m[1];
		}
		return $def;
	}
	public static function isEmpty($value)
	{
		if ($value === null) return true;
		if (is_array($value)) {
			return (count($value) == 0);
		} else {
			return ($value == '');
		}
//		return ($value === null || $value == '');
	}
	public static function isFile($path)
	{
		return (file_exists($path) && is_file($path));
	}
	public static function isDir($path)
	{
		return (file_exists($path) && is_dir($path));
	}
	public static function isNumber($var)
	{
		if (preg_match("/^\-?[0-9]+$/", $var)) {
			return true;
		}
		return false;
	}
	public static function isEmailFormat($str)
	{
		if (preg_match("/[a-z0-9\-_\.]+@[a-z0-9\-_\.]+\.[a-z]+/i", $str)) return true;
		else return false;
	}
	public static function arrayMerge(&$array1, $array2)
	{
		if (is_array($array2) === false || count($array2) == 0) return;
		$array1 = array_merge($array1, $array2);
	}
	public static function arrayUniqMerge(&$array1, $array2)
	{
		if (count($array2)>0) {
			foreach ($array2 as $v) {
				if (in_array($v, $array1) === false) $array1[] = $v;
			}
		}
	}
	public static function arrayToTextValue($array, $setType=0)
	{
		$ary = array();
		if (!is_array($array) || count($array)==0) return $ary;
		foreach ($array as $value => $text) {
			if ($setType===0) {
				$ary[] = array('value'=>$value, 'text'=>$text);
			} else {
				$ary[] = array('value'=>$text, 'text'=>$text);
			}
		}
		return $ary;
	}
	public static function deleteFile($filename)
	{
		return @unlink($filename);
	}
	public static function getExtension($filename)
	{
		preg_match("/\.([a-zA-Z0-9]+)$/i", $filename, $m);
		return strtolower($m[1]);
	}
	/**
	 * 連想配列内の指定キーの値を配列で返す
	 * @param string $key
	 * @param array $array
	 * @param boolean $unique
	 */
	public static function arraySelectKey($key, &$array, $unique=false)
	{
		$arr = array();
		if (!is_array($array) || count($array)==0) return $arr;
		foreach ($array as $k => $a) {
			if (isset($a[$key])) $arr[] = $a[$key];
		}
		return $unique ? array_unique($arr) : $arr;
	}

	/**
	 * 連想配列内の指定キーで再配列して返す
	 * @param string $key
	 * @param array $array
	 */
	public static function arrayKeyData($key, &$array)
	{
		$arr = array();
		if (!is_array($array) || count($array)==0) return $arr;
		list($key, $key2) = explode(',', $key, 2);
		foreach ($array as $k => $a) {
			if ($key2 == '') {
				$arr[$a[$key]] = $a;
			} else {
				if (isset($arr[$a[$key]])===false) $arr[$a[$key]] = array();
				$arr[$a[$key]][$a[$key2]] = $a;
			}
		}
		return $arr;
	}

	public static function arrayToString($array)
	{
		if (is_array($array) === false) return $array;
		if (count($array) == 0) return "(array) {}";
		$str = "(array) {\n";
		foreach ($array as $k => $v) {
			$str .= "\t${k} => ${v}\n";
		}
		$str .= "}";
		return $str;
	}
	public static function startsWith($str, $prefix)
	{
		$len = strlen($str);
		$plen = strlen($prefix);
		if ($len < $plen) return false;
		return (substr($str, 0, $plen) == $prefix);
	}
	public static function endsWidth($str, $suffix)
	{
		$len = strlen($str);
		$slen = strlen($suffix);
		if ($len < $slen) return false;
		return (substr($str, $len - $slen, $len) == $suffix);
	}
//	public static function import($file)
//	{
//		Sp::import($file.'.php', dirname(__FILE__));
//	}
	/**
	 * ヒアドキュメント対応
	 * #行コメント
	 * {}間は定数置換
	 * @param $filePath
	 * @return unknown_type
	 */
	public static function getReadConf($filePath, $sep='=')
	{
		$map = array();
		if (file_exists($filePath) === false) return $map;
		$lines = file($filePath);
		$key = '';
		$sum = '';
		$buf = '';
		foreach ($lines as $line) {
			if ($sum != '') {
				if (trim($line) == $sum.';') {
					self::convertConstant($buf);
					$map[$key] = $buf;
					$buf = '';
					$sum = '';
				} else {
					$buf .= $line;
				}
				continue;
			}
			$line = ltrim($line);
			if ($line == '') continue;
			if (substr($line, 0, 1) == '#') continue;
			list($key, $val) = explode($sep, $line, 2);
			$key = trim($key);
			$val = trim($val);
			if (substr($val, 0, 3) == '<<<') {
				$sum = substr($val, 3);
				$buf = '';
				continue;
			}
			self::convertConstant($val);
			$map[$key] = $val;
		}
		return $map;
	}
	private static function convertConstant(&$val)
	{
		if (preg_match_all("/\{([a-z0-9_\-]+)\}/i", $val, $m, PREG_SET_ORDER)) {
			foreach ($m as $mm) {
				$rep = defined($mm[1]) ? constant($mm[1]) : '';
				$val = str_replace('{'.$mm[1].'}', $rep, $val);
			}
		}
		return;
	}
	public static function uniqId()
	{
		return uniqid(rand(), true);
	}
	public static function password($password)
	{
		if (Util::isEmpty($password)) return $password;
		return md5($password);
	}
	public static function sendSmtpMail($to, $subject, $body, $from, $fromName, $encode, &$sendErrmsg)
	{
		mb_language("Japanese");
		Sp::import('PEAR/Mail.php', 'libs', true);

		$params = array(
			'host' => '127.0.0.1',
			'port' => '25',
			'auth' => false,
			'username' => '',
			'password' => '',
		);

		if (class_exists('AppConst')) if (isset(AppConst::$utilSmtpParams)) $params = AppConst::$utilSmtpParams;

		$recipients = $to;

		$headers = array();
		$headers['From']    = $fromName=='' ? $from : mb_encode_mimeheader(mb_convert_encoding($fromName, 'ISO-2022-JP', $encode), 'ISO-2022-JP').' <'.$from.'>';
		$headers['To']      = $to;
		$headers['Subject'] = mb_encode_mimeheader(mb_convert_encoding($subject, 'ISO-2022-JP', $encode), 'ISO-2022-JP');

		$mail =& Mail::factory('smtp', $params);
		$result = $mail->send($recipients, $headers, mb_convert_encoding($body, "ISO-2022-JP", $encode));
		if (PEAR::isError($result))
		{
			$sendErrmsg = $result->getMessage();
			return false;
		}
		return true;
	}
	public static function sendMail($to, $subject, $body, $from, $fromName, $encode, &$sendErrmsg)
	{
		$ln = "\n";

		$headers  = 'MIME-Version: 1.0'.$ln;

		$from_src = self::mailMimeEncode($from, $fromName, $encode);

		$headers .= 'From: '.$from_src.$ln;
		$headers .= 'Reply-To: '.$from_src.$ln;
		$headers .= 'Content-Type: text/plain;charset=ISO-2022-JP'.$ln;

		$subject = mb_convert_encoding($subject, 'ISO-2022-JP', $encode);
		$subject = self::mimeEncode($subject, 'ISO-2022-JP');

		$body = mb_convert_encoding($body, 'ISO-2022-JP', $encode);

		$sendmailParams  = '-f'.$from;

		ob_start();
		$result = mail($to, $subject, $body, $headers, $sendmailParams);
		$sendErrmsg = ob_get_contents();
		ob_end_clean();

		return $result;
	}
	public static function mailMimeEncode($from, $fromName, $encode)
	{
		$buf = '';
		if ($fromName != '') {
			$buf = mb_convert_encoding($fromName, 'ISO-2022-JP', $encode);
			$buf = self::mimeEncode($buf, 'ISO-2022-JP');
		} else {
			$buf = $from;
		}
		$buf .= ' <'.$from.'>';
		return $buf;
	}
	public static function mimeEncode($str, $charset)
	{
		$buf  = '=?'.$charset.'?B?';
		$buf .= base64_encode($str);
		$buf .= '?=';

		return $buf;
	}
//	public static function mimeEncode($str, $charset)
//	{
//		$buf = '';
//		$pos = 0;
//		$split = 36;
//
//		$orgEncoding = mb_internal_encoding();
//		mb_internal_encoding($charset);
//
//		while ($pos < mb_strlen($str, $charset)) {
//			$output = mb_strimwidth($str, $pos, $split, '', $charset);
//			$pos += mb_strlen($output, $charset);
//			$buf .= mb_encode_mimeheader($output, $charset);
//		}
//
//		mb_internal_encoding($orgEncoding);
//
//		return $buf;
//	}
	public static function jsonEncode($array)
	{
//		if (function_exists('json_encode')) {
//			$encode = json_encode($array);
//		} else {
			SP::import('JSON.php', SP_DIR.'/libs', true);
			$json = new Services_JSON;
			$encode = $json->encode($array);
//		}
		return $encode;
	}
	public static function jsonDecode($json_str)
	{
		SP::import('JSON.php', SP_DIR.'/libs', true);
		$json = new Services_JSON;
		$decode = $json->decode($json_str);

		return $decode;
	}
	public static function htmlEncode($string)
	{
		return htmlspecialchars($string, ENT_QUOTES);
	}

	/**
	 * 数値配列を返す
	 * @param int $start
	 * @param int $end
	 * @param string $format %d or %02d
	 * @param int $inc インクリメント値
	 */
	public static function makeNumberArray($start, $end, $format, $inc=1)
	{
		$start = (int)$start;
		$end = (int)$end;
		$arr = array();
		while ($start<=$end) {
			$arr[] = sprintf($format, $start);
			$start += $inc;
		}
		return $arr;
	}

	/**
	 * 配列をキーで結合（LEFT JOIN処理）
	 * @param array $list 結合元配列
	 * @param array $list_join 結合する配列
	 * @param string $merge_col 結合に使用するキー名
	 * @param string $null_str キーが無い場合に埋める値
	 */
	public static function leftJoin(&$list, &$list_join, $merge_col, $null_str = "", $update=true)
	{
		if (count($list) == 0 || count($list_join) == 0) return;

		list($merge_col, $merge_col_join) = explode('=', $merge_col, 2);
		if (empty($merge_col_join)) $merge_col_join = $merge_col;

		// インデックスを作る
		$indexed = array();
		$columns = array();
		foreach($list_join as $i => $row) {
			$key = $row[$merge_col_join];
			$indexed[$key] = $row;
			if ($i == 0) $columns = array_keys($row);
		}
		// join
		foreach ($list as $i => $row) {
			$key = $row[$merge_col];
			if (isset($indexed[$key])) {
				$vals = $indexed[$key];
				foreach($vals as $col => $val) {
					if (isset($row[$col])) {
						if ($update!==false) $row[$col] = $val;
					} else {
						$row[$col] = $val;
					}
				}
			} else {
				foreach ($columns as $col) {
					if (isset($row[$col]) === false) $row[$col] = $null_str;
				}
			}
			$list[$i] = $row;
		}
		return;
	}

	/**
	 * 配列を多次元ソート（order by処理）
	 * @param array $datas ソートする配列
	 * @param string $order ソートするキー
	 * @param string $sort ASC or DESC
	 * @param string $order1
	 * @param string $sort1
	 * @param string $order2
	 * @param string $sort2
	 * @return void
	 */
	public static function orderby(&$datas, $order, $sort, $order1 ="", $sort1="", $order2 = "", $sort2 = "")
	{
		$array1 = array();
		$array2 = array();
		$array3 = array();
		$sort2 = (trim($sort2) == "DESC") ? SORT_DESC : SORT_ASC;
		$sort1 = (trim($sort1) == "DESC") ? SORT_DESC : SORT_ASC;
		$sort = (trim($sort) == "DESC") ? SORT_DESC : SORT_ASC;
		if ($order2 != "") {
			foreach ($datas as $key => $row) {
				$array1[$key] = $row[$order2];
				$array2[$key] = $row[$order1];
				$array3[$key] = $row[$order];
			}
			array_multisort($array3, $sort, $array2, $sort1, $array1, $sort2, $datas);
		} else if($order1 != ""){
			foreach ($datas as $key => $row) {
				$array1[$key] = $row[$order1];
				$array2[$key] = $row[$order];
			}
			array_multisort($array2, $sort, $array1, $sort1, $datas);
		} else {
			foreach ($datas as $key => $row) {
				$array1[$key] = $row[$order];
			}
			array_multisort($array1, $sort, $datas);
		}
		return;
	}

	public static function isSSL()
	{
		return (empty($_SERVER['HTTPS'])===false);
	}

	public static function path($path)
	{
		$path = str_replace('\\', '/', $path);
		$path = str_replace('c:/', '/', $path);
		return $path;
	}

	public static function constant($name)
	{
		return (defined($name)) ? constant($name) : '';
	}

	public static function getTimestamp($datetime)
	{
		if (strlen($datetime)<=10) $datetime .= ' 0:0:0';
		$d = preg_split("/[\/\- :]+/", $datetime);
		return mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
	}

	/**
	 * リサイズした画像を新規作成
	 * @param String $img_path 元画像パス
	 * @param String $new_path 新規画像パス
	 * @param Int $new_width 新規画像横幅
	 * @param Int $new_height 新規画像縦
	 * @param String $to_image 作成する画像形式(auto|gif|jpeg|png)
	 * @param String $errmsg エラーメッセージ
	 * @param boolean $equals 新規画像の縦幅が同じ場合に 0:何もしない(画像がゆがむ) 1:上より中央寄せして生成
	 */
	public static function resizeImage($img_path, $new_path, $new_width, $new_height, $to_image, &$errmsg, $equals=0)
	{
		list($src_width, $src_height) = getimagesize($img_path);
//		if ($width <= $new_width && $height <= $new_height) return true;
		$src_x = 0;
		$src_y = 0;
		// 正方形の場合
		if ($equals==1 && $new_width == $new_height) {
			if ($src_width > $src_height) {
				// 中央をフォーカス
				$src_x = (int)(($src_width - $src_height) / 2);
				$src_width = $src_height;
			} else if ($src_height > $src_width) {
				$src_height = $src_width;
			}
		}

		$ext = self::getExtension($img_path);
		$imagecreate = self::getImagecreateFunc($ext);
		if ($imagecreate == '') {
			$errmsg = 'resizeImage(): It is an extension of the unsupport. ='.$ext;
			return false;
		}
		if ($to_image == 'auto') $to_image = $ext;

		$ret = true;
		$new_img = @imagecreatetruecolor($new_width, $new_height);
		$white = imagecolorallocate($new_img, 255, 255, 255);
		imagefilledrectangle($new_img, 0, 0, $new_width, $new_height, $white);

		$img = @$imagecreate($img_path);
		if ($img !== false) {
//			if (@imagecopyresized($new_img, $img, 0, 0, 0, 0, $new_width, $new_height, $src_width, $src_height)) {
			if (@imagecopyresampled($new_img, $img, 0, 0, $src_x, $src_y, $new_width, $new_height, $src_width, $src_height)) {
				if ($to_image == 'gif') {
					if (@imagegif($new_img, $new_path)===false) {
						$errmsg = 'resizeImage(): imagegif(): failure';
						$ret = false;
					}
				} else if ($to_image == 'png') {
					if (@imagepng($new_img, $new_path)===false) {
						$errmsg = 'resizeImage(): imagepng(): failure';
						$ret = false;
					}
				} else if ($to_image == 'jpg' || $to_image == 'jpeg') {
					if (@imagejpeg($new_img, $new_path, 80)===false) {
						$errmsg = 'resizeImage(): imagejpeg(): failure';
						$ret = false;
					}
				}
			} else {
				$errmsg = 'resizeImage(): imagecopyresized(): failure';
				$ret = false;
			}
			@imagedestroy($img);
		} else {
			$errmsg = 'resizeImage(): '.$imagecreate.'(): failure';
			$ret = false;
		}
		@imagedestroy($new_img);

		return $ret;
	}

	/**
	 * 拡張子に応じた画像作成関数名を返す
	 * @param String $ext
	 */
	public static function getImagecreateFunc($ext)
	{
		$imagecreate = '';
		if ($ext == 'gif') {
			$imagecreate = 'imagecreatefromgif';
		} else if ($ext == 'png') {
			$imagecreate = 'imagecreatefrompng';
		} else if ($ext == 'jpg' || $ext == 'jpeg') {
			$imagecreate = 'imagecreatefromjpeg';
		}
		return $imagecreate;
	}

	/**
	 * URLコンテンツの取得
	 * @param string $url
	 * @param string $errmsg
	 * @param int $timeout
	 */
	public static function getUrlContents($url, &$errmsg, $timeout=60)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		if ($result === false) {
			$errmsg = curl_error($ch);
		}
		curl_close($ch);
		return $result;
	}
}
?>