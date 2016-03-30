<?php


class CacheData
{

	var $DataList = array
	(
		"sys" => "Cache_SYS_ENV.php",
		"admin_menu" => "Cache_ADMIN_CATE_MENU.php",
		"catelist" => "Cache_CateList.php",
		"psn" => "Cache_PSN.php",
		"content_model" => "Cache_ContentModel.php",
		"nodelist_1" => "Cache_NodeList_1.php",
		"nodelist_2" => "Cache_NodeList_2.php"
	);
	var $CacheFileHeader = "<?php\n//CMS cache file, DO NOT modify me!\n//Created on ";
	var $CacheFileFooter = "\n?>";
	var $output = NULL;

	function getData( $key )
	{
		include( CACHE_DIR.$this->DataList[$key] );
	}

	function clearAllCache( )
	{
		foreach ( $this->DataList as $var )
		{
			if ( file_exists( CACHE_DIR.$var ) )
			{
				@unlink( CACHE_DIR.$var );
			}
		}
	}

	function makeCache( $key )
	{
		global $table;
		global $db;
		switch ( $key )
		{
		case "sys" :
			$recordSet = $db->Execute( "SELECT * FROM {$table->sys}" );
			if ( !$recordSet )
			{
				print $db->ErrorMsg( );
				exit( );
			}
			else
			{
				while ( !$recordSet->EOF )
				{
					$SYS_ENV[$recordSet->fields[varName]] = $recordSet->fields[varValue];
					$recordSet->MoveNext( );
				}
			}
			$results = var_export( $SYS_ENV, true );
			$results = "\$SYS_ENV = ".$results.";";
			$this->writeCache( $key, $results );
			break;
		case "content_model" :
			$sql = "SELECT * FROM {$table->content_table} ";
			$recordset = $db->Execute( $sql );
			while ( !$recordset->EOF )
			{
				$TableID = $recordset->fields['TableID'];
				$CONTENT_MODEL_INFO[$TableID] = $recordset->fields;
				$sql = "SELECT * FROM {$table->content_fields} where TableID={$TableID} Order By FieldOrder";
				$result = $db->Execute( $sql );
				while ( !$result->EOF )
				{
					if ( strpos( $result->fields[FieldDefaultValue], ";" ) )
					{
						$result->fields[selectValue] = explode( ";", $result->fields[FieldDefaultValue] );
					}
					if ( $result->fields[IsTitleField] )
					{
						$CONTENT_MODEL_INFO[$TableID]['TitleField'] = $result->fields[FieldName];
					}
					if ( $result->fields[IsMainField] )
					{
						$CONTENT_MODEL_INFO[$TableID]['MainField'] = $result->fields[FieldName];
					}
					$CONTENT_MODEL_INFO[$TableID]['Model'][] = $result->fields;
					$result->MoveNext( );
				}
				$recordset->MoveNext( );
			}
			$results = var_export( $CONTENT_MODEL_INFO, true );
			$results = "\$CONTENT_MODEL_INFO = ".$results.";";
			$this->writeCache( $key, $results );
			break;
		case "psn" :
			if ( !class_exists( "psn_admin" ) )
			{
				require_once( INCLUDE_PATH."admin/psn_admin.class.php" );
			}
			$recordSet = $db->Execute( "SELECT * FROM {$table->psn}" );
			if ( !$recordSet )
			{
				print $db->ErrorMsg( );
				exit( );
			}
			else
			{
				while ( !$recordSet->EOF )
				{
					$PSN_INFO[$recordSet->fields[PSNID]] = array_merge( $recordSet->fields, psn_admin::parsepsn( $recordSet->fields[PSN] ) );
					$recordSet->MoveNext( );
				}
			}
			$results = var_export( $PSN_INFO, true );
			$results = "\$PSN_INFO = ".$results.";";
			$this->writeCache( $key, $results );
			break;
		case "catelist" :
			$results = $this->nodelist( );
			$results = var_export( $results, true );
			$results = "\$NODE_LIST = ".$results.";";
			$this->writeCache( $key, $results );
			break;
		case "nodelist_1" :
			$results = $this->nodelist_tree( 1 );
			$results = var_export( $results, true );
			$results = "\$NODE_LIST_1 = ".$results.";";
			$this->writeCache( $key, $results );
			break;
		case "nodelist_2" :
			$results = $this->nodelist_tree( 2 );
			$results = var_export( $results, true );
			$results = "\$NODE_LIST_2 = ".$results.";";
			$this->writeCache( $key, $results );
			break;
		}
	}

