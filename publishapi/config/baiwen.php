<?php

//$IN['Top'] = 2;  //所有应用该配置文件的操作都将会自动将置顶值设为2.
//$IN['Title'] = "来自前台:".$IN['Title'];	//所有应用该配置文件的操作都将会自动在标题前加上"来自前台:"的字符.
$IN['NodeID']=$IN['com_NodeID'];  //节点id 设为选择的id **********************
function add_start(&$var) {	//新增操作预处理
	
}

function add_end(&$var) { //新增操作后置处理
	//refresh_index(1);   //刷新结点ID为1的首页
	smsg("新增内容成功,页面正在跳转中...",$var['referer']);  //显示新增成功,并返回,至此,新增操作已成功执行完成
}

function edit_start(&$var) {  //编辑操作预处理
	
}

function edit_end(&$var) {  //编辑操作后置处理
	smsg("编辑内容已成功执行,页面正在跳转",$var['referer']);
}

function del_start(&$var) {  //删除操作预处理
//	if($_SERVER['REMOTE_ADDR'] != "127.0.0.1") gback("操作禁止!");  //如果用户的IP不等于127.0.0.1,则警告返回
}

function del_end(&$var) {  //删除操作后置处理
	smsg("内容已被正功删除,页面正在跳转",$var['referer']);
}
?>