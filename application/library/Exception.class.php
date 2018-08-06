<?php
/**
 * 自定义错误处理
 * @Create             Sublime
 * @Author             MORTAL
 * @DateTime 		   2017-07-06
 * @version            [v1]
 */
class MyException extends Exception{
	public function __construct($message, $code = 0){
		parent::__construct($message, $code);
	}

	public function __toString(){
		$message = "<h1>出现异常了，信息如下</h1>";
		$message .= "<p>".__CLASS__."[{$this->code}]:{$this->message}</p>";
		return $message;
	}

	public function test(){
		echo "this is test";
	}

	public function stop(){
		exit('script stop..');
	}

	// 自定义其它方法
}
try{
	echo "出现异常了";
	throw new MyException('自定义异常');
}catch(MyException $e){
	echo $e->getMessage();
	echo '<hr/>';
	$e->test();
}
echo '<hr/>';
echo 'continue...';