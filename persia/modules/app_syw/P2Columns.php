<?php
// ===========================================================================================
//
// P2Columns.php
//
// Showing example on a 2 column layout page.
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

$htmlLeft = <<<EOD
<h2>2-kolumners layout</h2>
<p>
Denna sidan använder en stylesheet som stödjer 2 kolumner. Dessutom är CHTMLPage uppdaterad för att klara 
2-kolumners layout.
</p>
<p>
Stylesheeten ser du här: <a href='?p=style&dir=&file=original_2cols.css'>original_2cols.css</a>.
</p>
<p>
CHTMLPage ser du här: <a href='?p=ls&dir=src&file=CHTMLPage.php'>CHTMLPage.php</a>.
</p>
<p>
P2Columns ser du här: <a href='?p=ls&dir=pages/app_syw&file=P2Columns.php'>P2Columns.php</a>.
</p>
EOD;

$htmlRight = <<<EOD
<h3 class='menu'>Bra att ha</h3>
<p>
Här finns nu en meny kolumn som går att använda till bra att ha saker.
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$style = 'pages/app_syw/stylesheets/original_2cols.css';

$page = new CHTMLPage($style);

$page->addPageBodyLeft($htmlLeft);
$page->addPageBodyRight($htmlRight);

$page->printHTMLHeader('2 kolumner');
$page->printPageHeader();
$page->printPageBody();
$page->printPageFooter();
exit;
 
?>
