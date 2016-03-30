<?php


$table_header =& $db_config['table_pre'];
$install_sql = "CREATE TABLE {$table_header}plugin_base_comment (\r\n  `CommentID` int(10) NOT NULL auto_increment,\r\n  `IndexID` int(10) NOT NULL default '0',\r\n  `ContentID` int(10) NOT NULL default '0',\r\n  `NodeID` int(10) NOT NULL default '0',\r\n  `Author` varchar(100) default NULL,\r\n  `CreationDate` int(10) default NULL,\r\n  `Ip` varchar(15) default NULL,\r\n  `Comment` text,\r\n  PRIMARY KEY  (`CommentID`),\r\n  KEY `IndexID` (`IndexID`),\r\n  KEY `NodeID` (`NodeID`)\r\n) TYPE=MyISAM ;\r\n\r\n\r\n\r\n\r\nCREATE TABLE {$table_header}plugin_base_count (\r\n  `Hits_Total` int(10) NOT NULL default '0',\r\n  `Hits_Today` int(10) NOT NULL default '0',\r\n  `Hits_Week` int(10) NOT NULL default '0',\r\n  `Hits_Month` int(10) NOT NULL default '0',\r\n  `Hits_Date` int(10) NOT NULL default '0',\r\n  `IndexID` int(10) NOT NULL default '0',\r\n  `ContentID` int(10) NOT NULL default '0',\r\n  `NodeID` int(10) NOT NULL default '0',\r\n  `CommentNum` int(10) NOT NULL default '0',\r\n  PRIMARY KEY  (`IndexID`),\r\n  KEY `NodeID` (`NodeID`)\r\n) TYPE=MyISAM;\r\n\r\n\r\n\r\nCREATE TABLE {$table_header}plugin_base_setting (\r\n  `TableID` int(6) unsigned NOT NULL default '0',\r\n  `CommentMode` tinyint(1) default '0',\r\n  `CommentTpl` varchar(250) default NULL,\r\n  `CommentCache` tinyint(1) default '1',\r\n  `CommentPageOffset` tinyint(3) default '15',\r\n  `CommentLength` int(10) default NULL,\r\n  `IpHidden` tinyint(1) default '1',\r\n  `AllowBBcode` tinyint(1) default '0',\r\n  `AllowSmilies` tinyint(1) default '0',\r\n  `AllowHtml` tinyint(1) default '0',\r\n  `AllowImgcode` tinyint(1) default '0',\r\n  `SearchMode` tinyint(1) default '0',\r\n  `SearchTpl` varchar(250) default NULL,\r\n  `SearchProTpl` varchar(250) default NULL,\r\n  `SearchPageOffset` tinyint(3) default '15',\r\n  `AllowSearchField` text,\r\n  PRIMARY KEY  (`TableID`)\r\n) TYPE=MyISAM;\r\n\r\n\r\n\r\nINSERT INTO `{$table_header}plugin_base_setting` VALUES (1, 1, '/plugins/base/comment_bbsInterface.html', 1, 15, 1000, 1, 0, 0, 0, 0, 0, '/plugins/base/search_result.html', '/plugins/base/search_pro.html', 10, 'Title,Content');";
$result = plugin_runquery( $install_sql );
?>
