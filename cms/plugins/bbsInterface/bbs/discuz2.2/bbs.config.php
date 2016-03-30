<?php

$bbs_db_config['db_name']     = 'discuz2';		//数据库名称
$bbs_db_config['table_pre']     = "cdb_";	//数据表前缀
$onlinehold = 600;		// 在线保持时间(秒)

// ============= 如您对 cookie 作用范围有特殊要求,请修改下面变量 ==============

	$cookiepath = '/';		// cookie 作用路径 (如出现登录问题请修改此项)
	$cookiedomain = ''; 		// cookie 作用域 (如出现登录问题请修改此项)

// ============================================================================

/**
 * BBS字段到CMSware会员资料定义字段的映射
 * 
 * 不同的论坛,映射都不一样
 * UserID - 用户ID
 * UserName - 用户名
 * Password - 用户密码
 * GroupTable_GroupID - BBS用户组表,组ID
 * GroupID - BBS用户表,组ID
 * GroupName - 用户组名
 * Credit - 积分,威望等
 * 
 * @var array 
 */
$_FieldMapping = array(
	'UserID'=> 'uid',
	'UserName'=>'username',
	'Password'=>'password',
	'GroupTable_GroupID'=>'groupid',
	'GroupID'=>'groupid',
	'GroupName'=>'grouptitle',
	'Credit'=>'credit',
	//你可以在这里继续添加映射,从论坛提取更多的用户信息交给会员接口进行权限验证
);

/**
 * 返回的用户Session数据包含的内容,$_SessionInfo里面注册的key必须在$_FieldMapping里面有对应的映射定义
 * @var array 
 */
$_SessionInfo = array(
	'UserID',
	'UserName',
	'Password',
	'GroupID',
	'GroupName',
	'Credit',


);

/**
 * 论坛数据表类封装
 * @access  public
 */
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