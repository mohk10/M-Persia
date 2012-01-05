<?php
$html=<<<EOD
<div class='pagelinks' style='margin-left: auto; text-align: center;'><a href="#kmom02">Kmom02</a> | <a href="#kmom03">Kmom03</a> | <a href="#kmom04">Kmom04</a> | <a href="#kmom05">Kmom05</a> | <a href="#kmom06">Kmom06</a>| <a href="#kmom07-10">Kmom07-10</a></div>
<div class='content2'>
<div class='begin'>
<h1>Redovisning av kursmomenten</h1>
<h2>Kursmoment 01: Ordning och reda med versionshantering</h2>
<p>
Versionshanteringssystem,därmed också git och github, var något helt obekant för mig. Det tar en hel del tid att vänja sig vid git och github. Jag hoppas att det sjunker in under kursens gång. HTML5 känns spännade och jag hoppas lära mig en hel del.
</p>
<p>
Greco känns användbart till mindre webbplatser. Git-kommandona kändes ovant. Jag repeterade en del unix-kommandon i PUTTY. Rename trodde jag fanns, men efter lite gogglande kom jag fram till att man skulle använda mv. Jag var inne på irc-chatten och frågade om chmod .Där fick jag ett mycket användbart  skript av "sharpless". Dessutom fick jag veta att |man "kommando"| kunde vara användbart. Dock känns lite gogglande på kommandot lättare. Unix-kommandon,som körs från php, var en nyhet för mig. Jag gjorde en php-version av hide på irc-chatten.  
HTML5 känns ganska nytt för mig,trots att vi använde det på bloggen i förra kursen. De nya taggarna section och article känns lite svårt att skilja mellan.
</p>
</div>
<p>
Persia kändes mycket professionellt och mycket användbart på kursen. Kursens blogg fick jag gå in på,för att fräscha upp kunskaperna om hur man ska gå tillväga med config-filen. Jag lade in min blogg från förra kursen och me-katalogen i Persia. Den fick namnet M-Persia.
</p>
<p>Triggers och stored procedures har jag aldrig hållt på med. Därför känns nästa moment spännande,utmanande och lite svårt.
</p>
<a href="../../greco">Greco</a>
|
<a href="../../persia">Persia</a>
|
<a href="?p=home">M-Persia</a>
<h2 id='kmom02'>Kursmoment 02:SQL med funktioner,procedurer och triggers</h2>
<p>Procedurer,funktioner och triggers var något jag bara hört talas om. Dessa databas-moment hade jag aldrig tidigare självt hållit på med.
Därför tog laborationen ganska lång tid att genomföra.
</p>
<p>
Mysql-workbench använde jag flitigt under denna laboration. I början kändes det ovant,ty det var ett bra tag sen jag använde denna klient.
Ifrån php försökte jag köra flera stored-procedures i rad med multi_query. Tyvärr lyckades inte detta. Därefter körde jag istället varje procedur för sig med multi_query. I mysql-workbench glömde jag ibland
att använda statementet DELIMITER. Procedurer för hantering av artiklar kändes inte så speciellt intressanta. Däremot udf:n och triggers är jag mycket imponerade av.
</p>
<p>
Tankenöten funderade jag ett tag på. Det enda jag kunde komma på var en modifiering av FCheckUserIsOwnerOrAdmin. Jag kastade om ordningen och lade till en if-sats.
</p>
<p>
Sammanfattningsvis så insåg jag inte fördelarna med procedures. Udf:er och triggers tycker jag verkar mycket användbart. Eftersom jag är ny på detta område, kanske alla fördelar kommer fram under kursens gång.
</p>
<a href="?p=home">M-Persia</a>
<br /><br /><a href="#links">Upp</a>
<h2 id='kmom03'>Kursmoment 03:WYSIWYG-editor i javascript</h2>
<p>Någon erfarenhet av WYSIWYG-editorer i javascript hade jag inte innan denna laboration. Även subfrontcontroller var något nytt för mig. Laborationen tog en hel del tid (kanske pga min ovana av stored procedures).
</p>
<p>
Nicedit kändes lite word-aktigt med alla formatterings alternativ. WYMeditor verkade lite mer anpassningsbar. Coolast var dock markItUp, med skins och olika addons. Jag installerade ett windows-live skin. MarkItUp var den enda editorn jag valde att lägga till.
Firebug verkade mycket användbart. Därför installerade jag den på mina datorer. Firebug-lite lade jag till i Google Chrome. Kodningen av forumet tog betydligt längre tid än 8 h. Jag använde mysql-workbench en hel del för att sätta mig in i stored-procedures. På nätet sökte jag och hittade några roliga fotbolls-avatarer.
Från bloggen återanvände jag åtskilligt med kod. Frontcontroller och subfrontcontrollers tog lite tid att sätta sig in i. Config-filerna orsakade en del problem. Eftersom jag glömde att köra scriptet efter varje ändring i config-filerna. Min blog från förra kursen lade jag till som en modul.
Javascript har jag använt lite grand. För validering av olika formulär har jag använt javascript. Dessutom har jag använt ett bild-galleri med javascript.
</p>
<p>
Sammantaget var det flera för mig helt nya moment, som gjorde laborationen mycket lärorik.
</p>
<a href="?p=home">M-Persia</a>
<br /><br /><a href="#links">Upp</a>
<h2 id='kmom04'>Kursmoment 04: Ajax och autosave</h2>
<p>
Ajax och jQuery har jag använt tidigare lite grand. Därför gick den här laborationen något snabbare än förra.
</p>
<p>
Greco-exmplet gick ganska smärtfritt. Firebug,som jag installerade under den förra laborationen, underlättade felsökningen avsevärt.
Pluginet jGrowl är snyggt och smidigt. Det har också en del inställningsmöjligheter. Jag följde mos steg-för-steg till autosave. Timers har jag använt tidigare både i java och javascript. Ajax och jQuery ingår i min mysql-kalender, som ligger på bth:s server. Där använder jag mig av jQuery-pluginet qTip för tooltips (ladda om sidan för ny tooltips-design). De optionella momenten 4.7 och 4.8 gjorde jag. Tyvärr fick jag inte till så mycket egen kod utan lånade mest av mos.
Vid dessa moment hade jag stor hjälp av Firebug. Jag justerade css-felet vid markItUp-editorn. Factory pattern var nytt för mig. Jag integrerade CWYSIWYGEditor arkitektur med factory pattern i mitt M-Persia. Språkstödet orsakade lite problem. Jag fick flytta en del filer för att det skulle stämma.
</p>
<p>
Nu väntar jag med spänning på att nästa laboration skall läggas upp.
</p>
<a href="http://www.student.bth.se/~mohk10/dbwebb1/ajax_calender_monthly_test/myfiles/monthly_calender2.php">mysql-kalender</a> 
<br /><br />
<a href="?p=home">M-Persia</a>
<br /><br />
<a href="#links">Upp</a>
<h2 id='kmom05'>Kursmoment 05: </h2>
<a href="#links">Upp</a>
<h2 id='kmom06'>Kursmoment 06:</h2>
<a href="#links">Upp</a>
<h2 id='kmom07-10'>Kursmoment 07-10:</h2>
<a href="#links">Upp</a>
</div>
EOD;

//------------------------------------------------------------------------------------------
//
// Create and print out the html-page
//
require_once('common3.php');
$title="Redovisning";
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

