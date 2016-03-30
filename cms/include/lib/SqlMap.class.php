<?php


class SqlMap
{

	var $_db = NULL;
	var $_basefile = NULL;
	var $_pojo = array( );
	var $_sqlmap_select = array( );
	var $_sqlmap_update = array( );
	var $_sqlmap_delete = array( );
	var $_sqlmap_insert = array( );
	var $_contextIsLoaded = false;
	var $_debug = false;
	
	function SqlMap( $_context_bundle, $_auto_start = false )
	{
		global $table;
		global $db;
		global $plugin_table;
		$this->_db=$db;
		if ( file_exists( $_context_bundle ) )
		{
			include( $_context_bundle );
			$this->_sqlmap_select = $_sqlMap_select;
			$this->_sqlmap_update = $_sqlMap_update;
			$this->_sqlmap_delete = $_sqlMap_delete;
			$this->_sqlmap_insert = $_sqlMap_insert;
			
			unset( $_sqlMap_select );
			unset( $_sqlMap_update );
			unset( $_sqlMap_delete );
			unset( $_sqlMap_insert );
			$this->_contextIsLoaded = true;
			if ( $_auto_start )
			{
				$this->startTransaction( );
			}
		}
	}

	function startTransaction( )
	{
		global $table;
		global $plugin_table;
		global $db;
		$this->_db=$db;
		if ( !$this->_contextIsLoaded )
		{
			include( str_replace( ".php", "-sqlmap.php", $this->_basefile ) );
			$this->_sqlmap_select = $_sqlMap_select;
			$this->_sqlmap_update = $_sqlMap_update;
			$this->_sqlmap_delete = $_sqlMap_delete;
			$this->_sqlmap_insert = $_sqlMap_insert;
			unset( $_sqlMap_select );
			unset( $_sqlMap_update );
			unset( $_sqlMap_delete );
			unset( $_sqlMap_insert );
			$this->_contextIsLoaded = true;
		}
		if ( is_null( $this->_db ) )
		{
			if ( !empty( $GLOBALS['db'] ) )
			{
				$this->_db =& $db;
			}
			else
			{
				$this->error( "global datasource is null " );
			}
		}
		$this->_pojo = array( );
	}

	function commitTransaction( )
	{
//		$this->_db->commit();
	}

	function addData( $_data, $_val = NULL )
	{
		if ( is_array( $_data ) )
		{
			foreach ( $_data as $key => $var )
			{
				$this->_pojo[$key] = $this->_db->escape_string( $var );
			}
		}
		else
		{
			$this->_pojo[$_data] = $this->_db->escape_string( $_val );
		}
	}

	function delData( $_key )
	{
		unset( $this->_pojo[$_key] );
	}

	function filterData( &$IN, $_prefix = "data_" )
	{
		if ( !is_array( $IN ) )
		{
			return false;
		}
		foreach ( $IN as $key => $var )
		{
			if ( substr( $key, 0, 5 ) == $_prefix )
			{
				$field = substr( $key, 5 );
				$this->addData( $field, $var );
			}
		}
	}

	function getData( )
	{
		return $this->_pojo;
	}

	function _processQueryStr( $_sqlmap, $_escape_str = false )
	{
		if ( is_array( $_sqlmap ) )
		{
			$sql = $_sqlmap['sql'];
			if ( isset( $_sqlmap['sql_processor'] ) )
			{
				$sql_p = $_sqlmap['sql_processor'];
				if ( preg_match_all( "/#([a-zA-Z0-9_]+)#/isU", $sql_p, $matches ) )
				{
					foreach ( $matches[0] as $key => $var )
					{
						$sql_p = str_replace( $var, "\$this->_pojo['".$matches[1][$key]."']", $sql_p );
					}
				}
				eval( $sql_p );
			}
		}
		else
		{
			$sql = $_sqlmap;
		}
		if ( preg_match_all( "/#([a-zA-Z0-9_]+)#/isU", $sql, $matches ) )
		{
			foreach ( $matches[0] as $key => $var )
			{
				if ( $_escape_str )
				{
					$sql = str_replace( $var, $this->_db->escape_string( $this->_pojo[$matches[1][$key]] ), $sql );
				}
				else
				{
					$sql = str_replace( $var, $this->_pojo[$matches[1][$key]], $sql );
				}
			}
		}
		if ( $this->_debug )
		{
			exit( $sql );
		}
		return $sql;
	}

	function insert( $_queryid, $_pojo = NULL )
	{
		$escape_string = false;
		if ( !empty( $_pojo ) )
		{
			$this->_pojo = $_pojo;
			$escape_string = true;
		}
		if ( !isset( $this->_sqlmap_insert[$_queryid] ) )
		{
			$this->error( "Insert queryid {$_queryid} does not exists" );
		}
		$sql = $this->_processQueryStr( $this->_sqlmap_insert[$_queryid], $escape_string );
		return $this->_db->query( $sql );
	}

