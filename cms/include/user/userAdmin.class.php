<?php


class userAdmin extends iData
{

	function add( )
	{
		global $table;
		if ( $this->dataInsert( $table->user ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $uId )
	{
		global $table;
		$which = "uId";
		if ( $this->dataDel( $table->user, $which, $uId, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function userExist( $username )
	{
		global $db;
		global $table;
		$sql = "SELECT uId FROM {$table->user}  WHERE uName='{$username}' ";
		$result = $db->getRow( $sql );
		if ( !empty( $result[uId] ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $uId )
	{
		global $table;
		$where = "where uId=".$uId;
		if ( $this->dataUpdate( $table->user, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getAll( )
	{
		global $table;
		global $db;
		$sql = "SELECT u.*,g.gName FROM {$table->user} u,{$table->group} g  WHERE  g.gId=u.uGId ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getInfo( $uId, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->user}  WHERE uId='{$uId}'  ";
		$result = $db->getRow( $sql );
		if ( $field == "*" )
		{
			return $result;
		}
		else
		{
			return $result[$field];
		}
	}

}

?>
