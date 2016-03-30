DROP TABLE IF EXISTS `jianniang_block_ip`;
CREATE TABLE jianniang_block_ip (
  Id int(6) unsigned NOT NULL AUTO_INCREMENT,
  IP char(15) DEFAULT NULL,
  ExpireTime int(10) DEFAULT NULL,
  Reason char(250) DEFAULT NULL,
  PRIMARY KEY (Id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_category`;
CREATE TABLE jianniang_category (
  CateID int(8) NOT NULL AUTO_INCREMENT,
  TableID int(8) DEFAULT '0',
  `Name` varchar(20) DEFAULT NULL,
  ParentID int(8) DEFAULT NULL,
  OwnerID varchar(20) DEFAULT NULL,
  Disabled tinyint(1) DEFAULT '0',
  NodeID int(8) DEFAULT '0',
  SubNodeID varchar(250) DEFAULT NULL,
  IndexNodeID varchar(250) DEFAULT NULL,
  PRIMARY KEY (CateID),
  KEY C_D (CateID,Disabled)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_collection_1`;
CREATE TABLE jianniang_collection_1 (
  CollectionID int(10) NOT NULL AUTO_INCREMENT,
  CateID int(8) NOT NULL DEFAULT '0',
  CreationDate int(10) DEFAULT NULL,
  ModifiedDate int(10) DEFAULT NULL,
  ApprovedDate int(10) DEFAULT NULL,
  PublishDate int(10) DEFAULT NULL,
  State int(2) DEFAULT NULL,
  NodeID int(8) DEFAULT '0',
  SubNodeID varchar(250) DEFAULT NULL,
  Title varchar(250) DEFAULT NULL,
  TitleColor varchar(7) DEFAULT NULL,
  Author varchar(20) DEFAULT NULL,
  Editor varchar(20) DEFAULT NULL,
  Photo varchar(250) DEFAULT NULL,
  SubTitle varchar(250) DEFAULT NULL,
  Content longtext,
  Keywords varchar(250) DEFAULT NULL,
  FromSite varchar(250) DEFAULT NULL,
  Intro text,
  CustomLinks text,
  Src varchar(250) DEFAULT NULL,
  IsImported tinyint(1) DEFAULT '0',
  PRIMARY KEY (CollectionID,CateID),
  UNIQUE KEY CollectionID (CollectionID),
  KEY C_I (CateID,IsImported),
  KEY Src (Src)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_collection_2`;
CREATE TABLE jianniang_collection_2 (
  CollectionID int(10) NOT NULL AUTO_INCREMENT,
  CateID int(8) NOT NULL DEFAULT '0',
  CreationDate int(10) DEFAULT NULL,
  ModifiedDate int(10) DEFAULT NULL,
  ApprovedDate int(10) DEFAULT NULL,
  PublishDate int(10) DEFAULT NULL,
  State int(2) DEFAULT NULL,
  NodeID int(8) DEFAULT '0',
  SubNodeID varchar(250) DEFAULT NULL,
  SoftName varchar(250) DEFAULT NULL,
  SoftSize varchar(15) DEFAULT NULL,
  `Language` varchar(10) DEFAULT NULL,
  SoftType varchar(50) DEFAULT NULL,
  Environment varchar(50) DEFAULT NULL,
  Star int(2) DEFAULT '0',
  Developer varchar(250) DEFAULT NULL,
  SoftKeywords varchar(250) DEFAULT NULL,
  Intro text,
  Download text,
  Photo varchar(250) DEFAULT NULL,
  LocalUpload varchar(250) DEFAULT NULL,
  CustomSoftLinks text,
  CustomLinks text,
  Src varchar(250) DEFAULT NULL,
  IsImported tinyint(1) DEFAULT '0',
  PRIMARY KEY (CollectionID,CateID),
  UNIQUE KEY CollectionID (CollectionID),
  KEY C_I (CateID,IsImported),
  KEY Src (Src)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_collection_category`;
CREATE TABLE jianniang_collection_category (
  CateID int(10) NOT NULL AUTO_INCREMENT,
  TableID int(8) DEFAULT '0',
  `Name` varchar(50) DEFAULT NULL,
  ParentID int(8) DEFAULT '0',
  Disabled tinyint(1) DEFAULT '0',
  NodeID int(8) DEFAULT NULL,
  SubNodeID varchar(250) DEFAULT '0',
  IndexNodeID varchar(250) DEFAULT NULL,
  TargetURL text,
  TargetURLArea text,
  UrlFilterRule text,
  RepeatCollection tinyint(1) DEFAULT '0',
  HiddenImported tinyint(1) DEFAULT '1',
  AutoImport tinyint(1) DEFAULT '0',
  UrlPageRule text,
  DelAfterImport tinyint(1) DEFAULT '0',
  InRunPlan tinyint(1) DEFAULT '1',
  PRIMARY KEY (CateID),
  KEY C_D (CateID,Disabled)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_collection_rules`;
CREATE TABLE jianniang_collection_rules (
  RuleID int(10) NOT NULL AUTO_INCREMENT,
  CateID int(10) NOT NULL DEFAULT '0',
  ContentFieldID int(8) DEFAULT '0',
  TableID int(8) DEFAULT '0',
  Rule text,
  PRIMARY KEY (RuleID,CateID),
  UNIQUE KEY RuleID (RuleID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_content_1`;
CREATE TABLE jianniang_content_1 (
  ContentID int(10) NOT NULL AUTO_INCREMENT,
  Title varchar(250) DEFAULT NULL,
  TitleColor varchar(7) DEFAULT NULL,
  Author varchar(20) DEFAULT NULL,
  Editor varchar(20) DEFAULT NULL,
  Photo varchar(250) DEFAULT NULL,
  SubTitle varchar(250) DEFAULT NULL,
  Content longtext,
  Keywords varchar(250) DEFAULT NULL,
  FromSite varchar(250) DEFAULT NULL,
  Intro text,
  CustomLinks text,
  CreationDate int(10) DEFAULT '0',
  ModifiedDate int(10) DEFAULT '0',
  CreationUserID int(8) DEFAULT '0',
  LastModifiedUserID int(8) DEFAULT '0',
  ContributionUserID int(8) DEFAULT '0',
  ContributionID int(8) DEFAULT '0',
  PRIMARY KEY (ContentID)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_content_2`;
CREATE TABLE jianniang_content_2 (
  ContentID int(10) NOT NULL AUTO_INCREMENT,
  CreationDate int(10) DEFAULT '0',
  ModifiedDate int(10) DEFAULT '0',
  CreationUserID int(8) DEFAULT '0',
  LastModifiedUserID int(8) DEFAULT '0',
  ContributionUserID int(8) DEFAULT '0',
  ContributionID int(10) DEFAULT '0',
  SoftName varchar(250) DEFAULT NULL,
  SoftSize varchar(15) DEFAULT NULL,
  `Language` varchar(10) DEFAULT NULL,
  SoftType varchar(50) DEFAULT NULL,
  Environment varchar(50) DEFAULT NULL,
  Star int(2) DEFAULT '0',
  Developer varchar(250) DEFAULT NULL,
  SoftKeywords varchar(250) DEFAULT NULL,
  Intro text,
  Download text,
  Photo varchar(250) DEFAULT NULL,
  LocalUpload varchar(250) DEFAULT NULL,
  CustomSoftLinks text,
  CustomLinks text,
  PRIMARY KEY (ContentID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_content_fields`;
CREATE TABLE jianniang_content_fields (
  ContentFieldID int(8) NOT NULL AUTO_INCREMENT,
  TableID int(8) NOT NULL DEFAULT '0',
  FieldTitle varchar(100) DEFAULT NULL,
  FieldName varchar(20) DEFAULT NULL,
  FieldType varchar(20) DEFAULT NULL,
  FieldSize varchar(20) DEFAULT NULL,
  FieldInput varchar(20) DEFAULT NULL,
  FieldDefaultValue varchar(250) DEFAULT NULL,
  FieldInputFilter varchar(20) DEFAULT NULL,
  FieldInputPicker varchar(20) DEFAULT NULL,
  FieldInputTpl varchar(250) DEFAULT NULL,
  FieldDescription mediumtext,
  FieldOrder mediumint(8) DEFAULT '0',
  FieldListDisplay tinyint(1) DEFAULT '0',
  IsMainField tinyint(1) DEFAULT '0',
  IsTitleField tinyint(1) DEFAULT '0',
  FieldSearchable tinyint(1) DEFAULT '0',
  EnableContribution tinyint(1) DEFAULT '1',
  EnableCollection tinyint(1) DEFAULT '1',
  EnablePublish tinyint(1) DEFAULT '1',
  PRIMARY KEY (ContentFieldID,TableID),
  UNIQUE KEY ContentFiledID (ContentFieldID),
  KEY T_F (TableID,FieldListDisplay)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_content_index`;
CREATE TABLE jianniang_content_index (
  IndexID int(10) NOT NULL AUTO_INCREMENT,
  ContentID int(10) NOT NULL DEFAULT '0',
  NodeID int(10) NOT NULL DEFAULT '0',
  TableID int(10) DEFAULT NULL,
  ParentIndexID int(8) DEFAULT '0',
  `Type` tinyint(1) DEFAULT '1',
  PublishDate int(10) DEFAULT '0',
  SelfTemplate varchar(250) DEFAULT NULL,
  SelfPSN varchar(250) DEFAULT NULL,
  SelfPublishFileName varchar(250) DEFAULT NULL,
  SelfPSNURL varchar(250) DEFAULT NULL,
  SelfURL varchar(250) DEFAULT NULL,
  State tinyint(2) DEFAULT '0',
  URL varchar(250) DEFAULT NULL,
  Top smallint(5) DEFAULT '0',
  Pink smallint(5) DEFAULT '0',
  Sort smallint(5) DEFAULT '0',
  PRIMARY KEY (IndexID,ContentID,NodeID),
  UNIQUE KEY IndexID (IndexID),
  KEY N_P (NodeID,State,Top,PublishDate,Sort),
  KEY N_S (NodeID,State),
  KEY PID (ParentIndexID),
  KEY `Type` (`Type`),
  KEY Top (Top),
  KEY Pink (Pink)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_content_table`;
CREATE TABLE jianniang_content_table (
  TableID int(8) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) DEFAULT NULL,
  DSNID int(8) DEFAULT '0',
  PRIMARY KEY (TableID),
  UNIQUE KEY TableID (TableID)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_contribution_1`;
CREATE TABLE jianniang_contribution_1 (
  ContributionID int(10) NOT NULL AUTO_INCREMENT,
  CateID int(8) NOT NULL DEFAULT '0',
  CreationDate int(10) DEFAULT NULL,
  ModifiedDate int(10) DEFAULT NULL,
  ApprovedDate int(10) DEFAULT NULL,
  OwnerID int(8) DEFAULT NULL,
  State int(5) DEFAULT '0',
  Title varchar(250) DEFAULT NULL,
  TitleColor varchar(7) DEFAULT NULL,
  Author varchar(20) DEFAULT NULL,
  Editor varchar(20) DEFAULT NULL,
  Photo varchar(250) DEFAULT NULL,
  SubTitle varchar(250) DEFAULT NULL,
  Content longtext,
  Keywords varchar(250) DEFAULT NULL,
  FromSite varchar(250) DEFAULT NULL,
  Intro text,
  CustomLinks text,
  NodeID int(8) DEFAULT '0',
  SubNodeID varchar(250) DEFAULT NULL,
  IndexNodeID varchar(250) DEFAULT NULL,
  ContributionDate int(10) DEFAULT '0',
  PRIMARY KEY (ContributionID,CateID),
  UNIQUE KEY ContributionID (ContributionID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_contribution_2`;
CREATE TABLE jianniang_contribution_2 (
  ContributionID int(10) NOT NULL AUTO_INCREMENT,
  CateID int(8) NOT NULL DEFAULT '0',
  CreationDate int(10) DEFAULT NULL,
  ModifiedDate int(10) DEFAULT NULL,
  ApprovedDate int(10) DEFAULT NULL,
  OwnerID int(8) DEFAULT NULL,
  State int(2) DEFAULT NULL,
  NodeID int(8) DEFAULT '0',
  SubNodeID varchar(250) DEFAULT NULL,
  IndexNodeID varchar(250) DEFAULT NULL,
  ContributionDate int(10) DEFAULT NULL,
  SoftName varchar(250) DEFAULT NULL,
  SoftSize varchar(15) DEFAULT NULL,
  `Language` varchar(10) DEFAULT NULL,
  SoftType varchar(50) DEFAULT NULL,
  Environment varchar(50) DEFAULT NULL,
  Star int(2) DEFAULT '0',
  Developer varchar(250) DEFAULT NULL,
  SoftKeywords varchar(250) DEFAULT NULL,
  Intro text,
  Download text,
  Photo varchar(250) DEFAULT NULL,
  LocalUpload varchar(250) DEFAULT NULL,
  CustomSoftLinks text,
  CustomLinks text,
  PRIMARY KEY (ContributionID,CateID),
  UNIQUE KEY ContributionID (ContributionID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_contribution_note`;
CREATE TABLE jianniang_contribution_note (
  NoteID int(8) NOT NULL AUTO_INCREMENT,
  ContributionID int(10) NOT NULL DEFAULT '0',
  CateID int(8) NOT NULL DEFAULT '0',
  Note text,
  NoteUserID int(8) DEFAULT NULL,
  NoteUserName varchar(50) DEFAULT NULL,
  NoteDate int(10) DEFAULT '0',
  PRIMARY KEY (NoteID,ContributionID,CateID),
  UNIQUE KEY NoteID (NoteID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_action`;
CREATE TABLE jianniang_cwps_action (
  ActionID int(6) unsigned NOT NULL AUTO_INCREMENT,
  `Action` varchar(30) DEFAULT NULL,
  PRIMARY KEY (ActionID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_group`;
CREATE TABLE jianniang_cwps_group (
  GroupID int(8) unsigned NOT NULL AUTO_INCREMENT,
  GroupName varchar(32) DEFAULT NULL,
  Reserved tinyint(1) DEFAULT '0',
  RoleID int(6) DEFAULT NULL,
  SubRoleIDs text,
  OrderBy tinyint(2) DEFAULT '0',
  OpIDs text,
  PRIMARY KEY (GroupID)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_oas`;
CREATE TABLE jianniang_cwps_oas (
  OASID int(6) unsigned NOT NULL AUTO_INCREMENT,
  OASUID varchar(255) DEFAULT NULL,
  OASName varchar(20) DEFAULT NULL,
  IP varchar(255) DEFAULT NULL,
  ProvisionURL varchar(255) DEFAULT NULL,
  ProvisionPassword varchar(32) DEFAULT NULL,
  CWPSPassword varchar(32) DEFAULT NULL,
  PRIMARY KEY (OASID)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_operator`;
CREATE TABLE jianniang_cwps_operator (
  OpID int(6) unsigned NOT NULL AUTO_INCREMENT,
  PID int(6) DEFAULT NULL,
  RID int(6) DEFAULT NULL,
  OpName varchar(30) DEFAULT NULL,
  Enabled tinyint(1) DEFAULT '1',
  PRIMARY KEY (OpID)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_privilege`;
CREATE TABLE jianniang_cwps_privilege (
  PID int(6) unsigned NOT NULL AUTO_INCREMENT,
  PrivilegeUID varchar(20) DEFAULT NULL,
  PrivilegeName varchar(30) DEFAULT NULL,
  PRIMARY KEY (PID),
  UNIQUE KEY PrivilegeUID (PrivilegeUID)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_resource`;
CREATE TABLE jianniang_cwps_resource (
  RID int(6) unsigned NOT NULL AUTO_INCREMENT,
  ResourceUID varchar(20) DEFAULT NULL,
  ResourceName varchar(30) DEFAULT NULL,
  OASIDs varchar(250) DEFAULT NULL,
  PRIMARY KEY (RID),
  UNIQUE KEY ResourceUID (ResourceUID)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_role`;
CREATE TABLE jianniang_cwps_role (
  RoleID int(6) unsigned NOT NULL AUTO_INCREMENT,
  RoleName varchar(30) DEFAULT NULL,
  OpIDs text,
  RoleBaseUID enum('Administrator','User','Guest') DEFAULT NULL,
  Reserved tinyint(1) DEFAULT '0',
  PRIMARY KEY (RoleID)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_sessions`;
CREATE TABLE jianniang_cwps_sessions (
  sId varchar(32) NOT NULL DEFAULT '',
  UserName varchar(32) DEFAULT NULL,
  UserID int(8) DEFAULT '0',
  GroupID int(8) DEFAULT NULL,
  LogInTime int(10) DEFAULT '0',
  RunningTime int(10) DEFAULT '0',
  Ip varchar(16) DEFAULT NULL,
  SessionData blob,
  IsCookieLogin tinyint(1) DEFAULT '0',
  PRIMARY KEY (sId)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_soap`;
CREATE TABLE jianniang_cwps_soap (
  SoapID varchar(30) NOT NULL DEFAULT '',
  SoapName varchar(50) DEFAULT NULL,
  PRIMARY KEY (SoapID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_user`;
CREATE TABLE jianniang_cwps_user (
  UserID int(8) unsigned NOT NULL AUTO_INCREMENT,
  GroupID int(8) DEFAULT NULL,
  UserName varchar(32) DEFAULT NULL,
  `Password` varchar(32) DEFAULT NULL,
  PassQuestion varchar(30) DEFAULT NULL,
  PassAnswer varchar(30) DEFAULT NULL,
  Email varchar(30) DEFAULT NULL,
  NickName varchar(32) DEFAULT NULL,
  Gender tinyint(1) DEFAULT NULL,
  Birthday date DEFAULT '0000-00-00',
  QQ varchar(20) DEFAULT NULL,
  Description varchar(255) DEFAULT NULL,
  `Status` tinyint(1) DEFAULT '0',
  RegisterDate int(10) DEFAULT '0',
  LastLoginTime int(10) DEFAULT NULL,
  SubGroupIDs varchar(255) DEFAULT NULL,
  RoleID int(5) DEFAULT '0',
  SubRoleIDs varchar(255) DEFAULT NULL,
  OpIDs varchar(255) DEFAULT NULL,
  PRIMARY KEY (UserID)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_user_extra`;
CREATE TABLE jianniang_cwps_user_extra (
  UserID int(8) NOT NULL DEFAULT '0',
  Phone varchar(11) DEFAULT NULL,
  Money int(12) DEFAULT NULL,
  PRIMARY KEY (UserID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_cwps_user_fields`;
CREATE TABLE jianniang_cwps_user_fields (
  FieldID int(8) NOT NULL AUTO_INCREMENT,
  FieldTitle varchar(20) DEFAULT NULL,
  FieldName varchar(20) DEFAULT NULL,
  FieldType varchar(20) DEFAULT NULL,
  FieldSize varchar(20) DEFAULT NULL,
  FieldInput varchar(20) DEFAULT NULL,
  FieldDescription mediumtext,
  FieldOrder mediumint(8) DEFAULT '0',
  FieldAccess tinyint(1) DEFAULT '1',
  FieldDataSource text,
  PRIMARY KEY (FieldID)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_extra_publish`;
CREATE TABLE jianniang_extra_publish (
  PublishID int(8) NOT NULL AUTO_INCREMENT,
  NodeID int(8) DEFAULT '0',
  PublishName varchar(100) DEFAULT NULL,
  SelfPSN varchar(250) DEFAULT NULL,
  SelfPSNURL varchar(250) DEFAULT NULL,
  PublishFileName varchar(100) DEFAULT NULL,
  Tpl varchar(250) DEFAULT NULL,
  Intro text,
  CreationUserID int(8) DEFAULT NULL,
  LastModifiedUserID int(8) DEFAULT NULL,
  CreationDate int(10) DEFAULT NULL,
  ModifiedDate int(10) DEFAULT NULL,
  PRIMARY KEY (PublishID),
  KEY NodeID (NodeID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_group`;
CREATE TABLE jianniang_group (
  gId mediumint(8) NOT NULL AUTO_INCREMENT,
  gName varchar(50) DEFAULT NULL,
  gPass varchar(32) DEFAULT '0',
  gPublishAuth varchar(50) DEFAULT NULL,
  gInfo text,
  gIsAdmin tinyint(1) DEFAULT '0',
  canLoginAdmin tinyint(1) DEFAULT '0',
  canLogin tinyint(1) DEFAULT '1',
  canChangePW tinyint(1) DEFAULT '1',
  canTpl tinyint(1) DEFAULT '0',
  canNode tinyint(1) DEFAULT '0',
  canCollection tinyint(1) DEFAULT '0',
  ParentGID mediumint(8) DEFAULT NULL,
  canMakeG tinyint(1) DEFAULT '0',
  canMakeU tinyint(1) DEFAULT '0',
  CreationUserID mediumint(8) DEFAULT NULL,
  UNIQUE KEY gId (gId)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_keywords`;
CREATE TABLE jianniang_keywords (
  kId mediumint(8) NOT NULL AUTO_INCREMENT,
  keyword varchar(250) DEFAULT NULL,
  kReplace varchar(250) DEFAULT NULL,
  IsGlobal tinyint(1) DEFAULT '1',
  NodeScope text,
  UNIQUE KEY kId (kId)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_log_admin`;
CREATE TABLE jianniang_log_admin (
  LogID int(10) NOT NULL AUTO_INCREMENT,
  uName char(50) DEFAULT NULL,
  IP char(15) DEFAULT NULL,
  `Action` char(100) DEFAULT NULL,
  ActionURL char(250) DEFAULT NULL,
  `Time` int(10) DEFAULT NULL,
  PRIMARY KEY (LogID)
) ENGINE=MyISAM AUTO_INCREMENT=163 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_log_login`;
CREATE TABLE jianniang_log_login (
  LogID int(10) NOT NULL AUTO_INCREMENT,
  uName char(50) DEFAULT NULL,
  IP char(15) DEFAULT NULL,
  `Time` int(10) DEFAULT NULL,
  PRIMARY KEY (LogID)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_node_fields`;
CREATE TABLE jianniang_node_fields (
  FieldID int(8) NOT NULL AUTO_INCREMENT,
  FieldTitle varchar(20) DEFAULT NULL,
  FieldName varchar(20) DEFAULT NULL,
  FieldType varchar(20) DEFAULT NULL,
  FieldSize varchar(20) DEFAULT NULL,
  FieldInput varchar(20) DEFAULT NULL,
  FieldDescription mediumtext,
  FieldOrder mediumint(8) DEFAULT '0',
  FieldAccess tinyint(1) DEFAULT '1',
  FieldDataSource text,
  PRIMARY KEY (FieldID)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_base_comment`;
CREATE TABLE jianniang_plugin_base_comment (
  CommentID int(10) NOT NULL AUTO_INCREMENT,
  IndexID int(10) DEFAULT '0',
  ContentID int(10) DEFAULT '0',
  NodeID int(10) DEFAULT '0',
  Author varchar(100) DEFAULT NULL,
  CreationDate int(10) DEFAULT NULL,
  Ip varchar(15) DEFAULT NULL,
  `Comment` text,
  Approved tinyint(1) DEFAULT '0',
  PRIMARY KEY (CommentID),
  KEY IndexID (IndexID),
  KEY NodeID (NodeID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_base_count`;
CREATE TABLE jianniang_plugin_base_count (
  Hits_Total int(10) DEFAULT '0',
  Hits_Today int(10) DEFAULT '0',
  Hits_Week int(10) DEFAULT '0',
  Hits_Month int(10) DEFAULT '0',
  Hits_Date int(10) DEFAULT '0',
  IndexID int(10) NOT NULL DEFAULT '0',
  ContentID int(10) DEFAULT '0',
  NodeID int(10) DEFAULT '0',
  CommentNum int(10) DEFAULT '0',
  TableID int(5) DEFAULT '0',
  PRIMARY KEY (IndexID),
  KEY NodeID (NodeID),
  KEY CID (ContentID),
  KEY TID (TableID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_base_setting`;
CREATE TABLE jianniang_plugin_base_setting (
  TableID int(6) unsigned NOT NULL DEFAULT '0',
  CommentMode tinyint(1) DEFAULT '0',
  CommentTpl varchar(250) DEFAULT NULL,
  CommentCache tinyint(1) DEFAULT '1',
  CommentPageOffset tinyint(3) DEFAULT '15',
  CommentLength int(10) DEFAULT NULL,
  IpHidden tinyint(1) DEFAULT '1',
  AllowBBcode tinyint(1) DEFAULT '0',
  AllowSmilies tinyint(1) DEFAULT '0',
  AllowHtml tinyint(1) DEFAULT '0',
  AllowImgcode tinyint(1) DEFAULT '0',
  SearchMode tinyint(1) DEFAULT '0',
  SearchTpl varchar(250) DEFAULT NULL,
  SearchProTpl varchar(250) DEFAULT NULL,
  SearchPageOffset tinyint(3) DEFAULT '15',
  AllowSearchField text,
  PRIMARY KEY (TableID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_bbsi_access`;
CREATE TABLE jianniang_plugin_bbsi_access (
  AccessID int(10) NOT NULL AUTO_INCREMENT,
  AccessType int(1) NOT NULL DEFAULT '0',
  Info text,
  OwnerID int(10) DEFAULT '0',
  ReadIndex text,
  ReadContent text,
  PostComment text,
  ReadComment text,
  AuthInherit text,
  PRIMARY KEY (AccessID,AccessType),
  KEY PermissionType (AccessType,OwnerID)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_bbsi_setting`;
CREATE TABLE jianniang_plugin_bbsi_setting (
  ForegroundPath varchar(250) DEFAULT NULL,
  BBS varchar(50) DEFAULT NULL,
  DenyTpl varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_fulltext_fields`;
CREATE TABLE jianniang_plugin_fulltext_fields (
  SearchID int(6) unsigned NOT NULL AUTO_INCREMENT,
  SearchName varchar(50) DEFAULT NULL,
  FullTextFields varchar(250) DEFAULT NULL,
  TableID tinyint(6) DEFAULT NULL,
  PRIMARY KEY (SearchID)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_fulltext_search_1`;
CREATE TABLE jianniang_plugin_fulltext_search_1 (
  IndexID int(10) NOT NULL DEFAULT '0',
  ContentID int(10) DEFAULT '0',
  NodeID int(10) DEFAULT '0',
  PublishDate int(10) DEFAULT NULL,
  URL varchar(250) DEFAULT NULL,
  Content longtext,
  Title varchar(250) DEFAULT NULL,
  PRIMARY KEY (IndexID),
  KEY ContentID (ContentID),
  KEY NodeID (NodeID),
  KEY PublishDate (PublishDate),
  FULLTEXT KEY Content (Content),
  FULLTEXT KEY Main (Title,Content)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_fulltext_setting`;
CREATE TABLE jianniang_plugin_fulltext_setting (
  TableID int(6) unsigned NOT NULL DEFAULT '0',
  SearchMode tinyint(1) DEFAULT '0',
  SearchTpl varchar(250) DEFAULT NULL,
  SearchPageOffset tinyint(3) DEFAULT '15',
  SearchProTpl varchar(250) DEFAULT NULL,
  PRIMARY KEY (TableID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_oas_access`;
CREATE TABLE jianniang_plugin_oas_access (
  AccessID int(10) NOT NULL AUTO_INCREMENT,
  AccessType tinyint(1) DEFAULT '1',
  OwnerID int(10) DEFAULT NULL,
  AccessInherit text,
  Info text,
  PRIMARY KEY (AccessID),
  UNIQUE KEY AccessID (AccessID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_oas_access_map`;
CREATE TABLE jianniang_plugin_oas_access_map (
  AccessID int(10) NOT NULL DEFAULT '0',
  PermissionKey varchar(32) NOT NULL DEFAULT '',
  AccessNodeIDs text,
  PRIMARY KEY (AccessID,PermissionKey)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_oas_permission`;
CREATE TABLE jianniang_plugin_oas_permission (
  PermissionKey varchar(32) NOT NULL DEFAULT '',
  PermissionInfo varchar(250) DEFAULT NULL,
  Reserved tinyint(1) DEFAULT '0',
  OrderKey int(5) DEFAULT '0',
  PRIMARY KEY (PermissionKey),
  UNIQUE KEY PermissionKey (PermissionKey)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_oas_sessions`;
CREATE TABLE jianniang_plugin_oas_sessions (
  sId varchar(32) NOT NULL DEFAULT '',
  UserName varchar(32) DEFAULT NULL,
  UserID int(8) DEFAULT '0',
  GroupID int(8) DEFAULT '0',
  LogInTime int(10) DEFAULT '0',
  RunningTime int(10) DEFAULT '0',
  Ip varchar(16) DEFAULT NULL,
  SessionData blob,
  PRIMARY KEY (sId)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugin_oas_setting`;
CREATE TABLE jianniang_plugin_oas_setting (
  `key` varchar(32) NOT NULL DEFAULT '',
  `value` text,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugins`;
CREATE TABLE jianniang_plugins (
  pId int(10) NOT NULL AUTO_INCREMENT,
  pName varchar(250) DEFAULT NULL,
  Path varchar(250) DEFAULT NULL,
  Info text,
  LicenseKey text,
  AccessGroup text,
  AccessUser text,
  PRIMARY KEY (pId)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_plugins_oas_user`;
CREATE TABLE jianniang_plugins_oas_user (
  UserID int(11) NOT NULL AUTO_INCREMENT,
  UserName varchar(32) DEFAULT NULL,
  PRIMARY KEY (UserID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_psn`;
CREATE TABLE jianniang_psn (
  PSNID int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(20) DEFAULT NULL,
  PSN varchar(250) DEFAULT NULL,
  URL varchar(250) DEFAULT NULL,
  Description mediumtext,
  PermissionReadG text,
  PRIMARY KEY (PSNID),
  UNIQUE KEY PSNID (PSNID),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_pubadminmasks`;
CREATE TABLE jianniang_pubadminmasks (
  pId mediumint(8) NOT NULL AUTO_INCREMENT,
  pName varchar(50) DEFAULT NULL,
  pInfo varchar(250) DEFAULT NULL,
  NodeList text,
  NodeExtraPublish text,
  NodeSetting text,
  ContentRead text,
  ContentWrite text,
  ContentApprove text,
  ContentPublish text,
  AuthInherit text,
  UNIQUE KEY pAId (pId)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_publish_1`;
CREATE TABLE jianniang_publish_1 (
  IndexID int(10) NOT NULL DEFAULT '0',
  ContentID int(10) DEFAULT NULL,
  NodeID int(10) DEFAULT NULL,
  PublishDate int(10) DEFAULT NULL,
  URL char(250) DEFAULT NULL,
  Title varchar(250) DEFAULT NULL,
  TitleColor varchar(7) DEFAULT NULL,
  Author varchar(20) DEFAULT NULL,
  Editor varchar(20) DEFAULT NULL,
  Photo varchar(250) DEFAULT NULL,
  SubTitle varchar(250) DEFAULT NULL,
  Content longtext,
  Keywords varchar(250) DEFAULT NULL,
  FromSite varchar(250) DEFAULT NULL,
  Intro text,
  CustomLinks text,
  PRIMARY KEY (IndexID),
  KEY NodeID (NodeID),
  KEY ContentID (ContentID),
  KEY PublishDate (PublishDate)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_publish_2`;
CREATE TABLE jianniang_publish_2 (
  IndexID int(10) NOT NULL DEFAULT '0',
  ContentID int(10) DEFAULT NULL,
  NodeID int(10) DEFAULT NULL,
  PublishDate int(10) DEFAULT NULL,
  URL char(250) DEFAULT NULL,
  SoftName varchar(250) DEFAULT NULL,
  SoftSize varchar(15) DEFAULT NULL,
  `Language` varchar(10) DEFAULT NULL,
  SoftType varchar(50) DEFAULT NULL,
  Environment varchar(50) DEFAULT NULL,
  Star int(2) DEFAULT '0',
  Developer varchar(250) DEFAULT NULL,
  SoftKeywords varchar(250) DEFAULT NULL,
  Intro text,
  Download text,
  Photo varchar(250) DEFAULT NULL,
  LocalUpload varchar(250) DEFAULT NULL,
  CustomSoftLinks text,
  CustomLinks text,
  PRIMARY KEY (IndexID),
  KEY NodeID (NodeID),
  KEY ContentID (ContentID),
  KEY PublishDate (PublishDate)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_publish_log`;
CREATE TABLE jianniang_publish_log (
  logID int(8) NOT NULL AUTO_INCREMENT,
  ContentID int(10) NOT NULL DEFAULT '0',
  NodeID int(10) NOT NULL DEFAULT '0',
  PSN varchar(50) DEFAULT NULL,
  FileName varchar(100) DEFAULT NULL,
  `TYPE` varchar(20) DEFAULT NULL,
  URL varchar(250) DEFAULT NULL,
  PRIMARY KEY (logID,ContentID,NodeID),
  UNIQUE KEY logID (logID),
  KEY C_P_F (ContentID,PSN,FileName)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_resource`;
CREATE TABLE jianniang_resource (
  ResourceID int(10) NOT NULL AUTO_INCREMENT,
  NodeID int(10) NOT NULL DEFAULT '0',
  ParentID int(10) DEFAULT '0',
  `Type` tinyint(1) DEFAULT '1',
  Category varchar(20) DEFAULT NULL,
  `Name` varchar(250) DEFAULT NULL,
  Path varchar(250) DEFAULT NULL,
  Size int(10) DEFAULT NULL,
  Info varchar(250) DEFAULT NULL,
  CreationDate int(10) DEFAULT NULL,
  ModifiedDate int(10) DEFAULT NULL,
  Src varchar(250) DEFAULT NULL,
  Title varchar(250) DEFAULT NULL,
  CreationUserID int(8) DEFAULT '0',
  PRIMARY KEY (ResourceID,NodeID),
  KEY Path (Path),
  KEY `Name` (`Name`),
  KEY Src (Src),
  KEY Category (Category),
  KEY CUID (CreationUserID)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_resource_ref`;
CREATE TABLE jianniang_resource_ref (
  NodeID int(10) DEFAULT '0',
  IndexID int(10) DEFAULT '0',
  ResourceID int(10) DEFAULT '0',
  CollectionKey char(32) DEFAULT NULL,
  KEY I_R (IndexID,ResourceID),
  KEY N_I_R (NodeID,IndexID,ResourceID),
  KEY R_C (ResourceID,CollectionKey)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_sessions`;
CREATE TABLE jianniang_sessions (
  sId varchar(32) NOT NULL DEFAULT '',
  sIpAddress varchar(16) DEFAULT NULL,
  sUserName varchar(32) DEFAULT NULL,
  sUId int(8) DEFAULT '0',
  sLogInTime int(10) DEFAULT '0',
  sRunningTime int(10) DEFAULT '0',
  PRIMARY KEY (sId)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_site`;
CREATE TABLE jianniang_site (
  NodeID int(10) NOT NULL AUTO_INCREMENT,
  NodeGUID varchar(250) DEFAULT NULL,
  TableID int(8) DEFAULT '0',
  ParentID int(10) DEFAULT NULL,
  RootID int(10) DEFAULT '0',
  InheritNodeID int(8) DEFAULT '0',
  NodeType tinyint(1) DEFAULT '1',
  NodeSort tinyint(5) DEFAULT '0',
  `Name` varchar(250) DEFAULT NULL,
  ContentPSN varchar(250) DEFAULT NULL,
  ContentURL varchar(250) DEFAULT NULL,
  ResourcePSN varchar(250) DEFAULT NULL,
  ResourceURL varchar(250) DEFAULT NULL,
  PublishMode tinyint(1) DEFAULT '1',
  IndexTpl varchar(250) DEFAULT NULL,
  IndexName varchar(250) DEFAULT NULL,
  ContentTpl varchar(250) DEFAULT NULL,
  ImageTpl varchar(250) DEFAULT NULL,
  SubDir varchar(20) DEFAULT NULL,
  PublishFileFormat varchar(250) DEFAULT NULL,
  IsComment tinyint(1) DEFAULT '0',
  CommentLength int(10) DEFAULT NULL,
  IsPrint tinyint(1) DEFAULT '0',
  IsGrade tinyint(1) DEFAULT '0',
  IsMail tinyint(1) DEFAULT '0',
  Disabled tinyint(1) DEFAULT '0',
  AutoPublish tinyint(1) DEFAULT '1',
  IndexPortalURL varchar(250) DEFAULT NULL,
  ContentPortalURL varchar(250) DEFAULT NULL,
  Pager varchar(20) DEFAULT NULL,
  Editor varchar(50) DEFAULT NULL,
  WorkFlow int(8) DEFAULT '0',
  PermissionManageG text,
  PermissionManageU text,
  PermissionReadG text,
  PermissionReadU text,
  PermissionWriteG text,
  PermissionWriteU text,
  PermissionApproveG text,
  PermissionApproveU text,
  PermissionPublishG text,
  PermissionPublishU text,
  PermissionInherit text,
  CreationUserID int(8) DEFAULT NULL,
  keyWorld varchar(250) DEFAULT NULL,
  description varchar(250) DEFAULT NULL,
  UNIQUE KEY NodeID (NodeID),
  KEY P_D (ParentID,Disabled),
  KEY D (Disabled),
  KEY InheritNodeID (InheritNodeID)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_sys`;
CREATE TABLE jianniang_sys (
  id int(10) NOT NULL AUTO_INCREMENT,
  varName varchar(50) DEFAULT NULL,
  varValue text,
  UNIQUE KEY id (id),
  UNIQUE KEY var (varName)
) ENGINE=MyISAM AUTO_INCREMENT=138 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_tasks`;
CREATE TABLE jianniang_tasks (
  TaskID varchar(32) DEFAULT NULL,
  TaskData longblob,
  TaskTime int(10) DEFAULT '0',
  KEY TID (TaskID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_tpl_cate`;
CREATE TABLE jianniang_tpl_cate (
  TCID int(10) NOT NULL AUTO_INCREMENT,
  CateName varchar(50) DEFAULT NULL,
  ParentTCID int(10) DEFAULT '0',
  ReadG text,
  WriteG text,
  ManageG text,
  ReadU text,
  WriteU text,
  ManageU text,
  Inherit tinyint(1) DEFAULT '0',
  CreationUserID int(8) DEFAULT NULL,
  PRIMARY KEY (TCID),
  KEY ParentTCID (ParentTCID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_tpl_data`;
CREATE TABLE jianniang_tpl_data (
  TID int(11) NOT NULL AUTO_INCREMENT,
  TCID int(10) DEFAULT '0',
  TplName varchar(50) DEFAULT NULL,
  TplType int(3) DEFAULT '0',
  CreationUserID int(5) DEFAULT NULL,
  LastModifiedUserID int(5) DEFAULT NULL,
  CreationDate int(10) DEFAULT NULL,
  ModifiedDate int(10) DEFAULT NULL,
  PRIMARY KEY (TID),
  KEY NodeID (TCID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_tpl_vars`;
CREATE TABLE jianniang_tpl_vars (
  Id int(6) unsigned NOT NULL AUTO_INCREMENT,
  VarTitle varchar(250) DEFAULT NULL,
  VarName varchar(50) DEFAULT NULL,
  VarValue text,
  IsGlobal tinyint(1) DEFAULT '1',
  NodeScope text,
  PRIMARY KEY (Id)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_user`;
CREATE TABLE jianniang_user (
  uId mediumint(10) NOT NULL AUTO_INCREMENT,
  uGId mediumint(8) DEFAULT '0',
  uName varchar(50) DEFAULT NULL,
  uPass varchar(32) DEFAULT NULL,
  uInfo text,
  LastLoginDate int(10) DEFAULT '0',
  ApproveNum int(8) DEFAULT '0',
  ContributionNum int(8) DEFAULT '0',
  CallBackNum int(8) DEFAULT '0',
  NoContributionNum int(8) DEFAULT '0',
  CreationUserID mediumint(8) DEFAULT NULL,
  UNIQUE KEY uId (uId),
  UNIQUE KEY uName (uName),
  KEY uGId (uGId)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_workflow`;
CREATE TABLE jianniang_workflow (
  wID int(8) NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) DEFAULT NULL,
  Intro text,
  PRIMARY KEY (wID),
  UNIQUE KEY wID (wID)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_workflow_record`;
CREATE TABLE jianniang_workflow_record (
  OpID int(8) NOT NULL AUTO_INCREMENT,
  wID int(8) DEFAULT NULL,
  Executor int(8) DEFAULT NULL,
  OpName varchar(50) DEFAULT NULL,
  StateBeforeOp varchar(100) DEFAULT NULL,
  StateAfterOp varchar(100) DEFAULT NULL,
  AppendNote int(1) DEFAULT '0',
  OpIntro text,
  PRIMARY KEY (OpID),
  UNIQUE KEY OpID (OpID),
  KEY wID (wID)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
  


DROP TABLE IF EXISTS `jianniang_workflow_state`;
CREATE TABLE jianniang_workflow_state (
  ID int(8) NOT NULL AUTO_INCREMENT,
  `Name` char(30) DEFAULT NULL,
  State int(5) DEFAULT NULL,
  System int(1) DEFAULT '0',
  PRIMARY KEY (ID),
  UNIQUE KEY ID (ID),
  UNIQUE KEY State (State)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
  


