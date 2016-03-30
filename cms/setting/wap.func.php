<?php
function wapAutoPage2($content, $page_length = 900, $overflow = 100) {
	$page_length_max = $page_length + $overflow;
	$page_length_min = $page_length - $overflow;
	$length = strlen ( $content );
	$matches = explode ( "<br/>", $content );
	foreach ( $matches as $key => $var ) {
		$i = strpos ( $content, $var );
		if ($key == 0) {
			$temp = substr ( $content, 0, $i );
			$stack [] = $temp;
			$j = $i;
		} else {
			$temp = substr ( $content, $j, $i - $j );
			$stack [] = $temp;
			$j = $i;
		}
	}
	if ($j < $length) {
		$temp = substr ( $content, $j );
		$stack [] = $temp;
	}
	$temp = "";
	foreach ( $stack as $key => $var ) {
		if (strlen ( $var ) + strlen ( $temp ) < $page_length_max && $page_length_min < strlen ( $var ) + strlen ( $temp )) {
			$temp .= "<p>" . $var . "</p>\n";
			$wap_para [] = $temp;
			$temp = "";
		} else if (strlen ( $var ) + strlen ( $temp ) < $page_length_min) {
			$temp .= "<p>" . $var . "</p>\n";
		}
	}
	if ($temp != "") {
		$wap_para [] = $temp;
	}
	return $wap_para;
}
function wap_page($pagenum, $currentpage, $sendVar) {
	-- $pagenum;
	-- $currentpage;
	if ($pagenum == "" || $pagenum == - 1) {
		return false;
	}
	if (0 < $currentpage) {
		if ($currentpage - 1 == 0) {
			$link1 = $sendVar;
		} else {
			$link1 = str_replace ( ".", "_" . ($currentpage - 1) . ".", $sendVar );
		}
		$page = "<a href='" . $link1 . "' >上一页</a><br/>";
	}
	if ($currentpage < $pagenum) {
		$link1 = str_replace ( ".", "_" . ($currentpage + 1) . ".", $sendVar );
		$page = "<a href='" . $link1 . "' >下一页</a><br/>" . $page;
	}
	return $page;
}

?>
