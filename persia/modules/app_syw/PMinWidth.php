<?php
// ===========================================================================================
//
// PMinWidth.php
//
// Showing info on stylesheet using min width.
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
<h2>Minimum bredd</h2>
<p>
Denna sidan använder en stylesheet som har en minimum bredd definerad. Pröva att förstora och 
förminska webbläsaren så ser du.
</p>
<p>
Stylesheeten ser du här: <a href='?p=style&dir=&file=original_minwidth.css'>original_minwidth.css</a>.
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$style = 'pages/app_syw/stylesheets/original_minwidth.css';

$page = new CHTMLPage($style);

$page->printHTMLHeader('Minimum width');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();
exit;
 
?>
