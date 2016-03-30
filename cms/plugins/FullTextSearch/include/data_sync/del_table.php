<?php


$table_search = $db_config['table_pre']."plugin_fulltext_search_".$TableID;
$sql = "DROP TABLE {$table_search} ";
$db->query( $sql );
?>
