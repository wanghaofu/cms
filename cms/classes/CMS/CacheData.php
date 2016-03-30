<?php
class CMS_CacheData {
	var $cacheData = NULL;
	function CMS_CacheData() {
		require_once (INCLUDE_PATH . "admin/cache.class.php");
		$this->cacheData = new CacheData ();
	}
	function &getInstance() {
		return $this->cacheData;
	}
}

?>
