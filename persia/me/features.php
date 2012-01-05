<?php
$aSize="150";
$gravatar=md5( strtolower( trim( "morhag89@hotmail.com" ) ) );
$gravatar="http://www.gravatar.com/avatar/".$gravatar.".jpg?s=".$aSize;

$html=<<<EOD
<div class='content2'>
<div class='begin'>
<h1>Features och funktioner</h1>
<h2>Features och funktioner</h2>
<p>
Forumet återanvänder designen från bloggen i dbwebb1. Editorn i forumet har autosave-funktion m.h.a ajax och jquery. Dessutom går det att köra editorn i mark-it-up läge.
</p>
<p>
Bloggen har integrerats i M-Persia. Bloggen har samma utseende och funktioner som vid kursen dbwebb1. Jag använde mig av samma inloggningsförfarande som vid förra kursen. Detta gjorde att momentet "bilaga till blogg" blev svårt att genomföra.
Därför avstod jag från att göra  det optionella kravet "bilaga till blogg".
</p>
<p>
Nya konton har jag skapat och integrerat med Gravatar. Jag följde den nedlagda laborationen "användare,profiler och inloggning" ganska väl, men med en del modifierade lagrade procedurer. Språkstödet följer mos exempel. Jag skapade ett konto på gravatar och lade upp en bild.
Stöd för gravatar i M-Persia byggde jag in.
</p>
</div>
<p>
Jag skapade ett konto på recaptcha och fick två nycklar som jag använde. Jag stylade till en custom-kff-variant. Dessutom har jag stöd för flera språk även svenska. 
</p>
<p>
Jag hade lite tid över och så ville jag lära mig en del nya saker. Det går att logga in med e-post adress också. Ändrar man i profilens e-post, så skickas ett mejl ut. Lösenordet saltas och kryptering med plain,md5,sha-1 och sha-2(mysql 5.5) går att välja.
Vidare implementerade jag funktionen I-FORGOT-MY-PASSWORD.
</p>
<p>
Den nedlagda laborationen "uppladdning,nedladdning och hantering av filer" följde jag noga(modifierade vissa stored procedures). Från forumet via länk kan man ladda upp filer om man är inloggad. Det går även att ladda upp filer utan anknytning till forumet. Nedladdning av filer
från forumet går att göra av vem som helst. Om du är inloggad kan du även uppdatera meta-information om filerna till bilagor.
</p>
<h2>Validering</h2>
<p>
Valideringen sker korrekt utom vissa undantag. Det finns en del små fel, som jag inte lyckats få till. I modulerna blog och forum-romanum använder jag mig av rundade hörn på php-bilden. För att få det att fungera  i firefox använder jag  av egenskaperna -moz-border-radius-bottomleft och -moz-border-radius-bottomright i stylesheet2.css. De validerar fel, men jag har valt att behålla dem
för att det ger snygg design. jquery.jgrowl.css ger 18 fel som jag inte ger mig på att försöka rätta. Jag hoppas istället på en bättre variant från Jgrowl senare. 
</p>
<h2>Browser-stöd</h2>
<p>
I firefox fungerar M-Persia bra. I IE8 och Google Chrome blir det inga rundade hörn på php-bilden i bloggen och forumet. Jag hade problem med publish-knappen i forumet för IE8 och Chrome. Men jag har åtgärdat det. Browserna klarade inte 2st jQuery-funktioner efter varandra. Jag kör den andra funtionen (disable publish-button) efter ajax anropet.
I IE8 fungerade inte heller meta-refresh vid automatisk nedladdning. Jag har fixat detta med ett javascript och tagit bort meta-refresh för IE8. 
</p>
</div>
EOD;

//------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
require_once('common3.php');
$title="Hem";
$charset="utf-8";
$language="sv";


$html = <<< EOD
<?xml version="1.0" encoding="{$charset}" ?> 
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$language}" lang="{$language}">  
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$charset};" />
<link rel='stylesheet' href='me/stylesheet_start.css' type='text/css' media='screen' />
<title>{$title}</title>
		
	</head>

	<body>
		{$header}
		{$html}
		{$footer}
	</body>
</html>
EOD;


//header("Content-Type: application/xhtml+xml; charset={$charset}");
echo($html);
exit;

?>
