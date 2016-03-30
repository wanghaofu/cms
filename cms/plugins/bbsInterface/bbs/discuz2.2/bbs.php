<?php
/**
 * Discuz! 2.2F BBS Interface
 * @package bbsInterface
 * @access public
 */
class BBS {
	/**
	 * session id,discuz和ipb都保持一个session来进行会话,phpwind直接使用cookie
	 * @var string  
 	 */
	var $sId = '';

	/**
	 * 用户名,来自Cookie
	 * @var string  
 	 */
	var $user = '';

	/**
	 * 用户密码,来自cookie
	 * @var string 
	 */
	var $pass = '';

	/**
	 * 论坛的数据表(类封装)
	 * @var object
 	 */
	var $table = '';

	/**
	 * Cookie路径
	 * @var string  
	 */
	var $cookiepath = '';

	/**
	 * Cookie作用域
	 * @var string  
	 */
	var $cookiedomain = '';

	/**
	 * 用户Session数组,主要保存用户id,组id和其他附加数据,提供给CMSware会员权限接口进行权限验证
	 * @var   array  
	 */
	var $session = array(
			'UserID'=> '',
			'UserName'=> '',
			'Password'=> '',
			'GroupName'=> 'Guest',
			'GroupID'=> 1,
			'Credit'=> 0, //可是是用户威望,积分等
		
		);
	
	/**
	 * 当前时间
	 * @var int 
	 */
	var $timestamp = '';

	var $_FieldMapping = array();

	var $_SessionInfo = array();
	
