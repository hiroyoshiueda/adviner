<?php
/**
 * Enter description here...
 *
 */
class SpException extends Exception
{
	protected $traceMessage = '';
	function __construct($msg=null, $code=0) {
		parent::__construct($msg, $code);
//		$this->traceMessage = $this->getTraceMessage();
	}
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    public function printStackTrace() {
    	print $this->getStackTrace();
    }
    protected function getTraceMessage() {
    	$ret = "";
		$bt = debug_backtrace();
        foreach ($bt as $d) {
        	if ($d['function'] == __FUNCTION__) continue;
        	if ($d['function'] == '__lambda_func') continue;
        	$pathinfo = pathinfo($d['file']);
        	if ($d['function'] == '__construct') $d['function'] = '';
        	$name = (isset($d['class']) && $d['function'] != '') ? $d['class'].'.' : $d['class'];
			$ret .= sprintf("  at %s%s(%s:%s)\n",
				$name, $d['function'], $pathinfo['basename'], $d['line']);
        }
        return $ret;
    }
    public function getStackTrace() {
		$msg = "ErrorMessage : ".$this->message."\n";
		if ($this->code != 0) {
			$msg .= "ErrorCode : ".$this->code."\n";
		}
		//$msg .= $this->traceMessage;
		$msg .= $this->getTraceAsString();
		return $msg;
    }
}
?>