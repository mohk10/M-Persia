<?php
$aSize="150";
$gravatar=md5( strtolower( trim( "morhag89@hotmail.com" ) ) );
$gravatar="http://www.gravatar.com/avatar/".$gravatar.".jpg?s=".$aSize;

$html=<<<EOD
<div id='content'>
<h1>Hej!</h1>
<img src="{$gravatar}" width="150" height="150" alt="Morgan" />
<p style="width:540px;float:left;">
Jag heter Morgan Hagberg och bor i Kalmar.
XHTML,CSS och inledande programmering i JAVA var kurserna som jag gick på 2009 vid högskolan i Kalmar.
Oophp-kursen gick jag i hösten 2010. I våras studerade jag dbwebb1. Inlines är en rolig hobby, som jag ibland ägnar mig åt.
Fotboll är ett stort intresse. Speciellt Kalmar FF och Zlatans Milan.
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

