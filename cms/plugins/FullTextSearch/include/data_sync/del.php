<?php


$table_search = $db_config['table_pre']."plugin_fulltext_search_".$TableID;
$sql = "DELETE FROM {$table_search} WHERE IndexID={$IndexID}";
$db->query( $sql );
?>
