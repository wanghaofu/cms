<?php
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

if (!extension_loaded('ftp')) {
	require_once INCLUDE_PATH."ftp.class.php";
}

require_once INCLUDE_PATH."file.class.php";
require_once INCLUDE_PATH."Error.php";
require_once INCLUDE_PATH."user/auth.class.php";
require_once INCLUDE_PATH."admin/userAdmin.class.php";
require_once INCLUDE_PATH."exception.class.php";
require_once INCLUDE_PATH."user/cache.class.php";
require_once INCLUDE_PATH."admin/psn_admin.class.php";
//require_once LANG_PATH."$lang_user.php";


require_once KDB_DIR.'kDB.php';
$db = new kDB($db_config['db_driver']);
$db->connect($db_config);
$db->setDebug($db_config['debug']);
$db->setFetchMode('assoc');
$db->setCacheDir(SYS_PATH.'sysdata/cache/');

$SYS_CONFIG['language'] = empty($SYS_CONFIG['language']) ? 'chinese_gb' : $SYS_CONFIG['language'];
require_once LANG_PATH.$SYS_CONFIG['language'].'/charset.inc.php';
//$db->setCharset(CHARSET); //support Mysql4.x


//$db->setDebug(1);
//from ipb
$IN = parse_incoming();
$iWPC = new iWPC();
if(!file_exists(CACHE_DIR.'Cache_SYS_ENV.php')) {
	$cache = new CacheData();
	$cache->makeCache('sys');
	$cache->makeCache('psn');
 	$cache->makeCache('catelist');	
	$cache->makeCache('content_model');	
} 
include_once(CACHE_DIR.'Cache_SYS_ENV.php');
include_once(CACHE_DIR.'Cache_PSN.php');
include_once(CACHE_DIR.'Cache_CateList.php');
include_once(CACHE_DIR.'Cache_ContentModel.php');

$SYS_ENV['language'] = $SYS_CONFIG['language'];


$TPL = new kTemplate();
$TPL->template_dir = SYS_PATH.'skin/user/';
$TPL->compile_dir = SYS_PATH.'sysdata/templates_c/';
$TPL->cache_dir = SYS_PATH.'sysdata/cache/';
$TPL->lang_dir = LANG_PATH.$SYS_CONFIG['language'].'/lang_skin/user/';


$TPL->compile_lang = true;
$TPL->global_lang_name = LANG_PATH.$SYS_CONFIG['language'].'/lang_skin_global.php';

$TPL->assign('iwpc_version', CMSWARE_VERSION);
$TPL->assign('cmsware_version', CMSWARE_VERSION);
$TPL->assign('cms_version', CMSWARE_VERSION);
$TPL->assign_by_ref('SYS_ENV', $SYS_ENV);
$TPL->assign_by_ref('NODE_LIST', $NODE_LIST);
$TPL->assign_by_ref('CONTENT_MODEL_INFO', $CONTENT_MODEL_INFO);



if(!$IN[referer]) 
	$referer =  _addslashes($_SERVER[HTTP_REFERER]);
else 
	$referer = $IN[referer];
$params = array(
	'sId'=>$IN['sId'],
	'sIp'=>$IN['IP_ADDRESS'],
);
//echo $referer;

/*License验证
require('../license.php');
$license_array = $License;
unset($License);
if($license_array['Module-Contribution']!=1)
	goback('license_Module_Contribution_disabled');
License验证*/

//print_r($in);
$sys = new Auth($params);
$message = $_LANG_ADMIN["{$IN['message']}"];
$base_url = $_SERVER["PHP_SELF"]."?sId=".$IN['sId']."&";
$TPL->assign('base_url', $base_url);
$TPL->assign('referer', $referer);
$TPL->assign('sId', $IN['sId']);
if (!$sys->isLogin()) {
	$TPL->display("login.html");
	exit;

}
$TPL->assign('session', $sys->session);
 
require_once LANG_PATH.$SYS_CONFIG['language'].'/lang_user.php';
require_once LANG_PATH.$SYS_CONFIG['language'].'/lang_sys.php';
$TPL->assign('charset', CHARSET);
header('Content-Type: text/html; charset='.CHARSET);


//debug($sys->session);
//print_r($sys->session);

?>