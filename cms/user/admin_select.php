<?php
require_once 'common.php';
//require_once INCLUDE_PATH."admin/psn_admin.class.php";
$psn = new psn_admin();
switch($IN[o]) {

	case 'psn_picker':
		if(!empty($IN[psn])) {
			
			$patt = "/{PSN-URL:([0-9]+)}([\S]+)/is";
			preg_match ($patt, $IN[psn] ,$matches);
			//debug($matches);
			$psnInfo = $psn->getPSNInfo( $matches[1]);

			$TPL->assign('PSNID', $matches[1]); //have default psn
			
			$path = pathinfo($matches[2]);
			if($path[dirname] == "\\") $path[dirname]='';

			$TPL->assign('psn_path', $path[dirname]); //have default psn
			$TPL->assign('default_name', $path[basename]); //have default psn

		}
		//echo $matches[2];
		$TPL->assign('psnInfo', $psnInfo);
		$TPL->assign('psnList', $psn->getAllPSN());
		$TPL->display('../admin/select_psn_picker.html');
		break;
	case 'psn':
		if(!empty($IN[psn])) {
			
			$patt = "/{PSN:([0-9]+)}([\S]+)/is";
			preg_match ($patt, $IN[psn] ,$matches);
			//debug($matches);

			$TPL->assign('PSNID', $matches[1]); //have default psn
			
			$path = pathinfo($matches[2]);
			if($path[dirname] == "\\") $path[dirname]='';

			$TPL->assign('psn_path', $path[dirname]); //have default psn
			$TPL->assign('default_name', $path[basename]); //have default psn

		}

		$psnList = $psn->getAllPSNByPermission();
		$TPL->assign_by_ref('psnList', $psnList);
		$TPL->display('../admin/select_psn.html');
		break;
	case 'psn_list_file':

		$psnInfo = $psn->getPSNInfo($IN[PSNID]);
		//debug($psnInfo);

		$psn->connect($psnInfo[PSN]);
		//debug($psn->listFile());
		if($IN[extra] == 'updir') {
			
			$path = pathinfo($IN[PATH]);
			if($path[dirname] == "\\") $path[dirname]='';
			$fileList = $psn->listFile($path[dirname]);
			$IN[PATH] = $path[dirname];
			//debug($path);
		
		}else {
			$fileList = $psn->listFile($IN[PATH]);
		
		}
		$length = 0;
		foreach($fileList as $key=>$var) {
			$filelength = strlen($var[name]);
			if($filelength > $length ) $length  = $filelength;

		}
		$psn->close();
		$TPL->assign('default_name', $IN[default_name]);
		$TPL->assign('PSNID', $IN[PSNID]);
		$TPL->assign('PATH', $IN[PATH]);
		$TPL->assign('width', $length*7 + 20);
		$TPL->assign('fileList', $fileList);
		$TPL->display('../admin/select_psn_fileList.html');
		break;
	case 'psn_picker_list_file':

		$psnInfo = $psn->getPSNInfo($IN[PSNID]);
		//debug($psnInfo);

		$psn->connect($psnInfo[PSN]);
		//debug($psn->listFile());
		if($IN[extra] == 'updir') {
			
			$path = pathinfo($IN[PATH]);
			if($path[dirname] == "\\") $path[dirname]='';
			$fileList = $psn->listFile($path[dirname]);
			$IN[PATH] = $path[dirname];
			//debug($path);
		
		}else {
			$fileList = $psn->listFile($IN[PATH]);
		
		}
		$length = 0;
		foreach($fileList as $key=>$var) {
			$filelength = strlen($var[name]);
			if($filelength > $length ) $length  = $filelength;

		}
		$psn->close();
		$TPL->assign('default_name', $IN[default_name]);
		$TPL->assign('PSN-URL', $psnInfo[URL]);
		$TPL->assign('PSNID', $IN[PSNID]);
		$TPL->assign('PATH', $IN[PATH]);
		$TPL->assign('width', $length*7 + 20);
		$TPL->assign('fileList', $fileList);
		$TPL->display('../admin/select_psn_picker_fileList.html');
		break;
	case 'targetCateWindow':
		$TPL->display('select_cate.html');
		break;
	//case 'targetNodeList':
	//	header;
	//	break;
}

?>