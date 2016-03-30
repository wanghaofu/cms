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

}

?>
