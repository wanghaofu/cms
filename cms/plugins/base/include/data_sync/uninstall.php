<?php


$table_header =& $db_config['table_pre'];
$uninstall_sql = "DROP TABLE {$table_header}plugin_base_comment;\r\nDROP TABLE {$table_header}plugin_base_count;\r\nDROP TABLE {$table_header}plugin_base_setting;";
$result = plugin_runquery( $uninstall_sql );
?>
