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
$img = WS_IMAGES;

$html = <<<EOD
<div>
<div style='background: url("img/700px-Roman_forum_cropped.jpg") no-repeat center ; height: 100px;'>
</div>
<p class='small'>
Picture from: http://en.wikipedia.org/wiki/File:Forum_Romanum_Rom.jpg
</p>
</div>

<h1>Forum Romanum</h1>
<h2>A Persia Forum</h2>
<p>
According to Wikipedia, a forum is:
</p>

<p class='intendent'>
"The forum served as a city square and central hub where the people of Rome gathered for justice, and faith. The forum was also the economic hub of the city and considered to be the center of the Republic and Empire."
<br>
<br>
<a class='mya' href='http://en.wikipedia.org/wiki/Roman_Forum'>http://en.wikipedia.org/wiki/Roman_Forum</a>
</p>

<p>
Forum Romanum is the name of a Persia Forum. A forum built upon Persia.
<p>

</p>
EOD;

require_once(TP_SOURCEPATH.'CHTMLPage3.php');
$stylesheet = WS_STYLESHEET2;

$page = new CHTMLPage3($stylesheet);
// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
//$page = new CHTMLPage();

//$page->PrintPage('Forum Romanum Mission Statement', "", $html, "");
//exit;
$name="Forum Romanum Mission Statement";




$page->printHTMLHeader($name);
$page->printPageHeader2($name);
$page->printStartSection();
$page->printPageBody($html);
$page->printCloseSection();
//$page->printAside();
$page->printPageFooter();
 
?>



