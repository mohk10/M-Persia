<?php
// ===========================================================================================
//
// index.php
//
// Modulecontroller. An implementation of a PHP module frontcontroller (module controller).
// This page is called from the global frontcontroller. Its function could be named a
// sub-frontcontroller or module frontcontroller. I call it a modulecontroller.
//
// All requests passes through this page, for each request a pagecontroller is choosen.
// The pagecontroller results in a response or a redirect.
//
// Author: Mikael Roos, mos@bth.se
//

// -------------------------------------------------------------------------------------------
//
// Redirect to the choosen pagecontroller.
//
//$currentDir = dirname(__FILE__) . '/';
global $gPage;

switch($gPage) {

//
// The home-page
//
case 'home': require_once(TP_PAGESPATH . 'home/PIndex.php'); break;
case 'about': require_once(TP_PAGESPATH . 'home/PAbout.php'); break;
case 'template': require_once(TP_PAGESPATH . 'home/PTemplate.php'); break;
case 'NOT_EXISTING': require_once(TP_PAGESPATH . 'home/P404.php'); break;
	
//
// Me
//
case 'myme': require_once(TP_MEPATH . 'hem.php'); break;
case 'redovisn': require_once(TP_MEPATH . 'redovisning.php'); break;
case 'features': require_once(TP_MEPATH . 'features.php'); break;
case 'mydownload': require_once(TP_MEPATH . 'my_download_page.php'); break;
	

//
// Install database
//
case 'install': require_once(TP_PAGESPATH . 'install/PInstall.php'); break;
case 'installp': require_once(TP_PAGESPATH . 'install/PInstallProcess.php'); break;

//
// Login, logout
//
case 'login': require_once(TP_PAGESPATH . 'login/PLogin.php'); break;
case 'loginp': require_once(TP_PAGESPATH . 'login/PLoginProcess.php'); break;
case 'logoutp': require_once(TP_PAGESPATH . 'login/PLogoutProcess.php'); break;

//
// Administration
//
case 'admin': require_once(TP_PAGESPATH . 'admin_users/PUsersList.php'); break;

//
// User Profile
//
//case 'account-details': require_once(TP_PAGESPATH . 'account/PAccount.php'); break;
//case 'style-details': require_once(TP_PAGESPATH . 'style/PStyle.php'); break;
//
//case 'settingsprofile': require_once(TP_PAGESPATH . 'userprofile/PProfileShow.php'); break;
//case 'account': require_once(TP_PAGESPATH . 'account/PAccount.php'); break;
//
// User, profile and settings
//
case 'account-create': require_once(TP_PAGESPATH . 'account/PAccountCreate.php'); break;
case 'account-createp': require_once(TP_PAGESPATH . 'account/PAccountCreateProcess.php'); break;
case 'account-settings': require_once(TP_PAGESPATH . 'account/PAccountSettings.php'); break;
case 'account-update': require_once(TP_PAGESPATH . 'account/PAccountSettingsProcess.php'); break;
// Process for aid with resetting password
    case 'account-forgot-pwd1':        require_once(TP_PAGESPATH . 'account/PAccountForgotPassword1.php'); break;
    case 'account-forgot-pwd1p':        require_once(TP_PAGESPATH . 'account/PAccountForgotPassword1Process.php'); break;
    case 'account-forgot-pwd2':        require_once(TP_PAGESPATH . 'account/PAccountForgotPassword2.php'); break;
    case 'account-forgot-pwd2p':    require_once(TP_PAGESPATH . 'account/PAccountForgotPassword2Process.php'); break;
    case 'account-forgot-pwd3':        require_once(TP_PAGESPATH . 'account/PAccountForgotPassword3.php'); break;
    case 'account-forgot-pwd3p':    require_once(TP_PAGESPATH . 'account/PAccountForgotPassword3Process.php'); break;
    case 'account-forgot-pwd4':        require_once(TP_PAGESPATH . 'account/PAccountForgotPassword4.php'); break;


//
// Directory listning
//
case 'ls': require_once(TP_PAGESPATH . 'viewfiles/PListDirectory.php'); break;

//
// Article (Forum Romanum 0.1 (0.01))
//
//case 'article-edit': require_once(TP_PAGESPATH . 'forum/PArticleEdit.php'); break;
case 'article-save': require_once(TP_PAGESPATH . 'forum/PArticleSave.php'); break;
case 'article-delete': require_once(TP_PAGESPATH . 'forum/PArticleDelete.php'); break;
case 'article-show': require_once(TP_PAGESPATH . 'forum/PArticleShow.php'); break;
case 'topics': require_once(TP_PAGESPATH . 'forum/PTopics.php'); break;
case 'topic': require_once(TP_PAGESPATH . 'forum/PTopicShow.php'); break;
case 'post-edit': require_once(TP_PAGESPATH . 'forum/PPostEdit.php'); break;

// Testing JavaScript editors
case 'article-edit': require_once(TP_PAGESPATH . 'forum/jseditors/PArticleEdit.php'); break;
case 'article-edit-all': require_once(TP_PAGESPATH . 'forum/jseditors/PArticleEditAll.php'); break;
case 'article-nicedit': require_once(TP_PAGESPATH . 'forum/jseditors/PArticleEdit_NicEdit.php'); break;
case 'article-wymeditor': require_once(TP_PAGESPATH . 'forum/jseditors/PArticleEdit_WYMeditor.php'); break;
case 'article-markitup': require_once(TP_PAGESPATH . 'forum/jseditors/PArticleEdit_markItUp.php'); break;

//
// Style Your Web, app_syw
// Example for working with stylesheets
//
/*
case 'install': require_once(TP_PAGESPATH . 'app_syw/install/PInstall.php'); break;
case 'installp': require_once(TP_PAGESPATH . 'app_syw/install/PInstallProcess.php'); break;
case 'home': require_once(TP_PAGESPATH . 'app_syw/PIndex.php'); break;
case 'style': require_once(TP_PAGESPATH . 'app_syw/PShowStyle.php'); break;
case 'minwidth': require_once(TP_PAGESPATH . 'app_syw/PMinWidth.php'); break;
case 'centered': require_once(TP_PAGESPATH . 'app_syw/PCentered.php'); break;
case '2cols': require_once(TP_PAGESPATH . 'app_syw/P2Columns.php'); break;
case '3cols': require_once(TP_PAGESPATH . 'app_syw/P3Columns.php'); break;
case '123cols': require_once(TP_PAGESPATH . 'app_syw/P123Columns.php'); break;
case 'liquid': require_once(TP_PAGESPATH . 'app_syw/P123Liquid.php'); break;
*/

//
// Rate My Professor, app_rmp
// Show, add, edit, delete professors
//
/*
case 'home': require_once(TP_PAGESPATH . 'app_rmp/PIndex.php'); break;
case 'install': require_once(TP_PAGESPATH . 'app_rmp/install/PInstall.php'); break;
case 'installp': require_once(TP_PAGESPATH . 'app_rmp/install/PInstallProcess.php'); break;
case 'visalarare': require_once(TP_PAGESPATH . 'app_rmp/PVisaLarare.php'); break;
case 'insertlarare': require_once(TP_PAGESPATH . 'app_rmp/PInsertLarare.php'); break;
case 'deletelarare': require_once(TP_PAGESPATH . 'app_rmp/PDeleteLarare.php'); break;
case 'editlarare': require_once(TP_PAGESPATH . 'app_rmp/PEditLarareInfo.php'); break;
case 'editlararep': require_once(TP_PAGESPATH . 'app_rmp/PEditLarareInfoProcess.php'); break;
case 'visalararebetyg': require_once(TP_PAGESPATH . 'app_rmp/PVisaLarareBetyg.php'); break;
case 'kommentera': require_once(TP_PAGESPATH . 'app_rmp/PSattBetygLarare.php'); break;
case 'kommenterap': require_once(TP_PAGESPATH . 'app_rmp/PSattBetygLarareProcess.php'); break;
*/

/*
//
// Blog
//
case 'install': require_once(TP_PAGESPATH . 'blog/install/PInstall.php'); break;
case 'installp': require_once(TP_PAGESPATH . 'blog/install/PInstallProcess.php'); break;
case 'home': require_once(TP_PAGESPATH . 'blog/PHome.php'); break;
case 'post': require_once(TP_PAGESPATH . 'blog/PPost.php'); break;
case 'poste': require_once(TP_PAGESPATH . 'blog/PPostEdit.php'); break;
*/

//
// Default case, trying to access some unknown page, should present some error message
// or show the home-page
//
default: require_once(TP_PAGESPATH . 'home/P404.php'); break;
}


?>
