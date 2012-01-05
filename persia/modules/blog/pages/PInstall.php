<?php
// ===========================================================================================
//
//
// An implementation of a PHP pagecontroller for a web-site.
//
// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
require_once(TP_SOURCEPATH . 'CPageController.php');

$pc = new CPagecontroller();


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
require_once(TP_SOURCEPATH . 'CInterceptionFilter.php');

$intFilter = new CInterceptionFilter();

$intFilter->frontcontrollerIsVisitedOrDie();
//$intFilter->userIsSignedInOrRecirectToSign_in();
//$intFilter->userIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of global pageController settings, can exist for several pagecontrollers.
// Decide how page is displayed, review CHTMLPage for supported types.
//
//$displayAs = $pc->GETisSetOrSetDefault('pc_display', '');


// -------------------------------------------------------------------------------------------
//
//  Allow only access to pagecontrollers through frontcontroller
//
//if(!isset($indexIsVisited)) die('No direct access to pagecontroller is allowed.');

// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$database = DB_DATABASE;
$prefix  = DB_PREFIX;

// -------------------------------------------------------------------------------------------
//
// html
//
$html = <<<EOD
<h2>Installation</h2>
<h3>Skapa tabeller</h3>
<p>
Klicka pånedanstående länk för att radera databasen på allt innehåll och skapa nya tabeller. Du har valt databasen
{$database} och tabellerna kommer att skapas med prefixet {$prefix}. Ändra i config.php om detta inte stämmer.
</p>
<a href='?p=installp2&amp;idAuthor={$_SESSION['idAuthor']}'>Töm databasen och skapa nya tabeller</a>
EOD;

// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
//require_once(TP_SOURCEPATH.'CHTMLPage.php');

//$page = new CHTMLPage();
require_once($currentDir .'src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);

$page->printHTMLHeader('Installation');
$page->printPageHeader('Fogglers blogg');
$page->printStartSection('commentandnotes');
$page->printPageBody($html);
$page->printCloseSection();
$page->printPageFooter();
?>