	/**
	 * Constructor
	 *
	 * 初始化一些必要参数,读取论坛的Cookie,准备进行Cookie有效性验证
	 *
	 * @param string 论坛名
	 * @access private 
 	 */
	function BBS($bbs_name) 
	{
		include PLUGINS_PATH.'bbs/'.BBS_NAME.'/bbs.config.php';
		$this->table = new bbs_table($bbs_db_config);
		$this->onlinehold = $onlinehold;

		//按照GET,POST,COOKIE的顺序读取sid,对discuz,ipb这样的论坛有效,phpwind无须管这项
		$this->sId = isset($_GET['sid']) ? $_GET['sid'] : (isset($_POST['sid']) ? $_POST['sid'] : $_COOKIE['sid']); 
		
		//一个论坛注册的Cookie最基本也会保留username和password
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
	
	/**
	 * 验证sId/Cookie有效性
	 *
	 * 对于不同的论坛,这里的验证过程会有很大不同
	 * Discuz和IPB使用sId来进行会话保持,所有我们先判断sId的有效性,否则验证Cookie有效性
	 * 如果有效,更新当前会话时间
	 * 
	 * @param string | array | bool | int $xxx
	 * @access private | public
	 * @return string | array | bool | int
	 */
	function init()
	{
		global $db;

		if(!empty($this->sId)) {
			if($this->user) {
				//whether member sid
				$result = $db->getRow("SELECT s.sid, s.groupid, m.uid, m.username , m.password , m.status, m.email,  m.credit,g.grouptitle 
							FROM {$this->table->sessions} s, {$this->table->user} m, {$this->table->group} g WHERE m.username=s.username AND s.groupid=g.groupid AND s.sid='{$this->sId}' AND s.ip='{$this->ip}' ");
			} else {
				//maybe a guest sid
				$result = $db->getRow("SELECT sid, status, username, groupid FROM {$this->table->sessions} WHERE sid='{$this->sId}' AND ip='{$this->ip}'");
			}		
		
			if(empty($result['sid'])) { //sid not valid, maybe the session expired, now we check whether the cookies is valid
				
				$ips = explode('.', $this->ip); //whether is banned
				$sql = "SELECT COUNT(*) FROM {$this->table->banned} WHERE (ip1='$ips[0]' OR ip1='-1') AND (ip2='$ips[1]' OR ip2='-1') AND (ip3='$ips[2]' OR ip3='-1') AND (ip4='$ips[3]' OR ip4='-1')";
				$info = $db->getRow($sql);
				if($info[0]) {
					$statusverify = 'u.status=\'IPBanned\'';
					$ipbanned = 1;
				} else {
					$statusverify = 'u.status=m.status';
				}
				if($this->user) {
					$sql = "SELECT m.*,  u.groupid, u.specifiedusers LIKE '%\t".addcslashes($this->user, '%_')."\t%' AS specifieduser
							FROM {$this->table->user} m LEFT JOIN {$this->table->group} u ON u.specifiedusers LIKE '%\t".addcslashes($this->user, '%_')."\t%' OR ( {$statusverify} AND ((u.creditshigher='0' AND u.creditslower='0' AND u.specifiedusers='') OR (m.credit>=u.creditshigher AND m.credit<u.creditslower)))
							WHERE username='{$this->user}' AND password='{$this->pass}' ORDER BY specifieduser DESC";

					if(!($result = $db->getRow($sql))) { //cookies invalidate
						$this->clearcookies();
					} else { 
						$this->registerSession($result);
						return true;
					}
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
				return true;

			
			}		
		} else {
			if($this->user) {
				$ips = explode('.', $this->ip); //whether is banned
				$sql = "SELECT COUNT(*) FROM {$this->table->banned} WHERE (ip1='$ips[0]' OR ip1='-1') AND (ip2='$ips[1]' OR ip2='-1') AND (ip3='$ips[2]' OR ip3='-1') AND (ip4='$ips[3]' OR ip4='-1')";
				$info = $db->getRow($sql);
				if($info[0]) {
					$statusverify = 'u.status=\'IPBanned\'';
					$ipbanned = 1;
				} else {
					$statusverify = 'u.status=m.status';
				}


				$sql = "SELECT m.*,  u.groupid,u.grouptitle, u.specifiedusers LIKE '%\t".addcslashes($this->user, '%_')."\t%' AS specifieduser
						FROM {$this->table->user} m LEFT JOIN {$this->table->group} u ON u.specifiedusers LIKE '%\t".addcslashes($this->user, '%_')."\t%' OR ($statusverify AND ((u.creditshigher='0' AND u.creditslower='0' AND u.specifiedusers='') OR (m.credit>=u.creditshigher AND m.credit<u.creditslower)))
						WHERE username='{$this->user}' AND password='{$this->pass}' ORDER BY specifieduser DESC";

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
	 * 用户为匿名用户,为Guest用户生成一个session
	 * @return array
	 */
	function makeGuestSession()
	{
		$result['grouptitle'] = 'Guest';
		$result['groupid'] = '1';
		$result['credit'] = '0';
		return $this->registerSession($result);

	}





	/**
	 * 注册Session
	 *
	 * 如果上面的init()判断sId无效(过期或不存在),并且判断Cookie为有效,我们就在这里给用户注册一个session
	 * 注: PHPwind无须注册,它直接用个Cookie 
	 * 
	 * @param  array  $result 用户参数,来自论坛用户数据表
	 * @access private  
	 * @return bool  
	 */
	function registerSession(&$result)
	{
		global $db,$_FieldMapping,$_SessionInfo;
		$this->clearRubbishSession();
		$sid = random(8);
		$db->query("DELETE FROM ".$this->table->sessions." WHERE ip='{$this->ip}' AND username='{$this->user}'");		

		$sql = "INSERT INTO {$this->table->sessions} (sid, ip, ipbanned, status, username, lastactivity, groupid, styleid, action, fid, tid)
				VALUES ('$sid', '{$this->ip}', '0', '{$result[status]}', '{$this->user}', '{$this->timestamp}', '{$result[groupid]}', '', '', '', '')";
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

	/**
	 * 更新用户Session
	 * 
	 * 用户sId是有效的,我们这里更新用户的最新会话时间,不更新的话,说明用户在潜水
	 *
 	 * @access private  
	 * @return bool 
	 */
	function updateSession()
	{
		global $db ;
		$sql = "UPDATE {$this->table->sessions} SET lastactivity='{$this->timestamp}' WHERE  sid='{$this->sId}'";
		if($db->query($sql))	return true;
		else return false;
	
	}

	/**
	 * 清除无效session
	 *
	 * 潜水太久的用户session,我删
	 *
 	 * @access private 
	 * @return bool 
	 */
	function clearRubbishSession()
	{
		global $db;

		$cut_off_stamp = $this->timestamp - $this->onlinehold;
		
		return $db->query("DELETE FROM ".$this->table->sessions." WHERE lastactivity < $cut_off_stamp");		
	
	}

	/**
	 * 清空用户Cookie
	 * 
	 * 上面检测到用户的Cookie是无效的,清除吧
	 * @ignore
	 * @access private  
	 */
	function clearcookies() {
		setcookie('_discuz_user', '', $this->timestamp - 86400 * 365, $this->cookiepath,  $this->cookiedomain);
		setcookie('_discuz_pw', '', $this->timestamp - 86400 * 365,  $this->cookiepath,  $this->cookiedomain);
	}

}
?>