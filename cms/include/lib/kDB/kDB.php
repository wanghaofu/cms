<?php


class kDB
{

	var $driver_path = "driver/";
	var $Driver = NULL;
	var $connection = NULL;
	var $debug = false;

	function kDB( $type = NULL )
	{
		$this->driver_path = KDB_DIR.$this->driver_path;
		$this->_regDriver( $type );
	}

	function _regDriver( $type = NULL )
	{
		$driver = $this->driver_path.$type.".php";
		if ( file_exists( $driver ) )
		{
			include_once( $driver );
			$this->Driver = new $type( );
			return true;
		}
		else
		{
			return false;
		}
	}

	function getDbVersion( )
	{
		return $this->Driver->getDbVersion( );
	}

	function connect( $params )
	{
		return $this->Driver->connect( $params );
	}

	function close( )
	{
		return $this->Driver->close( );
	}

	function query( $queryStr )
	{
		return $this->Driver->query( $queryStr );
	}

	function &Execute( $query, $cache = false )
	{
		$rs =& $this->Driver->Execute( $query, $cache );
		return $rs;
	}

	function selectLimit( $query, $start, $offset )
	{
		return $this->Driver->selectLimit( $query, $start, $offset );
	}

	function getRow( $res, $cache = false )
	{
		return $this->Driver->getRow( $res, $cache );
	}

	function getRowsNum( $res )
	{
		return $this->Driver->getRowsNum( $res );
	}

	function errormsg( )
	{
		return $this->Driver->errormsg( );
	}

	function Insert_ID( )
	{
		return $this->Driver->Insert_ID( );
	}

	function escape_string( $string )
	{
		return $this->Driver->escape_string( $string );
	}

	function SetFetchMode( $mode )
	{
		$this->Driver->SetFetchMode( $mode );
	}

	function setDebug( $debug )
	{
		$this->debug = $debug;
		$this->Driver->setDebug( $debug );
	}

	function setCacheDir( $cache_dir )
	{
		$this->Driver->setCacheDir( $cache_dir );
	}

	function info( )
	{
		return $this->Driver->info( );
	}

	function errno( )
	{
		return $this->Driver->errno( );
	}

	function getTotalQueryTime( )
	{
		return $this->Driver->getTotalQueryTime( );
	}

	function getTotalQueryNum( )
	{
		return $this->Driver->getTotalQueryNum( );
	}

	function getTotalCacheNum( )
	{
		return $this->Driver->getTotalCacheNum( );
	}

	function getQueryLog( )
	{
		return $this->Driver->getQueryLog( );
	}

	function setCharset( $charset )
	{
		return $this->Driver->setCharset( $charset );
	}

	function getServerInfo( )
	{
		return $this->Driver->getServerInfo( );
	}
	
	function exec( $query, $cache = false )
	{
		$rs = $this->query( $query );
		return $rs;
	}
	
	function getRows( $query, $cache = false )
	{
		$result = $this->Execute( $query );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}
	
	// 查询数据库记录
	function select ( $dbTable, $condition = '', $orderBy = '', $limit = 0, $offset = 0, $fields = '*', $groupBy = '' )
	{
		if ( is_array ( $fields ) )
		{
			$fieldList = @implode( ',', $fields );
		}
		else
		{
			$fieldList = $fields;
		}
		if ( $condition != '' )
		{
			$condition = "WHERE $condition";
		}
		$orderBy = trim ( $orderBy );
		if ( $orderBy != '' && !strstr ( strtoupper ( $orderBy ), 'ORDER BY' ) )
		{
			$orderBy = "ORDER BY $orderBy";
		}

		$groupBy = trim ( $groupBy );
		if ( $groupBy != '' && !strstr ( strtoupper ( $groupBy ), 'GROUP BY' ) )
		{
			$groupBy = "GROUP BY $groupBy";
		}

		$strSql = " SELECT $fieldList FROM $dbTable $condition $groupBy $orderBy";

		$limit = intval ( $limit );
		$offset = intval ( $offset );
		if ( $limit )
		{
			$strSql .= " LIMIT $limit";
		}
		if ( $offset )
		{
			$strSql .= " OFFSET $offset";
		}
		//		$res = $this->query ( $strSql );
		$res =  $this->getRows($strSql, $cache );
		if ( $res )
		{
			return $res;
		}
		else
		{
			return false;
		}
	}

