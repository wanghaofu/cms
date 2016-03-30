<?php 
 
if(!defined('IN_IWPC')) {
 	exit('Access Denied');
}

switch($action) {
	case 'login':
		if($sys->login($IN[username],$IN[password])) {
			$TPL->assign('base_url', "index.php?sId={$sys->sId}&");
			$TPL->display("panel_frameset.html");
		} elseif(isset($IN[username]) || isset($IN[password])) {
			$TPL->assign('error_message',$_LANG_ADMIN['username_error']);
			$TPL->display("login.html");
		} else {
			$TPL->display("login.html");
		
		}

		break;
	case 'logout':
		if($sys->logout())
			$TPL->display("logout.html");

		break;
	case 'view':
		$TPL->assign('session', $sys->session);

		switch($IN[extra]) {
			case 'header':
				$TPL->display("panel_header.html");
				break;
			case 'box':
				$TPL->display("panel_box.html");
				break;
			case 'menu':
				header("Location:admin_tree.php?sId={$IN['sId']}&o=contribution");
				//$TPL->display("panel_admin_sys.html");
				break;
			case 'admin_sys':
				$TPL->display("panel_admin_sys.html");
				break;
			case 'workarea':
				include MODULES_DIR.'DM_right.php';
				break;
			case 'phpinfo':
				phpinfo();
				exit;
				break;

			default:
				$TPL->display("panel_frameset.html");

		}
		break;


	case 'chpassword':
		$TPL->display("chpassword.html");
		break;
	case 'chpassword_submit':
		if( $IN[password]!='') {
			if($IN[newpassword] != $IN[newpassword2]) {
				goback('sys_chpassword_password_not_match');
			} else {
				if($sys->chpassword($IN[password], $IN[newpassword])) {
					showmessage('sys_chpassword_ok', $referer);
					
				} else {
					showmessage('sys_chpassword_fail', $referer);
				
				}

			}
		} else {
			goback('sys_chpassword_password_null');
		
		}

		break;

	default:
		$TPL->display("login.html");

}

	

?>