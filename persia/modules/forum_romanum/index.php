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
//require_once("config.php");
// -------------------------------------------------------------------------------------------
//
// Redirect to the choosen pagecontroller.
//
$currentDir = dirname(__FILE__) . '/';
global $gPage;

switch($gPage) {

//
// Forum Romanum
//
case 'home': require_once($currentDir . 'PIndex.php'); break;
case 'topics': require_once($currentDir . 'PTopics.php'); break;
case 'topic': require_once($currentDir . 'PTopicShow.php'); break;
case 'post-edit': require_once($currentDir . 'PPostEdit.php'); break;
case 'post-save': require_once($currentDir . 'PPostSave.php'); break;

//
// Login, logout
//
case 'login': require_once(TP_PAGESPATH . 'login/PLogin.php'); break;
case 'loginp': require_once(TP_PAGESPATH . 'login/PLoginProcess.php'); break;
case 'logoutp': require_once(TP_PAGESPATH . 'login/PLogoutProcess.php'); break;
    //
    // User, profile and settings
    //
    
    // Create new account
    case 'account-create':      require_once(TP_PAGESPATH . 'account/PAccountCreate.php'); break;
    case 'account-createp':    require_once(TP_PAGESPATH . 'account/PAccountCreateProcess.php'); break;

    // Maintain account profile
    case 'account-settings':            require_once(TP_PAGESPATH . 'account/PAccountSettings.php'); break;
    case 'account-update':                require_once(TP_PAGESPATH . 'account/PAccountSettingsProcess.php'); break;
    // Process for aid with resetting password
    case 'account-forgot-pwd1':        require_once(TP_PAGESPATH . 'account/PAccountForgotPassword1.php'); break;
    case 'account-forgot-pwd1p':        require_once(TP_PAGESPATH . 'account/PAccountForgotPassword1Process.php'); break;
    case 'account-forgot-pwd2':        require_once(TP_PAGESPATH . 'account/PAccountForgotPassword2.php'); break;
    case 'account-forgot-pwd2p':    require_once(TP_PAGESPATH . 'account/PAccountForgotPassword2Process.php'); break;
    case 'account-forgot-pwd3':        require_once(TP_PAGESPATH . 'account/PAccountForgotPassword3.php'); break;
    case 'account-forgot-pwd3p':    require_once(TP_PAGESPATH . 'account/PAccountForgotPassword3Process.php'); break;
    case 'account-forgot-pwd4':        require_once(TP_PAGESPATH . 'account/PAccountForgotPassword4.php'); break;


//
// Using common files from modules/core
//
case 'ls': require_once(TP_MODULESPATH . '/core/viewfiles/PListDirectory.php'); break;

//
// Default case, trying to access some unknown page, should present some error message
// or show the home-page
//
default: require_once(TP_MODULESPATH . '/core/home/P404.php'); break;
}


?>
