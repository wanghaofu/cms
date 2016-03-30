<?php


class adodb
{

	var $adodb = NULL;

	function connect( $params )
	{
		require_once( KDB_DIR."lib/adodb/adodb.inc.php" );
		switch ( $params['db_type'] )
		{
		case "mysql" :
			$this->adodb = newadoconnection( "mysql" );
			$this->adodb->Connect( $params['db_host'], $params['db_user'], $params['db_password'], $params['db_name'] );
			$this->adodb->SetFetchMode( ADODB_FETCH_ASSOC );
			$this->adodb->fnExecute = "CountExecs";
			$this->adodb->fnCacheExecute = "CountCachedExecs";
			break;
		case "pgsql" :
			break;
		case "oracle" :
			require_once( KDB_DIR."data/oracle.data.class.php" );
			$this->adodb =& adonewconnection( "oracle" );
			$this->adodb->PConnect( false, $params['db_user'], $params['db_password'], $params['db_host'] );
			$this->adodb->SetFetchMode( ADODB_FETCH_ASSOC );
			break;
		case "sqlserver" :
			break;
		}
	}

	function &Execute( $query )
	{
		$rs =& $this->adodb->Execute( $query );
		if ( $rs )
		{
			return $rs;
		}
		else
		{
			echo "Adodb Error:".$this->adodb->ErrorNo( )." ".$this->adodb->ErrorMsg( );
			echo "<HR>{$query}<HR>";
		}
	}

	function close( )
	{
		return $this->adodb->Close( );
	}

	function &getRow( $query )
	{
		$rs =& $this->adodb->getRow( $query );
		return $rs;
	}

	function query( $query )
	{
		return $this->adodb->query( $query );
	}

	function escape_string( $str )
	{
		return mysql_real_escape_string( $str );
	}

	function selectLimit( $query, $start, $offset )
	{
		return $this->adodb->SelectLimit( $query, $start, $offset );
	}

	function errormsg( )
	{
		$result[code] = $this->adodb->ErrorNo( );
		$result[message] = $this->adodb->ErrorMsg( );
		return $result;
	}

}

?>
