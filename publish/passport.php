<?php
/*
****  用户通行证
调用方法：
直接包含即可
*/
require_once 'init.php';
include_once ( '../passport/x_passport.php');
include_once ( '../passport/include/functions/function_session.php');
include_once ( '../passport/include/functions/function_string.php');
include_once ( '../passport/include/functions/function_client.php');
include_once ( '../passport/libs/lib_session.php');

//
require_once KTPL_DIR . 'kTemplate.class.php';
$TPL = new kTemplate();
$TPL->template_dir = $_SERVER['DOCUMENT_ROOT'].'/templates/';  //定义模板根路径
$TPL->compile_dir = CACHE_DIR.'templates_c/';  //定义模板变异路径
$TPL->cache_dir = CACHE_DIR.'cache/';
$TPL->client_caching = false;		//不做客户端缓存
$TPL->cache_lifetime = $cacheTime;

/****** 主程序 ******/
header ( "Cache-Control: no-cache" );
define ('USE_COOKIE', '1' );
define('COOKIE_NAME','tiantangwan_cn');
$arrHost = explode ( '.', preg_replace ( "/:.+$/", '', $_SERVER['HTTP_HOST'] ) );
$hostParts = count ( $arrHost );
// array_shift ( $arrHost );
// 读取 Session



define ( 'COOKIE_DOMAIN', '.' . $arrHost[$hostParts - 2] . '.' . $arrHost[$hostParts - 1] ); // Cookie 域

define ( 'PASSPORT_APP_ID', 359 ); // 通行证服务 ID
define ( 'PASSPORT_APP_KEY', 'ZAKjm4454oSZD5FLMMJOLPDSFM10iiF' ); // 通行证私钥
$userInfo = load_user_session (); //用户登录检测

//de( COOKIE_DOMAIN ,0 ,1 );

$clsPassport = new x_Passport;
$clsPassport->appId = PASSPORT_APP_ID; // 通行证服务ID
$clsPassport->encryptKey = PASSPORT_APP_KEY; // 私钥
$clsPassport->cookieDomain = COOKIE_DOMAIN;


$tplname = "./tiantangwancn/login.html";  //定义模板文件
$act = strtolower ( $_GET['act'] );
$referer = $_GET['referer'] ? $_GET['referer'] : ( $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : ( 'http://' . $_SERVER['HTTP_HOST'] ) );
switch ( $act )
{
	case 'relogin': // 重新登入
	{
		$clsPassport->deleteSession ( $clsPassport->passportVarName );
		_url_redirect ( "/passport.php?act=login&referer=" . $referer );
	}
	case 'login': // 登入
	{
		$clsPassport->verify ();
		if ( $clsPassport->vars )
		{
			save_user_session ( $clsPassport->vars );
			_url_redirect ( $referer );
		}else
		{
			$TPL->display($tplname);
			exit();
		}
		break;
	}
	case 'logout': // 登出
	{
		if ( $gUsername )
		{
			delete_user_session ();
			$clsPassport->logout ( $referer );
		}
		break;
	}
	case 'checkUserInfo':
		if ( !$userInfo )
		{
			$clsPassport->verify ();
			if ( $clsPassport->vars )
			{
				save_user_session ( $clsPassport->vars );
			}
		}
		exit();
		break;
}
$gUrlRoot='http://tiantangwan.cn';
_url_redirect ( $gUrlRoot );
function de( $str ,$track=1 ,$exit=false ){
	global $debugnum;
	$debugnum++;
	$debugInfo =  debug_backtrace();
	echo "<div style='font-size:14px;background-color:#f1f6f7'>";
	echo "<div style='font-size:16px;background-color:dfe5e6;color:#001eff;font-weight:bold'>";
	foreach( $debugInfo as $key=>$value ){
		if($key==0 ){
			echo "*** <span style='font-size:18px'>{$debugnum}</span> {$value['file']} (debug in file)  {$value['line']} (row) </br>";
		} else {
			if ( $track )
			{
				echo "&nbsp;&nbsp;<span style='font-size:12px;'>>> include in file:{$value['file']} line:{$value['line']} row </br></span>";
			} else {
				break;
			}
		}
	}
	echo "</div>";
	echo '<pre>';
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
?>