	function writeCache( $key, $cacheData )
	{
		$cacheData = $this->CacheFileHeader.date( "F j, Y, H:i" )."\n\n".$cacheData.$this->CacheFileFooter;
		$handle = fopen( CACHE_DIR.$this->DataList[$key], "w" );
		@flock( $handle, 3 );
		fwrite( $handle, $cacheData );
		fclose( $handle );
	}

	function nodelist_tree( $NodeID = "", $header = "" )
	{
		global $db;
		global $table;
		$this->output = array( );
		if ( empty( $NodeID ) )
		{
			return false;
		}
		$sql = "select * from {$table->site} where NodeID='{$NodeID}'  AND Disabled=0 ";
		$result = $db->getRow( $sql );
		if ( empty( $result['NodeID'] ) )
		{
			return false;
		}
		else
		{
			$this->output[$NodeID] = array(
				"NodeID" => $result[NodeID],
				"Name" => $result[Name],
				"ParentID" => $result[ParentID],
				"TableID" => $result[TableID],
				"PublishMode" => $result['PublishMode'],
				"NodeType" => $result['NodeType'],
				"WorkFlow" => $result['WorkFlow'],
				"NodeGUID" => $result['NodeGUID'],
				"NodeClassMark" => $result['NodeClassMark'],
				"NodeSort" => $result['NodeSort']
			);
			$NUM = $db->getRow( "SELECT COUNT(*) as nr  FROM {$table->site} where ParentID='{$NodeID}'  AND Disabled=0" );
			if ( !empty( $NUM['nr'] ) )
			{
				$this->nodelist( $NodeID );
			}
		}
		return $this->output;
	}

	function nodelist( $NodeID = "", $header = "" )
	{
		global $db;
		global $table;
		if ( empty( $NodeID ) )
		{
			$this->output = array( );
			$sql = "select * from {$table->site} where ParentID=0  AND Disabled=0 Order by NodeSort ASC";
			$result = $db->Execute( $sql );
			while ( !$result->EOF )
			{
				$NodeID = $result->fields[NodeID];
				$this->output[$NodeID] = array(
					"NodeID" => $result->fields[NodeID],
					"Name" => $result->fields[Name],
					"ParentID" => $result->fields[ParentID],
					"TableID" => $result->fields[TableID],
					"PublishMode" => $result->fields['PublishMode'],
					"NodeType" => $result->fields['NodeType'],
					"WorkFlow" => $result->fields['WorkFlow'],
					"NodeGUID" => $result->fields['NodeGUID'],
					"NodeClassMark" => $result->fields['NodeClassMark'],
					"NodeSort" => $result->fields['NodeSort']
				);
				$NUM = $db->getRow( "SELECT COUNT(*) as nr  FROM {$table->site} where ParentID='{$NodeID}'  AND Disabled=0" );
				if ( !empty( $NUM['nr'] ) )
				{
					$this->nodelist( $NodeID );
				}
				$result->MoveNext( );
			}
			return $this->output;
		}
		else
		{
			$header += 1;
			$sql = "select * from {$table->site} where ParentID={$NodeID}  AND Disabled=0  Order by NodeSort ASC";
			$result = $db->Execute( $sql );
			while ( !$result->EOF )
			{
				$NodeID = $result->fields[NodeID];
				$this->output[$NodeID] = array(
					"NodeID" => $result->fields[NodeID],
					"Name" => $result->fields[Name],
					"ParentID" => $result->fields[ParentID],
					"TableID" => $result->fields[TableID],
					"PublishMode" => $result->fields['PublishMode'],
					"cHeader" => $header,
					"NodeType" => $result->fields['NodeType'],
					"WorkFlow" => $result->fields['WorkFlow'],
					"NodeGUID" => $result->fields['NodeGUID'],
					"NodeClassMark" => $result->fields['NodeClassMark'],
					"NodeSort" => $result->fields['NodeSort']
				);
				$NUM = $db->getRow( "SELECT COUNT(*) as nr  FROM {$table->site} where ParentID='{$NodeID}'  AND Disabled=0" );
				if ( !empty( $NUM['nr'] ) )
				{
					$this->nodelist( $NodeID, $header );
				}
				$result->MoveNext( );
			}
		}
	}

}

?>
