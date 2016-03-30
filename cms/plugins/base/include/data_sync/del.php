<?php


$table_count = $db_config['table_pre']."plugin_base_count";
$table_comment = $db_config['table_pre']."plugin_base_comment";
$sql = "DELETE FROM {$table_count} WHERE IndexID={$IndexID}";
$db->query( $sql );
$sql = "DELETE FROM {$table_comment} WHERE IndexID={$IndexID}";
$db->query( $sql );
?>
