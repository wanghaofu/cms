<?php
require_once 'common.php';
require_once INCLUDE_PATH."user/cate_admin.class.php";
require_once INCLUDE_PATH."admin/site_admin.class.php";


$cate = new cate_admin();
$node = new site_admin();
switch($IN[o]) {
	case 'contribution':
		$TPL->assign('CateInfo', $cate->getAll4Tree());
		$TPL->display('tree_contribution.html');
		break;
	case 'contribution_xml':
		if(!empty($IN[CateID])) {
			$TPL->assign('CateInfo', $cate->getAll4Tree($IN[CateID]));
			header("Content-Type: text/xml; charset=".CHARSET."\n");
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
		    header('Expires: ' . $now);
			$TPL->display('contribution_xml.xml');
		} else {
	
		}
		break;
	case 'node_contribution':
		$TPL->assign('NodeInfo', $node->getAll4Tree());
		$TPL->display('tree_node_contribution.html');
		break;
	case 'node_contribution_xml':
		if(!empty($IN[NodeID])) {
			$TPL->assign('NodeInfo', $node->getAll4Tree($IN[NodeID]));
			header("Content-Type: text/xml; charset=".CHARSET."\n");
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
		    header('Expires: ' . $now);
			$TPL->display('node_contribution_xml.xml');
		} else {
	
		}
		break;
	case 'cate':
		$TPL->assign('CateInfo', $cate->getAll4Tree());
		$TPL->display('tree_cate.html');
		break;
		break;
	case 'cate_xml':
		if(!empty($IN[CateID])) {
			$TPL->assign('CateInfo', $cate->getAll4Tree($IN[CateID]));
			header("Content-Type: text/xml; charset=".CHARSET."\n");
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
		    header('Expires: ' . $now);
			$TPL->display('cate_xml.xml');
		} else {
	
		}
		break;
	case 'cate_select':
		$TPL->assign('CateInfo', $cate->getAll4Tree());
		$TPL->display('tree_cate_select.html');
		break;
	case 'cate_select_xml':
		if(!empty($IN[CateID])) {
			$TPL->assign('CateInfo', $cate->getAll4Tree($IN[CateID]));
			header("Content-Type: text/xml; charset=".CHARSET."\n");
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
		    header('Expires: ' . $now);
			$TPL->display('cate_select_xml.xml');
		} else {
	
		}
		break;

}

?>