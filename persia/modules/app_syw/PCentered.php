<?php
// ===========================================================================================
//
// PCentered.php
//
// Showing info on stylesheet using centered and fixed width.
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
<h2>Centrerad och fast bredd</h2>
<p>
Denna sidan använder en stylesheet som är centrerad och har en fast bredd definerad. Pröva att förstora och 
förminska webbläsaren så ser du.
</p>
<p>
Stylesheeten ser du här: <a href='?p=style&dir=&file=original_centered.css'>original_centered.css</a>.
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$style = 'pages/app_syw/stylesheets/original_centered.css';

$page = new CHTMLPage($style);

$page->printHTMLHeader('Minimum width');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();
exit;
 
?>
