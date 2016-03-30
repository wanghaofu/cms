<?php
class CMS_DataSource {
	var $db = NULL;
	function CMS_DataSource() {
		global $db_config;
		require_once (KDB_DIR . "kDB.php");
		$this->db = new kDB ( $db_config ['db_driver'] );
		$this->db->connect ( $db_config );
		$this->db->setDebug ( $db_config ['debug'] );
		$this->db->setFetchMode ( "assoc" );
		$this->db->setCacheDir ( SYS_PATH . "sysdata/cache/" );
	}
	function &getConnection() {
		return $this->db;
	}
}

?>
