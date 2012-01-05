<?php
$aSize="150";
$gravatar=md5( strtolower( trim( "morhag89@hotmail.com" ) ) );
$gravatar="http://www.gravatar.com/avatar/".$gravatar.".jpg?s=".$aSize;
//<h2>Steg för nedladdning</h2>
$html=<<<EOD
<div class='content2'>
<h2 class='begin'>Steg för nedladdning</h2>
<ol>
<li class='begin'>
Ladda ned programvaran M-Persia från Github. Kör clone.
</li>
<li class='begin'>
Ändra i config.php. config.php finns i huvudkatalogen och i varje modul utom core.
<ul>
<li class='begin'>
För databasen: lägg in host,user,password,databasename och prefix.
</li>
<li class='begin'>
Ändra site-link och file-archive-path.
</li>
<li class='begin'>
I config.php i modulen file-archive kan du ändra MAX-FILE-SIZE(standard 30kB) för uppladdning av fil.
</li>
<li>
Om du vill använda recaptcha: skaffa nycklar till din site på recaptcha och lägg in den privata och publika nyckeln.
</li>
<li class='begin'>
I config.php i katalogen sql behöver du inte göra några ändringar.
</li>
</ul>
</li>
<li>
Ange vilken password-hashing du vill använda i filen config-global-sample.php i huvudkatalogen. Du kan använda plain,md5,sha-1 eller
sha-2(mysql 5.5).
</li>
<li>
I config.php  i bloggen ska du ge ett prefix skillt ifrån huvudprefixet.
</li>
</ol>
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
