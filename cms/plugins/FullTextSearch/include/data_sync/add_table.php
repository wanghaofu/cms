<?php


$table_search = $db_config['table_pre']."plugin_fulltext_search_".$TableID;
$sql = "Create table IF NOT EXISTS {$table_search}  (\r\n\t\t\t\t\tIndexID Integer(10) NOT NULL ,\r\n\t\t\t\t\tContentID Integer(10) NOT NULL ,\r\n\t\t\t\t\tNodeID Integer(10) NOT NULL ,\r\n\t\t\t\t\tPublishDate Integer(10) ,\r\n\t\t\t\t\tURL Char(250) ,\r\n\t\t\t\t\tPrimary Key (IndexID) ,\r\n\t\t\t\t\tKEY ContentID (ContentID), \r\n\t\t\t\t\tKEY NodeID (NodeID), \r\n\t\t\t\t\tKEY PublishDate (PublishDate) \r\n\t\t\t\t)";
$db->query( $sql );
?>
