<?php
if ( preg_match_all( $patt, $publishInfo[$mainContentLabel], $match ) )
{
	$contentArray = preg_split( $patt, $publishInfo[$mainContentLabel] );
	if ( $contentArray[0] == "" )
	{
		$contentArray = array_slice( $contentArray, 1 );
		$pagenum = count( $contentArray );
		$pagenum_pre = $pagenum;
		$this->mDoc[PageNum] = $pagenum;
		$totalnum = $pagenum;
		--$pagenum;
		--$pagenum_pre;
		$hawking = 1;
	}
	else
	{
		$pagenum = count( $contentArray );
		$pagenum_pre = $pagenum;
		$this->mDoc[PageNum] = $pagenum;
		$totalnum = $pagenum;
		--$pagenum;
		--$pagenum_pre;
		$hawking = 0;
	}
	do
	{
		if ( $pagenum_pre == 0 )
		{
			$sign = "";
		} else
		{
			$sign = "_".$pagenum_pre;
		}

		if ( $publishInfo[SelfPublishFileName] != "" )
		{
			$publishFileName = $publishInfo[SelfPublishFileName];
		}
		else
		{
			eval( "\$publishFileName = \"{$PublishFileFormat}\";" );
		}

		$publishFileName = preg_replace( "/\\.([A-Za-z0-9]+)\$/isU", $sign.".\\1", $publishFileName );

		if ( $hawking == 1 )
		{
			$pageNav[] = array(
			"Title" => $match[1][$pagenum_pre],
			"URL" => $publishFileName,
			"Link" => $publishFileName
			);
		} else
		{
			if ( $match[1][$pagenum_pre - 1] == "" )
			{
				$match[1][$pagenum_pre - 1] = $IndexPageTitle;
			}
			$pageNav[] = array(
			"Title" => $match[1][$pagenum_pre - 1],
			"URL" => $publishFileName,
			"Link" => $publishFileName
			);
		}
	} 	while ( $pagenum_pre-- );
	$pageNav = array_reverse( $pageNav );
	do
	{
		$pagenumlist = $totalnum;
		--$pagenumlist;
		$pagelist = "";
		if ( $publishInfo[SelfPublishFileName] != "" )
		{
			$publishFileName = $publishInfo[SelfPublishFileName];
		}
		else
		{
			eval( "\$publishFileName = \"{$PublishFileFormat}\";" );
		}
		$publishFileName = preg_replace( "/\\.([A-Za-z0-9]+)\$/isU", "{symbol}{page}.\\1", $publishFileName );
		$pagelist = content_page( $totalnum, $pagenum + 1, $publishFileName );  //该处？
		$template->assign( $mainContentLabel, $contentArray[$pagenum] );

		if ( $hawking == 1 )
		{
			$template->assign( $_pageTitle, $match[1][$pagenum] );
		}
		else
		{
			$template->assign( $_pageTitle, $match[1][$pagenum - 1] );
		}
		$template->assign( $_pageList, $pagelist );
		$template->assign( $_pageNav, $pageNav );

		//多处的内容
		$_CMS['ContentPageNav'] = $pageNav;
		$_CMS['CurrentPage'] = $pagenum;
		$_CMS['PublishDate'] = $publishInfo['PublishDate'];
		$_CMS['NodeID'] = $publishInfo['NodeID'];
		//多出的内容
		if ( $pagenum == 0 )
		{
			$sign = "";
		}
		else
		{
			$sign = "_".$pagenum;
		}
		if ( $publishInfo[SelfPublishFileName] != "" )
		{
			$publishFileName = $publishInfo[SelfPublishFileName];
		}
		else
		{
			eval( "\$publishFileName = \"{$PublishFileFormat}\";" );
		}
		if ( !empty( $this->NodeInfo[SubDir] ) && empty( $publishInfo[SelfPSNURL] ) )
		{
			if ( $this->NodeInfo[SubDir] == "auto" )
			{
				$publishFileName = $this->makeIndexSavePath( $IndexID )."/".$publishFileName;
			}
			else
			{
				$publishFileName = date( $this->NodeInfo[SubDir], $this->publishInfo[CreationDate] )."/".$publishFileName;
			}
		}
		else if ( !empty( $publishInfo[SelfPSNURL] ) )
		{
			$publishFileName = $publishFileName;
		}
		$realURL = $this->getHtmlURL( $publishFileName );
		$template->assign( "URL", $realURL );
		$publishFileName = preg_replace( "/\\.([A-Za-z0-9]+)\$/isU", $sign.".\\1", $publishFileName );
		if ( $pagenum == 0 && ( $this->publishInfo[Type] == 1 || $this->publishInfo[Type] == 0 || $this->publishInfo[Type] == 3 ) )
		{
			$FieldsInfo = content_table_admin::gettablefieldsinfo( $NodeInfo[TableID] );
			$this->flushData( );
			foreach ( $FieldsInfo as $key => $var )
			{
				if ( empty( $var['EnablePublish'] ) )
				{
					continue;
				}
				$this->addData( $var[FieldName], $publishInfo[$var[FieldName]] );
			}
			$this->addData( "IndexID", $publishInfo[IndexID] );
			$this->addData( "ContentID", $publishInfo[ContentID] );
			$this->addData( "NodeID", $publishInfo[NodeID] );
			$this->addData( "PublishDate", $publishInfo[PublishDate] );
			$this->addData( "URL", $realURL );
			$publishInfo['URL'] = $realURL;
			$this->publishUpdate( $NodeInfo['TableID'] );
			if ( !isset( $Plugin ) )
			{
				require_once( INCLUDE_PATH."admin/plugin.class.php" );
				$Plugin = new Plugin( );
			}
			$Plugin->update( $publishInfo );
		}
		$output = $template->fetch( $tplname, 0 );
		$output = restorexmlheader( $output );
		if ( $this->_publishing( $publishFileName, $output ) )
		{
			$right = true;
		}
		else
		{
			$right = false;
		}
		//		if ( $pagenum-- )
		//		{
		//		}
	} while ( $pagenum-- );
} elseif ( preg_match_all( $patt1, $publishInfo[$mainContentLabel], $match ) )
{
	$contentArray = preg_split( $patt1, $publishInfo[$mainContentLabel] );
	if ( $contentArray[0] == "" )
	{
		$contentArray = array_slice( $contentArray, 1 );
		$pagenum = count( $contentArray );
		$pagenum_pre = $pagenum;
		$this->mDoc[PageNum] = $pagenum;
		$totalnum = $pagenum;
		--$pagenum;
		--$pagenum_pre;
		$hawking = 1;
	}
	else
	{
		$pagenum = count( $contentArray );
		$pagenum_pre = $pagenum;
		$this->mDoc[PageNum] = $pagenum;
		$totalnum = $pagenum;
		--$pagenum;
		--$pagenum_pre;
		$hawking = 0;
	}
	do
	{
		if ( $pagenum_pre == 0 )
		{
			$sign = "";
		}
		else
		{
			$sign = "_".$pagenum_pre;
		}
		if ( $publishInfo[SelfPublishFileName] != "" )
		{
			$publishFileName = $publishInfo[SelfPublishFileName];
		}
		else
		{
			eval( "\$publishFileName = \"{$PublishFileFormat}\";" );
		}
		$publishFileName = preg_replace( "/\\.([A-Za-z0-9]+)\$/isU", $sign.".\\1", $publishFileName );
		if ( $hawking == 1 )
		{
			$pageNav[] = array(
			"Title" => $match[1][$pagenum_pre],
			"URL" => $publishFileName,
			"Link" => $publishFileName
			);
		}
		else
		{
			if ( $match[1][$pagenum_pre - 1] == "" )
			{
				$match[1][$pagenum_pre - 1] = $IndexPageTitle;
			}
			$pageNav[] = array(
			"Title" => $match[1][$pagenum_pre - 1],
			"URL" => $publishFileName,
			"Link" => $publishFileName
			);
		}
	} while ( $pagenum_pre-- );

	$pageNav = array_reverse( $pageNav );
	do
	{
		$pagenumlist = $totalnum;
		--$pagenumlist;
		$pagelist = "";
		if ( $publishInfo[SelfPublishFileName] != "" )
		{
			$publishFileName = $publishInfo[SelfPublishFileName];
		}
		else
		{
			eval( "\$publishFileName = \"{$PublishFileFormat}\";" );
		}
		$publishFileName = preg_replace( "/\\.([A-Za-z0-9]+)\$/isU", "{symbol}{page}.\\1", $publishFileName );
		$pagelist = content_page( $totalnum, $pagenum + 1, $publishFileName );
		$template->assign( $mainContentLabel, $contentArray[$pagenum] );
		if ( $hawking == 1 )
		{
			$template->assign( $_pageTitle, $match[1][$pagenum] );
		}
		else
		{
			$template->assign( $_pageTitle, $match[1][$pagenum - 1] );
		}
		$template->assign( $_pageList, $pagelist );
		$template->assign( $_pageNav, $pageNav );
		if ( $pagenum == 0 )
		{
			$sign = "";
		}
		else
		{
			$sign = "_".$pagenum;
		}
		if ( $publishInfo[SelfPublishFileName] != "" )
		{
			$publishFileName = $publishInfo[SelfPublishFileName];
		}
		else
		{
			eval( "\$publishFileName = \"{$PublishFileFormat}\";" );
		}
		if ( !empty( $this->NodeInfo[SubDir] ) && empty( $publishInfo[SelfPSNURL] ) )
		{
			if ( $this->NodeInfo[SubDir] == "auto" )
			{
				$publishFileName = $this->makeIndexSavePath( $IndexID )."/".$publishFileName;
			}
			else
			{
				$publishFileName = date( $this->NodeInfo[SubDir], $this->publishInfo[CreationDate] )."/".$publishFileName;
			}
		}
		else if ( !empty( $publishInfo[SelfPSNURL] ) )
		{
			$publishFileName = $publishFileName;
		}
		$publishFileName = preg_replace( "/\\.([A-Za-z0-9]+)\$/isU", $sign.".\\1", $publishFileName );
		$realURL = $this->getHtmlURL( $publishFileName );
		$template->assign( "URL", $realURL );
		if ( $pagenum == 0 && ( $this->publishInfo[Type] == 1 || $this->publishInfo[Type] == 0 || $this->publishInfo[Type] == 3 ) )
		{
			$FieldsInfo = content_table_admin::gettablefieldsinfo( $NodeInfo[TableID] );
			$this->flushData( );
			foreach ( $FieldsInfo as $key => $var )
			{
				if ( empty( $var['EnablePublish'] ) )
				{
					continue;
				}
				$this->addData( $var[FieldName], $publishInfo[$var[FieldName]] );
			}
			$this->addData( "IndexID", $publishInfo[IndexID] );
			$this->addData( "ContentID", $publishInfo[ContentID] );
			$this->addData( "NodeID", $publishInfo[NodeID] );
			$this->addData( "PublishDate", $publishInfo[PublishDate] );
			$this->addData( "URL", $realURL );
			$publishInfo['URL'] = $realURL;
			$this->publishUpdate( $NodeInfo['TableID'] );
			if ( !isset( $Plugin ) )
			{
				require_once( INCLUDE_PATH."admin/plugin.class.php" );
				$Plugin = new Plugin( );
			}
			$Plugin->update( $publishInfo );
		}
		$output = $template->fetch( $tplname, 0 );
		$output = restorexmlheader( $output );
		if ( $this->_publishing( $publishFileName, $output ) )
		{
			$right = true;
		}
		else
		{
			$right = false;
		}
		//	if ( $pagenum-- )
		//	{
		//	}
	} while ( $pagenum-- );
} else {
	$template->assign( $mainContentLabel, $publishInfo[$mainContentLabel] );
	if ( $publishInfo[SelfPublishFileName] != "" )
	{
		$publishFileName = $publishInfo[SelfPublishFileName];
	}
	else
	{
		eval( "\$publishFileName = \"{$PublishFileFormat}\";" );
	}
	if ( !empty( $this->NodeInfo[SubDir] ) && empty( $publishInfo[SelfPSNURL] ) )
	{
		if ( $this->NodeInfo[SubDir] == "auto" )
		{
			$publishFileName = $this->makeIndexSavePath( $IndexID )."/".$publishFileName;
		}
		else
		{
			$publishFileName = date( $this->NodeInfo[SubDir], $this->publishInfo[CreationDate] )."/".$publishFileName;
		}
	}
	else if ( !empty( $publishInfo[SelfPSNURL] ) )
	{
		$publishFileName = $publishFileName;
	}
	$realURL = $this->getHtmlURL( $publishFileName );
	$template->assign( "URL", $realURL );
	if ( $this->publishInfo[Type] == 1 || $this->publishInfo[Type] == 0 || $this->publishInfo[Type] == 3 )
	{
		$FieldsInfo = content_table_admin::gettablefieldsinfo( $NodeInfo[TableID] );
		$this->flushData( );
		foreach ( $FieldsInfo as $key => $var )
		{
			if ( empty( $var['EnablePublish'] ) )
			{
				continue;
			}
			$this->addData( $var[FieldName], $publishInfo[$var[FieldName]] );
		}
		$this->addData( "IndexID", $publishInfo[IndexID] );
		$this->addData( "ContentID", $publishInfo[ContentID] );
		$this->addData( "NodeID", $publishInfo[NodeID] );
		$this->addData( "PublishDate", $publishInfo[PublishDate] );
		$this->addData( "URL", $realURL );
		$publishInfo['URL'] = $realURL;
		$this->publishUpdate( $NodeInfo['TableID'] );
		if ( !isset( $Plugin ) )
		{
			require_once( INCLUDE_PATH."admin/plugin.class.php" );
			$Plugin = new Plugin( );
		}
		$Plugin->update( $publishInfo );
	}
	$output = $template->fetch( $tplname, 0 );
	$output = restorexmlheader( $output );
	if ( $this->_publishing( $publishFileName, $output ) )
	{
		$right = true;
	}
	else
	{
		$right = false;
	}
}
?>
