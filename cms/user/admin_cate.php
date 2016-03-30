<?php
require_once 'common.php';



require_once INCLUDE_PATH."user/cate_admin.class.php";
require_once INCLUDE_PATH."admin/content_table_admin.class.php";

$cate = new cate_admin();

switch($IN['o']) {
	case 'add':
		if(empty($IN[ParentID])) {
			$TPL->assign('ParentID', 0);
		} else {
			$TPL->assign('ParentID', $IN[ParentID]);
		}
		if(empty($IN[TableID])) {
			$TPL->assign('TableID', 1);
		} else {
			$TPL->assign('TableID', $IN[TableID]);
		}
		
		$TPL->assign('NODE_LIST', $NODE_LIST);
		$TPL->assign('tableInfo', content_table_admin::getAllTable());
		$TPL->display("cate_add.html");
		break;

	case 'add_submit':
		$cate->flushData();
		foreach($IN as $key=>$var) {
			$prefix = substr($key, 0, 5);
			$suffix = substr($key, 5);
			if($prefix == 'data_')
				$cate->addData($suffix,$var);
			else
				continue;
		}
		//$cate->debugData();
		$cate->addData('OwnerID', $sys->session[sUId]);
		$cate->addData('NodeID', $IN[TargetNodeID]);
		
		if(!empty($IN[SubTargetNodeID])) {
			foreach($IN[SubTargetNodeID] as $key=>$var) {
				if($key == 0)
					$subTargetNodeID = $var;
				else
					$subTargetNodeID .= ','.$var;

			}
			$cate->addData('SubNodeID', $subTargetNodeID);
		}

		if(!empty($IN[IndexTargetNodeID])) {
			foreach($IN[IndexTargetNodeID] as $key=>$var) {
				if($key == 0)
					$subTargetNodeID = $var;
				else
					$subTargetNodeID .= ','.$var;

			}		
			$cate->addData('IndexNodeID', $subTargetNodeID);
		}


		if($cate->add()) { //插入分类数据
			showmessage('add_cate_ok', $referer);
		} else	
			showmessage( 'add_cate_fail', $referer);


		
				

		break;

	case 'edit':
		if(!empty($IN[CateID])) {
			$TPL->assign('NODE_LIST', $NODE_LIST);
			$TPL->assign('tableInfo', content_table_admin::getAllTable());
			$CateInfo = $cate->getCateInfo($IN[CateID]);
			$CateInfo[SubNodeIDs] = explode(',', $CateInfo[SubNodeID]);
			$CateInfo[IndexNodeIDs] = explode(',', $CateInfo[IndexNodeID]);
			$TPL->assign('CateInfo', $CateInfo);
			$TPL->display("cate_edit.html");
	
		} else
			_goto('view');
		break;

	case 'edit_submit':
		if(empty($IN[CateID])) _goto('view');

		$cate->flushData();
		foreach($IN as $key=>$var) {
			$prefix = substr($key, 0, 5);
			$suffix = substr($key, 5);
			if($prefix == 'data_')
				$cate->addData($suffix,$var);
			else
				continue;
		}
		//$cate->debugData();
	
		$cate->addData('NodeID', $IN[TargetNodeID]);

		if(!empty($IN[SubTargetNodeID])) {
			foreach($IN[SubTargetNodeID] as $key=>$var) {
				if($key == 0)
					$subTargetNodeID = $var;
				else
					$subTargetNodeID .= ','.$var;

			}
			$cate->addData('SubNodeID', $subTargetNodeID);
		}

		if(!empty($IN[IndexTargetNodeID])) {
			foreach($IN[IndexTargetNodeID] as $key=>$var) {
				if($key == 0)
					$subTargetNodeID = $var;
				else
					$subTargetNodeID .= ','.$var;

			}		
			$cate->addData('IndexNodeID', $subTargetNodeID);
		}

		if($cate->update($IN[CateID])) { //更新节点数据
			showmessage('edit_cate_ok', $referer);
		} else	
			showmessage('edit_cate_fail', $referer);
		
		break;

	case 'del':
		if(empty($IN[CateID])) _goto('view');

		if($cate->haveSon($IN[CateID]) && $IN[action] != 'force') {
				confirm("del&CateID={$IN[CateID]}&action=force", $_LANG_ADMIN['del_cate_haveson']);
			
		} elseif($cate->haveSon($IN[CateID]) && $IN[action] == 'force') {

			if($cate->del($IN[CateID])) {
				alert('del_cate_haveson_ok','panelMenu');


			} else
				alert('del_cate_haveson_fail','panelMenu');

		} else {

			if($cate->del($IN[CateID])) {
				$cache = new CacheData();
				$cache->makeCache('catelist');
				alert('del_cate_ok','panelMenu');
			
			} else
				alert('del_cate_fail','panelMenu');

	
		}
		break;

	case 'move':
		if(empty($IN[CateID])) _goto('view');
		
		$cate->flushData();
		$cate->addData('ParentID', $IN[targetCateID]);

	


		if($IN[CateID] == $IN[targetCateID]) {
			alert('move_cate_id_conflict','panelMenu');
	
		} elseif($cate->update($IN[CateID])) { //更新分类数据
			alert('move_cate_ok','panelMenu');
		} else
			alert('move_cate_fail','panelMenu');

}

	
include MODULES_DIR.'footer.php' ;

?>
