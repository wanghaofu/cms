<?php
require_once (INCLUDE_PATH . "/admin/collection_cate_admin.class.php");
class CMS_CollectionCate extends collection_cate_admin {
	function parseRuleXML($content) {
		$rulePattern ['TargetURL'] = "/<TargetURL>(.*)<\\/TargetURL>/isU";
		$rulePattern ['TargetURLArea'] = "/<TargetURLArea>(.*)<\\/TargetURLArea>/isU";
		$rulePattern ['UrlFilterRule'] = "/<UrlFilterRule>(.*)<\\/UrlFilterRule>/isU";
		$rulePattern ['UrlPageRule'] = "/<UrlPageRule>(.*)<\\/UrlPageRule>/isU";
		$rulePattern_ContentModel = "/<ContentModel>(.*)<\\/ContentModel>/isU";
		foreach ( $rulePattern as $key => $var ) {
			if (preg_match ( $var, $content, $match )) {
				$CateRules [$key] = html_entity_decode ( $match [1] );
			}
		}
		if (preg_match ( $rulePattern_ContentModel, $content, $match )) {
			$pattern = "/<(.*)>(.*)<\\/\\1>/isU";
			if (preg_match_all ( $pattern, $match [1], $matches )) {
				foreach ( $matches [1] as $key => $var ) {
					$ContentModelRules [$var] = html_entity_decode ( $matches [2] [$key] );
				}
			}
		}
		$rule ['CateRules'] = $CateRules;
		$rule ['ContentModelRules'] = $ContentModelRules;
		return $rule;
	}
}

?>
