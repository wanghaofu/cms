<?php
require_once 'common.php';


require_once INCLUDE_PATH."admin/publishAdmin.class.php";
require_once INCLUDE_PATH."admin/content_table_admin.class.php";
//require_once INCLUDE_PATH."admin/tplAdmin.class.php";
require_once INCLUDE_PATH."admin/site_admin.class.php";
require_once INCLUDE_PATH."cms.class.php";
require_once INCLUDE_PATH."cms.func.php";
require_once INCLUDE_PATH.'encoding/encoding.inc.php';
require_once INCLUDE_PATH."admin/psn_admin.class.php";
require_once INCLUDE_PATH."admin/plugin.class.php";
require_once INCLUDE_PATH."admin/publishAuthAdmin.class.php";
require_once INCLUDE_PATH."admin/task.class.php";
require_once INCLUDE_PATH.'image.class.php';


require_once INCLUDE_PATH."user/contribution_admin.class.php";
require_once INCLUDE_PATH."user/cate_admin.class.php";
$Plugin = new Plugin();

$contribution = new contributionUser();
//$db->setDebug(1);

if(!empty($IN[CateID])) {
	$CateInfo = cate_admin::getCateInfo($IN[CateID]);

} elseif(!empty($IN[NodeID])) {
	require_once INCLUDE_PATH."admin/site_admin.class.php";
	$NodeInfo = $iWPC->loadNodeInfo($IN[NodeID]);
	$CateInfo[TableID] = $NodeInfo[TableID];
} else {
	goback('error_CateID_null');

}


	switch($IN[o]) {
		case 'list':
			$TPL->assign('CateID', $IN[CateID]);
			$TPL->display('content_admin_frameset.html');
			
			break;
		case 'node_list':
			$TPL->assign('NodeID', $IN[NodeID]);
			$TPL->display('node_content_admin_frameset.html');
			
			break;
		case 'content_header'://内容管理 头部功能导航
			$TPL->assign('CateID', $IN[CateID]);
			$TPL->assign("CateInfo", $CateInfo);
			$TPL->display('content_admin_header.html');
			$diableDebug = true;
			break;
		case 'node_content_header'://节点视图
			$TPL->assign('NodeID', $IN[NodeID]);
			$TPL->assign("NodeInfo", $NodeInfo);
			$TPL->display('node_content_admin_header.html');
			$diableDebug = true;
			break;
		case 'content_list': //内容管理 文章列表
			$offset = 20;
			$num= $contribution->getContributionRecordNum($CateInfo);
			
			$pagenum=ceil($num/$offset);
			if(empty($IN[Page]))
				$Page = 1;
			else
				$Page = $IN[Page];

			$start=($Page-1)*$offset;
			
			 
			$TPL->assign('DisplayItem', content_table_admin::getDisplayFieldsInfo($CateInfo[TableID]));
			$TPL->assign('catelist', $CATE_LIST);
			$TPL->assign("pList", $contribution->getContributionLimit($CateInfo, $start, $offset));
			$TPL->assign("CateInfo", $CateInfo);
			

			$TPL->assign("pagelist",pagelist($pagenum,$Page,"{$base_url}o=content_list&type=main&CateID={$IN[CateID]}",'#000000'));
			$TPL->display('content_admin_list.html');
			
			break;
		case 'node_content_list': //内容管理 文章列表
			$offset = 20;
			$num= $contribution->getContributionRecordNumByNode($NodeInfo);
			
			$pagenum=ceil($num/$offset);
			if(empty($IN[Page]))
				$Page = 1;
			else
				$Page = $IN[Page];

			$start=($Page-1)*$offset;
			
			 
			$TPL->assign('DisplayItem', content_table_admin::getDisplayFieldsInfo($NodeInfo[TableID]));
			$TPL->assign('catelist', $CATE_LIST);
			$TPL->assign("pList", $contribution->getContributionLimitByNode($NodeInfo, $start, $offset));
			$TPL->assign("NodeInfo", $NodeInfo);
			

			$TPL->assign("pagelist",pagelist($pagenum,$Page,"{$base_url}o=node_content_list&type=main&NodeID={$IN[NodeID]}",'#000000'));
			$TPL->display('node_content_admin_list.html');
			
			break;
		case 'content_editor_frameset':
			$TPL->assign('CateID', $IN[CateID]);
			$TPL->assign('NodeID', $IN[NodeID]);
			$TPL->assign('ContributionID', $IN[ContributionID]);
			$TPL->assign('o', $IN[extra]);
			$TPL->display('content_editor_frameset.html');

			break;
		case 'content_editor_header':
			$TPL->assign('NodeID', $IN[NodeID]);
			$TPL->assign('CateID', $IN[CateID]);
			$TPL->assign("NodeInfo", $NodeInfo);
			$TPL->assign("CateInfo", $CateInfo);
			$TPL->display('content_editor_header.html');
			$diableDebug = true;
			break;
		
		case 'add':	//生成内容添加的UI,已经转至admin_editor.php处理
			$TableID = 	$CateInfo[TableID];
			$CateInfo[SubNodeIDs] = explode(',', $CateInfo[SubNodeID]);
			$CateInfo[IndexNodeIDs] = explode(',', $CateInfo[IndexNodeID]);

			$tableInfo = content_table_admin::getTableFieldsInfo($TableID);

 			$diableDebug = true;
			require_once INCLUDE_PATH."admin/TplVarsAdmin.class.php";
			$PUBLISH_URL = TplVarsAdmin::getValue('PUBLISH_URL');


			include MODULES_DIR.'editor.php' ;

			break;
		case 'add_submit':
			include_once SETTING_DIR ."cms.ini.php";

			//debug($IN);
			$fieldInfo = content_table_admin::getTableFieldsInfo($CateInfo[TableID]);			

			$contribution->flushData();
			if($CateInfo[TableID] == 1) {
				if($contribution->recordExists($CateInfo,'Title', $IN['data_Title']))
					goback('contribution_title_exits');
			
			}

			foreach($fieldInfo as $key=>$var) {
					$field = 'data_'.$var[FieldName];
					if(is_array($IN[$field])) {
						foreach($IN[$field] as $keyIn=>$varIn) {
							if($keyIn == 0)
								$value = $varIn;
							else
								$value .= ';'.$varIn;


						}
					} elseif($var[FieldInput] == 'RichEditor') {
						$field = 'data_'.$var[FieldName].'_html';
						$value = RichEditor_Filter($IN[$field]);
							//debug($IN);//data_Intro_ImgAutoLocalize
						if($IN['data_'.$var[FieldName].'_ImgAutoLocalize'] == '1') {
							//echo $result;
							$ImgAutoLocalize = new ImgAutoLocalize($IN[CateID]);
							$result = $ImgAutoLocalize->execute($value);
							if($result)
								$value = $result;

							
						} 
						//echo $value;exit;
					} else
						$value = $IN[$field];
					
					
					//debug($IN);
					LocalImgPathA2R::A2R($value);

					$contribution->addData($var[FieldName], $value);
				
			}
			$time = time();
			if($IN[isContribution] == '1') {
				$contribution->addData('ContributionDate', time());
				$contribution->addData('State', 1);
			} else {
				$contribution->addData('State', 0);

			}

			$contribution->addData('NodeID', $IN[TargetNodeID]);
			if(!empty($IN[SubTargetNodeID])) {
				foreach($IN[SubTargetNodeID] as $key=>$var) {
					if($key == 0)
						$subTargetNodeID = $var;
					else
						$subTargetNodeID .= ','.$var;

				}			
			}

			$contribution->addData('SubNodeID', $subTargetNodeID);
			$subTargetNodeID ='';
			if(!empty($IN[IndexTargetNodeID])) {
				foreach($IN[IndexTargetNodeID] as $key=>$var) {
					if($key == 0)
						$subTargetNodeID = $var;
					else
						$subTargetNodeID .= ','.$var;

				}			
			}

			$contribution->addData('IndexNodeID', $subTargetNodeID);

			$contribution->addData('CateID', $CateInfo[CateID]);
			$contribution->addData('CreationDate', $time);
			$contribution->addData('ModifiedDate', $time);
			$contribution->addData('OwnerID', $sys->session[sUId]);
			if($contribution->add($CateInfo)) {
				if($IN[isContribution] == '1') {
					userAdmin::Counter($sys->uId, 'ContributionNum');
				} else {
					userAdmin::Counter($sys->uId, 'NoContributionNum');

				}				
				echo "<script>\n
					parent.window.opener.refreshWorkArea();				
					</script>\n";
				showmessage('contribution_add_ok',$referer);	
				exit;				
				
			}else
				goback('contribution_add_fail');
			break;
		case 'edit':
			if(empty($IN[ContributionID])) _goto('contribution_list');
			$pInfo = $contribution->getContributionInfo($CateInfo, $IN[ContributionID]);
			$pInfo[SubNodeIDs] = explode(',', $pInfo[SubNodeID]);
			$pInfo[IndexNodeIDs] = explode(',',  $pInfo[IndexNodeID]);

			$TableID = 	$CateInfo[TableID];

			$tableInfo = content_table_admin::getTableFieldsInfo($TableID);

 			$diableDebug = true;
			require_once INCLUDE_PATH."admin/TplVarsAdmin.class.php";
			$PUBLISH_URL = TplVarsAdmin::getValue('PUBLISH_URL');

			include MODULES_DIR.'editor.php' ;

			break;
		case 'edit_submit':
			include_once SETTING_DIR ."cms.ini.php";

			if(empty($IN[ContributionID])) _goto('contribution_list');

			$fieldInfo = content_table_admin::getTableFieldsInfo($CateInfo[TableID]);			

			$contribution->flushData();

			foreach($fieldInfo as $key=>$var) {
					$field = 'data_'.$var[FieldName];
					if(is_array($IN[$field])) {
						foreach($IN[$field] as $keyIn=>$varIn) {
							if($keyIn == 0)
								$value = $varIn;
							else
								$value .= ';'.$varIn;


						}
					} elseif($var[FieldInput] == 'RichEditor') {
						$field = 'data_'.$var[FieldName].'_html';
						$value = RichEditor_Filter($IN[$field]);
						//debug($IN);
						if($IN['data_'.$var[FieldName].'_ImgAutoLocalize'] == '1') {
							//echo $result;
							$ImgAutoLocalize = new ImgAutoLocalize($IN[CateID]);
							$result = $ImgAutoLocalize->execute($value);

							if($result)
								$value = $result;
						} 
					} else
						$value = $IN[$field];
					LocalImgPathA2R::A2R($value);

					$contribution->addData($var[FieldName], $value);
				
			}
			//CreationDate  ModifiedDate  CreationUserID  LastModifiedUserID  ApprovedByUserID 
			$contribution->addData('ModifiedDate', time());

			$contribution->addData('NodeID', $IN[TargetNodeID]);
			if(!empty($IN[SubTargetNodeID])) {
				foreach($IN[SubTargetNodeID] as $key=>$var) {
					if($key == 0)
						$subTargetNodeID = $var;
					else
						$subTargetNodeID .= ','.$var;

				}			
			}

			$contribution->addData('SubNodeID', $subTargetNodeID);

			if(!empty($IN[IndexTargetNodeID])) {
				foreach($IN[IndexTargetNodeID] as $key=>$var) {
					if($key == 0)
						$subTargetNodeID = $var;
					else
						$subTargetNodeID .= ','.$var;

				}			
			}

			$contribution->addData('IndexNodeID', $subTargetNodeID);

			if($contribution->contributionEdit($CateInfo, $IN[ContributionID])) {
				echo "<script>\n
					parent.window.opener.refreshWorkArea();				
					</script>\n";
				showmessage('contribution_edit_ok',$referer);	
				exit;				
		


			}else
				goback('contribution_edit_fail');
			break;

		case 'del':
			if(!empty($IN[multi]) && !empty($IN[pData]) ) {
				foreach($IN[pData] as $var) {
					$result = $contribution->del($CateInfo, $var);				
					userAdmin::Counter($sys->uId, 'NoContributionNum', '-');
				}

				if($result)
					showmessage('contribution_del_ok', $referer);
				else
					showmessage('contribution_del_fail', $referer);

			} else {
			
				if($contribution->del($CateInfo, $IN[ContributionID])) {
					userAdmin::Counter($sys->uId, 'NoContributionNum', '-');
					showmessage('contribution_del_ok', $referer);
				
				}else
					showmessage('contribution_del_fail', $referer);
			
			}

			break;
		case 'view':
			if(empty($IN[ContributionID])) _goto('contribution_list');
			$pInfo = $contribution->getContributionInfo($CateInfo,$IN[ContributionID]);

			$TableID = 	$CateInfo[TableID];

			$tableInfo = content_table_admin::getTableFieldsInfo($TableID);
			include MODULES_DIR.'contribution_admin_view.php' ;
			break;

		case 'contribution':
			if(!empty($IN[multi]) && !empty($IN[pData]) ) {
				foreach($IN[pData] as $var) {

					if($contribution->canContribution($CateInfo, $var)) {
						$State =  $contribution->getContributionInfo($CateInfo,$var, 'State');
						if($State == 3) {
							userAdmin::Counter($sys->uId, 'CallBackNum', '-');
					
						} else
							userAdmin::Counter($sys->uId, 'NoContributionNum', '-');

						$result = $contribution->contribution($CateInfo, $var);
						userAdmin::Counter($sys->uId, 'ContributionNum');
				
					}


				}

				if($result)
					showmessage('contribution_contribution_ok', $referer);
				else
					showmessage('contribution_contribution_fail', $referer);

			} elseif(!empty($IN[ContributionID])) {
				if($contribution->canContribution($CateInfo, $IN[ContributionID])) {
					$State =  $contribution->getContributionInfo($CateInfo,$IN[ContributionID], 'State');
					if($State == 3) {
							userAdmin::Counter($sys->uId, 'CallBackNum', '-');
						
					} else
							userAdmin::Counter($sys->uId, 'NoContributionNum', '-');
				
					if($contribution->contribution($CateInfo, $IN[ContributionID])) {
						userAdmin::Counter($sys->uId, 'ContributionNum');
						//userAdmin::Counter($sys->uId, 'NoContributionNum', '-');
						showmessage('contribution_contribution_ok', $referer);
					
					} else
						showmessage('contribution_contribution_fail', $referer);
				
				}
			
			} else
				showmessage('contribution_contribution_fail_not_select', $referer);

			break;

		case 'uncontribution':

			if(!empty($IN[multi]) && !empty($IN[pData]) ) {
				foreach($IN[pData] as $var) {
					if($contribution->canUnContribution($CateInfo, $var)) {
						$result = $contribution->unContribution($CateInfo, $var);				
						userAdmin::Counter($sys->uId, 'ContributionNum', '-');
						userAdmin::Counter($sys->uId, 'NoContributionNum');
					
					}
				}
				if($result)
					showmessage('contribution_uncontribution_ok', $referer);
				else
					showmessage('contribution_uncontribution_fail', $referer);

			} elseif(!empty($IN[ContributionID])) {
				//echo 'a';exit;
				if($contribution->canUnContribution($CateInfo, $IN[ContributionID])) {
					if($contribution->unContribution($CateInfo, $IN[ContributionID])) {
						userAdmin::Counter($sys->uId, 'ContributionNum', '-');
						userAdmin::Counter($sys->uId, 'NoContributionNum');
						showmessage('contribution_uncontribution_ok', $referer);
					
					} else
						showmessage('contribution_uncontribution_fail', $referer);
				
				}

			}
			
			


			break;
		case 'viewNote':
			$TPL->assign("NoteList", $contribution->getNoteList($CateInfo, $IN[ContributionID]));
			$TPL->display('note_list.html');
			break;
		case 'cut':
			if(empty($IN[targetCateID]))
				showmessage('targetCateID_null', $referer);
			
			$srcCateInfo[TableID] = $CateInfo[TableID];
			$desCateInfo = cate_admin::getCateInfo($IN[targetCateID]);

			if($srcCateInfo['TableID'] != $desCateInfo['TableID'])
				showmessage('contribution_fail_TableID_unmatch', $referer);
			
			
			
			if(!empty($IN[multi]) && !empty($IN[pData]) ) {
				foreach($IN[pData] as $var) {
					$result = $contribution->move($var, $IN[targetCateID],$CateInfo);				
				}
				if($result)
					showmessage('contribution_move_ok', $referer);
				else
					showmessage('contribution_move_fail', $referer);

			} elseif(!empty($IN[ContributionID])) {
				if($contribution->move($IN[ContributionID], $IN[targetCateID],$CateInfo))
					showmessage('contribution_move_ok', $referer);
				else
					showmessage('contribution_move_fail', $referer);

			}


			break;
		case 'copy':
			if(empty($IN[targetCateID]))
				showmessage('targetCateID_null', $referer);

			$srcCateInfo = cate_admin::getCateInfo($IN[CateID]);
			$desCateInfo = cate_admin::getCateInfo($IN[targetCateID]);

			if($srcCateInfo['TableID'] != $desCateInfo['TableID'])
				showmessage('contribution_fail_TableID_unmatch', $referer);
			
			
			if(!empty($IN[multi]) && !empty($IN[pData]) ) {
				foreach($IN[pData] as $var) {
					$result = $contribution->copyTo($var, $IN[targetCateID]);				
				}
				if($result)
					showmessage('contribution_copy_ok', $referer);
				else
					showmessage('contribution_copy_fail', $referer);

			} elseif(!empty($IN[ContributionID])) {
				if($contribution->copyTo($IN[ContributionID], $IN[targetCateID]))
					showmessage('contribution_copy_ok', $referer);
				else
					showmessage('contribution_copy_fail', $referer);

			} elseif(empty($IN[ContributionID]))
				showmessage('ContributionID_null', $referer);




			break;

	}

 
include MODULES_DIR.'footer.php' ;
?>
