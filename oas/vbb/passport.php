<?php
/*************通行证配置开始***************/
$passport_key = "iampassword";	//应用程序与OAS间设置的通行证密码，请保持跟oas目录中的oas.config.php中的$SYS_ENV['passport_key']变量一致
$passport_expire = "3600";					//在跟OAS交互的过程中，用户会话数据最大过期时间，一般设为3600即可
$oas_url	= "http://localhost/cmsphp4/oas/";		//您自己OAS的URL，请勿忘记最后的斜框"/"
/*************通行证配置结束**************/

/*************************以下请勿更改***************************************/
require_once('./global.php');
require_once(DIR . '/includes/functions_login.php');

$_GET['verify'] != md5($_GET['action'].$_GET['auth'].$_GET['forward'].$passport_key) && exit('Illegal request');
empty($_GET['forward']) && $_GET['forward'] = "/";

if($_GET['action'] == 'login') {
	$memberfields = $remoteinfo = array();
	parse_str(passport_decrypt($_GET['auth'], $passport_key), $member);
	foreach($member as $key => $val) {
		if(in_array($key, array('username', 'password', 'email', 'gender', 'bday', 'regip', 'regdate', 'nickname', 'site', 'qq', 'msn', 'yahoo'))) {
			$memberfields[$key] = addslashes($val);
		} elseif(in_array($key, array('cookietime', 'time'))) {
			$remoteinfo[$key] = $val;
		} 
	}

	if(strlen($memberfields['username'] = preg_replace("/(c:\\con\\con$|[%,\*\"\s\t\<\>\&])/i", "", $memberfields['username'])) > 15) {
		$memberfields['username'] = substr($memberfields['username'], 0, 15);
	}

	if(empty($remoteinfo['time']) || empty($memberfields['username']) || empty($memberfields['password']) || empty($memberfields['email'])) {
		exit('Lack of required parameters');
	} elseif($timestamp - $remoteinfo['time'] > $passport_expire) {
		exit('Request expired');
	}

	$memberfields['regip'] = empty($memberfields['regip']) ? $onlineip : $memberfields['regip'];
	$memberfields['regdate'] = empty($memberfields['regdate']) ? $timestamp : $memberfields['regdate'];
	
	$rs = $vbulletin->db->query_first("SELECT userid, usergroupid, membergroupids, infractiongroupids, salt FROM " . TABLE_PREFIX . "user WHERE username = '{$memberfields['username']}'");
	$isReg = $rs ? 'yes' : 'no'; //是否已经注册
	switch ($isReg) {
		case 'no':
			$userdata =& datamanager_init('User', $vbulletin, ERRTYPE_ARRAY);
			$userdata->set('password',$memberfields['password']);
			$userdata->set('email', $memberfields['email']);
			$userdata->set('username', $memberfields['username']);
			$userdata->set('usergroupid', 2);
			$userdata->set('languageid', 1);
			$userdata->set_usertitle('', false, $vbulletin->usergroupcache["$newusergroupid"], false, false);
			//$userdata->set_userfields($vbulletin->GPC['userfield'], true, 'register');
			$userdata->set('showbirthday', $vbulletin->GPC['showbirthday']);
			/*$userdata->set('birthday', array(
				'day'   => $vbulletin->GPC['day'],
				'month' => $vbulletin->GPC['month'],
				'year'  => $vbulletin->GPC['year']
			));*/
			$userdata->set_dst($vbulletin->GPC['dst']);
			$userdata->set('timezoneoffset', 8);
			$userdata->set('ipaddress', IPADDRESS);
			$userdata->pre_save();
			$userdata->save();
			$rs = $vbulletin->db->query_first("SELECT userid, usergroupid, membergroupids, infractiongroupids, salt FROM " . TABLE_PREFIX . "user WHERE username = '{$memberfields['username']}'");
			$isReg = $rs ? 'yes' : 'no'; //是否已经注册成功
		case 'yes':
			$memberfields['timestamp'] = time();
			$memberfields['regdate'] = strtotime($memberfields['regdate']);
			$memberfields['brithdaydate'] = $memberfields['bday']=='0000-00-00' ? '' : strftime('%m-%d-%Y',strtotime($memberfields['bday']));
			$memberfields['password'] = md5($memberfields['password'].$rs['salt']);
			$sql = "UPDATE " . TABLE_PREFIX . "user SET password='{$memberfields['password']}',passworddate=CURRENT_DATE(), ipaddress='{$memberfields['regip']}', joindate='{$memberfields['regdate']}', lastvisit='{$memberfields['timestamp']}', lastactivity='{$memberfields['timestamp']}',email='{$memberfields['email']}',birthday='{$memberfields['brithdaydate']}',birthday_search='{$memberfields['bday']}' WHERE userid ='{$rs['userid']}'";
			//exit($sql);
			$vbulletin->db->query_write($sql);
			$rs['username'] = $memberfields['username'];
			$rs['password'] = $memberfields['password'];
			$vbulletin->userinfo = $rs;
			vbsetcookie('userid', '', true, true, true);
			vbsetcookie('password', '', true, true, true);
			exec_unstrike_user($memberfields['username']);
			process_new_login('', '', '');
			exec_shut_down();
	}
} elseif ($_GET['action'] == 'logout') {
	process_logout();
} else {
	exit('Invalid action');
}
header("location:{$_GET['forward']}");
exit;

function passport_decrypt($txt, $key) {
	$txt = passport_key(base64_decode($txt), $key);
	$tmp = '';
	for($i = 0;$i < strlen($txt); $i++) {
		$md5 = $txt[$i];
		$tmp .= $txt[++$i] ^ $md5;
	}
	return $tmp;
}

function passport_key($txt, $encrypt_key) {
	$encrypt_key = md5($encrypt_key);
	$ctr = 0;
	$tmp = '';
	for($i = 0; $i < strlen($txt); $i++) {
		$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
	}
	return $tmp;
}

/********************
@   CWPS for VBB
@   Author: AT
@   2007.11.12
**********************/
?>