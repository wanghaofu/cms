<?php
header('Content-Type:text/html;charset=GBK');
/*
****  用户通行证
调用方法：
直接包含即可
*/

require_once 'init.php';
require_once KTPL_DIR . 'kTemplate.class.php';
require_once('../passport/userInfo.php');
$TPL = new kTemplate();
$TPL->template_dir ='../templates/tiantangwancn/';  //定义模板根路径
$TPL->compile_dir = CACHE_DIR.'templates_c/';  //定义模板变异路径
$TPL->cache_dir = CACHE_DIR.'cache/';
$TPL->client_caching = false;		//不做客户端缓存
$TPL->cache_lifetime = $cacheTime;
$TPL->assign('userInfo', $userInfo);
define ( 'COOKIE_DOMAIN', '.tiantangwan.cn' ); // Cookie 域
$FromURL = $_SERVER['HTTP_REFERER'];
$pathArr = parse_url ($FromURL);
$SelfURL="";
if ( $pathArr['path'] ){
	$SelfURL="{$pathArr['path']}";
}
if ( $pathArr['query'] ){
	$SelfURL.="/{$pathArr['query']}";
}
$TPL->assign('SelfURL', $SelfURL);
//$TPL->assign('PUBLISH_URL',"http://{$_SERVER['HTTP_HOST']}/publish/");

//$referer ='/';
$referer =$_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$TPL->assign('urlRegister',$clsPassport->urlRegister );
$TPL->assign('urlLogin', '/publish/passport.php?act=login&referer='.$referer );
$TPL->assign('urlLogout', '/publish/passport.php?act=logout&referer='.$referer );
$tplname = "common/logininfo.html";  //定义模板文件

$TPL->display($tplname);
?>

