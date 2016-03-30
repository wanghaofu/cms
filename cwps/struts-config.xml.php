<?php
$STRUTS_CONFIG = array(
	"entrance" => "index.php",
	"message-resources" => array(
		"language-dir" => "language",
		"language-package" => "chinese_gb",
		"sys-messages" => array( "charset.inc.php", "lang_user.php" ),
		"template-messages-package" => "lang_skin"
	),
	"template-resources" => "templates",
	"template-forward-action" => "url_forward.html",
	"form-beans" => array(
		"LoginForm" => array(
			"type" => "com.member.form.LoginForm",
			"form-property" => array( "UserName" => "string", "Password" => "string", "validCode" => "string", "CookieTime" => "int" )
		),
		"RegisterForm" => array(
			"type" => "com.member.form.RegisterForm",
			"form-property" => array(
				"UserName" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.notEmpty" ),
				"Password" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.notEmpty" ),
				"Password2" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.notEmpty" ),
				"Email" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.isEmail" ),
				"Gender" => "int",
				"Year" => "int",
				"Month" => "int",
				"Day" => "int",
				"QQ" => "string",
				"NickName" => "string",
				"Description" => "string"
			)
		),
		"ChangePassForm" => array(
			"type" => "com.member.form.ChangePassForm",
			"form-property" => array(
				"OldPassword" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.notEmpty" ),
				"NewPassword" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.notEmpty" ),
				"NewPassword2" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.notEmpty" )
			)
		),
		"EditProfileSubmitForm" => array(
			"type" => "super",
			"form-property" => array(
				"Email" => array( "type" => "string", "validator" => "com.member.utils.StringUtils.isEmail" ),
				"Gender" => "int",
				"Year" => "int",
				"Month" => "int",
				"Day" => "int",
				"QQ" => "string",
				"NickName" => "string",
				"Description" => "string"
			)
		),
		"OnlineListForm" => array(
			"type" => "super",
			"form-property" => array( "o" => "string" )
		),
		"AjaxForm" => array(
			"type" => "super",
			"form-property" => array( "o" => "string", "TargetUserID" => "int" )
		)
	),
	"global-forwards" => array( "login" => "login.html", "default" => "main.html", "main" => "main.html", "editprofile" => "editprofile.html", "viewprofile" => "viewprofile.html", "getPass" => "getPass.html", "getPass.newPass" => "getPass.newPass.html", "onlinelist" => "chat/onlinelist.html", "ajax_viewChatMsg" => "chat/ajax_msg_list.xml" ),
	"action-mappings" => array(
		"default" => array(
			"input" => "login.html",
			"scope" => "request",
			"type" => "com.member.action.DefaultAction",
			"db" => true,
			"tpl" => true
		),
		"isLogin" => array(
			"input" => "login.html",
			"scope" => "request",
			"type" => "com.member.action.IsLoginAction",
			"db" => true
		),
		"main" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.action.MainAction" ),
		"login" => array( "input" => "login.html", "attribute" => "LoginForm", "scope" => "request", "type" => "com.member.action.LoginAction" ),
		"logout" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.action.LogoutAction" ),
		"register" => array( "forward" => "register.html", "type" => "com.member.action.RegisterInitAction" ),
		"changePass" => array( "forward" => "change_password.html", "type" => "com.member.action.InitSessionAction" ),
		"registerSubmit" => array( "input" => "register.html", "attribute" => "RegisterForm", "scope" => "request", "type" => "com.member.action.RegisterAction" ),
		"changePassSubmit" => array( "input" => "change_password.html", "attribute" => "ChangePassForm", "scope" => "request", "type" => "com.member.action.ChangePassAction" ),
		"editProfile" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.action.EditProfileAction" ),
		"viewProfile" => array( "input" => "login.html", "scope" => "request", "type" => "com.member.action.ViewProfileAction" ),
		"editProfileSubmit" => array( "input" => "editprofile.html", "attribute" => "EditProfileSubmitForm", "scope" => "request", "type" => "com.member.action.EditProfileSubmitAction" ),
		"getPass" => array( "input" => "getPass.html", "type" => "com.member.action.GetPass" ),
		"inputMsg" => array( "forward" => "chat/inputMsg.html", "type" => "com.member.action.InitSessionAction" ),
		"onlineList" => array( "input" => "login.html", "attribute" => "OnlineListForm", "scope" => "request", "type" => "com.member.action.OnlineListAction" ),
		"OASLoginReq" => array( "input" => "login.html", "attribute" => "AjaxForm", "scope" => "request", "type" => "com.member.action.AjaxAction" )
	),
	"before-filter" => array(
		"com.member.filter.DefaultBeforeFilter" => array( )
	),
	"after-filter" => array(
		"com.member.filter.DefaultAfterFilter" => array(
			"params" => array( "par1" => 1, "par2" => 2 ),
			"filter-mapping-action" => array( "Login", "", "" )
		)
	)
);
?>
