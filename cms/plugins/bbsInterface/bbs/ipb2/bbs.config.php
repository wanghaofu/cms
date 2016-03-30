<?php

$bbs_db_config['db_name']     = 'ipb';		//数据库名称
$bbs_db_config['table_pre']     = "ibf_";	//数据表前缀
$onlinehold = 600;		// 在线保持时间(秒)

// ============= 如您对 cookie 作用范围有特殊要求,请修改下面变量 ==============

	$cookiepath = '/';		// cookie 作用路径 (如出现登录问题请修改此项)
	$cookiedomain = ''; 		// cookie 作用域 (如出现登录问题请修改此项)

// ============================================================================

$_FieldMapping = array(
	'UserID'=> 'member_id',
	'UserName'=>'member_name',
	'Password'=>'member_login_key',
    'UserTable_UserID'=>'id', //用户表-用户ID
    'UserTable_UserName'=>'name', //用户表-用户名
    'GroupTable_GroupID'=>'g_id', //用户组表-组ID
	'GroupID'=>'member_group',  
	'GroupName'=>'g_title',
    'Credit'=>'posts',

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

	function bbs_table($bbs_db_config)
	{

		$this->user = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'members';
		$this->group = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'groups';
        $this->sessions = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'sessions';
		//$this->config = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'config';
		//$this->banned = $bbs_db_config['db_name'].'.'.$bbs_db_config['table_pre'].'banned';

	}


}
$bbs_table = new bbs_table($bbs_db_config);


?>