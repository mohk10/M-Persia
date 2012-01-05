<?php
// ===========================================================================================
//
// Class CHTMLPage
//
// Creating and printing out a HTML page.
//

class CHTMLPage {
  // ------------------------------------------------------------------------------------
  //
  // Internal variables
  //
  protected $iMenu;
  protected $iStylesheet;

  // ------------------------------------------------------------------------------------
  //
  // Constructor
  //
  public function __construct($aStylesheet = WS_STYLESHEET) {
    $this->iStylesheet   = $aStylesheet;
    $this->iMenu    = unserialize(WS_MENU);
  }

  // ------------------------------------------------------------------------------------
  //
  // Destructor
  //
  public function __destruct() {
    ;
  }

  // ------------------------------------------------------------------------------------
  //
  //
  //
  public function printHTMLHeader($aTitle) {
    $html = <<<EOD
<!doctype html>
<html lang=sv>
<head>
<meta charset=utf-8>
        <title>{$aTitle}</title>
        <link rel=stylesheet href='{$this->iStylesheet}'  type="text/css">
        <!-- om webbläsaren är under internet explorer 9 så fixar vi till html5-element -->
        <!--[if lt IE 9]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
</head>
EOD;

    echo $html;
  }

  // ------------------------------------------------------------------------------------
  //
  //
  //
  public function printPageHeader($aHeader = WS_TITLE) {
  
    $menu = "";
    foreach($this->iMenu as $key => $value) {
      $menu .= "<a href='{$value}'>{$key}</a> | ";
    }
    $menu = substr($menu, 0, -3);
    $htmlLoginMenu=$this->getLoginLogoutMenu();
    $html = <<<EOD
<body>
<!-- inloggning längst upp till höger -->
{$htmlLoginMenu}
<!-- rubrik, tagline och lite navigering -->
<header>
<h1>{$aHeader}</h1>
<h2>Fooglers publika blogg med nyheter om företaget</h2>
<nav>
{$menu}
</nav>
</header>
<!-- en kontainer som innehåller en sektion med blogginlägg (article) och en aside som högra kolumnen -->
<div class='content'>    
EOD;

    echo $html;  
  }
  // ------------------------------------------------------------------------------------
  //  Skriver ut taggen section och ger den eventuellt ett klassnamn.
  //
  //
public function printStartSection($aClass=""){
	echo("<section class='{$aClass}'>");
  } 
  // ------------------------------------------------------------------------------------
  // Skriver ut artikelkod för inlägg på startsidan.
  // Inparametrar tas från databas.
  //
  public function buildArticleCode($aId,$aTitle,$aNumbers,$aText,$aImage,$aTags,$aName,$aDate){
  	 
  	  $dayOfWeek=$this->GetDaySwedish($aDate);
  	 
  	   $aDate=substr($aDate,0,-3);
  	  
$html = <<<EOD
<article >
<h1>{$aTitle}({$aNumbers})</h1>
<p>
EOD;
// För att få html-validering att stämma.
$aTitle=str_replace(" ","%20",$aTitle);
 if(is_null($aImage)||($aImage=="")){
		$html.="{$aText}</p>";
}
else{
	$html.="<img src='{$aImage}'  />{$aText}</p>";
}

$html.=<<<EOD
<p><strong>Taggar:</strong> {$aTags}</p>
<a href='?p=gorakommentar&amp;idComment={$aId}&amp;titleComment={$aTitle}&amp;numbersNote={$aNumbers}'>Kommentera</a><br />
<a href='?p=visainlaggkommentar&amp;idComment={$aId}'>Visa kommentarer</a><br /><br />
<footer>Skrivet av {$aName} @ $dayOfWeek<time>{$aDate}</time></footer>
</article>
EOD;
	return $html;
  }
  // ------------------------------------------------------------------------------------
  // Skriver ut artikelkod för inlägg på PShowNameBlogEdit.php.
  // Inparametrar tas från databas.
  //
  public function buildArticleCodeEdit($aId,$aTitle,$aText,$aImage,$aTags,$aName,$aDate){
  	 
  	  $dayOfWeek=$this->GetDaySwedish($aDate);
  	  
  	   $aDate=substr($aDate,0,-3);
$html = <<<EOD
<article>
<h1>{$aTitle}</h1>
<p>
EOD;
if(is_null($aImage)||($aImage=="")){
		$html.="{$aText}</p>";
}
else{
	$html.="<img src='{$aImage}' />{$aText}</p>";
}
$html.=<<<EOD
<p><strong>Taggar:</strong> {$aTags}</p>
<a href='?p=editinlagg&amp;idComment={$aId}'>Editera inlägg</a><br />
<a href='?p=deleteinlaggkommentar&amp;idComment={$aId}&amp;idAuthor={$_SESSION['idAuthor']}' class='delete'>Ta bort inlägg(även kommentarer)</a><br /><br />
<footer>Skrivet av {$aName} @ $dayOfWeek<time>{$aDate}</time></footer>
</article>
EOD;
	return $html;
  } 
  // ------------------------------------------------------------------------------------
  // Skriver ut artikelkod för inlägg på PShowCommentAndNotes.php.
  // Inparametrar tas från databas.
  //
  public function buildArticleCode2($aTitle,$aText,$aImage,$aTags,$aDate){
  	 
  	  $dayOfWeek=$this->GetDaySwedish($aDate);
  	  
  	   $aDate=substr($aDate,0,-3);
$html = <<<EOD
<article>
<h1>{$aTitle}</h1>
<p>
EOD;
if(is_null($aImage)||($aImage=="")){
		$html.="{$aText}</p>";
}
else{
	$html.="<img src='{$aImage}' />{$aText}</p>";
}
$html.=<<<EOD
<p><strong>Taggar:</strong> {$aTags}</p>
<a href='javascript:history.back()'>Tillbaka</a><br /><br />
<footer>$dayOfWeek<time>{$aDate}</time></footer>
</article>
EOD;
	return $html;
  }
  // ------------------------------------------------------------------------------------
  // Skriver ut artikelkod för inlägg på PShowTag.php.
  // Inparametrar tas från databas.
  //
  public function buildArticleCode2Tag($aId,$aTitle,$aText,$aImage,$aTags,$aDate){
  	 
  	  $dayOfWeek=$this->GetDaySwedish($aDate);
  	  
  	   $aDate=substr($aDate,0,-3);
$html = <<<EOD
<article>
<h1>{$aTitle}</h1>
<p>
EOD;

$aTitle=str_replace(" ","%20",$aTitle);

if(is_null($aImage)||($aImage=="")){
		$html.="{$aText}</p>";
}
else{
	$html.="<img src='{$aImage}' />{$aText}</p>";
}
$html.=<<<EOD
<p><strong>Taggar:</strong> {$aTags}</p>
<a href='?p=gorakommentar&amp;idComment={$aId}&amp;titleComment={$aTitle}&amp;numbersNote=-1'>Kommentera</a><br />
<a href='?p=visainlaggkommentar&amp;idComment={$aId}'>Visa kommentarer</a><br /><br />
<a href='javascript:history.back()'>Tillbaka</a><br /><br />
<footer>$dayOfWeek<time>{$aDate}</time></footer>
</article>
EOD;
	return $html;
  }
  // ------------------------------------------------------------------------------------
  // Skriver ut artikelkod för kommentarer på startsidan.
  // Inparametrar tas från databas.
  //
 public function buildArticleCode3($aTitle,$aText,$aEmail,$aDate){
  	 
 	 $dayOfWeek=$this->GetDaySwedish($aDate);
  	  $nameArray=explode("@",$aEmail);
  	  $firstName=$nameArray[0];
  	   $aDate=substr($aDate,0,-3);
$html = <<<EOD
<article>
<h1 class='note'>{$aTitle}</h1>
<p>{$aText}</p>
<p><strong>Av: </strong> {$firstName}</p>
<a href='javascript:history.back()'>Tillbaka</a><br /><br />
<footer>$dayOfWeek<time>{$aDate}</time></footer>
</article>
EOD;
	return $html;
  } 
  // ------------------------------------------------------------------------------------
  // Skriver ut artikelkod för kommentarer på PShowNameBlogEdit.php.
  // Inparametrar tas från databas.
  //
   public function buildArticleCodeEdit3($aId,$aTitle,$aText,$aEmail,$aDate){
  	 
 	 $dayOfWeek=$this->GetDaySwedish($aDate);
  	  $nameArray=explode("@",$aEmail);
  	  $firstName=$nameArray[0];
  	   $aDate=substr($aDate,0,-3);
$html = <<<EOD
<article>
<h1 class='note'>{$aTitle}</h1>
<p>{$aText}</p>
<p><strong>Av:</strong> {$firstName}</p>
<a href="?p=deletenote&amp;idNote={$aId}&amp;idAuthor={$_SESSION['idAuthor']}" class='delete'>Ta bort</a><br /><br />
<footer>$dayOfWeek<time>{$aDate}</time></footer>
</article>
EOD;
	return $html;
  } 
  private function GetDaySwedish($aDate){
  	  $myDate=new DateTime($aDate);
  	  $dayOfWeek=$myDate->format("l");
  	  switch($dayOfWeek){
  	  case 'Monday':
  	  	  $dayOfWeek="Måndag";
  	  	  break;
  	  case 'Tuesday':
  	  	  $dayOfWeek="Tisdag";
  	  	  break;
  	  case 'Wednesday':
  	  	  $dayOfWeek="Onsdag";
  	  	  break;
  	  case 'Thursday':
  	  	  $dayOfWeek="Torsdag";
  	  	  break;
  	   case 'Friday':
  	  	  $dayOfWeek="Fredag";
  	  	  break;
  	   case 'Saturday':
  	  	  $dayOfWeek="Lördag";
  	  	  break;
  	   case 'Sunday':
  	  	  $dayOfWeek="Söndag";
  	  	  break;
  	   default:
  	  	  echo("Något gick fel vid dagomvandlingen");
  	  	  break;
  	  }
  	  return $dayOfWeek." ";
  }
  public function printCloseSection(){
  	  echo("</section>");
  }
  public function printAside($aFirstBox="",$aSecondBox="",$aThirdBox="",$aFourthBox=""){

  	  if(($aThirdBox=="")&&($aFourthBox=="")){ 
$html = <<<EOD
<aside>
{$aFirstBox}
{$aSecondBox}
</aside>
EOD;
	  }else{
$html = <<<EOD
<aside>
{$aFirstBox}
{$aSecondBox}
{$aThirdBox}
{$aFourthBox}
</aside>
EOD;
	  }



	echo $html;
  }
  // ------------------------------------------------------------------------------------
  //
  //
  //
  public function printPageBody($aBody) {

    $html = <<<EOD
{$aBody}
EOD;
    echo $html;
  }
  public function printPageFooter($aFooter=WS_FOOTER) {

    $html = <<<EOD
</div>
<!-- footer med länkar till validering och visa källa -->
<footer>
            <a href="http://validator.w3.org/check/referer">html</a>
            <a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">css</a>
            <a href="source.php">source</a>
            <small>{$aFooter}</small>
        </footer>
    </body>
</html>
EOD;

    echo $html;
  } 
// ------------------------------------------------------------------------------------
  //
  // Produces the login-menu, changes look if user is logged in or not
  //
private function getLoginLogoutMenu() {
  	  $htmlMenu="";
  	  // If user is logged in, show details about user and some links
  	  // TBD
  	  if(isset($_SESSION['accountAuthor'])){
$htmlMenu= <<<EOD
 <a href='?p=visaprofil'>Mitt konto </a><a href='?p=visanamnbloggedit&amp;idAuthor={$_SESSION['idAuthor']}'>{$_SESSION['accountAuthor']}</a><a href='?p=logout'>Logga ut</a>
EOD;
  	  }else{
  	  // If user is not logged in, show link to login-page
$htmlMenu= <<<EOD
<a href='?p=login'>Logga in</a>
EOD;
	  }
         $html=<<<EOD
<div class='login'>
{$htmlMenu}
</div>
EOD;
  	return $html;
  	  
  }
}
?>
