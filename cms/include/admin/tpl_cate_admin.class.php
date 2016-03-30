<?php


class tpl_cate_admin extends iData
{

	function add( )
	{
		global $table;
		global $sys;
		$this->addData( "CreationUserID", $sys->session['sUId'] );
		if ( $this->dataInsert( $table->tpl_cate ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $TCID )
	{
		global $table;
		global $iWPC;
		global $db;
		global $sys;
		$sql = "DELETE FROM {$table->tpl_cate}  WHERE TCID='{$TCID}'";
		if ( $db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $TCID )
	{
		global $table;
		global $iWPC;
		global $sys;
		$where = "where  TCID=".$TCID;
		if ( $this->dataUpdate( $table->tpl_cate, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getCateInfo( $TCID, $field = "*" )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT {$field} FROM {$table->tpl_cate}  WHERE TCID='{$TCID}'";
		$result = $db->getRow( $sql );
		if ( $field != "*" && count( $result ) == 1 )
		{
			return $result[$field];
		}
		else
		{
			return $result;
		}
	}

	function getAll( $ParentTCID = 0 )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT * FROM {$table->tpl_cate} where ParentTCID='{$ParentTCID}'";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function haveSon( $TCID )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT count(*) as nr FROM {$table->tpl_cate}  WHERE ParentTCID='{$TCID}'";
		$result = $db->getRow( $sql );
		if ( 0 < $result[nr] )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getAll4Tree( $ParentTCID = 0 )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT TCID,CateName,ParentTCID FROM {$table->tpl_cate} where ParentTCID='{$ParentTCID}'";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			if ( $this->haveSon( $result->fields[TCID] ) )
			{
				$haveSon = 1;
			}
			else
			{
				$haveSon = 0;
			}
			$data[] = array(
				"TCID" => $result->fields[TCID],
				"CateName" => $result->fields[CateName],
				"ParentTCID" => $result->fields[ParentTCID],
				"haveSon" => $haveSon
			);
			$result->MoveNext( );
		}
		return $data;
	}

	function canAccess( &$CateInfo, $mode )
	{
		global $sys;
		if ( $sys->session['sGIsAdmin'] == 1 )
		{
			return true;
		}
		if ( $this->isCreationUser( $CateInfo ) )
		{
			return true;
		}
		if ( $CateInfo['Inherit'] == 1 )
		{
			$ParentCateInfo = $this->getCateInfo( $CateInfo['ParentTCID'] );
			return $this->canAccess( $ParentCateInfo, $mode );
		}
		if ( !empty( $CateInfo['ManageG'] ) )
		{
			$posMG = strpos( ",".$CateInfo['ManageG'].",", ",".$sys->session['sGId']."," );
			if ( $posMG === false )
			{
				}
			else
			{
				return true;
			}
		}
		if ( !empty( $CateInfo['ManageU'] ) )
		{
			$posMU = strpos( ",".$CateInfo['ManageU'].",", ",".$sys->session['sUId']."," );
			if ( $posMU === false )
			{
				}
			else
			{
				return true;
			}
		}
		if ( !empty( $CateInfo[$mode."G"] ) )
		{
			$posG = strpos( ",".$CateInfo[$mode."G"].",", ",".$sys->session['sGId']."," );
		}
		else
		{
			$posG = false;
		}
		if ( $posG === false )
		{
			if ( !empty( $CateInfo[$mode."U"] ) )
			{
				$posU = strpos( ",".$CateInfo[$mode."U"].",", ",".$sys->session['sUId']."," );
			}
			else
			{
				return false;
			}
			if ( $posU === false )
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}

	function isCreationUser( &$CateInfo )
	{
		global $sys;
		if ( $sys->session['sGIsAdmin'] == 1 )
		{
			return true;
		}
		if ( $CateInfo['CreationUserID'] == $sys->session['sUId'] )
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
