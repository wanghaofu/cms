<?php


$table_header =& $db_config['table_pre'];
$uninstall_sql = "DROP TABLE IF EXISTS {$table_header}plugin_oas_access ;\r\nDROP TABLE IF EXISTS {$table_header}plugin_oas_setting ;\r\nDROP TABLE IF EXISTS {$table_header}plugin_oas_sessions ;\r\nDROP TABLE IF EXISTS {$table_header}plugin_oas_permission ;\r\nDROP TABLE IF EXISTS {$table_header}plugin_oas_access_map ;\r\nDROP TABLE IF EXISTS {$table_header}plugins_oas_user;";
$result = plugin_runquery( $uninstall_sql );
?>
