<?php
/**
 * session会话管理类库
 * @Create             Sublime
 * @Author             MORTAL
 * @DateTime 		   2017-07-16
 * @version            [v1]
 */
class Session implements SessionHandlerInterface
{
	private $link;
	private $lifetime;

	public function open ( string $save_path , string $session_name ){
		$this->lifetime = get_cfg_var('session.gc_maxlifetime');
		$this->link = mysqli_connect('localhost', 'root', 'root');
		mysqli_set_charset($this->link, 'utf-8');
		mysqli_select_db($this->link, 'test1');
		if($this->link){
			return true;
		}
		return false;
	}

	/**
	 * 写入操作
	 * @param  string    $session_id   
	 * @param  string    $session_data 
	 * @return bool     
	 */
	public function write ( string $session_id , string $session_data ){
		$nesExp = time() + $this->lifetime;
		$session_id = mysqli_escape_string($this->link, $session_id);
		// 首先查询是否存在指定的sessionId,如果存在则相当于更新数据，否则是第一次写入数据
		$sql = "SELECT * FROM session WHERE id='{$session_id}'";
		$result = mysqli_query($this->link, $sql);
		if(mysqli_num_rows($result) == 1){
			$sql = "UPDATE session SET session_expires='{$nesExp}',session_data='{$session_data}' WHERE session_id = '{$session_id}'";
			// $result = mysqli_query($this->link, $sql);
		}else{
			$sql = "INSERT session VALUES ('{$session_id}', '{$session_data}', '{$nesExp}')";
		}
		$result =mysqli_query($this->link, $sql);
		return mysqli_affected_rows($this->link) == 1;
	}

	/**
	 * 读取操作
	 * @param  string    $session_id [description]
	 * @return [type]                [description]
	 */
	public function read ( string $session_id ){
		$id = mysqli_escape_string($this->link, $session_id);
		$sql = "SELECT * FROM session WHERE session_id='{$session_id}' AND session_expires>".time();
		$result = mysqli_query($this->link, $sql);
		if(mysqli_num_rows($result) == 1){
			return mysqli_fetch_assoc($result)['session_data'];
		}
		return '';
	}

	

	public function close ( void ){
		mysqli_close($this->link);
		return true;
	}

	/**
	 * 删除session
	 * @param  string    $session_id [description]
	 * @return [bool]                [description]
	 */
	public function destroy ( string $session_id ){
		$session_id = mysqli_escape_string($this->link, $session_id);
		$sql = "DELETE FROM session WHERE session_id='{$session_id}'";
		$result = mysqli_query($this->link, $sql);
		return mysqli_affected_rows($result) == 1; //三元操作符
	}

	/**
	 * 垃圾回收 清空旧的session
	 * @param  int       $maxlifetime [最大时间]
	 * @return  [bool]                 [description]
	 */
	public function gc ( int $maxlifetime ){
		$sql = "DELETE FROM session WHERE session_expires < ".time();
		$result = mysqli_query($this->link, $sql);
		if(mysqli_affected_rows($this->link) > 0){
			return true;
		}
		return false;
	}	
}