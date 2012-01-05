<?php
//header("Content-type: text/css");
$color = "green";        // <--- define the variable 
echo <<<CSS   
/* --- start of css --- */
h5
	{
	color: $color;  /* <--- use the variable */
	font-weight: bold;
	font-size: 1.2em;
	text-align: left;
	}
/* --- end of css --- */
CSS;
?>