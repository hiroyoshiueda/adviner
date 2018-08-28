<?php
/**
 * Enter description here...
 *
 */
class DbException extends SpException
{
	protected $sql = '';
    public function __construct($sql='', $errmsg=null, $errcode=0) {
        $this->sql = $sql;
        $this->traceMessage = $this->getTraceMessage();
        parent::__construct($errmsg, (int)$errcode);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    public function getStackTrace() {
		$msg = "ErrorMessage : ".$this->message."\n";
		if ($this->code != 0) {
			$msg .= "ErrorCode : ".$this->code."\n";
		}
		if ($this->sql != '') $msg .= "Sql : ".$this->sql."\n";
		$msg .= $this->traceMessage;
		return $msg;
    }
}
?>