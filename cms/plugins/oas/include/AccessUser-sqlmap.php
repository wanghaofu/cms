<?php
$_sqlMap_select = array(
	"getOasInfo" => "select * from {$plugin_table['oas']['setting']}",
	"getAccessInfoByUserID" => "SELECT * FROM {$plugin_table['oas']['access']} where OwnerID=#UserID# AND AccessType=0 ",
	"getAccessInfoByAccessID" => "SELECT * FROM {$plugin_table['oas']['access']} where AccessID=#AccessID#",
	"getRecordNum" => "SELECT count(*) nr FROM {$plugin_table['oas']['access']} where AccessType=0",
	"getRecordLimit" => "SELECT * FROM {$plugin_table['oas']['access']} WHERE AccessType=0 Limit #start#, #offset# ",
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
