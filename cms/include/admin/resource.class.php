<?php


class Resource
{

	function getResourceListByNodeIDLimit( $NodeID, $Category, $start = 0, $offset = 15 )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		$existsLib = array( );
		if ( $start == "" )
		{
			$start = 0;
		}
		if ( $offset == "" )
		{
			$offset = 15;
		}
		if ( empty( $NodeID ) )
		{
			$sql = "select distinct r.*,ref.NodeID,ref.IndexID,u.uName as CreationUser from {$table->resource} r left join {$table->resource_ref} ref ON ref.ResourceID=r.ResourceID left join {$table->user} u ON u.uId=r.CreationUserID  where r.Category='{$Category}' Order By r.ResourceID DESC  Limit {$start}, {$offset} ";
		}
		else
		{
			$sql = "select distinct ref.*,r.*,u.uName as CreationUser from {$table->resource} r left join {$table->resource_ref} ref ON ref.ResourceID=r.ResourceID left join {$table->user} u ON u.uId=r.CreationUserID where   (ref.NodeID='{$NodeID}' or r.NodeID='{$NodeID}') and r.Category='{$Category}' Order By r.ResourceID DESC  Limit {$start}, {$offset} ";
		}
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			if ( !in_array( $result->fields['ResourceID'], $existsLib ) )
			{
				$data[] = $result->fields;
				$existsLib[] = $result->fields['ResourceID'];
			}
			$result->MoveNext( );
		}
		$result->close( );
		unset( $existsLib );
		return $data;
	}

	function getResourceNumByNodeID( $NodeID = 0, $Category = "img" )
	{
		global $table;
		global $db;
		if ( empty( $NodeID ) )
		{
			$sql = "select count(distinct ResourceID) as nr from {$table->resource}  where Category='{$Category}'";
		}
		else
		{
			$sql = "select count(distinct r.ResourceID) as nr from {$table->resource} r left join {$table->resource_ref} ref ON ref.ResourceID=r.ResourceID where  (ref.NodeID='{$NodeID}' or r.NodeID='{$NodeID}') and r.Category='{$Category}'";
		}
		$result = $db->getRow( $sql );
		return $result['nr'];
	}

	function getQuoteContents( $ResourceID )
	{
		global $table;
		global $db;
		global $publish;
		if ( !class_exists( "publishAdmin" ) )
		{
			require_once( INCLUDE_PATH."admin/publishAdmin.class.php" );
		}
		if ( !isset( $publish ) )
		{
			$publish = new publishAdmin( );
		}
		$sql = "select ref.NodeID,ref.IndexID,r.* from {$table->resource_ref} ref left join {$table->resource} r ON ref.ResourceID=r.ResourceID where ref.ResourceID='{$ResourceID}' ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			if ( !empty( $result->fields['IndexID'] ) )
			{
				$ContentInfo[] = $publish->getContentInfo( $result->fields['IndexID'] );
			}
			$result->MoveNext( );
		}
		return $ContentInfo;
	}

	function getResourceInfo( $ResourceID )
	{
		global $table;
		global $db;
		$sql = "select r.*,ref.NodeID,ref.IndexID from {$table->resource} r left join {$table->resource_ref} ref ON ref.ResourceID=r.ResourceID  where r.ResourceID='{$ResourceID}' Limit 1";
		$result = $db->getRow( $sql );
		return $result;
	}

	function getResourceInfoByPath( $Path )
	{
		global $table;
		global $db;
		$sql = "select r.*,ref.NodeID,ref.IndexID,ref.CollectionKey from {$table->resource} r left join {$table->resource_ref} ref ON ref.ResourceID=r.ResourceID  where r.Path='{$Path}' Limit 1";
		$result = $db->getRow( $sql );
		return $result;
	}

	function delResourceByPath( $Path )
	{
		global $table;
		global $db;
		global $SYS_ENV;
		if ( empty( $Path ) )
		{
			return true;
		}
		$ResourceInfo = $this->getResourceInfoByPath( $Path );
		if ( empty( $ResourceInfo['IndexID'] ) && empty( $ResourceInfo['CollectionKey'] ) )
		{
			$fileToDel = $SYS_ENV['ResourcePath']."/".$ResourceInfo['Path'];
			echo "Delete ".$fileToDel." ...<br/>";
			$db->query( "DELETE FROM {$table->resource} where ResourceID='".$ResourceInfo['ResourceID']."'" );
			if ( file_exists( $fileToDel ) && !is_dir( $fileToDel ) )
			{
				return unlink( $fileToDel );
			}
			else
			{
				return true;
			}
		}
	}

	function delResourceRefByCollectionKey( $_ck )
	{
		global $table;
		global $db;
		global $SYS_ENV;
		return $db->query( "DELETE FROM {$table->resource_ref} where CollectionKey='".$_ck."'" );
	}

	function delResource( $ResourceID )
	{
		global $table;
		global $db;
		global $SYS_ENV;
		if ( empty( $ResourceID ) )
		{
			return true;
		}
		$ResourceInfo = $this->getResourceInfo( $ResourceID );
		if ( empty( $ResourceInfo['IndexID'] ) )
		{
			$fileToDel = $SYS_ENV['ResourcePath']."/".$ResourceInfo['Path'];
			echo "Delete ".$fileToDel." ...<br/>";
			$db->query( "DELETE FROM {$table->resource} where ResourceID='{$ResourceID}'" );
			if ( file_exists( $fileToDel ) )
			{
				return unlink( $fileToDel );
			}
			else
			{
				return true;
			}
		}
	}

}

?>
