<?php


class db
{

	var $db = NULL;
	var $charset = NULL;

	function connect( $params )
	{
		switch ( $params['db_type'] )
		{
		case "mysql" :
			require_once( KDB_DIR."lib/mysql.php" );
			if ( file_exists( KDB_DIR."data/mysql.data.class.php" ) )
			{
				require_once( KDB_DIR."data/mysql.data.class.php" );
			}
			$this->db = new mysql( );
			$this->db->charset = $this->charset;
			return $this->db->connect( $params );
			break;
		case "pgsql" :
			break;
		case "oracle" :
			break;
		case "sqlserver" :
			break;
		}
	}

	function &Execute( $query, $cache = false )
	{
		$rs =& $this->db->Execute( $query, $cache );
		return $rs;
	}

	function query( $query )
	{
		return $this->db->query( $query );
	}

	function fetch_array( $result )
	{
		return $this->db->fetch_array( $result );
	}

	function getDbVersion( )
	{
		return $this->db->getDbVersion( );
	}

	function close( )
	{
		return $this->db->db_close( );
	}

	function getRow( $query, $cache = false )
	{
		return $this->db->getRow( $query, $cache );
	}

	function errormsg( )
	{
		return $this->db->errormsg( );
	}

	function Insert_ID( )
	{
		return $this->db->Insert_ID( );
	}

	function escape_string( $string )
	{
		return $this->db->escape_string( $string );
	}

	function SetFetchMode( $mode )
	{
		$this->db->SetFetchMode( $mode );
	}

	function setDebug( $debug )
	{
		$this->db->setDebug( $debug );
	}

	function info( )
	{
		return $this->db->info( );
	}

	function errno( )
	{
		return $this->db->errno( );
	}

	function getTotalQueryTime( )
	{
		return $this->db->TotalQueryTime;
	}

	function getTotalQueryNum( )
	{
		return $this->db->TotalQueryNum;
	}

	function getTotalCacheNum( )
	{
		return $this->db->TotalCacheNum;
	}

	function getQueryLog( )
	{
		return $this->db->QueryLog;
	}

	function setCacheDir( $cache_dir )
	{
		$this->db->cache_dir = $cache_dir;
	}

	function setCharset( $charset )
	{
		return $this->db->setCharset( $charset );
	}

	function getServerInfo( )
	{
		return $this->db->getServerInfo( );
	}

}

?>
