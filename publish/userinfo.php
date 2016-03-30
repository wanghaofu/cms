<?php
$UserInfo = include_once("{$PUBLISH_CONFIG['OAS_PATH']}getuserinfo.php");   //获取用户信息  wangtao add
$TPL->assign('UserInfo', $UserInfo);
$SelfURL = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$TPL->assign('SelfURL', $SelfURL);
$TPL->assign('PUBLISH_URL',"http://{$_SERVER['HTTP_HOST']}/publish/");
$TPL->assign('OAS_URL', $PUBLISH_CONFIG['OAS_PATH']);

?>