<?php

$bbs_db_config['db_name']     = 'phpwind';		//数据库名称
$bbs_db_config['table_pre']     = "pw_";	//数据表前缀
$onlinehold = 600;		// 在线保持时间(秒)

// ============= 如您对 cookie 作用范围有特殊要求,请修改下面变量 ==============

	$cookiepath = '/bbs/';		// cookie 作用路径 (如出现登录问题请修改此项)
	$cookiedomain = 'www.localhost.com'; 		// cookie 作用域 (如出现登录问题请修改此项)

// ============================================================================

$_FieldMapping = array(
	'UserID'=> 'uid',
	'UserName'=>'username',
	'Password'=>'password',
	'GroupTable_GroupID'=>'gid',
	'GroupID'=>'groupid',
	'GroupName'=>'grouptitle',
	'Credit'=>'credit',
	'Money'=>'money',

);
$_SessionInfo = array(
	'UserID',
	'UserName',
	'Password',
	'GroupID',
	'GroupName',
	'Credit',
	'Money',

);
class bbs_table {
	var $user;
	var $group;
	var $sessions;
	var $banned;

	function bbs_table($bbs_db_config)
	{

		$this->user = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'members';
		$this->group = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'usergroups';
		$this->config = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'config';
		//$this->banned = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'banned';

	}


}
$bbs_table = new bbs_table($bbs_db_config);


?>