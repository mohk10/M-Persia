<?php
// ===========================================================================================
//
// PTemplate.php
//
// A standard template page for a pagecontroller.
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

$html = <<<EOD
<h2>Hem</h2>
<h3>Stylesheets och CSS</h3>
<p>
En liten webbapp för att testa lite olika stylesheets. 
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$page = new CHTMLPage();

$page->printHTMLHeader('Hem');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();
exit;
 
?>
