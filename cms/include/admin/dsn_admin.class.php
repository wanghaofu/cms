<?php


class dsn_admin extends iData
{

	var $deploy_table_comment = NULL;
	var $deploy_table_search = NULL;
	var $deploy_table_count = NULL;
	var $db = NULL;

	function add( )
	{
		global $table;
		if ( $this->dataInsert( $table->dsn ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $DSNID )
	{
		global $table;
		$which = "DSNID";
		if ( $this->dataDel( $table->dsn, $which, $DSNID, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $DSNID )
	{
		global $table;
		$where = "where DSNID=".$DSNID;
		if ( $this->dataUpdate( $table->dsn, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getDSNInfo( $DSNID )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->dsn}  WHERE DSNID='{$DSNID}'";
		$result = $db->getRow( $sql );
		return $result;
	}

	function getAllDSN( )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->dsn}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function detect( $config )
	{
		$this->db = new kDB( $config['db_driver'] );
		if ( $this->db->connect( $config ) )
		{
			$this->db->close( );
			return true;
		}
		else
		{
			return false;
		}
	}

}

class dbDeploy extends kData
{

	var $db = NULL;

	function connectTo( $DSNID, $TableID )
	{
		global $db_config;
		global $db;
		$dsnInfo = dsn_admin::getdsninfo( $DSNID );
		$this->db =& $db;
		$this->table->deploy_table_comment = $dsnInfo[dbName].".".$db_config['table_pre']."deploy_comment_".$TableID;
		$this->table->deploy_table_count = $dsnInfo[dbName].".".$db_config['table_pre']."deploy_count_".$TableID;
		$this->table->deploy_table_search = $dsnInfo[dbName].".".$db_config['table_pre']."deploy_search_".$TableID;
	}

	function close( )
	{
		}

	function addIntoCount( )
	{
		if ( $this->dataInsert( $this->table->deploy_table_count ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function isCountExists( $IndexID )
	{
		$sql = "SELECT IndexID FROM ".$this->table->deploy_table_count." WHERE IndexID = '{$IndexID}'";
		$result = $this->db->getRow( $sql );
		if ( empty( $result[IndexID] ) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function add( )
	{
		if ( $this->dataReplace( $this->table->deploy_table_search ) )
		{
			$IndexID = $this->insData[IndexID];
			$ContentID = $this->insData[ContentID];
			$NodeID = $this->insData[NodeID];
			$this->flushData( );
			$this->addData( "IndexID", $IndexID );
			$this->addData( "ContentID", $ContentID );
			$this->addData( "NodeID", $NodeID );
			$this->addIntoCount( );
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( )
	{
		if ( $this->dataReplace( $this->table->deploy_table_search ) )
		{
			if ( !$this->isCountExists( $this->insData[IndexID] ) )
			{
				$IndexID = $this->insData[IndexID];
				$ContentID = $this->insData[ContentID];
				$NodeID = $this->insData[NodeID];
				$this->flushData( );
				$this->addData( "IndexID", $IndexID );
				$this->addData( "ContentID", $ContentID );
				$this->addData( "NodeID", $NodeID );
				$this->addIntoCount( );
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $IndexID )
	{
		$which = "IndexID";
		if ( $this->dataDel( $this->table->deploy_table_search, $which, $IndexID, $method = "=" ) && $this->dataDel( $this->table->deploy_table_count, $which, $IndexID, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function deploy( )
	{
		$this->createTable( );
	}

	function undeploy( )
	{
		$this->delTable( );
	}

	function addField( $data )
	{
		global $db_config;
		if ( $data[FieldSize] != "" && $data[FieldType] != "text" && $data[FieldType] != "mediumtext" && $data[FieldType] != "longtext" )
		{
			$length = "({$data[FieldSize]})";
		}
		$sql = "ALTER TABLE ".$this->table->deploy_table_search." ADD `{$data[FieldName]}` {$data[FieldType]} {$length}  NOT NULL";
		$result = $this->db->query( $sql );
	}

	function editField( $fieldInfo, $data )
	{
		global $db_config;
		if ( $data[FieldSize] != "" && $data[FieldType] != "text" && $data[FieldType] != "mediumtext" && $data[FieldType] != "longtext" )
		{
			$length = "({$data[FieldSize]})";
		}
		$sql = "ALTER TABLE ".$this->table->deploy_table_search." CHANGE `{$fieldInfo[FieldName]}` `{$data[FieldName]}` {$data[FieldType]} {$length}  NOT NULL";
		$result = $this->db->query( $sql );
		if ( !$result )
		{
			$this->addField( $data );
			$this->createIndex( $data );
		}
	}

	function delField( $fieldInfo )
	{
		global $db_config;
		$sql = "ALTER TABLE ".$this->table->deploy_table_search." DROP `{$fieldInfo[FieldName]}`";
		$result = $this->db->query( $sql );
	}

	function createIndex( $data )
	{
		global $db_config;
		if ( $data[FieldType] == "text" || $data[FieldType] == "mediumtext" || $data[FieldType] == "longtext" || $data[FieldType] == "varchar" )
		{
			$sql = "ALTER TABLE ".$this->table->deploy_table_search." ADD FULLTEXT (`{$data[FieldName]}`) ";
			$result = $this->db->query( $sql );
			return true;
		}
	}

	function deleteIndex( $fieldInfo )
	{
		global $db_config;
		if ( $fieldInfo[FieldType] == "text" || $fieldInfo[FieldType] == "mediumtext" || $$fieldInfo[FieldType] == "longtext" || $fieldInfo[FieldType] == "varchar" )
		{
			$sql = "ALTER TABLE ".$this->table->deploy_table_search." DROP INDEX `{$fieldInfo[FieldName]}` ";
			$result = $this->db->query( $sql );
		}
	}

	function createTable( )
	{
		$sql = "Create table IF NOT EXISTS ".$this->table->deploy_table_search."  (\r\n\t\t\t\t\tIndexID Integer(10) NOT NULL ,\r\n\t\t\t\t\tContentID Integer(10) NOT NULL ,\r\n\t\t\t\t\tNodeID Integer(10) NOT NULL ,\r\n\t\t\t\t\tPublishDate Integer(10) ,\r\n\t\t\t\t\tURL Char(250) ,\r\n\t\t\t\t\tPrimary Key (IndexID) ,\r\n\t\t\t\t\tKEY ContentID (ContentID), \r\n\t\t\t\t\tKEY NodeID (NodeID), \r\n\t\t\t\t\tKEY PublishDate (PublishDate) \r\n\t\t\t\t)";
		$sql_1 = "Create table IF NOT EXISTS ".$this->table->deploy_table_comment." (\r\n\t\t\t\t\tCommentID Integer(10) NOT NULL AUTO_INCREMENT UNIQUE ,\r\n\t\t\t\t\tIndexID Integer(10) NOT NULL ,\r\n\t\t\t\t\tContentID Integer(10) NOT NULL ,\r\n\t\t\t\t\tNodeID Integer(10) NOT NULL ,\r\n\t\t\t\t\tAuthor Char(100) ,\r\n\t\t\t\t\tCreationDate Integer(10) ,\r\n\t\t\t\t\tIp Char(15) ,\r\n\t\t\t\t\tComment Text ,\r\n\t\t\t\t\tPrimary Key (CommentID),\r\n\t\t\t\t\tKEY IndexID (IndexID), \r\n\t\t\t\t\tKEY ContentID (ContentID), \r\n\t\t\t\t\tKEY NodeID (NodeID)\r\n\t\t\t\t)";
		$sql_2 = "Create table IF NOT EXISTS ".$this->table->deploy_table_count."  (\r\n\t\t\t\t\t`IndexID` Integer(10) NOT NULL ,\r\n\t\t\t\t\t`ContentID` Integer(10) NOT NULL ,\r\n\t\t\t\t\t`NodeID` Integer(10) NOT NULL ,\r\n\t\t\t\t\t`Hits_Total` INT( 10 ) DEFAULT '0' ,\r\n\t\t\t\t\t`Hits_Month` INT( 10 ) DEFAULT '0' ,\r\n\t\t\t\t\t`Hits_Week` INT( 10 ) DEFAULT '0' ,\r\n\t\t\t\t\t`Hits_Today` INT( 10 ) DEFAULT '0' ,\r\n\t\t\t\t\t`Hits_Date` int(10) default '0',\r\n\t\t\t\t\t`CommentNum` int(10) NOT NULL default '0',\r\n\t\t\t\t\tPrimary Key (IndexID),\r\n\t\t\t\t\tKEY ContentID (ContentID), \r\n\t\t\t\t\tKEY NodeID (NodeID)\r\n\t\t\t\t) ";
		$this->db->query( $sql );
		$this->db->query( $sql_1 );
		$this->db->query( $sql_2 );
	}

	function delTable( )
	{
		$sql = "DROP TABLE  ".$this->table->deploy_table_search;
		$sql_1 = "DROP TABLE  ".$this->table->deploy_table_comment;
		$sql_2 = "DROP TABLE  ".$this->table->deploy_table_count;
		$this->db->query( $sql );
		$this->db->query( $sql_1 );
		$this->db->query( $sql_2 );
	}

}

?>
