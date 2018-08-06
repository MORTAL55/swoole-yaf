<?php
/**
 * 自定义错误器
 * @Create             Sublime
 * @Author             MORTAL
 * @DateTime 		   2017-07-16
 * @version            [v1]
 */
class ErrorHandler{
	public $message = '';
	public $filename = '';
	public $line = '';
	public $vars = array();
	protected $_noticeLog = 'D:\phpStudy\PHPTutorial\WWW\php\exception\noticeLog.log';

 	public function __construct($message, $filename, $line, $vars){
 		$this->message = $message;
 		$this->filename = $filename;
 		$this->line = $line;
 		$this->vars = $vars;
	}

	public static function deal($errno, $errmsg, $filename, $line, $vars){
		$self = new self($errmsg, $filename, $line, $vars);
		switch ($errno) {
			case E_USER_ERROR:
				return $self->dealError();
				break;
			case E_USER_WARNING:
				return $self->dealWarning();
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				return $self->dealNotice();
				break;
			default:
				return false;
				break;
		}
	}

	/**
	 * 处理致命级别的错误
	 */
	public function dealError(){
		ob_start();
		debug_print_backtrace();
		$backtrace = ob_get_flush();
		$errorMsg = <<<EOF
		出现了致命错误，如下：
		产生错误的文件：{$this->filename}
		产生的错误信息：{$this->message}
		产生错误的行号：{$this->line}
		追踪信息：{$backtrace}
EOF;
		// 将错误信息以邮件信息发送
		error_log($errorMsg, 1, '22@qq.com');
		exit(1);
	}

	/**
	 * 处理警告级别的错误
	 */
	public function dealWarning(){
		$errorMsg = <<<EOF
		出现了警告错误，如下：
		产生警告的文件：{$this->filename}
		产生的警告信息：{$this->message}
		产生警告的行号：{$this->line}
EOF;
		return error_log($errorMsg, 1, '22@qq.com');
	}

	/**
	 * 处理通知级别的错误
	 */
	public function dealNotice(){
		$datetime = date("Y-m-d H:i:s", time());
		$errorMsg = <<<EOF
		出现了通知错误，如下：
		产生通知的文件：{$this->filename}
		产生的通知信息：{$this->message}
		产生通知的行号：{$this->line}
		产生通知的时间：{$datetime}
EOF;
		if(!file_exists($this->_noticeLog)){
			mkdir('D:\phpStudy\PHPTutorial\WWW\php\exception\noticeLog.log', 0777, true);
		}
		return error_log($errorMsg, 3, $this->_noticeLog);
	}
}

error_reporting(-1);
set_error_handler(array('MyErrorHandler', 'deal'));
test();