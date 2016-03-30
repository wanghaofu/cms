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

	function BBS($bbs_name)                      //读取bbs的cookie
	{
		include PLUGINS_PATH.'bbs/'.BBS_NAME.'/bbs.config.php';
		$this->table = new bbs_table($bbs_db_config);
		$this->onlinehold = $onlinehold;
		$this->sId = isset($_GET['sessionhash']) ? $_GET['sessionhash'] : (isset($_POST['sessionhash']) ? $_POST['sessionhash'] : $_COOKIE['sessionhash']);
		$this->user = kAddslashes($_COOKIE['bbusername']);
		$this->pass = kAddslashes($_COOKIE['bbpassword']);
		$this->ip = $GLOBALS['IN']['IP_ADDRESS'];
		$this->cookiepath = $cookiepath;
		$this->cookiedomain = $cookiedomain;
		$this->timestamp = time();
		$this->_FieldMapping = &$_FieldMapping ;
		$this->_SessionInfo = $_SessionInfo;
		$this->init();


	}

	function init()                     //获取用户的id和组id .
	{
		global $db;

		if(!empty($this->sId)) {         //如用户id存在

        //echo $this->sId;



        	$nowIP = explode(".",$this->ip);

			if($this->user) {

				//whether member sid



				$result = $db->getRow("SELECT s.sessionhash, m.userid, m.usergroupid, m.username , m.password , m.email,  m.posts, g.title
							FROM {$this->table->sessions} s, {$this->table->user} m, {$this->table->group} g WHERE m.username='{$this->user}' AND m.usergroupid=g.usergroupid AND s.sessionhash='{$this->sId}' AND s.host='$this->ip' ");
			} else {

				//maybe a guest sid

				$result = $db->getRow("SELECT s.sessionhash, m.userid, m.usergroupid, m.username , m.password , m.email,  m.posts, g.title
							FROM {$this->table->sessions} s, {$this->table->user} m, {$this->table->group} g WHERE s.sessionhash='{$this->sId}' AND s.host='$this->ip' AND s.userid > 0 ");
			}
            //echo "aaaa".$result['sessionhash'].$this->sId;
			if(empty($result['sessionhash'])) { //sid not valid, maybe the session expired, now we check whether the cookies is valid

				$this->clearcookies();

			} else { // valid session


				if(!empty($result['username'])) { //a Member Session
					//$GroupInfo = Access::getGroupInfo($result['groupid']);
					foreach($this->_SessionInfo as $key=>$var) {
						$this->session[$var] = $result[$this->_FieldMapping[$var]];


					}
					//$this->session['UserID'] = $result['uid'];
					//$this->session['UserName'] = $result['username'];
					//$this->session['Password'] = $result['password'];
					//$this->session['Group'] = $result['status'];
					//$this->session['GroupID'] = $result['groupid'];
					//$this->session['Credit'] = $result['credit'];
				}
				$this->updateSession();
                //print_r($this->session);
				return true;


			}
		} else {

			if($this->user) {

				$sql = "SELECT *
						FROM {$this->table->user}
						WHERE username='{$this->user}' AND password='{$this->pass}'";

				if($result = $db->getRow($sql)) { //cookies invalidate
					$this->registerSession($result);
					return true;
				} else {
					$this->clearcookies();
					return $this->makeGuestSession();
				}
			} else {

				return $this->makeGuestSession();
                //$this->clearcookies();
			}

		}






	}

	/**
	 * 为Guest用户生成一个session
	 *
	 */
	function makeGuestSession()
	{
		$result['title'] = 'Guest';
		$result['usergroupid'] = '1';
		$result['posts'] = '0';
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

	function updateSession()
	{
		global $db ;
		$sql = "UPDATE {$this->table->sessions} SET lastactivity='{$this->timestamp}' WHERE  sessionhash='{$this->sId}'";
		if($db->query($sql))	return true;
		else return false;

	}

	function clearRubbishSession()
	{
		global $db;

		$cut_off_stamp = $this->timestamp - $this->onlinehold;

		$db->query("DELETE FROM ".$this->table->sessions." WHERE lastactivity < $cut_off_stamp");

	}


	function clearcookies() {
		setcookie('sessionhash', '', $this->timestamp - 86400 * 365, $this->cookiepath,  $this->cookiedomain);
		setcookie('bbpassword', '', $this->timestamp - 86400 * 365,  $this->cookiepath,  $this->cookiedomain);
	}

}
?>