<?php
class Access
{

	var $GuestGroupID = 1;
	var $sId = "";
	var $user = "";
	var $pass = "";
	var $bbs = "";
	var $ActionMap = array
	(
		0 => "ReadIndex",
		1 => "ReadContent",
		2 => "PostComment",
		3 => "ReadComment"
	);

	function Access( &$oas )
	{
		$this->oas =& $oas;
		$this->sId =& $this->oas->sId;
		$this->user =& $this->oas->user;
		$this->pass =& $this->oas->pass;
		$this->oas->ActionMap =& $this->ActionMap;
	}

	function canAccess( $NodeID, $Action )
	{
		if ( $this->_canAccess( $NodeID, $Action ) )
		{
			return true;
		}
		else
		{
			if ( !$this->oas->isLogin( ) && !$this->oas->fromCWPS )
			{
				}
			return false;
		}
	}

	function _canAccess( $NodeID, $Action )
	{
		global $plugin_table;
		global $bbs_table;
		global $db;
		global $_FieldMapping;
		global $iWPC;
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$ParentNodeID = explode( "%", $NodeInfo[ParentNodeID] );
		$sql = "SELECT m.AccessNodeIDs,a.AccessInherit FROM {$plugin_table['oas']['access']} a, {$plugin_table['oas']['access_map']} m  WHERE a.OwnerID={$this->oas->session[GroupID]}  AND  a.AccessType=1 AND a.AccessID=m.AccessID AND m.PermissionKey='{$Action}'";
		$result = $db->getRow( $sql );
		if ( strpos( $result['AccessNodeIDs'], ",".$NodeID."," ) )
		{
			return true;
		}
		else
		{
			foreach ( $ParentNodeID as $var )
			{
				if ( strpos( $result['AccessNodeIDs'], ",".$var."," ) && strpos( $result['AccessInherit'], ",".$var."," ) )
				{
					return true;
				}
			}
			if ( !empty( $this->oas->session[UserID] ) )
			{
				$sql = "SELECT m.AccessNodeIDs,a.AccessInherit FROM {$plugin_table['oas']['access']} a, {$plugin_table['oas']['access_map']} m  WHERE a.OwnerID={$this->oas->session[UserID]}  AND  a.AccessType=0 AND a.AccessID=m.AccessID AND m.PermissionKey='{$Action}'";
				$result = $db->getRow( $sql );
				if ( strpos( $result['AccessNodeIDs'], ",".$NodeID."," ) )
				{
					return true;
				}
				else
				{
					foreach ( $ParentNodeID as $var )
					{
						if ( strpos( $result['AccessNodeIDs'], ",".$var."," ) && strpos( $result['AccessInherit'], ",".$var."," ) )
						{
							return true;
						}
					}
				}
			}
			return false;
		}
	}

	function isLogin( )
	{
		return $this->oas->isLogin( );
	}

}

function kAddslashes( $string, $force = 0 )
{
	if ( !$magic_quotes_gpc || $force )
	{
		if ( is_array( $string ) )
		{
			foreach ( $string as $key => $val )
			{
				$string[$key] = kaddslashes( $val, $force );
			}
		}
		else
		{
			$string = addslashes( $string );
		}
	}
	return $string;
}

function random( $length )
{
	$hash = "";
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	$max = strlen( $chars ) - 1;
	mt_srand( ( double )microtime( ) * 1000000 );
	$i = 0;
	for ( ;	$i < $length;	++$i	)
	{
		$hash .= $chars[mt_rand( 0, $max )];
	}
	return $hash;
}

?>
