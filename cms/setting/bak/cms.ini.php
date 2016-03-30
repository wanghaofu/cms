<?php

//自定义增加的调用函数请加到此行以下

function quote($str) 
{ 
$str=str_replace(array('[quote]','[/quote]','[br]'),array('<div class="quote"><table width=99% cellspacing=1 cellpadding=4 align=center border=0 bgcolor=#999999><tr bgcolor=#ffffee><td><span style="color:blue">','</span></td></tr></table><br></div>','<br />'),$str);

return $str;
}


//获取论坛数据

function getbbslist($num=10,$order='tid',$where=''){
	$conn=mysql_connect("localhost","manbbs","198110") or die ("Unable to connect!");
	//mysql_select_db("manbbs") or die ("Unable to select database!");;
	if($where!=''){
	$sql="SELECT tid,fid,subject as Title,views,replies,dateline FROM manbbs.manbbs_threads where subject!='' ".$where." order by $order desc limit ".$num;
	}else{
	$sql="SELECT tid,fid,subject as Title,views,replies,dateline FROM manbbs.manbbs_threads where subject!='' order by $order desc limit ".$num;	
	}
	 //mysql_query("SET NAMES gb2312"); 
	 mysql_query("SET NAMES 'GBK'"); 
	 $result=mysql_query($sql,$conn)or die("Invalid query: " . mysql_error());
	 $List=array();
	 $i=0;
	 //生成二维数组 一维为数字， 二维为关联
	while ($row = mysql_fetch_assoc($result)) {
			$List[$i]=$row;
			$i++;
		}
	mysql_connect("localhost","sesoe_f","198110") or die ("Unable to connect!");
		return $List;
}



//获取论坛附件图片数据

function getattachment($num=10,$order='aid',$where=''){
	$conn=mysql_connect("203.191.145.62:3306","manbbs","198110") or die ("Unable to connect!");
	//mysql_select_db("manbbs") or die ("Unable to select database!");
	$sql="SELECT aid,tid,pid,attachment as url FROM manbbs.manbbs_attachments where isimage=1 order by aid desc limit ".$num;
	mysql_query("SET NAMES 'GBK'"); 
	$result=mysql_query($sql,$conn)or die("Invalid query: " . mysql_error());
	$List=array();
	$i=0;
	 //生成二维数组 一维为数字， 二维为关联
	while ($row = mysql_fetch_assoc($result)) {
			$List[$i]=$row;
			$i++;
		}
	mysql_connect("localhost","sesoe_f","198110") or die ("Unable to connect!");	
		return $List;
}



//自定义增加调用函数结束

/*
//获取最新
$s=getbbslist(12);
//获取访问量最高
$s2=getbbslist(12,'views');
//获取某一个板块最热门
$s3=getlist(12,'views','and fid=15');
echo "<pre>";
print_r($s);
print_r($s2);
print_r($s3);
echo "</pre>";
*/
?>