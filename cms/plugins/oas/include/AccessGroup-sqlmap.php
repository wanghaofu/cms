<?php
$_sqlMap_select = array(
	"getOasInfo" => "select * from {$plugin_table['oas']['setting']}",
	"getAccessInfoByGroupID" => "SELECT * FROM {$plugin_table['oas']['access']} where OwnerID=#GroupID# AND AccessType=1 ",
	"getAccessInfoByAccessID" => "SELECT * FROM {$plugin_table['oas']['access']} where AccessID=#AccessID#",
	"getAllPermission" => "SELECT * FROM {$plugin_table['oas']['permission']} ORDER BY OrderKey ",
	"getAccessMapByAccessID" => "SELECT * FROM {$plugin_table['oas']['access_map']} where AccessID=#AccessID#"
);
$_sqlMap_insert = array(
	"addAccess" => array(
		"table" => $plugin_table['oas']['access']
	),
	"addAccessMap" => array(
		"table" => $plugin_table['oas']['access_map']
	)
);
$_sqlMap_update = array(
	"updateAccess" => array(
		"table" => $plugin_table['oas']['access'],
		"where" => "AccessID='#AccessID#'"
	)
);
$_sqlMap_delete = array(
	"user" => array(
		"table" => $table->user,
		"where" => "UserID=#UserID# AND UserName=#UserName#",
		"sql" => "delete from {$table->user} where UserID=#UserID# "
	),
	"delAccess" => array(
		"table" => $plugin_table['oas']['access'],
		"where" => "AccessID='#AccessID#'"
	),
	"delAccessMap" => array(
		"table" => $plugin_table['oas']['access_map'],
		"where" => "AccessID='#AccessID#'"
	)
);
?>
