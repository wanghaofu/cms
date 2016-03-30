<?php

$bbs_db_config['db_name']     = 'discuz_4';		//数据库名称
$bbs_db_config['table_pre']     = "cdb_";	//数据表前缀
$onlinehold = 600;		// 在线保持时间(秒)

// ============= 如您对 cookie 作用范围有特殊要求,请修改下面变量 ==============

	$cookiepath = '/';		// cookie 作用路径 (如出现登录问题请修改此项)
	$cookiedomain = ''; 		// cookie 作用域 (如出现登录问题请修改此项)

// ============================================================================

$_FieldMapping = array(
	'UserID'=> 'uid',
	'UserName'=>'username',
	'Password'=>'password',
    'UserTable_UserID'=>'uid', //用户表-用户ID
    'UserTable_UserName'=>'username', //用户表-用户名
	'GroupTable_GroupID'=>'groupid',
	'GroupID'=>'groupid',
	'GroupName'=>'grouptitle',
	'Credit'=>'credit',

);
$_SessionInfo = array(
	'UserID',
	'UserName',
	'Password',
	'GroupID',
	'GroupName',
	'Credit',

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
		$this->sessions = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'sessions';
		$this->banned = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'banned';

	}


}
$bbs_table = new bbs_table($bbs_db_config);


?>