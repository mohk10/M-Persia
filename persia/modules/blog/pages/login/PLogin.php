<?php
// ===========================================================================================
//
// PLogin.php
//
// Show a login-form, ask for user name and password.
//

// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$rdirect     = isset($_GET['r'])     ? $_GET['r']     : 'home';
//$rdirect='visanamnbloggedit';
//echo($rdirect);
$html = <<<EOD
<h2>Logga in</h2>
<p>
Ange din användare och lösenord för att logga in <em>(henke - henke, petter- petter,tobbe - tobbe eller danne-danne)</em>. 
</p>
<fieldset>
<legend>Logga in</legend>
<form action="?m=blog&amp;p=loginp2" method="post">
<input type='hidden' name='redirect' value='{$rdirect}'>
<table>
<tr>
<td style="text-align: right">
<label for="accountAuthor">Användare:</label>
</td>
<td>
<input id="accountAuthor"  type="text" name="accountAuthor">
</td>
</tr>
<tr>
<td style="text-align: right">
<label for="passwordAuthor">Lösenord:</label>
</td>
<td>
<input id="passwordAuthor" class="password" type="password" name="passwordAuthor">
</td>
</tr>
<tr>
<td colspan='2' style="text-align: right"><button type="submit" name="submit">Logga in</button></td>
</tr>
</table>
</form>
</fieldset>
EOD;
if(isset($_SESSION['errorMessage'])&&(!isset($_SESSION['accountAuthor']))){
$html.="<div class='errorMessage'>
{$_SESSION['errorMessage']}
</div>";
unset($_SESSION['errorMessage']);
}

// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
//require_once(TP_SOURCEPATH . 'CHTMLPage.php');

//$page = new CHTMLPage();
require_once($currentDir.'src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);

$page->printHTMLHeader('Logga in');
$page->printPageHeader('Fogglers blogg');
$page->printPageBody($html);
$page->printPageFooter();
?>
