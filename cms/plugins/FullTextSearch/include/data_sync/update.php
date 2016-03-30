<?php


$table_fields = $db_config['table_pre']."plugin_fulltext_fields";
$table_search = $db_config['table_pre']."plugin_fulltext_search_".$TableID;
$fields = array( );
$sql = "SELECT * FROM  {$table_fields} WHERE  TableID={$TableID}";
$result = $db->Execute( $sql );
while ( !$result->EOF )
{
	$data = explode( ",", $result->fields['FullTextFields'] );
	$fields = array_merge( $fields, $data );
	$result->MoveNext( );
}
$fields = array_unique( $fields );
$this->flushData( );
foreach ( $fields as $key => $var )
{
	$this->addData( $var, fulltextencoder( html2txt( $publishInfo[$var] ) ) );
}
$this->addData( "IndexID", $publishInfo[IndexID] );
$this->addData( "ContentID", $publishInfo[ContentID] );
$this->addData( "NodeID", $publishInfo[NodeID] );
$this->addData( "PublishDate", $publishInfo[PublishDate] );
$this->addData( "URL", $publishInfo['URL'] );
$this->dataReplace( $table_search );
?>
