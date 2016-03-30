<?php
class Base extends iData
{

	function getPublishRecordNum( $NodeID )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		global $iWPC;
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$TableID =& $NodeInfo['TableID'];
		$table_publish = $db_config['table_pre'].$db_config['table_publish_pre']."_".$TableID;
		$sql_num = "SELECT Count(*) as TotalNum  FROM {$table_publish} where NodeID={$NodeID}";
		$result = $db->getRow( $sql_num );
		return $result['TotalNum'];
	}

	function getPublishRecordLimit( $NodeID, $start, $offset )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		global $iWPC;
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$TableID =& $NodeInfo['TableID'];
		$table_publish = $db_config['table_pre'].$db_config['table_publish_pre']."_".$TableID;
		$sql = "SELECT i.URL,i.IndexID,i.PublishDate,i.Type,i.Sort,i.Pink,p.*,co.Hits_Total, co.Hits_Today, co.Hits_Week, co.Hits_Month, co.Hits_Date, co.CommentNum FROM {$table_publish} p LEFT JOIN {$table->content_index} i ON p.ContentID=i.ContentID AND i.Type=1  LEFT JOIN {$plugin_table['base']['count']} co ON co.IndexID=i.IndexID where (UNIX_TIMESTAMP() >= i.PublishDate) AND p.NodeID={$NodeID} Order by i.PublishDate DESC Limit {$start}, {$offset} ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		$result->Close( );
		return $data;
	}

	function getPublishInfo( $IndexID )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		global $iWPC;
		$result = $db->getRow( "SELECT NodeID FROM {$table->content_index} where  IndexID={$IndexID}" );
		$NodeID = $result['NodeID'];
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$TableID =& $NodeInfo['TableID'];
		$table_publish = $db_config['table_pre'].$db_config['table_publish_pre']."_".$TableID;
		$sql = "SELECT i.NodeID,i.URL,i.IndexID,i.PublishDate,i.Type,i.Sort,i.Pink,p.* FROM {$table->content_index} i,{$table_publish} p  where i.IndexID={$IndexID} AND i.ContentID =p.ContentID";
		$result = $db->getRow( $sql );
		$result['TableID'] = $TableID;
		return $result;
	}

	function getCommentRecordNum( $IndexID )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		global $iWPC;
		$result = $db->getRow( "SELECT COUNT(*) as nr  FROM  {$plugin_table['base']['comment']} where IndexID={$IndexID}" );
		return $result['nr'];
	}

	function getCommentRecordLimit( $IndexID, $start, $offset )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		global $iWPC;
		global $CONTENT_MODEL_INFO;
		$sql = "SELECT * FROM {$plugin_table['base']['comment']} where IndexID={$IndexID} Order by Approved ASC,CommentID DESC LIMIT {$start},{$offset}";
		$recordSet = $db->Execute( $sql );
		while ( !$recordSet->EOF )
		{
			$PublishInfo = $this->getPublishInfo( $IndexID );
			$recordSet->fields['Title'] = $PublishInfo[$CONTENT_MODEL_INFO[$PublishInfo['TableID']]['TitleField']];
			$recordSet->fields['URL'] = $PublishInfo['URL'];
			$data[] = $recordSet->fields;
			$recordSet->MoveNext( );
		}
		$recordSet->Close( );
		return $data;
	}

	function getCommentInfo( $CommentID, $field = "*" )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		global $iWPC;
		$sql = "SELECT {$field} FROM {$plugin_table['base']['comment']} where CommentID={$CommentID}  ";
		$result = $db->getRow( $sql );
		if ( $field == "*" )
		{
			return $result;
		}
		else
		{
			return $result[$field];
		}
	}

	function commentEdit( $CommentID )
	{
		global $plugin_table;
		$where = "where CommentID=".$CommentID;
		if ( $this->dataUpdate( $plugin_table['base']['comment'], $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function commentDel( $CommentID )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		global $iWPC;
		$IndexID = $this->getCommentInfo( $CommentID, "IndexID" );
		$db->query( "UPDATE {$plugin_table['base']['count']} SET `CommentNum`=CommentNum-1 where IndexID='{$IndexID}'" );
		$result = $db->query( "DELETE FROM {$plugin_table['base']['comment']} where CommentID={$CommentID}" );
		return $result;
	}

	function commentApprove( $CommentID )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		global $iWPC;
		$IndexID = $this->getCommentInfo( $CommentID, "IndexID" );
		$result = $db->query( "update {$plugin_table['base']['comment']} set Approved=1 where CommentID={$CommentID}" );
		return $result;
	}

	function commentDelByTime( $_start_time, $_end_time )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		$where = " CreationDate > ".strtotime( $_start_time )." AND CreationDate < ".( strtotime( $_end_time ) + 86400 );
		$result = $db->query( "DELETE FROM {$plugin_table['base']['comment']} where {$where} " );
		return $result;
	}

	function commentDelByIP( $_ip )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		$where = " Ip='".$_ip."'";
		$result = $db->query( "DELETE FROM {$plugin_table['base']['comment']} where {$where} " );
		return $result;
	}

	function commentDelByAuthor( $_author )
	{
		global $table;
		global $plugin_table;
		global $db_config;
		global $db;
		$where = " Author='".$_author."'";
		$result = $db->query( "DELETE FROM {$plugin_table['base']['comment']} where {$where} " );
		return $result;
	}

}

?>