	function dataInsert( $_queryid )
	{
		if ( !isset( $this->_sqlmap_insert[$_queryid] ) )
		{
			$this->error( "Insert queryid {$_queryid} does not exists" );
		}
		if ( is_array( $this->_sqlmap_insert[$_queryid] ) )
		{
			$table_name = $this->_sqlmap_insert[$_queryid]['table'];
		}
		else
		{
			$this->error( "Insert queryid {$_queryid} do not have  the table name defined" );
		}
		$insData_Num = count( $this->_pojo );
		$Foreach_I = 0;
		$query = "Insert into ".$table_name." \n(\n";
		$query_key = "";
		$query_val = "";
		foreach ( $this->_pojo as $key => $val )
		{
			if ( 0 < strlen( $val ) )
			{
				if ( $Foreach_I == 0 )
				{
					$query_key .= "`".$key."`";
					$query_val .= "'".$val."'";
				}
				else
				{
					$query_key .= ",\n`".$key."`";
					$query_val .= ",\n'".$val."'";
				}
				$Foreach_I += 1;
			}
		}
		$query .= $query_key."\n) \nValues \n(\n".$query_val."\n)";
		if ( empty( $query ) )
		{
			$this->error( "query is empty" );
		}
		if ( $result = $this->_db->query( $query ) )
		{
			$this->db_insert_id = $this->_db->Insert_ID( );
			return true;
		}
		else
		{
			return false;
		}
	}

