<?php
class CMS_Plugin {
	var $plugin = NULL;
	function CMS_Plugin() {
		require_once (INCLUDE_PATH . "admin/plugin.class.php");
		$this->plugin = new Plugin ();
	}
	function &getInstance() {
		return $this->plugin;
	}
}

?>
