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
			'GroupID'=> 7,
			'Credit'=> 0,

		);

	function BBS($bbs_name)                      //读取bbs的cookie
	{
		include PLUGINS_PATH.'bbs/'.BBS_NAME.'/bbs.config.php';
		$this->table = new bbs_table($bbs_db_config);
		$this->onlinehold = $onlinehold;
		$this->sId = isset($_GET['sid']) ? $_GET['sid'] : (isset($_POST['sid']) ? $_POST['sid'] : $_COOKIE['sid']);
		$this->user = kAddslashes($_COOKIE['_discuz_user']);
		$this->pass = kAddslashes($_COOKIE['_discuz_pw']);
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



				$result = $db->getRow("SELECT s.sid, s.groupid, m.uid, m.username , m.password , m.sigstatus, m.email,  m.credit,g.grouptitle
							FROM {$this->table->sessions} s, {$this->table->user} m, {$this->table->group} g WHERE m.username=s.username AND s.groupid=g.groupid AND s.sid='{$this->sId}' AND s.ip1='$nowIP[0]' AND s.ip2='$nowIP[1]' AND s.ip3='$nowIP[2]' AND s.ip4='$nowIP[3]' ");
			} else {

				//maybe a guest sid

				$result = $db->getRow("SELECT sid, uid, username, groupid FROM {$this->table->sessions} WHERE sid='{$this->sId}' AND ip1='$nowIP[0]' AND ip2='$nowIP[1]' AND ip3='$nowIP[2]' AND ip4='$nowIP[3]'");
			}

			if(empty($result['sid'])) { //sid not valid, maybe the session expired, now we check whether the cookies is valid

				$ips = explode('.', $this->ip); //whether is banned
				$sql = "SELECT COUNT(*) FROM {$this->table->banned} WHERE (ip1='$ips[0]' OR ip1='-1') AND (ip2='$ips[1]' OR ip2='-1') AND (ip3='$ips[2]' OR ip3='-1') AND (ip4='$ips[3]' OR ip4='-1')";
				$info = $db->getRow($sql);
				if($info[0]) {
					$statusverify = 'u.allowcstatus =\'IPBanned\'';
					$ipbanned = 1;
				} else {
					$statusverify = 'u.allowcstatus=m.sigstatus';
				}

				if($this->user) {
					$sql = "SELECT m.*,  u.groupid, u.groupavatar LIKE '%\t".addcslashes($this->user, '%_')."\t%' AS specifieduser
							FROM {$this->table->user} m LEFT JOIN {$this->table->group} u ON u.groupavatar LIKE '%\t".addcslashes($this->user, '%_')."\t%' OR ( {$statusverify} AND ((u.creditshigher='0' AND u.creditslower='0' AND u.groupavatar='') OR (m.sigstatus>=u.creditshigher AND m.sigstatus<u.creditslower)))
							WHERE username='{$this->user}' AND password='{$this->pass}' ORDER BY groupavatar DESC";

					if(!($result = $db->getRow($sql))) { //cookies invalidate
						$this->clearcookies();

					} else {
						$this->registerSession($result);
						return true;
					}
				}else{

                	$this->clearcookies();


                }

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
				$ips = explode('.', $this->ip); //whether is banned
				$sql = "SELECT COUNT(*) FROM {$this->table->banned} WHERE (ip1='$ips[0]' OR ip1='-1') AND (ip2='$ips[1]' OR ip2='-1') AND (ip3='$ips[2]' OR ip3='-1') AND (ip4='$ips[3]' OR ip4='-1')";
				$info = $db->getRow($sql);
				if($info[0]) {
					$statusverify = 'u.allowcstatus=\'IPBanned\'';
					$ipbanned = 1;
				} else {
					$statusverify = 'u.allowcstatus=m.sigstatus';
				}


				$sql = "SELECT m.*,  u.groupid, u.groupavatar LIKE '%\t".addcslashes($this->user, '%_')."\t%' AS specifieduser
							FROM {$this->table->user} m LEFT JOIN {$this->table->group} u ON u.groupavatar LIKE '%\t".addcslashes($this->user, '%_')."\t%' OR ( {$statusverify} AND ((u.creditshigher='0' AND u.creditslower='0' AND u.groupavatar='') OR (m.sigstatus>=u.creditshigher AND m.sigstatus<u.creditslower)))
							WHERE username='{$this->user}' AND password='{$this->pass}' ORDER BY groupavatar DESC";

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
		$result['grouptitle'] = 'Guest';
		$result['groupid'] = '7';
		$result['credit'] = '0';
		return $this->registerSession($result);

	}






	function registerSession(&$result)
	{
		global $db,$_FieldMapping,$_SessionInfo;
		$this->clearRubbishSession();
		$sid = random(8);
        $nowIP = explode(".",$this->ip);
		$db->query("DELETE FROM ".$this->table->sessions." WHERE ip1='$nowIP[0]' AND ip2='$nowIP[1]' AND ip3='$nowIP[2]' AND ip4='$nowIP[3]' AND username='{$this->user}'");

		$sql = "INSERT INTO {$this->table->sessions} (`sid` , `ip1` , `ip2` , `ip3` , `ip4` , `uid` , `username` , `groupid` , `styleid` , `invisible` , `action` , `lastactivity` , `fid` , `tid`)
				VALUES ('$sid', '$nowIP[0]', '$nowIP[1]', '$nowIP[2]', '$nowIP[3]', '$sId', '{$this->user}', '$result[groupid]', '0', '0', '0', '{$this->timestamp}', '0', '0')";
		if($db->query($sql)) {
			//echo '+';
			setcookie('sid', $sid, 0, $this->cookiepath, $this->cookiedomain);
			foreach($this->_SessionInfo as $key=>$var) {
				$this->session[$var] = $result[$this->_FieldMapping[$var]];


			}
			//print_r($this->session);
			return true;
		} else return false;


	}

	function updateSession()
	{
		global $db ;
		$sql = "UPDATE {$this->table->sessions} SET lastactivity='{$this->timestamp}' WHERE  sid='{$this->sId}'";
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
		setcookie('_discuz_user', '', $this->timestamp - 86400 * 365, $this->cookiepath,  $this->cookiedomain);
		setcookie('_discuz_pw', '', $this->timestamp - 86400 * 365,  $this->cookiepath,  $this->cookiedomain);
	}

}
?>