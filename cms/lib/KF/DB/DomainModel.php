<?php


import( "KF.Util.Observable" );
class KF_DB_DomainModel extends KF_Util_Observable
{

	var $_vars = NULL;
	var $_vars_loaded = false;

	function create( )
	{
		}

	function update( $IndexID )
	{
		}

	function del( $IndexID )
	{
		}

	function get( $IndexID )
	{
		}

	function updateBy( $params )
	{
		}

	function delBy( $params )
	{
		}

	function getBy( $params )
	{
		}

	function findAll( $params, $start = 0, $end = 0 )
	{
		}

	function findOne( $params, $kql = "" )
	{
		}

	function _init( )
	{
		if ( $this->_vars_loaded === true )
		{
			return true;
		}
		$this->_vars = get_object_vars( $this );
		$this->_vars_loaded = true;
		return true;
	}

	function setVar( $varName, $varValue )
	{
		}

	function getVar( $varName )
	{
		}

	function toArray( )
	{
		$return = array( );
		foreach ( $this->_vars as $var )
		{
			if ( substr( $var, 1 ) == "_" )
			{
				continue;
			}
			$return[$var] = $this->$var;
		}
		return $return;
	}

}

?>
