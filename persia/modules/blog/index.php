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
  // Show, add, edit, delete professors
  //  	  
  case 'visabloggar':  require_once($currentDir .'pages/PShowAllBlogs.php'); break; 
  case 'visanamnblogg':  require_once($currentDir .'pages/PShowNameBlog.php'); break;
  case 'visanamnbloggedit':  require_once($currentDir .'pages/PShowNameBlogEdit.php'); break; 
  case 'visatagg':  require_once($currentDir .'pages/PShowTag.php'); break;  	  
  case 'visaprofil':  require_once($currentDir .'pages/PProfileShow.php'); break; 	  
  case 'gorakommentar':  require_once($currentDir .'pages/PMakeNote.php'); break;
  case 'editinlagg':  require_once($currentDir .'pages/PEditComment.php'); break;
  case 'editinlaggp':  require_once($currentDir .'pages/PEditCommentProcess.php'); break;
  case 'visainlaggkommentar':  require_once($currentDir .'pages/PShowCommentAndNotes.php'); break;	 
  case 'kommentera':  require_once($currentDir .'pages/PMakeNote.php'); break;
  case 'kommenterap':  require_once($currentDir .'pages/PMakeNoteProcess.php'); break; 
  case 'nyttinlagg':  require_once($currentDir .'pages/PMakeComment.php'); break; 
  case 'nyttinlaggp':  require_once($currentDir .'pages/PMakeCommentProcess.php'); break; 
  case 'deletenote':  require_once($currentDir .'pages/PDeleteNote.php'); break;   
  case 'deleteinlaggkommentar':  require_once($currentDir .'pages/PDeleteCommentAndNotes.php'); break;   	  
  case 'rss10':  require_once($currentDir .'pages/PRSS1.0.php'); break;
//
// Forum Romanum
//
//case 'home': require_once($currentDir . 'PIndex.php'); break;
//case 'topics': require_once($currentDir . 'PTopics.php'); break;
//case 'topic': require_once($currentDir . 'PTopicShow.php'); break;
//case 'post-edit': require_once($currentDir . 'PPostEdit.php'); break;
//case 'post-save': require_once($currentDir . 'PPostSave.php'); break;
	case 'install2':		require_once($currentDir . 'pages/PInstall.php'); break;
	case 'installp2':	require_once($currentDir . 'pages/PInstallProcess.php'); break;
//
// Login, logout
//
case 'login2': require_once($currentDir. 'pages/login/PLogin.php'); break;
case 'loginp2': require_once($currentDir . 'pages/login/PLoginProcess.php'); break;
case 'logoutp2': require_once($currentDir . 'pages/login/PLogoutProcess.php'); break;

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
