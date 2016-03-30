<?php
//require_once './sources/validate.php';
require_once 'init.php';
require_once LIB_PATH."Spring.php";
$_BeanFactory = new Spring("spring.appcontext.php");
require_once INCLUDE_PATH."data.class.php";
require_once INCLUDE_PATH."functions.php";
require_once SYS_PATH."config.php";
if ($iwpc_debug) {
	$debugger = new Debug();
	$debugger->startTimer();
}

require_once KTPL_DIR . 'kTemplate.class.php';
require_once INCLUDE_PATH.'image.class.php';
require_once INCLUDE_PATH."file.class.php";
require_once INCLUDE_PATH."Error.php";
require_once INCLUDE_PATH."admin/userAdmin.class.php";
require_once INCLUDE_PATH."user/auth.class.php";
require_once INCLUDE_PATH."exception.class.php";
require_once INCLUDE_PATH."user/cache.class.php";
require_once INCLUDE_PATH."user/psn_admin.class.php";
//require_once LANG_PATH."$lang_user.php";
require_once KDB_DIR.'kDB.php';


$db = new kDB($db_config['db_driver']);
$db->connect($db_config);
$db->setFetchMode('assoc');
$db->setCacheDir(SYS_PATH.'sysdata/cache/');

$SYS_CONFIG['language'] = empty($SYS_CONFIG['language']) ? 'chinese_gb' : $SYS_CONFIG['language'];
require_once LANG_PATH.$SYS_CONFIG['language'].'/charset.inc.php';
//$db->setCharset(CHARSET); //support Mysql4.x

//from ipb
$IN = parse_incoming();

$iWPC = new iWPC();

if(!file_exists(CACHE_DIR.'Cache_SYS_ENV.php')) {
	$cache = new CacheData();
	$cache->makeCache('sys');
	$cache->makeCache('psn');
	$cache->makeCache('catelist');	
	

} 
include_once(CACHE_DIR.'Cache_SYS_ENV.php');
include_once(CACHE_DIR.'Cache_CateList.php');
include_once(CACHE_DIR.'Cache_PSN.php');

$SYS_ENV['language'] = $SYS_CONFIG['language'];


$TPL = new kTemplate();
$TPL->template_dir = SYS_PATH.'skin/user/';
$TPL->compile_dir = SYS_PATH.'sysdata/templates_c/';
$TPL->cache_dir = SYS_PATH.'sysdata/cache/';
$TPL->lang_dir = LANG_PATH.$SYS_ENV['language'].'/lang_skin/user/';
$TPL->assign('iwpc_version', CMSWARE_VERSION);
$TPL->assign('cmsware_version', CMSWARE_VERSION);
$TPL->assign('cms_version', CMSWARE_VERSION);
require_once LANG_PATH.$SYS_CONFIG['language'].'/lang_user.php';
require_once LANG_PATH.$SYS_CONFIG['language'].'/lang_sys.php';
header('Content-Type: text/html; charset='.CHARSET);
$TPL->assign('charset', CHARSET);



if(!$referer) {
	$referer =  _addslashes($_SERVER[HTTP_REFERER]);
}

$params = array(
	'sId'=>$IN['sId'],
	'sIp'=>$IN['IP_ADDRESS'],
);
//print_r($in);
$sys = new Auth($params);
list($module, $action) = explode('::',$IN['o']);

$choice = array(
	'sys' => 'sys',

);
 
// Check to make sure the array key exits..
if($module == 'sys' && $action == 'login') {

}elseif (! isset($choice[$module])  ||  !$sys->isLogin()) {
		$module = 'sys';
		$action = '';

} elseif (!$sys->canAccess($module, $action)) {
	$TPL->assign('error_message',$_LANG_ADMIN['access_deny_module'].$LANG_SYS_AUTH[$sys->op]['error_text']);
	$TPL->display("login.html");
	exit;

}

//print_r($sys->session);
$base_url = "index.php?sId=".$IN['sId']."&";
$TPL->assign('base_url', $base_url);
$TPL->assign('sId', $IN['sId']);
$TPL->assign('Auth', $sys->session['sGAuthData']);
require "./modules/admin_".$choice[$module].".php";

?>