<?php
// ===========================================================================================
//
// PIndex.php
//
// The home-page
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = new CPageController();
//$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = new CInterceptionFilter();

$intFilter->FrontControllerIsVisitedOrDie();
//$intFilter->UserIsSignedInOrRecirectToSignIn();
//$intFilter->UserIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$html = <<<EOD
<h1>Welcome</h1>
<p>
This is the index-page (page/home/PIndex.php). M-Persia is a student project(by Morgan Hagberg) and modified version of Mikael Roos's Persia on course dbwebb2 at Blekinge Tekniska Högskola. Change it to get going. Review the PTemplate.php
(page/home/PTemplate.php) for a more complete pagecontroller.
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage('Index (change this)', "", $html, "");
exit;

 
?>