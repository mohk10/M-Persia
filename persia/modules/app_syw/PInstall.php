<?php
// ===========================================================================================
//
// PInstall.php
//
// Info page for installation. Links to page for creating tables in the database.
//


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
if(!isset($indexIsVisited)) die('No direct access to pagecontroller is allowed.');


// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$database = DB_DATABASE;
$prefix	= DB_PREFIX;

$html = <<<EOD
<h2>Installation</h2>
<h3>Skapa tabeller</h3>
<p>
Klicka på nedanstående länk för att radera databasen på allt innehåll och skapa nya tabeller. 
Du har valt databasen '{$database}' och tabellerna kommer skapas med prefixet '{$prefix}'. Ändra i
config.php om detta inte stämmer.
</p>
<p>
<a href='?p=installp'>Töm databasen och skapa nya tabeller</a>.
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$page = new CHTMLPage();

$page->printHTMLHeader('Installation');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();

 
?>
