<?php
$STRUTS_CONFIG = array(
	"entrance" => "admin.php",
	"session-id-store" => "page",
	"session-id-name" => "sId",
	"message-resources" => array(
		"language-dir" => "language",
		"language-package" => "chinese_gb",
		"sys-messages" => array( "charset.inc.php", "lang_admin.php" ),
		"template-messages-package" => "lang_skin/admin",
		"templates-global-messages-resource" => "lang_skin_global.php"
	),
	"template-resources" => "templates/admin",
	"template-forward-action" => "url_forward.html",
	"form-beans" => array(
		"LoginForm" => array(
			"type" => "com.member.admin.form.LoginForm",
			"form-property" => array( "UserName" => "string", "Password" => "string", "validCode" => "string", "IpSecurity" => "int" )
		),
		"ChangePassForm" => array(
			"type" => "com.member.admin.form.ChangePassForm",
			"form-property" => array(
				"OldPassword" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.notEmpty" ),
				"NewPassword" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.notEmpty" ),
				"NewPassword2" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.notEmpty" )
			)
		)
	),
	"global-forwards" => array( "login" => "login.html", "deny_login" => "deny_login.html", "default" => "main.html", "main.frameset" => "main.frameset.html", "main.nav" => "main.nav.html", "main.home" => "main.home.html", "user.list" => "user.list.html", "user.view" => "user.view.html", "user.edit" => "user.edit.html", "user.resetPass" => "user.resetPass.html", "userProperty.main" => "userProperty.main.html", "userProperty.add" => "userProperty.add.html", "userProperty.edit" => "userProperty.edit.html", "oas.add" => "oas.add.html", "oas.edit" => "oas.edit.html", "oas.view" => "oas.view.html", "oas.list" => "oas.list.html", "resource.add" => "resource.add.html", "resource.edit" => "resource.edit.html", "resource.view" => "resource.view.html", "resource.list" => "resource.list.html", "privilege.add" => "privilege.add.html", "privilege.edit" => "privilege.edit.html", "privilege.view" => "privilege.view.html", "privilege.list" => "privilege.list.html", "operator.add" => "operator.add.html", "operator.edit" => "operator.edit.html", "operator.view" => "operator.view.html", "operator.list" => "operator.list.html", "role.add" => "role.add.html", "role.edit" => "role.edit.html", "role.view" => "role.view.html", "role.list" => "role.list.html", "group.add" => "group.add.html", "group.edit" => "group.edit.html", "group.view" => "group.view.html", "group.list" => "group.list.html", "soapadmin.add" => "soapadmin.add.html", "soapadmin.edit" => "soapadmin.edit.html", "soapadmin.view" => "soapadmin.view.html", "soapadmin.list" => "soapadmin.list.html", "setting.edit" => "sys.setting.html" ),
	"action-mappings" => array(
		"default" => array(
			"input" => "login.html",
			"scope" => "request",
			"type" => "com.member.admin.action.DefaultAction",
			"db" => true,
			"tpl" => true
		),
		"login" => array( "input" => "login.html", "attribute" => "LoginForm", "scope" => "request", "type" => "com.member.admin.action.LoginAction" ),
		"logout" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.LogoutAction" ),
		"main" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.MainAction" ),
		"user" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.UserAction" ),
		"changePass" => array( "forward" => "change_password.html" ),
		"changePassSubmit" => array( "input" => "change_password.html", "attribute" => "ChangePassForm", "scope" => "request", "type" => "com.member.admin.action.ChangePassAction" ),
		"userProperty" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.UserPropertyAction" ),
		"oas" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.OASAction" ),
		"resource" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.ResourceAction" ),
		"privilege" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.PrivilegeAction" ),
		"operator" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.OperatorAction" ),
		"role" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.RoleAction" ),
		"group" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.GroupAction" ),
		"soapadmin" => array( "input" => "soapadmin.list.html", "scope" => "request", "type" => "com.member.admin.action.SoapAdminAction" ),
		"setting" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.admin.action.SettingAction" ),
		"resetPass" => array( "type" => "com.member.admin.action.ResetPass" )
	),
	"before-filter" => array(
		"com.member.admin.filter.DefaultBeforeFilter" => array( )
	),
	"after-filter" => array(
		"com.member.admin.filter.DefaultAfterFilter" => array(
			"params" => array( "par1" => 1, "par2" => 2 ),
			"filter-mapping-action" => array( "Login", "", "" )
		)
	)
);
?>
