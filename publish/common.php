<?php
function de( $str ,$track=1 ,$exit=false ){
	global $debugnum;
	$debugnum++;
	$debugInfo =  debug_backtrace();
	echo "<div style='font-size:14px;background-color:#f1f6f7'>\n";
	echo "<div style='font-size:16px;background-color:dfe5e6;color:#001eff;font-weight:bold'>\n";
	foreach( $debugInfo as $key=>$value ){
		if($key==0 ){
			echo "*** <span style='font-size:18px'>{$debugnum}</span> {$value['file']} (debug in file)  {$value['line']} (row) </br>\n";
		} else {
			if ( $track )
			{
				echo "&nbsp;&nbsp;<span style='font-size:12px;'>>> include in file:{$value['file']} line:{$value['line']} row </br></span>\n";
			} else {
				break;
			}
		}
	}
	echo "</div>\n";
	echo '<pre>\n';
	if ( !isset( $str ) )
	{
		echo 'the vars in not set!';
	}elseif ( is_numeric($str) ){
		echo $str;
	}elseif ( is_object( $str ) ){
		print_r( $str);
	}elseif ( is_string( $str )){
		echo $str;
	}elseif( is_array( $str ) ){
		print_r( $str );
	}elseif ( is_null( $str )){
		echo 'the vars is null ';
	}elseif( is_bool( $str ) ){
		echo $str;
	}
	echo '</pre>';
	echo "</div>";
	if ( $exit ){
		exit();
	}
}
require_once 'init.php';
define ('Error_Display','html');
require_once ROOT_PATH."config.php";
require_once  ROOT_INCLUDE_PATH.'functions.php';
$debugger = new Debug();
$debugger->startTimer();

require_once( ROOT_INCLUDE_PATH.'data.class.php');
require_once INCLUDE_PATH."Error.php";
include_once SETTING_DIR."cms.ini.php";
include_once INCLUDE_PATH."cms.func.php";
include_once INCLUDE_PATH."cms.class.php";
include_once INCLUDE_PATH."admin/psn_admin.class.php";
require_once KTPL_DIR . 'kTemplate.class.php';
require_once KDB_DIR.'kDB.php';

$db = new kDB($db_config['db_driver']);
$db->connect($db_config);
$db->setDebug($db_config['debug']);
$db->setFetchMode('assoc');
$db->setCacheDir(SYS_PATH.'sysdata/cache/');

new Error();

$SYS_CONFIG['language'] = empty($SYS_CONFIG['language']) ? 'chinese_gb' : $SYS_CONFIG['language'];
require_once LANG_PATH.$SYS_CONFIG['language'].'/charset.inc.php';
//$db->setCharset(CHARSET); //support Mysql4.x

 
$IN = parse_incoming();
$iWPC = new iWPC();

if(!$IN[referer]) 
	$referer =  _addslashes($_SERVER[HTTP_REFERER]);
else 
	$referer = $IN[referer];



$TPL = new kTemplate();
$TPL->template_dir = '../templates'.DS.'sesoe';
$TPL->compile_dir = CACHE_DIR.'templates_c'.DS;
$TPL->cache_dir = CACHE_DIR.'cache/';
$TPL->assign_by_ref('referer', $referer);
$TPL->assign('URL_SELF', 'http://'.$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF']);
$TPL->assign('cmsware_version', VERSION);
$TPL->assign('cms_version', VERSION);

require(ROOT_PATH.'license.php');
$license_array = $License;
unset($License);

$NoCache = true; //设置不进行缓存

include_once(CACHE_DIR.'Cache_SYS_ENV.php');
include_once(CACHE_DIR.'Cache_CateList.php');
include_once(CACHE_DIR.'Cache_ContentModel.php');
$TPL->assign_by_ref('SYS_ENV', $SYS_ENV);
$TPL->assign_by_ref('NODE_LIST', $NODE_LIST);
$TPL->assign_by_ref('CONTENT_MODEL_INFO', $CONTENT_MODEL_INFO);


$SYS_ENV['language'] = $SYS_CONFIG['language'];



$SYS_ENV['CMSware_Mark'] = str_replace('{date}', date('Y-m-d H:i:s', time()), $license_array['Publish-Marker']);
$SYS_ENV['CMSware_Mark'] = str_replace('{version}', CMSWARE_VERSION, $SYS_ENV['CMSware_Mark']);

if(empty($SYS_ENV['CMSware_Mark']) || !strpos($SYS_ENV['CMSware_Mark'], CMSWARE_VERSION) ) {
	$TPL->add_meta_mark = false;
}


if(!class_exists('TplVarsAdmin')) {
			require_once  ROOT_INCLUDE_PATH.'admin/TplVarsAdmin.class.php';
}
$tpl_vars = TplVarsAdmin::getAll();
foreach($tpl_vars as $key=>$var) {
	if($var['IsGlobal']) {//全局模板变量
		$TPL->assign($var['VarName'], $var['VarValue']);	
	} 
}

/////////////// 获取用户信息
//$UserInfo = include_once("{$PUBLISH_CONFIG['OAS_PATH']}getuserinfo.php");   //获取用户信息  wangtao add
include_once("../passport/userInfo.php");
//
$TPL->assign('userInfo', $userInfo);

////$referer ='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
//$referer ='http://'.$_SERVER['HTTP_HOST'];
$TPL->assign('urlRegister',$clsPassport->urlRegister );
$TPL->assign('urlLogin', '/passport/passport.php?act=login');
////$TPL->assign('urlLogin', '/passport/passport.php?act=login&referer='.$referer );
$TPL->assign('urlLogout', '/passport/passport.php?act=logout');
//$TPL->assign('urlLogout', '/passport/passport.php?act=logout&referer='.$referer );
?>