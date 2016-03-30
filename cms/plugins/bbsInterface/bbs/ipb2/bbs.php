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
			'Credit'=> 0, //可是是用户威望,积分等

	);

	function BBS($bbs_name)                      //读取bbs的cookie
	{
		include PLUGINS_PATH.'bbs/'.BBS_NAME.'/bbs.config.php';
		$this->table = new bbs_table($bbs_db_config);
		$this->onlinehold = $onlinehold;
		$this->sId = isset($_GET['member_id']) ? $_GET['member_id'] : (isset($_POST['member_id']) ? $_POST['member_id'] : $_COOKIE['member_id']);
		$this->user = $this->sId;
		$this->pass = kAddslashes($_COOKIE['pass_hash']);
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
			if($this->user) {
				//whether member sid
				$result = $db->getRow("SELECT s.member_id, s.member_name,s.member_group, m.id, m.name, m.member_login_key, m.title, m.posts, m.email,g.g_title
							FROM {$this->table->sessions} s, {$this->table->user} m, {$this->table->group} g WHERE m.name=s.member_name AND s.member_group=g.g_id AND s.member_id='{$this->sId}' AND s.ip_address='{$this->ip}' ");
				//print_r($result);
			} else {
				//maybe a guest sid
				$result = $db->getRow("SELECT member_id, member_name, member_group FROM {$this->table->sessions} WHERE member_id='{$this->sId}' AND ip_address='{$this->ip}'");
			}

        	if(empty($result['member_id'])) { //sid not valid, maybe the session expired, now we check whether the cookies is valid

				$this->clearcookies();

			} else { // valid session

				if(!empty($result['member_id'])) { //a Member Session
					//$GroupInfo = Access::getGroupInfo($result['groupid']);
				   	foreach($this->_SessionInfo as $key=>$var) {
						$this->session[$var] = $result[$this->_FieldMapping[$var]];


					}
					//$this->session['UserID'] = $result['member_id'];
					//$this->session['UserName'] = $result['member_name'];
					//$this->session['Password'] = $result['member_login_key'];
					//$this->session['Group'] = $result['g_title'];
					//$this->session['GroupID'] = $result['g_id'];
					//$this->session['Credit'] = $result['posts'];
				}
				//$this->updateSession();
				return true;


			}
		} else {

			if($this->user) {

				$sql = "SELECT *
						FROM {$this->table->user}
						WHERE name='{$this->user}' AND member_login_key='{$this->pass}'";

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
        $this->session['UserID'] = $result['member_id'];
		$this->session['UserName'] = $result['member_name'];
		$this->session['Password'] = $result['member_login_key'];
		$this->session['Group'] = $result['g_title'];
		$this->session['GroupID'] = $result['g_id'];
		$this->session['Credit'] = $result['posts'];
			//print_r($this->session);
		return true;


	}





	function clearcookies() {
		setcookie('member_id', '', $this->timestamp - 86400 * 365, $this->cookiepath,  $this->cookiedomain);
		setcookie('pass_hash', '', $this->timestamp - 86400 * 365,  $this->cookiepath,  $this->cookiedomain);
	}




	function clearRubbishSession()
	{
		global $db;

		$cut_off_stamp = $this->timestamp - $this->onlinehold;

		$db->query("DELETE FROM ".$this->table->sessions." WHERE lastactivity < $cut_off_stamp");

	}




}
?>