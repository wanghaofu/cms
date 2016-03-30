<?php
require_once (INCLUDE_PATH . "admin/content_table_admin.class.php");
class CMS_ContentModel extends content_table_admin {
	var $ContentModelReservedFieldName = array (
			0 => "IndexID",
			1 => "ContentID",
			2 => "NodeID",
			3 => "ParentIndexID",
			4 => "Type",
			5 => "PublishDate",
			6 => "Template",
			7 => "State",
			8 => "URL",
			9 => "Top",
			10 => "Pink",
			11 => "Sort",
			12 => "CreationDate",
			13 => "ModifiedDate",
			14 => "CreationUserID",
			15 => "LastModifiedUserID",
			16 => "ContributionUserID",
			17 => "ContributionID",
			18 => "ApprovedDate",
			19 => "TableID",
			20 => "ParentID",
			21 => "Name" 
	);
	function CMS_ContentModel() {
	}
	function import($_content) {
		$TableData = $this->parseContentTableXML ( $_content );
		return $this->_import ( $TableData );
	}
	function importFromFile($_file) {
		$TableData = $this->parseContentTableXMLFile ( $_file );
		return $this->_import ( $TableData );
	}
	function _import(&$TableData) {
		if (! empty ( $TableData )) {
			$this->flushData ();
			$this->addData ( "Name", $TableData ['Name'] );
			$TableInfo = $this->getTableInfo ( $TableData ['TableID'] );
			if (empty ( $TableInfo ['TableID'] )) {
				$this->addData ( "TableID", $TableData ['TableID'] );
			}
			if ($this->addTable ()) {
				$TableID = $this->db_insert_id;
				foreach ( $TableData ['Fields'] as $var ) {
					if (in_array ( $var [FieldName], $this->ContentModelReservedFieldName )) {
						{
							// header( "Location: http://www.cmsware.org/invalid/?expire=1203868800" );
							continue;
						}
					}
					$FieldsMulti [] = $var;
				}
				$this->addField ( $TableID, $FieldsMulti, true );
				if (file_exists ( CACHE_DIR . "Cache_ContentModel.php" )) {
					unlink ( CACHE_DIR . "Cache_ContentModel.php" );
				}
				$factory = & get_singleton ( "CMS.CacheData" );
				$cache = $factory->getInstance ();
				$cache->makeCache ( "content_model" );
				cleardir ( SYS_PATH . "sysdata/cache/", "index.html;.htaccess" );
				return true;
			}
		} else {
			return false;
		}
	}
	function parseContentTableXMLFile($_file) {
		$this->File = & get_singleton ( "FileSystem.File" );
		$xml_content = $this->File->read ( $_file );
		return $this->parseContentTableXML ( $xml_content );
	}
	function parseContentTableXML($content) {
		$rulePattern ['Name'] = "/<Name>(.*)<\\/Name>/isU";
		$rulePattern ['TableID'] = "/<TableID>(.*)<\\/TableID>/isU";
		$rulePattern_ContentModel = "/<Field>(.*)<\\/Field>/isU";
		if (strpos ( $content, "<version>1.1</version>" ) !== false) {
			$version = 11;
		} else {
			$version = 10;
		}
		foreach ( $rulePattern as $key => $var ) {
			if (preg_match ( $var, $content, $match )) {
				if ($version == 10) {
					$Rules [$key] = html_entity_decode ( $match [1] );
				} else if ($version == 11) {
					$Rules [$key] = $this->encoding ( $match [1] );
				}
			}
		}
		if (preg_match_all ( $rulePattern_ContentModel, $content, $match )) {
			$pattern = "/<(.*)>(.*)<\\/\\1>/isU";
			foreach ( $match [1] as $key => $var ) {
				$tmp = array ();
				if (preg_match_all ( $pattern, $var, $matches )) {
					foreach ( $matches [1] as $keyIn => $varIn ) {
						if ($version == 10) {
							$tmp [$varIn] = html_entity_decode ( $matches [2] [$keyIn] );
						} else if ($version == 11) {
							$tmp [$varIn] = $this->encoding ( $matches [2] [$keyIn] );
						}
					}
					$ContentModelRules [] = $tmp;
				}
			}
		}
		$Rules ['Fields'] = $ContentModelRules;
		return $Rules;
	}
	function encoding($str) {
		if (! empty ( $this->getEncoding ) && ! empty ( $this->toEncoding )) {
			if (empty ( $this->CharEncoding )) {
				require_once (INCLUDE_PATH . "/encoding/encoding.inc.php");
				$this->CharEncoding = new Encoding ();
			}
			$this->CharEncoding->SetGetEncoding ( $this->getEncoding );
			$this->CharEncoding->SetToEncoding ( $this->toEncoding );
			return $this->CharEncoding->EncodeString ( $str );
		} else {
			return $str;
		}
	}
}

?>
