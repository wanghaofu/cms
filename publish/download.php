<?php
require_once "common.php";
require_once ROOT_PATH."plugins/base/plugin.config.php";
require_once ROOT_INCLUDE_PATH."kDate.class.php";
require_once "download.config.php";


$ClientInfo = parse_url($_SERVER[HTTP_REFERER]);
$url = $IN['url'];
if(_Protect_Link && $ClientInfo[host] != _Domain) {
	die($_DIE_INFO);

}

if(empty($IN['id']) && empty($IN['IndexID']) && empty($IN['Id'])) {
	die('IndexID empty');
} else {
	$IndexID = $Id = empty($IN['id']) ? (empty($IN['IndexID']) ? $IN['Id'] : $IN['IndexID']) : $IN['id'];
}
$table_count =  &$plugin_table['base']['count'];

$result = $db->getRow("SELECT Download,LocalUpload  FROM $table_download  where IndexID='$IndexID'");
$result['Download']= str_replace("\r\n", "\n",  $result['Download']);
$result['Download'] = str_replace("\r", "\n", $result['Download']);
$urls = explode("\n", $result['Download']);
$urls['local'] = $result['LocalUpload'];

$result = $db->getRow("SELECT Hits_Date  FROM $table_count  where IndexID='$IndexID'");
$Hits_Date = $result['Hits_Date'];

if(kDate::InToday($Hits_Date)) {
	$db->query("UPDATE $table_count Set `Hits_Today`=Hits_Today+1 where IndexID='$IndexID'"); //本日计数

} else {
	$db->query("UPDATE $table_count Set `Hits_Today`=1 where IndexID='$IndexID'"); //本日计数

}

if(kDate::InWeek($Hits_Date)) {
	$db->query("UPDATE $table_count Set `Hits_Week`=Hits_Week+1 where IndexID='$IndexID'"); //本周计数
} else {
	$db->query("UPDATE $table_count Set `Hits_Week`=1 where IndexID='$IndexID'"); //本周计数
}

if(kDate::InMonth($Hits_Date)) {
	$db->query("UPDATE $table_count Set `Hits_Month`=Hits_Month+1 where IndexID='$IndexID'"); //本月计数

} else {
	$db->query("UPDATE $table_count Set `Hits_Month`=1 where IndexID='$IndexID'"); //本月计数

}


$db->query("UPDATE $table_count Set `Hits_Total`=Hits_Total+1 where IndexID='$IndexID'"); //总计数

$db->query("UPDATE $table_count Set `Hits_Date`='".time()."' where IndexID='$IndexID'"); //更新时间

$db->close();
//header("Location:".$urls[$url]);





$url=$urls[$url];  
//de( $url ,0 ,0 );

$urlarr=explode("/",$url); 
//de( $urlarr ,0 ,1 );

$domain=$urlarr[2];//分解出域名 
//$getfile=str_replace($urlarr[3],"",$url); 
//echo $domain;
$getfile=$urlarr[3];
//echo $getfile;
$content = @fsockopen($domain, 80, $errno, $errstr, 12 );   //先连接上对方的服务器 
if(!$content){  
die("对不起,无法连接上{$domain}");
}
fputs($content, "GET /{$getfile} HTTP/1.0\r\n");
fputs($content, "Host: $domain\r\n");
fputs($content, "Referer: $domain\r\n");//伪造referfer
fputs($content, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n");

while (!feof($content)) {
	$tp.=fgets($content, 128);  // 将头部信息读取出来，里面将包含：Location:http://xxx/xxx.mp3，我们要的就是这个地址。
}
//de( $tp ,0 ,0 );

$arr=explode("\n",$tp);
$arr1=explode("Location: ",$tp);
$arr2=explode("\n",$arr1[1]);//分解出Location:后面的地址
//header('Content-Type:application/force-download');
//de( $arr2[0] ,0 ,0 );
exit($arr2[0]);
header("location:".$arr2[0]);
header("Referer: $domain\r\n");
fclose($content);

//OK，目的达到了。

//这个原来的地址：http://img.namipan.com/downfile/3a7c64518d46d986283eab73175a8b119305a76480b89200/Equilibrium-Turis_Fratyr-02-Wingthors_Hammer.mp3

//转换后：

//http://mms.music.krmcn.com/mms.music/namipan/img~~/3a7c64518d46d986283eab73175a8b119305a76480b89200/Equilibrium-Turis_Fratyr-02-Wingthors_Hammer.mp3


?>
