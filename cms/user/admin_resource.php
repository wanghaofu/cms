<?php
require_once 'common.php';

require_once INCLUDE_PATH."admin/resource.class.php";

$resource = new Resource();

switch($IN[o]) {
	case 'list_ui_main':
		$TPL->assign('Category', $IN['Category']);
		$TPL->assign_by_ref('NODE_LIST', $NODE_LIST);
		$TPL->display("../admin/resource_list_ui_main.html");
		break;
	case 'list_ui_iframe':
		break;
	case 'list':
		$Category = empty($IN['Category']) ? 'img' : $IN['Category'];
		$NodeID = intval($IN['NodeID']);

		
		$offset = empty( $IN['offset']) ?  28 : $IN['offset'];
		$num= $resource->getResourceNumByNodeID($NodeID, $Category);

		$pagenum = ceil($num/$offset);
		//$Page = empty($IN[Page]) ? 1 : intval($IN[Page]);
		if(!empty($IN['next']))  $_SESSION[ResourceListPage]++; 
		if(!empty($IN['back']))  $_SESSION[ResourceListPage]--; 
		
		$_SESSION[ResourceListPage] = empty($_SESSION[ResourceListPage]) ? 1 : $_SESSION[ResourceListPage] ;
		$_SESSION[ResourceListPage] = $_SESSION[ResourceListPage] > $pagenum ? $pagenum : $_SESSION[ResourceListPage] ;
		$Page = empty($IN[Page]) ? $_SESSION[ResourceListPage] : intval($IN[Page]);
		$Page = intval($Page);

		$start=($Page-1)*$offset;
			
		$recordInfo[currentPage] = $Page;
		$recordInfo[pageNum] = $pagenum;
		$recordInfo[recordNum] = $num;
		$recordInfo[offset] = $offset;
		$recordInfo[from] = $start;
		$recordInfo[to] = $start+$offset;
		
		$List = $resource->getResourceListByNodeIDLimit($NodeID, $Category, $start, $offset);
 		$TPL->assign_by_ref('List', $List);
		$TPL->assign_by_ref('NODE_LIST', $NODE_LIST); 
 		if($Category == 'img') $TPL->display('../admin/resource_list.html');
		elseif($Category == 'attach')$TPL->display('../admin/resource_attach_list.html');
		elseif($Category == 'flash')$TPL->display('../admin/resource_flash_list.html');
		
 		break;
 
	default:
		break;

}
?>