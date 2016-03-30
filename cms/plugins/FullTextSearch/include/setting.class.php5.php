<?php


class PluginSetting extends iData
{

	function update( )
	{
		global $plugin_table;
		if ( $this->dataReplace( $plugin_table['fulltext']['setting'] ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function getAll( )
	{
		global $plugin_table;
		global $db;
		global $table;
		$sql = "SELECT s.*,c.* FROM {$table->content_table} c LEFT JOIN {$plugin_table['fulltext']['setting']} s ON c.TableID=s.TableID";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	public static function getInfo( $TableID )
	{
		global $plugin_table;
		global $db;
		global $table;
		$sql = "SELECT s.*,c.* FROM {$table->content_table} c LEFT JOIN {$plugin_table['fulltext']['setting']} s ON c.TableID=s.TableID WHERE c.TableID={$TableID}";
		$result = $db->getRow( $sql );
		return $result;
	}

	function getAllFullText( $TableID )
	{
		global $plugin_table;
		global $db;
		global $table;
		$fields = array( );
		$sql = "SELECT * FROM  {$plugin_table['fulltext']['fields']} WHERE  TableID={$TableID}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getFullText( $SearchID )
	{
		global $plugin_table;
		global $db;
		global $table;
		$sql = "SELECT * FROM  {$plugin_table['fulltext']['fields']} WHERE  SearchID={$SearchID}";
		$result = $db->getRow( $sql );
		return $result;
	}

	function getAllFullTextedFields( $TableID )
	{
		global $plugin_table;
		global $db;
		global $table;
		$fields = array( );
		$sql = "SELECT * FROM  {$plugin_table['fulltext']['fields']} WHERE  TableID={$TableID}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data = explode( ",", $result->fields['FullTextFields'] );
			$fields = array_merge( $fields, $data );
			$result->MoveNext( );
		}
		$fields = array_unique( $fields );
		return $fields;
	}

	function delFullText( $TableID, $SearchID )
	{
		global $plugin_table;
		global $db;
		global $table;
		global $db_config;
		$Info = $this->getFullText( $SearchID );
		$data = explode( ",", $Info['FullTextFields'] );
		$table_search = $db_config['table_pre']."plugin_fulltext_search_".$TableID;
		$db->query( "ALTER TABLE {$table_search} DROP Index `{$Info['SearchName']}` " );
		foreach ( $data as $key => $var )
		{
			$result = $db->getRow( "SELECT Count(*) as nr FROM  {$plugin_table['fulltext']['fields']} WHERE  TableID={$TableID} AND FullTextFields LIKE '%{$var}%'" );
			if ( $result['nr'] == 1 )
			{
				$db->query( "ALTER TABLE {$table_search} DROP `{$var}`" );
			}
		}
		return $this->dataDel( $plugin_table['fulltext']['fields'], "SearchID", $SearchID, "=" );
	}

	function addFullText( $TableID, $SearchName, $Fields )
	{
		global $plugin_table;
		global $table;
		global $db;
		global $db_config;
		$existsFields = $this->getAllFullTextedFields( $TableID );
		$table_search = $db_config['table_pre']."plugin_fulltext_search_".$TableID;
		include( CACHE_DIR."Cache_ContentModel.php" );
		$ContentModel = $CONTENT_MODEL_INFO[$TableID]['Model'];
		foreach ( $Fields as $key => $var )
		{
			if ( $key == 0 )
			{
				$fulltext_field = $var;
			}
			else
			{
				$fulltext_field .= ",".$var;
			}
			if ( !in_array( $var, $existsFields ) )
			{
				$data = $db->getRow( "SELECT FieldType,FieldSize FROM {$table->content_fields} WHERE FieldName='{$var}' AND TableID={$TableID} " );
				if ( $data[FieldSize] != "" && $data[FieldType] != "text" && $data[FieldType] != "mediumtext" && $data[FieldType] != "longtext" )
				{
					$length = "({$data[FieldSize]})";
				}
				if ( $db->query( "ALTER TABLE {$table_search} ADD `{$var}` {$data[FieldType]} {$length}  NOT NULL" ) )
				{
					$existsFields[] = $var;
				}
			}
		}
		$sql = "ALTER TABLE {$table_search} ADD FULLTEXT {$SearchName}(".$fulltext_field.")";
		if ( $db->query( $sql ) )
		{
			$this->flushData( );
			$this->addData( "TableID", $TableID );
			$this->addData( "SearchName", $SearchName );
			$this->addData( "FullTextFields", $fulltext_field );
			if ( $this->dataInsert( $plugin_table['fulltext']['fields'] ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else if ( $db->errno( ) == 1146 )
		{
			include( PLUGIN_PATH."include/data_sync/add_table.php" );
		}
		else
		{
			return false;
		}
	}

}

?>
