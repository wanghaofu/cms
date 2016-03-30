<?php
$name='sesoe';
$_oasid = 21;
require_once("SoapOAS.class.php");
$CWPS_Address = "http://www.{$name}.com/cwps/soap.php"; //CWPS地址
$TransactionAccessKey = "887273c6d2a6ef8ddd2074475a5cac4a"; //CWPS访问密码
$oas = new SoapOAS($CWPS_Address); //初始化OAS客户端
$oas->setTransactionAccessKey($TransactionAccessKey); //设置CWPS访问密码
$oas->doLog = false; //是否对SOAP数据包进行记录
$oas->logFile = "oas.log.".date("Y-m-d").".txt"; //log文件名
/*
调用CWPS的用户登陆操作接口，登陆成功系统将返回登陆的SESSION ID
*/

 $ip='220.248.115.130';
$oas->setOASID($_oasid);
$oas->setTransactionID(time()); //设置事务消息ID
$Action = "Login"; //调用CWPS的SOAP接口名，该接口必须在CWPS上使用Register进行注册
$params = array( 
"UserName"=>"wangtao",
"Password"=>"wangtianfan",
"Ip"=>'220.248.115.130',//203.191.144.199
); //传递给接口的参数
$oas->DataEncode = true; //默认所有params数据都进行base64编码
//设为false的话，开发人员需要自行处理OAS端与CWPS端的数据编码与解码
$return = $oas->call($Action, $params); //执行调用

echo '<pre>';
print_r($return);
echo '</pre>';

$sId = $return['sId'];

$oas->setTransactionID(time());
	$Action = "QueryUserSession";
	$params = array(
			"sId"=> $sId,
			"Ip"=> $ip,
		);

	$return = $oas->call($Action, $params);
	

if($return === false) { //执行发生错误,错误处理...
echo "Error Code:".$oas->errorCode."<HR>";
print_r($oas->Response);
} else { //执行成功，$return包含返回的数据
	echo '<pre>';
	print_r($return);
	echo '</pre>';
}








	
	


































/*
调用CWPS的会员金币操作接口，扣除用户10单位金币

$oas->setTransactionID(time());
$Action = "updateMoney";
$params = array( 
"UserID"=>"12",
"Operator"=>"-",
"Money"=>"10",
);

$return = $oas->call($Action, $params);
if($return === false) {
echo"Error Code:".$oas->errorCode."<HR>";
print_r($oas->Response);
} else {
echo "OK!";
print_r($return);
}*/



?>