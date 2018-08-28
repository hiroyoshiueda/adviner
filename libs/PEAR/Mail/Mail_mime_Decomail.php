<?php
/**
 * デコメール専用 MIMEエンコードクラス.
 * PEAR Mail_mime を継承.
 *
 * @package mail
 * @author y-110 <m@y-110.net>
 * @author N-devel <nishide@any-sense.com>
 * @since 2005/10/22
 * @version $Id$
 *
 * Y-110's wiki: <http://php.y-110.net/wiki/>
 * any-sense: <http://any-sense.com/>
 */

require_once "Mail/mime.php";

class Mail_Mime_Decomail extends Mail_mime
{
    /**
     * Constructor
     */
    function Mail_Mime_Decomail($crlf = "\r\n")
    {
        parent::Mail_mime($crlf);
    }

	/**
	 * Override Mail_mime::addHTMLImage
	 * @param string $cid Content-ID for image. If not set, cid will automatically generated.
	 */
//    function addHTMLImage($file, $c_type='application/octet-stream',
//                          $name = '', $isfile = true, $cid = null)
//    {
//        $filedata = ($isfile === true) ? $this->_file2str($file)
//                                           : $file;
//        if ($isfile === true) {
//            $filename = ($name == '' ? $file : $name);
//        } else {
//            $filename = $name;
//        }
//        if (PEAR::isError($filedata)) {
//            return $filedata;
//        }
//		if (!strlen($cid)) {
//			$cid = md5(uniqid(time()));
//		}
//
//        $this->_html_images[] = array(
//                                      'body'   => $filedata,
//                                      'name'   => $filename,
//                                      'c_type' => $c_type,
//                                      'cid'    => $cid
//                                     );
//        return true;
//    }

    /**
     * Override Mail_mime::_addHtmlImagePart
     */
    function &_addHtmlImagePart(&$obj, $value)
    {
        // image/xxx; name=xxx の様に name=xxx を付けないと画像が表示されない
        $params['content_type'] = $value['c_type']. ";\r\n name=\"${value['name']}\"";
        $params['encoding']     = 'base64';
        $params['cid']          = $value['cid'];
        $obj->addSubpart($value['body'], $params);
    }

    /**
     * Override Mail_mime::get
     *
     * デコメのインライン画像の場合に対応
     *
     * multipart/related
     * ├ multipart/alternative
     * │ ├ text/plain
     * │ └ text/html
     * └ image/xxx (*n)
     */
    function &get($build_params = null)
    {
        if (isset($build_params)) {
            while (list($key, $value) = each($build_params)) {
                $this->_build_params[$key] = $value;
            }
        }

        if (!empty($this->_html_images) AND isset($this->_htmlbody)) {
            foreach ($this->_html_images as $value) {
                $regex = '#(\s)((?i)src|background|href(?-i))\s*=\s*(["\']?)' . preg_quote($value['name'], '#') .
                         '\3#';
                $rep = '\1\2=\3cid:' . $value['cid'] .'\3';
                $this->_htmlbody = preg_replace($regex, $rep,
                                       $this->_htmlbody
                                   );
            }
        }

        $null        = null;
        $attachments = !empty($this->_parts)                ? true : false;
        $html_images = !empty($this->_html_images)          ? true : false;
        $html        = !empty($this->_htmlbody)             ? true : false;
        $text        = (!$html AND !empty($this->_txtbody)) ? true : false;

        switch (true) {
        // デコメール本文のみ
        case $html AND !$attachments AND !$html_images:
            if (isset($this->_txtbody)) {
                $message =& $this->_addAlternativePart($null);
                $this->_addTextPart($message, $this->_txtbody);
                $this->_addHtmlPart($message);
            } else {
                $message =& $this->_addHtmlPart($null);
            }
            break;
        // デコメール本文とインライン画像
        case $html AND !$attachments AND $html_images:
            if (isset($this->_txtbody)) {
                $message =& $this->_addRelatedPart($null);
                $alt =& $this->_addAlternativePart($message);
                $this->_addTextPart($alt, $this->_txtbody);
            } else {
				return false;
                die("no text part");
            }
            $this->_addHtmlPart($alt);
            for ($i = 0; $i < count($this->_html_images); $i++) {
                $this->_addHtmlImagePart($message, $this->_html_images[$i]);
            }
            break;
        // デコメール本文と添付画像
        case $html AND $attachments AND !$html_images:
            $message =& $this->_addMixedPart();
            if (isset($this->_txtbody)) {
                $alt =& $this->_addAlternativePart($message);
                $this->_addTextPart($alt, $this->_txtbody);
                $this->_addHtmlPart($alt);
            } else {
				return false;
                die("no text part");
            }
            for ($i = 0; $i < count($this->_parts); $i++) {
                $this->_addAttachmentPart($message, $this->_parts[$i]);
            }
            break;
        // デコメール本文と添付画像とインライン画像
        case $html AND $attachments AND $html_images:
            $message =& $this->_addMixedPart();
            if (isset($this->_txtbody)) {
                $message =& $this->_addRelatedPart($null);
                $alt =& $this->_addAlternativePart($message);
                $this->_addTextPart($alt, $this->_txtbody);
            } else {
				return false;
                die("no text part");
            }
            $this->_addHtmlPart($alt);
            for ($i = 0; $i < count($this->_html_images); $i++) {
                $this->_addHtmlImagePart($message, $this->_html_images[$i]);
            }
            for ($i = 0; $i < count($this->_parts); $i++) {
                $this->_addAttachmentPart($message, $this->_parts[$i]);
            }
            break;

        default:
            // setHTMLBody されていないとデコメールとみなさずエラー
			return false;
            die("not decomail.");
            break;
        }

        if (isset($message)) {
            $output = $message->encode();
            $this->_headers = array_merge($this->_headers,
                                          $output['headers']);
            return $output['body'];

        } else {
            return false;
        }
    }
}
?>