	// 修改数据库记录
	function update ( $dbTable, $condition, $arrUpdate = null, $strUpdate = null, $limit = 0 )
	{
		if ( $condition != '' )
		{
			$condition = "WHERE $condition";
		}
		while ( list ( $key, $item ) = @each ( $arrUpdate ) ) # 连接要更新的字段
		{
			$updateFieldArr[] = " $key = '$item'";
		}
		$updateFields = @join ( ',', $updateFieldArr );
		if ( $strUpdate )
		{
			if ( $updateFields ) $strUpdate .= ', ' . $updateFields;
		}
		else
		{
			$strUpdate = $updateFields;
		}
		$strSql = " UPDATE $dbTable SET $strUpdate $condition";
		if ( $limit > 0 )
		{
			$strSql .= " LIMIT $limit";
		}
		return  $this->Driver->query( $strSql );
	}

	// 插入记录
	function insert ( $dbTable, $arrInsert )
	{
		if ( is_array ( current ( $arrInsert ) ) )
		{
			$insertfields = $this->charSplit . join ( $this->charSplit . ', ' . $this->charSplit, array_keys ( current ( $arrInsert ) ) ) . $this->charSplit;
			while ( list ( $key, $item ) = @each ( $arrInsert ) )
			{
				$insertValuesArr[] = "( '" . join ( "', '", $item ) . "' )";
			}
			$insertValues = join ( ', ', $insertValuesArr );
		}
		else
		{
			$insertfields = $this->charSplit . join ( $this->charSplit . ', ' . $this->charSplit, array_keys ( $arrInsert ) ) . $this->charSplit;
			$insertValues = "( '" . join ( "', '", $arrInsert ) . "' )";
		}

		$strSql = "INSERT INTO $dbTable ( $insertfields ) VALUES $insertValues";
		return  $this->Driver->query( $strSql );
	}

	// 插入记录
	function replace ( $dbTable, $arrInsert )
	{
		if ( is_array ( current ( $arrInsert ) ) )
		{
			$insertfields = $this->charSplit . join ( $this->charSplit . ', ' . $this->charSplit, array_keys ( current ( $arrInsert ) ) ) . $this->charSplit;
			while ( list ( $key, $item ) = @each ( $arrInsert ) )
			{
				$insertValuesArr[] = "( '" . join ( "', '", $item ) . "' )";
			}
			$insertValues = join ( ', ', $insertValuesArr );
		}
		else
		{
			$insertfields = $this->charSplit . join ( $this->charSplit . ', ' . $this->charSplit, array_keys ( $arrInsert ) ) . $this->charSplit;
			$insertValues = "( '" . join ( "', '", $arrInsert ) . "' )";
		}

		$strSql = "REPLACE INTO $dbTable ( $insertfields ) VALUES $insertValues";

		return  $this->Driver->query( $strSql );
	}

	// 删除数据库记录
	function delete ( $dbTable, $condition = '',$orderBy = '', $limit = 0, $offset = 0  )
	{
		if( $condition != '' )
		{
			$condition = "WHERE $condition";
		}

		$orderBy = trim ( $orderBy );
		if ( $orderBy != '' && !strstr ( strtoupper ( $orderBy ), 'ORDER BY' ) )
		{
			$orderBy = "ORDER BY $orderBy";
		}

		$strSql = "DELETE FROM $dbTable $condition $orderBy";

		$limit = intval ( $limit );
		$offset = intval ( $offset );
		if ( $limit )
		{
			$strSql .= " LIMIT $limit";
		}
		if ( $offset )
		{
			$strSql .= " OFFSET $offset";
		}

		return  $this->query( $strSql );
	}

}

?>
