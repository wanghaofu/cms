<?php
$_sqlMap_select = array(
	"getOasInfo" => "select * from {$plugin_table['oas']['setting']}",
	"getAllPermission" => "select * from {$plugin_table['oas']['permission']}",
	"getPermissionInfo" => "select * from {$plugin_table['oas']['permission']} where PermissionKey='#PermissionKey#'"
);
$_sqlMap_insert = array(
	"user" => array(
		"table" => $table->user,
		"sql" => "insert into {$plugin_table['oas']['setting']} values(#UserName#)"
	),
	"addPermission" => array(
		"table" => $plugin_table['oas']['permission']
	)
);
$_sqlMap_update = array(
	"updateOasInfo" => array(
		"table" => $plugin_table['oas']['setting'],
		"sql" => "update {$table->user} set UserName=#UserName# where UserID=#UserID#"
	)
);
$_sqlMap_delete = array(
	"user" => array(
		"table" => $table->user,
		"where" => "UserID=#UserID# AND UserName=#UserName#",
		"sql" => "delete from {$table->user} where UserID=#UserID# "
	),
	"delPermission" => array(
		"table" => $plugin_table['oas']['permission'],
		"where" => "PermissionKey='#PermissionKey#'"
	),
	"delAccessMap" => array(
		"table" => $plugin_table['oas']['access_map'],
		"where" => "PermissionKey='#PermissionKey#'"
	)
);
?>
