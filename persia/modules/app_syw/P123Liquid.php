<?php
// ===========================================================================================
//
// P123Liquid.php
//
// Showing example of a flexible 1, 2, 3 liquid column layout page.
//


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
if(!isset($indexIsVisited)) die('No direct access to pagecontroller is allowed.');


// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$left 	= isset($_GET['left']) 	? $_GET['left'] 	: '';
$right 	= isset($_GET['right']) ? $_GET['right'] 	: '';


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$htmlMain = <<<EOD
<h2>123-kolumners flytande layout</h2>
<p>
Denna sidan använder en stylesheet som stödjer både 1, 2 och 3 kolumner. Stylesheeten är byggt för 
att enligt flytande layout och med hjälp av negativa marginaler. CHTMLPage uppdaterad för att klara 
denna layout.
</p>
<p>
Stylesheeten ser du här: <a href='?p=style&dir=&file=original_123liquid.css'>original_123liquid.css</a>.
</p>
<p>
CHTMLPage ser du här: <a href='?p=ls&dir=src&file=CHTMLPage.php'>CHTMLPage.php</a>.
</p>
<p>
P123Liquid ser du här: <a href='?p=ls&dir=pages/app_syw&file=P123Liquid.php'>P123Liquid.php</a>.
</p>
<p>
<a href='?p=liquid'>Visa alla 3 kolumnerna</a>.
</p>
<p>
<a href='?p=liquid&amp;right=hide'>Visa 2 kolumner, vänster</a>.
</p>
<p>
<a href='?p=liquid&amp;left=hide'>Visa 2 kolumner, höger</a>.
</p>
<p>
<a href='?p=liquid&amp;left=hide&amp;right=hide'>Visa endast denna kolumn</a>.
</p>
EOD;

$htmlLeft = <<<EOD
<h3 class='menu'>Bra att ha vänster</h3>
<p>
Här finns nu en meny kolumn som går att använda till bra att ha saker.
</p>
<p>
<a href='?p=liquid&amp;left=hide'>Göm denna kolumn</a>.
</p>
EOD;

$htmlRight = <<<EOD
<h3 class='menu'>Bra att ha höger</h3>
<p>
Här finns nu en meny kolumn som går att använda till bra att ha saker.
</p>
<p>
<a href='?p=liquid&amp;right=hide'>Göm denna kolumn</a>.
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$style = 'pages/app_syw/stylesheets/original_123liquid.css';

$page = new CHTMLPage($style);


if($left != 'hide') {
	$page->addPageBodyLeft($htmlLeft);
}
if($right != 'hide') {
	$page->addPageBodyRight($htmlRight);
}
$page->addPageBodyMain($htmlMain);

$page->printHTMLHeader('123-kolumners liquid layout med negativa marginaler');
$page->printPageHeader();
$page->printPageBody();
$page->printPageFooter();
exit;
 
?>
