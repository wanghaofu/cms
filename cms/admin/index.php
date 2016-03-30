<?php
//加载基本文件
require_once( "../validate.php" ); //许可文件
//初始化文件
require_once( "init.php" );
require_once( LIB_PATH."Spring.php" );
$_BeanFactory = new Spring( "spring.appcontext.php" );
$BeanFactory =& $_BeanFactory;
require_once( INCLUDE_PATH."data.class.php" );
require_once( INCLUDE_PATH."functions.php" );
require_once( SYS_PATH."config.php" );
define( "Error_Display", $SYS_CONFIG['error_reporting'] );
 $iwpc_debug=1;
if ( $iwpc_debug )
{
	$debugger = new Debug( );
	$debugger->startTimer( );
}
//类文件包含
require_once( KTPL_DIR."kTemplate.class.php" );
require_once( INCLUDE_PATH."file.class.php" );
require_once( INCLUDE_PATH."Error.php" );
require_once( INCLUDE_PATH."admin/auth.class.php" );
require_once( INCLUDE_PATH."admin/publishAuthAdmin.class.php" );
require_once( INCLUDE_PATH."admin/cache.class.php" );
require_once( INCLUDE_PATH."admin/psn_admin.class.php" );
require_once( SETTING_DIR."global.php" );
require_once( KDB_DIR."kDB.php" );
//开始对象处理
$db = new kDB( $db_config['db_driver'] );
$db->connect( $db_config );
$db->setFetchMode( "assoc" );
$db->setCacheDir( SYS_PATH."sysdata/cache/" );
$SYS_CONFIG['language'] = empty( $SYS_CONFIG['language'] ) ? "chinese_gb" : $SYS_CONFIG['language'];
require_once( LANG_PATH.$SYS_CONFIG['language']."/charset.inc.php" );
$IN = parse_incoming( );
$iWPC = new iWPC( );
if ( !file_exists( CACHE_DIR."Cache_SYS_ENV.php" ) || !file_exists( CACHE_DIR."Cache_ContentModel.php" ) )
{
	$cache = new CacheData( );
	$cache->makeCache( "sys" );
	$cache->makeCache( "psn" );
	$cache->makeCache( "dsn" );
	$cache->makeCache( "catelist" );
	$cache->makeCache( "content_model" );
}
include_once( CACHE_DIR."Cache_SYS_ENV.php" );
include_once( CACHE_DIR."Cache_PSN.php" );
include_once( CACHE_DIR."Cache_CateList.php" );
include_once( CACHE_DIR."Cache_ContentModel.php" );
$SYS_ENV['language'] = $SYS_CONFIG['language'];
if ( $SYS_ENV['isLogAdmin'] == 1 || $SYS_ENV['isLogLogin'] == 1 )
{
	require_once( INCLUDE_PATH."admin/logAdmin.class.php" );
}
$TPL = new kTemplate( );
$TPL->template_dir = SYS_PATH."skin/admin/";
$TPL->compile_dir = SYS_PATH."sysdata/templates_c/";
$TPL->cache_dir = SYS_PATH."sysdata/cache/";
$TPL->lang_dir = LANG_PATH.$SYS_CONFIG['language']."/lang_skin/admin/";
if ( !isset( $SYS_CONFIG['enable_validcode'] ) )
{
	$SYS_CONFIG['enable_validcode'] = $EnableLoginValidCode;
}
$TPL->compile_lang = true;
$TPL->assign( "iwpc_version", CMSWARE_VERSION );
$TPL->assign( "cmsware_version", CMSWARE_VERSION );
$TPL->assign( "cms_version", CMSWARE_VERSION );
$TPL->assign( "BUILD_VERSION", BUILD_VERSION );
$TPL->assign( "EnableLoginValidCode", $SYS_CONFIG['enable_validcode'] );
$TPL->global_lang_name = LANG_PATH.$SYS_CONFIG['language']."/lang_skin_global.php";
if ( !$referer )
{
	$referer = _addslashes( $_SERVER['HTTP_REFERER'] );
}
$params = array(
	"sId" => $IN['sId'],
	"sIp" => $IN['IP_ADDRESS']
);
//系统登陆定义
$sys = new Auth( $params );
list( $module, $action ) = explode( "::", $IN['o'] );
$choice = array( "sys" => "sys" );//配置加载模块
require_once( LANG_PATH.$SYS_CONFIG['language']."/lang_admin.php" );
require_once( LANG_PATH.$SYS_CONFIG['language']."/lang_sys.php" );
//$sys->isLogin( );
/*如果模块没有设定 */
if ( $module == "sys" && $action == "login" )
{
	

	$sys->login( $IN[username], $IN[password], $IN[IpSecurity] );
} else if ( !isset( $choice[$module] ) || !$sys->isLogin( ) )
{
	$module = "sys";
	$action = "";
} else if ( !$sys->canAccess( "canLoginAdmin" ) )
{
	goback( "access_deny_login" );
}
$TPL->assign( "charset", CHARSET );
new Error( );
$_ERROR_CODE = "";
$LicenseInfo['Module-Collection'] = 1;
$LicenseInfo['Module-Contribution'] = 1;
$LicenseInfo['Module-FileManager'] = 1;
$LicenseInfo['Module-PublishAuth'] = 1;
$LicenseInfo['Module-DataImport'] = 1;
$LicenseInfo['Module-bbsInterface'] = 1;
$LicenseInfo['Module-FullText'] = 1;

$base_url = "index.php?sId=".$IN['sId']."&";
$TPL->assign( "base_url", $base_url );
$TPL->assign( "Auth", $sys->Auth );
$TPL->assign( "sId", $sys->session['sId'] );
$TPL->assign( "Auth", $sys->session['sGAuthData'] );
$TPL->assign( "LicenseInfo", $LicenseInfo );

require( "./modules/admin_".$choice[$module].".php" ); //最终功能加载模块
?>
