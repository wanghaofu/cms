<?php
class BBS {
	var $sId = '';
	var $user = '';
	var $pass = '';
	var $table = '';
	var $cookiepath = '';
	var $cookiedomain = '';
	var $session = array(
			'UserID'=> '',
			'UserName'=> '',
			'Password'=> '',
			'GroupName'=> 'Guest',
			'GroupID'=> 1,
			'Credit'=> 0,

		);

	function BBS($bbs_name)
	{
		include PLUGINS_PATH.'bbs/'.BBS_NAME.'/bbs.config.php';
		$this->table = new bbs_table($bbs_db_config);
		$this->onlinehold = $onlinehold;
		$this->sId = isset($_GET['sid']) ? $_GET['sid'] : (isset($_POST['sid']) ? $_POST['sid'] : $_COOKIE['sid']);
  		$this->uId = kAddslashes($_COOKIE['winduid']);
		$this->user = kAddslashes($_COOKIE['winduid']);
		$this->pass = kAddslashes($_COOKIE['windpwd']);
		$this->ip = $GLOBALS['IN']['IP_ADDRESS'];
		$this->cookiepath = $cookiepath;
		$this->cookiedomain = $cookiedomain;
		$this->timestamp = time();
		$this->_FieldMapping = &$_FieldMapping ;
		$this->_SessionInfo = $_SessionInfo;
		$this->init();
		//print_r($_COOKIE);


	}

	function init()
	{
		global $db;


		if($this->user) {
			//  $ips = explode('.', $this->ip); //whether is banned
			//	$sql = "SELECT db_value FROM {$this->table->config} WHERE db_name='db_ipban'";
			//	$info = $db->getRow($sql);
				$sql = "SELECT u.*, g.grouptitle,g.gptype FROM {$this->table->user} u,{$this->table->group} g WHERE u.groupid=g.gid AND u.uid='{$this->uId}' AND u.password='{$this->pass}'";

				if($result = $db->getRow($sql)) { //cookies invalidate
					$this->registerSession($result);
					return true;
				} else {
					$this->clearcookies();
					return $this->makeGuestSession();
				}
		} else {

			return $this->makeGuestSession();
		}







	}

	/**
	 * 为Guest用户生成一个session
	 *
	 */
	function makeGuestSession()
	{
		$result['grouptitle'] = 'Guest';
		$result['groupid'] = '1';
		$result['credit'] = '0';
		return $this->registerSession($result);

	}






	function registerSession(&$result)
	{
		global $db,$_FieldMapping,$_SessionInfo;
 		foreach($this->_SessionInfo as $key=>$var) {
			$this->session[$var] = $result[$this->_FieldMapping[$var]];


		}
		return true;


	}





	function clearcookies() {
		setcookie('_discuz_user', '', $this->timestamp - 86400 * 365, $this->cookiepath,  $this->cookiedomain);
		setcookie('_discuz_pw', '', $this->timestamp - 86400 * 365,  $this->cookiepath,  $this->cookiedomain);
	}

}
?>