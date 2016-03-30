

//var action=new Array("psn_backward","psn_updir","psn_mkdir")
//var tooltip=new Array("后退","上一级目录", "新建目录")
var action=new Array("psn_updir","psn_mkdir")
var tooltip=new Array("上一级目录", "新建目录")

var s=
'     ' +
'    <style>' +
'    .Utoolbutton{border:1 double;border-color:#D6D3CE #D6D3CE #D6D3CE #D6D3CE; background:#D6D3CE}' +
'    .Dtoolbutton{border:1 double;border-color:#FFFFFF #999999 #999999 #FFFFFF; background:#D6D3CE}' +
'    .Ctoolbutton{border:1 double;border-color:#999999 #FFFFFF #FFFFFF #999999; background:#D6D3CE}' +
'    </style>' +
'    <table border=0 cellPadding=0 cellSpacing=0 ><tbody>' +
'      <tr><td colspan="2">';
for (var i=0;i<action.length;i++) {
    s+='<img width="21" height="20" src="../html/images/' + action[i] + '.gif" class="Utoolbutton" onmouseover="this.className=\'Dtoolbutton\';" onmouseout="this.className=\'Utoolbutton\';" onclick="this.className=\'Ctoolbutton\';'   
     if(i==0) s+='psn_updir();'
	 else if(i==1) s+='psn_mkdir();'
	 //else if(i==2) s+='psn_mkdir();'

	s+='" title="' + tooltip[i] + '" hspace="2" vspace="0">'
}

s+=
'      </td></tr></table>' ;

document.write(s)