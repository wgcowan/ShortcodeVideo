<?php
class ShortcodeVideo_View_Helper_GetCalculatedTime extends Zend_View_Helper_Abstract
{
	function getCalculatedTime($timestring) {
		if (!strpos($timestring,":")){
			return $timestring;
		}else{
			sscanf($timestring, "%d:%d:%d", $hours, $minutes, $seconds);
			return isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
		}
	}
}
?>