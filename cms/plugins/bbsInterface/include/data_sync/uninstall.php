<?php


$table_header =& $db_config['table_pre'];
$uninstall_sql = "DROP TABLE {$table_header}plugin_bbsi_access;\r\nDROP TABLE {$table_header}plugin_bbsi_setting;";
$result = plugin_runquery( $uninstall_sql );
?>
