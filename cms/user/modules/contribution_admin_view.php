<?php   
if(!defined('IN_IWPC')) {
 	exit('Access Denied');
}
require_once INCLUDE_PATH."admin/userAdmin.class.php";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> </TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META NAME="Description" CONTENT="">

</HEAD>
<script type="text/javascript" src="../html/helptip.js"></script>
<link type="text/css" rel="StyleSheet" href="../html/helptip.css" />
<link type="text/css" rel="StyleSheet" href="../html/style.css" />
<style type="text/css">
<!--
.tablebg {
	background-color: #F5F5F5;
}
-->
</style>
<script language="javascript">
function mytext_zoomin(){	mytext.style.fontSize="10.5pt";}function mytext_zoomout(){	mytext.style.fontSize="9pt";}

function MM_openBrWindow(theURL,winName,features) { 
  window.open(theURL,winName,features);
}

</script>
<!--------------------------><CENTER>[ <A HREF="javascript:window.close();">关闭</A> ]</CENTER>
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="1"  class="table_border" >
<tr > 
              <td align=right width=90 class="table_td1">状态：</td>
              <td class="table_td2"><?=$pInfo[State]?>
			  </td>
</tr>
<?php
//--------------------------------------------------------
foreach( $tableInfo as $key=>$var) {
	if(empty($var['EnableContribution'])) continue;

	echo " <tr class='table_td1'><td align=right >{$var[FieldTitle]}：</td><td class='table_td2'>".$pInfo[$var['FieldName']]."</td></tr>";

}
?>
<tr > 
              <td align=right   class="table_td1">创建日期：</td>
              <td class="table_td2"><?php echo date('Y-m-d H:i:s',$pInfo[CreationDate]);?>
			  </td>
</tr>
<tr > 
              <td align=right  class="table_td1">上次修改日期：</td>
              <td class="table_td2"><?php echo date('Y-m-d H:i:s',$pInfo[ModifiedDate]);?>
			  </td>
</tr>
<tr > 
              <td align=right   class="table_td1">所有者：</td>
              <td class="table_td2"><?php echo userAdmin::getInfo($pInfo[OwnerID],'uName');?>
			  </td>
</tr>



</table>
<CENTER>[ <A HREF="javascript:window.close();">关闭</A> ]</CENTER>
</body></html>