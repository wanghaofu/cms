<?php


class userextraAdmin extends iData
{

	function add( )
	{
		global $table;
		if ( $this->dataInsert( $table->user_extra ) )
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
		if ( $this->dataDel( $table->user_extra, $which, $uId, $method = "=" ) )
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
		if ( $this->dataUpdate( $table->user_extra, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}

?>
