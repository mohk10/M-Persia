<?php
// ===========================================================================================
//
// PMakeNote.php
//
// An implementation of a PHP pagecontroller for a web-site.
//
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
$displayAs = $pc->GETisSetOrSetDefault('pc_display', '');


// -------------------------------------------------------------------------------------------
//
//  Allow only access to pagecontrollers through frontcontroller
//
//if(!isset($indexIsVisited)) die('No direct access to pagecontroller is allowed.');


// -------------------------------------------------------------------------------------------
//
// Take care of _GET variables. Store them in a variable (if they are set).
//
$idComment = isset($_GET['idComment']) ? $_GET['idComment'] : '';

$titleComment = isset($_GET['titleComment']) ? $_GET['titleComment'] : '';

$numbersNote = isset($_GET['numbersNote']) ? $_GET['numbersNote'] : '';

if(!is_numeric($idComment)) {
    die("idComment måste vara ett integer. Försök igen.");
}


$html = <<<EOD
<h2>Kommentera</h2>
EOD;



// -------------------------------------------------------------------------------------------
//
// Javascript to validate email.Form to create the note
//
$html .= <<<EOD
<script type='text/javascript'>
function skicka_Click() {
	/*var nam = document.matain.namn.value;*/
	//alert("skicka_OnClick");
	var title=document.forms[0][2].value;
	var text=document.forms[0][3].value;
	/*var epo = document.matain.epost.value;*/
        var epost=document.forms[0][4].value;	
	if((title.length != 0) && (text.length != 0) && (epost.length != 0)) {
		if(validate_email(epost)==false){
			return false;}
			else{
				return true;
			}
	} else {
		alert("Du glömde att fylla i titel, text eller epost");
		return false;
	}
}
function validate_email(emailstr){
		apos=emailstr.indexOf("@");
		dotpos=emailstr.lastIndexOf(".");
		if(apos<1||dotpos-apos<2){
			alert("Epost adress är felaktig");
			return false;
		}
		else{
			return true;
		}
}
</script>
<fieldset>
<legend><strong>Kommentera {$titleComment}</strong></legend>
<form action='?m=blog&amp;p=kommenterap' method='POST' onsubmit="return skicka_Click();">
<input type='hidden' name='Note_idComment' value='{$idComment}'>
<input type='hidden' name='redirect' value='visabloggar&amp;idComment={$idComment}'>
<table>
<tr>
<td>Titel:</td><td> <input type='text' size='40' name='titleNote' /></td>
</tr>
<tr>
<td>Text:</td>
<td><textarea rows='6' cols='80' name='textNote'>
</textarea></td>
</tr>
<tr>
<td>Email:</td><td><input type='text' size='40' name='emailNote' /></td>
</tr>

<tr>
<td colspan='2'>
<input name='back' value='Tillbaka' type='button' onClick='history.back();' />
<input name='undo' value='Återställ' type='reset' />
<input name='save' value='Spara' type='submit' />
</td>
</tr>
</table>
</form>
</fieldset>
EOD;


// -------------------------------------------------------------------------------------------
//
// Finns det några kommentarer?
//

if($numbersNote!=-1){
  $html .= <<< EOD
<p>
Det finns {$numbersNote} kommentarer  för detta inlägg.
</p>
EOD;
}else{
}


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


$page->printHTMLHeader('Visa inlägg med kommentarer');
$page->printPageHeader('Fogglers blogg');
$page->printPageBody($html);
$page->printPageFooter();
?>
