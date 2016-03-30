<?php


class collection_admin extends iData
{

	function import( $CollectionID, $targetNodeID, $CateInfo )
	{
		global $publish;
		global $sys;
		global $_Counter;
		global $IN;
		if ( !isset( $publish ) )
		{
			require_once( INCLUDE_PATH."admin/publishAdmin.class.php" );
			$publish = new publishAdmin( );
		}
		$CollectionInfo = $this->getCollectionInfo( $CateInfo, $CollectionID );
		$fieldInfo = content_table_admin::gettablefieldsinfo( $CateInfo[TableID] );
		$publish->flushData( );
		foreach ( $fieldInfo as $key => $var )
		{
			LocalImgPathA2R::a2r( $CollectionInfo[$var[FieldName]] );
			$publish->addData( $var[FieldName], $CollectionInfo[$var[FieldName]] );
		}
		$time = time( );
		$publish->addData( "CreationDate", $time );
		$publish->addData( "ModifiedDate", $time );
		$publish->addData( "CreationUserID", $sys->session[sUId] );
		$publish->addData( "LastModifiedUserID", $sys->session[sUId] );
		$IndexInfo[PublishDate] = $time + $_Counter;
		$IN['NodeID'] = $targetNodeID;
		if ( $publish->contentAdd( $targetNodeID, $IndexInfo ) )
		{
			$SubNodeIDs = explode( ",", $CateInfo[SubNodeID] );
			$IndexID = $publish->db_insert_id;
			$IN['IndexID'] = $IndexID;
			foreach ( $fieldInfo as $key => $var )
			{
				LocalImgPathA2R::a2r( $CollectionInfo[$var[FieldName]] );
			}
			if ( !empty( $SubNodeIDs[0] ) )
			{
				foreach ( $SubNodeIDs as $key => $var )
				{
					$publish->createLink( $IndexID, $var );
				}
			}
			$SubNodeIDs = explode( ",", $CateInfo[IndexNodeID] );
			if ( !empty( $SubNodeIDs[0] ) )
			{
				foreach ( $SubNodeIDs as $key => $var )
				{
					$publish->createIndexLink( $IndexID, $var );
				}
			}
			if ( $CateInfo['DelAfterImport'] == 1 )
			{
				$this->del( $CateInfo, $CollectionID );
			}
			else
			{
				$this->flushData( );
				$this->addData( "IsImported", 1 );
				$this->update( $CateInfo, $CollectionID );
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function isValid( )
	{
		require( SYS_PATH."/license.php" );
		$license_array = $License;
		unset( $License );
		if ( $license_array['Module-Collection'] != 1 )
		{
			goback( "license_Module_Collection_disabled" );
		}
	}

	function copyTo( $IndexID, $targetCateID )
	{
		global $iWPC;
		global $sys;
		$contentInfo = $this->getContentInfo( $IndexID );
		$CateInfo = $iWPC->loadNodeInfo( $contentInfo[CateID] );
		$fieldInfo = content_table_admin::gettablefieldsinfo( $CateInfo[TableID] );
		$this->flushData( );
		foreach ( $fieldInfo as $key => $var )
		{
			$this->addData( $var[FieldName], $contentInfo[$var[FieldName]] );
		}
		$time = time( );
		$this->addData( "CreationDate", $time );
		$this->addData( "ModifiedDate", $time );
		$this->addData( "CreationUserID", $sys->session[sUId] );
		$this->addData( "LastModifiedUserID", $sys->session[sUId] );
		$PublishDate = $contentInfo[PublishDate];
		if ( $this->contentAdd( $targetCateID, $PublishDate ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $CateInfo, $CollectionID )
	{
		global $db_config;
		global $resource;
		global $SYS_ENV;
		$table_name = $db_config['table_pre'].$db_config['table_collection_pre']."_".$CateInfo[TableID];
		$which = "CollectionID";
		if ( !isset( $resource ) )
		{
			require_once( INCLUDE_PATH."admin/resource.class.php" );
			$resource = new Resource( );
		}
		$CollectionInfo = $this->getCollectionInfo( $CateInfo, $CollectionID );
		foreach ( $CollectionInfo as $var )
		{
			if( is_array($imgArray) ){
				$imgArray = array_merge( $imgArray, LocalImgPathA2R::_parsecontent( $var ) );
			} else{
				$imgArray = LocalImgPathA2R::_parsecontent( $var );
			}
		}
		$imgArray = array_unique( $imgArray );
		$resource->delResourceRefByCollectionKey( md5( $CollectionInfo['Src'] ) );
		foreach ( $imgArray as $var )
		{
			$var = str_replace( $SYS_ENV['ResourcePath']."/", "", $var );
			$resource->delResourceByPath( $var );
		}
		if ( $this->dataDel( $table_name, $which, $CollectionID, "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function recordExists( $CateInfo, $url )
	{
		global $db_config;
		global $iWPC;
		global $db;
		$table_name = $db_config['table_pre'].$db_config['table_collection_pre']."_".$CateInfo[TableID];
		$sql = "SELECT CollectionID  FROM {$table_name} WHERE Src='{$url}'";
		$result = $db->getRow( $sql );
		if ( empty( $result[CollectionID] ) )
		{
			return false;
		}
		else
		{
			return $result[CollectionID];
		}
	}

	function getCollectionLimit( &$CateInfo, $start = 0, $offset = 15 )
	{
		global $db;
		global $db_config;
		global $sys;
		if ( $start == "" )
		{
			$start = 0;
		}
		if ( $offset == "" )
		{
			$offset = 15;
		}
		$table_name = $db_config['table_pre'].$db_config['table_collection_pre']."_".$CateInfo[TableID];
		if ( $CateInfo[HiddenImported] == 1 )
		{
			$sql = "SELECT * FROM {$table_name}  where CateID='{$CateInfo[CateID]}' AND IsImported=0 ORDER BY CreationDate DESC LIMIT {$start}, {$offset}";
		}
		else
		{
			$sql = "SELECT * FROM {$table_name}  where CateID='{$CateInfo[CateID]}'  ORDER BY CreationDate DESC LIMIT {$start}, {$offset}";
		}
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getCollectionRecordNum( $CateInfo, $State = "!= -1" )
	{
		global $table;
		global $db;
		global $db_config;
		global $sys;
		$table_name = $db_config['table_pre'].$db_config['table_collection_pre']."_".$CateInfo[TableID];
		if ( $CateInfo[HiddenImported] == 1 )
		{
			$sql = "SELECT COUNT(*) as nr  FROM {$table_name} WHERE CateID='{$CateInfo[CateID]}' AND IsImported=0 ";
		}
		else
		{
			$sql = "SELECT COUNT(*) as nr  FROM {$table_name} WHERE CateID='{$CateInfo[CateID]}' ";
		}
		$result = $db->getRow( $sql );
		return $result[nr];
	}

	function getCollectionInfo( $CateInfo, $CollectionID, $field = "*" )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		$table_name = $db_config['table_pre'].$db_config['table_collection_pre']."_".$CateInfo[TableID];
		$sql = "SELECT {$field} FROM {$table_name} WHERE  CollectionID='{$CollectionID}'";
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

	function add( $CateInfo )
	{
		global $db_config;
		global $iWPC;
		global $publish;
		if ( !isset( $publish ) )
		{
			require_once( INCLUDE_PATH."admin/publishAdmin.class.php" );
			$publish = new publishAdmin( );
		}
		$table_name = $db_config['table_pre'].$db_config['table_collection_pre']."_".$CateInfo[TableID];
		if ( $this->dataInsert( $table_name ) )
		{
			if ( $CateInfo['AutoImport'] )
			{
				$this->import( $this->db_insert_id, $CateInfo['NodeID'], $CateInfo );
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $CateInfo, $CollectionID )
	{
		global $db_config;
		global $iWPC;
		$table_name = $db_config['table_pre'].$db_config['table_collection_pre']."_".$CateInfo[TableID];
		$where = "where CollectionID=".$CollectionID;
		if ( $this->dataUpdate( $table_name, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getNoteList( $CateInfo, $collectionID )
	{
		global $db;
		global $table;
		global $sys;
		$sql = "SELECT * FROM {$table->collection_note}  where CateID='{$CateInfo[CateID]}' AND collectionID={$collectionID} ORDER BY NoteDate DESC ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

}

?>
