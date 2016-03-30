<?php


$table_header =& $db_config['table_pre'];
$install_sql = "CREATE TABLE {$table_header}plugin_bbsi_access (\r\n  `AccessID` int(10) NOT NULL auto_increment,\r\n  `AccessType` int(1) NOT NULL default '0',\r\n  `Info` text NOT NULL,\r\n  `OwnerID` int(10) NOT NULL default '0',\r\n  `ReadIndex` text NOT NULL,\r\n  `ReadContent` text NOT NULL,\r\n  `PostComment` text NOT NULL,\r\n  `ReadComment` text NOT NULL,\r\n  `AuthInherit` text NOT NULL,\r\n  PRIMARY KEY  (`AccessID`,`AccessType`),\r\n  KEY `PermissionType` (`AccessType`,`OwnerID`)\r\n) TYPE=MyISAM AUTO_INCREMENT=19 ;\r\n\r\nCREATE TABLE {$table_header}plugin_bbsi_setting (\r\n  `ForegroundPath` varchar(250) NOT NULL default '',\r\n  `BBS` varchar(50) NOT NULL default '',\r\n  `DenyTpl` varchar(250) NOT NULL default ''\r\n) TYPE=MyISAM;\r\n\r\nINSERT INTO `{$table_header}plugin_bbsi_setting` VALUES ('../publish/member', 'phpwind2.0.1', '/dynamic/error.html');\r\n";
$result = plugin_runquery( $install_sql );
?>
