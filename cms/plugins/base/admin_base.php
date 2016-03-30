<?php
if ( !defined( "IN_CMSWARE" ) )
{
	exit( "Access Denied" );
}
require_once( INCLUDE_PATH."admin/content_table_admin.class.php" );
require_once( PLUGIN_PATH."include/setting.class.php" );
require_once( PLUGIN_PATH."include/base.class.php" );
$base = new Base( );
switch ( $action )
{
case "list" :
	$NodeInfo = $iWPC->loadNodeInfo( $IN['NodeID'] );
	$offset = empty( $IN['offset'] ) ? 20 : $IN['offset'];
	$num = $base->getPublishRecordNum( $IN['NodeID'] );
	$pagenum = ceil( $num / $offset );
	if ( empty( $IN[Page] ) )
	{
		$Page = 1;
	}
	else
	{
		$Page = $IN[Page];
	}
	$start = ( $Page - 1 ) * $offset;
	$recordInfo[currentPage] = $Page;
	$recordInfo[pageNum] = $pagenum;
	$recordInfo[recordNum] = $num;
	$recordInfo[offset] = $offset;
	$recordInfo[from] = $start;
	$recordInfo[to] = $start + $offset;
	$TPL->assign( "DisplayItem", content_table_admin::getdisplayfieldsinfo( $NodeInfo[TableID] ) );
	$TPL->assign( "pList", $base->getPublishRecordLimit( $IN[NodeID], $start, $offset ) );
	$TPL->assign( "recordInfo", $recordInfo );
	$TPL->assign( "NodeInfo", $NodeInfo );
	$TPL->assign( "offset", $offset );
	$TPL->assign( "pagelist", pagelist( $pagenum, $Page, "{$base_url}o=admin_base::list&NodeID={$IN[NodeID]}&offset={$offset}", "#000000" ) );
	$TPL->display( "publish_list.html" );
	break;
case "commentAdmin" :
	$num = $base->getCommentRecordNum( $IN['IndexID'] );
	$offset = empty( $IN['offset'] ) ? 20 : $IN['offset'];
	$pagenum = ceil( $num / $offset );
	if ( empty( $IN[Page] ) )
	{
		$Page = 1;
	}
	else
	{
		$Page = $IN[Page];
	}
	$start = ( $Page - 1 ) * $offset;
	$CommentList = $base->getCommentRecordLimit( $IN['IndexID'], $start, $offset );
	$TPL->assign_by_ref( "CommentList", $CommentList );
	$TPL->assign( "CountNum", $num );
	$TPL->assign_by_ref( "Publish", $base->getPublishInfo( $IN['IndexID'] ) );
	$TPL->assign( "Page", pagelist( $pagenum, $Page, "{$base_url}o=admin_base::commentAdmin&IndexID={$IN[IndexID]}" ) );
	$TPL->display( "comment_list.html" );
	break;
case "commentSearchAdmin" :
	if ( !empty( $IN['start_time'] ) )
	{
		$where = " CreationDate > ".strtotime( $IN['start_time'] )." AND CreationDate < ".( strtotime( $IN['end_time'] ) + 86400 );
	}
	else if ( !empty( $IN['viewmode'] ) )
	{
		if ( $IN['viewmode'] == "approved" )
		{
			$where = " Approved=1";
		}
		else if ( $IN['viewmode'] == "unapproved" )
		{
			$where = " Approved=0";
		}
		else
		{
			$where = " Approved=1 || Approved=0 ";
		}
	}
	else
	{
		$where = " Comment LIKE '%".$IN[Keywords]."%' ";
	}
	$result = $db->getRow( "SELECT COUNT(*) as nr  FROM  ".$plugin_table['base']['comment']." where {$where} " );
	$num = $result[nr];
	$offset = empty( $IN['offset'] ) ? 20 : $IN['offset'];
	$pagenum = ceil( $num / $offset );
	if ( empty( $IN[Page] ) )
	{
		$Page = 1;
	}
	else
	{
		$Page = $IN[Page];
	}
	$start = ( $Page - 1 ) * $offset;
	$sql = "SELECT * FROM ".$plugin_table['base']['comment']." where {$where} Order by Approved ASC,CommentID DESC LIMIT {$start},{$offset}";
	$result = $db->Execute( $sql );
	while ( !$result->EOF )
	{
		$PublishInfo = $base->getPublishInfo( $result->fields['IndexID'] );
		$result->fields['Title'] = $PublishInfo[$CONTENT_MODEL_INFO[$PublishInfo['TableID']]['TitleField']];
		$result->fields['URL'] = $PublishInfo['URL'];
		$CommentList[] = $result->fields;
		$result->MoveNext( );
	}
	$TPL->assign( "CountNum", $num );
	$TPL->assign( "Page", pagelist( $pagenum, $Page, "{$base_url}o=admin_base::commentSearchAdmin&start_time={$IN['start_time']}&end_time={$IN['end_time']}&Keywords=".urlencode( $IN[Keywords] ) ) );
	$TPL->assign_by_ref( "CommentList", $CommentList );
	$TPL->assign_by_ref( "IN", $IN );
	$TPL->display( "comment_list.html" );
	break;
case "commentEdit" :
	$commentInfo = $base->getCommentInfo( $IN['CommentID'] );
	$TPL->assign_by_ref( "commentInfo", $commentInfo );
	$TPL->display( "comment_edit.html" );
	break;
case "commentEdit_submit" :
	$base->flushData( );
	$base->addData( "Comment", $IN['Comment'] );
	if ( $base->commentEdit( $IN['CommentID'] ) )
	{
		showmessage( "comment_edit_ok", $referer );
	}
	else
	{
		showmessage( "comment_edit_fail", $referer );
	}
	break;
case "commentDel" :
	if ( !empty( $IN['CommentIDs'] ) )
	{
		foreach ( $IN['CommentIDs'] as $key => $var )
		{
			if ( !empty( $var ) )
			{
				$result = $base->commentDel( $var );
			}
		}
		if ( $result )
		{
			showmessage( "comment_del_ok", $referer );
		}
		showmessage( "comment_del_fail", $referer );
	}
	if ( !empty( $IN['CommentID'] ) )
	{
		if ( $base->commentDel( $IN['CommentID'] ) )
		{
			showmessage( "comment_del_ok", $referer );
		}
		showmessage( "comment_del_fail", $referer );
	}
	if ( !empty( $IN['start_time'] ) )
	{
		if ( $base->commentDelByTime( $IN['start_time'], $IN['end_time'] ) )
		{
			showmessage( "comment_del_ok", $referer );
		}
		showmessage( "comment_del_fail", $referer );
	}
	if ( !empty( $IN['ip'] ) )
	{
		if ( $base->commentDelByIP( $IN['ip'] ) )
		{
			showmessage( "comment_del_ok", $referer );
		}
		showmessage( "comment_del_fail", $referer );
	}
	if ( !empty( $IN['username'] ) )
	{
		if ( $base->commentDelByAuthor( $IN['username'] ) )
		{
			showmessage( "comment_del_ok", $referer );
		}
		showmessage( "comment_del_fail", $referer );
	}
	showmessage( "comment_del_fail", $referer );
case "commentApprove" :
	if ( !empty( $IN['CommentIDs'] ) )
	{
		foreach ( $IN['CommentIDs'] as $key => $var )
		{
			if ( !empty( $var ) )
			{
				$result = $base->commentApprove( $var );
			}
		}
		if ( $result )
		{
			showmessage( "comment_approve_ok", $referer );
		}
		else
		{
			showmessage( "comment_approve_fail", $referer );
		}
	}
	else if ( !empty( $IN['CommentID'] ) )
	{
		if ( $base->commentApprove( $IN['CommentID'] ) )
		{
			showmessage( "comment_approve_ok", $referer );
		}
		else
		{
			showmessage( "comment_approve_fail", $referer );
		}
	}
	else
	{
		showmessage( "comment_approve_fail", $referer );
		break;
	}
}
include( MODULES_DIR."footer.php" );
?>