	function dataReplace( $_queryid )
	{
		if ( !isset( $this->_sqlmap_insert[$_queryid] ) )
		{
			$this->error( "Insert queryid {$_queryid} does not exists" );
		}
		if ( is_array( $this->_sqlmap_insert[$_queryid] ) )
		{
			$table_name = $this->_sqlmap_insert[$_queryid]['table'];
		}
		else
		{
			$this->error( "Insert queryid {$_queryid} do not have  the table name defined" );
		}
		$insData_Num = count( $this->_pojo );
		$Foreach_I = 0;
		$query = "Replace into ".$table_name." \n(\n";
		$query_key = "";
		$query_val = "";
		foreach ( $this->_pojo as $key => $val )
		{
			if ( 0 < strlen( $val ) )
			{
				if ( $Foreach_I == 0 )
				{
					$query_key .= "`".$key."`";
					$query_val .= "'".$val."'";
				}
				else
				{
					$query_key .= ",\n`".$key."`";
					$query_val .= ",\n'".$val."'";
				}
				$Foreach_I += 1;
			}
		}
		$query .= $query_key."\n) \nValues \n(\n".$query_val."\n)";
		if ( empty( $query ) )
		{
			$this->error( "query is empty" );
		}
		if ( $result = $this->_db->query( $query ) )
		{
			$this->db_insert_id = $this->_db->Insert_ID( );
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $_queryid, $_pojo = NULL )
	{
		$escape_string = false;
		if ( !empty( $_pojo ) )
		{
			$this->_pojo = $_pojo;
			$escape_string = true;
		}
		if ( !isset( $this->_sqlmap_update[$_queryid] ) )
		{
			$this->error( "Update queryid {$_queryid} does not exists" );
		}
		$sql = $this->_processQueryStr( $this->_sqlmap_update[$_queryid], $escape_string );
		return $this->_db->query( $sql );
	}

	function settingUpdate( $_queryid )
	{
		
		if ( !isset( $this->_sqlmap_update[$_queryid] ) )
		{
			$this->error( "Update queryid {$_queryid} does not exists" );
		}
		if ( is_array( $this->_sqlmap_update[$_queryid] ) )
		{
			$table_name = $this->_sqlmap_update[$_queryid]['table'];  //table 没有设定
		}
		else
		{
			$this->error( "Update queryid {$_queryid} do not have  the table name defined" );
		}
		foreach ( $this->_pojo as $key => $val )
		{
			$sql = "Replace Into {$table_name} Values('".$key."', '".$val."')";
			if ( $this->_db->query( $sql ) )
			{
				$return = true;
			}
			else
			{
				$return = false;
			}
		}
		
		return $return;
	}

	function dataUpdate( $_queryid )
	{
		if ( !isset( $this->_sqlmap_update[$_queryid] ) )
		{
			$this->error( "Update queryid {$_queryid} does not exists" );
		}
		if ( is_array( $this->_sqlmap_update[$_queryid] ) )
		{
			$table_name = $this->_sqlmap_update[$_queryid]['table'];
			if ( !empty( $this->_sqlmap_update[$_queryid]['where'] ) )
			{
				$where = " Where ".$this->_sqlmap_update[$_queryid]['where'];
			}
		}
		else
		{
			$this->error( "Update queryid {$_queryid} do not have  the table name defined" );
		}
		$where = $this->_processQueryStr( $where );
		$Foreach_I = 0;
		$query = "update ".$table_name." set ";
		$query_key = "";
		$query_val = "";
		foreach ( $this->_pojo as $key => $val )
		{
			if ( 0 <= strlen( $val ) )
			{
				if ( $Foreach_I == 0 )
				{
					$query_key = "`".$key."`";
					$query_val = "='".$val."'";
					$query .= $query_key.$query_val;
				}
				else
				{
					$query_key = ",`".$key."`";
					$query_val = "='".$val."'";
					$query .= $query_key.$query_val;
				}
				$Foreach_I += 1;
			}
		}
		$query .= $where;
		if ( empty( $query ) )
		{
			$this->error( "query is empty" );
		}
		if ( $this->_db->query( $query ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $_queryid, $_pojo = NULL )
	{
		$escape_string = false;
		if ( !empty( $_pojo ) )
		{
			$this->_pojo = $_pojo;
			$escape_string = true;
		}
		if ( !isset( $this->_sqlmap_delete[$_queryid] ) )
		{
			$this->error( "Delete queryid {$_queryid} does not exists" );
		}
		$sql = $this->_processQueryStr( $this->_sqlmap_delete[$_queryid], $escape_string );
		return $this->_db->query( $sql );
	}

	function dataDel( $_queryid )
	{
		if ( !isset( $this->_sqlmap_delete[$_queryid] ) )
		{
			$this->error( "Delete queryid {$_queryid} does not exists" );
		}
		if ( is_array( $this->_sqlmap_delete[$_queryid] ) )
		{
			$table_name = $this->_sqlmap_delete[$_queryid]['table'];
			$where = " Where ".$this->_sqlmap_delete[$_queryid]['where'];
		}
		else
		{
			$this->error( "Delete queryid {$_queryid} do not have  the table name defined" );
		}
		$where = $this->_processQueryStr( $where );
		$query = "Delete From ".$table_name;
		$query .= $where;
		if ( empty( $query ) )
		{
			$this->error( "query is empty" );
		}
		if ( $this->_db->query( $query ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function queryForObject( $_queryid, $_pojo = NULL )
	{
		$escape_string = false;
		if ( !empty( $_pojo ) )
		{
			$this->_pojo = $_pojo;
			$escape_string = true;
		}
		if ( !isset( $this->_sqlmap_select[$_queryid] ) )
		{
			$this->error( "Select queryid {$_queryid} does not exists" );
		}
		$sql = $this->_processQueryStr( $this->_sqlmap_select[$_queryid], $escape_string );
		if ( empty( $sql ) )
		{
			$this->error( "query is empty" );
		}
		$result = $this->_db->getRow( $sql );
		if ( empty( $result ) )
		{
			return false;
		}
		else
		{
			return $result;
		}
	}

	function queryForList( $_queryid, $_pojo = NULL )
	{
		$escape_string = false;
		$data = array( );
		if ( !empty( $_pojo ) )
		{
			$this->_pojo = $_pojo;
			$escape_string = true;
		}
		if ( !isset( $this->_sqlmap_select[$_queryid] ) )
		{
			$this->error( "Select queryid {$_queryid} does not exists" );
		}
		$sql = $this->_processQueryStr( $this->_sqlmap_select[$_queryid], $escape_string );
		if ( empty( $sql ) )
		{
			$this->error( "query is empty" );
		}
		$result = $this->_db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function queryForSettingMap( $_queryid, $_pojo = NULL )
	{
		$escape_string = false;
		$data = array( );
	
		if ( !empty( $_pojo ) )
		{
			$this->_pojo = $_pojo;
			$escape_string = true;
		}
		if ( !isset( $this->_sqlmap_select[$_queryid] ) )
		{
			$this->error( "Select queryid {$_queryid} does not exists" );
		}
		$sql = $this->_processQueryStr( $this->_sqlmap_select[$_queryid], $escape_string );
		
		if ( empty( $sql ) )
		{
			$this->error( "query is empty" );
		}
		if ( is_array( $this->_sqlmap_select[$_queryid]['map'] ) )
		{
			foreach ( $this->_sqlmap_select[$_queryid]['map'] as $key => $var )
			{
				$map_key = $key;
				$map_value = $var;
			}
		}
		else
		{
			$map_key = "key";
			$map_value = "value";
		}
		
		$result = $this->_db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[$result->fields[$map_key]] = $result->fields[$map_value];
			$result->MoveNext( );
		}
		return $data;
	}

	function error( $_msg )
	{
		trigger_error( "SqlMap Error : ".$_msg, E_USER_ERROR );
	}

	function setDebug( $_debug )
	{
		$this->_debug = $_debug;
	}

}

?>
