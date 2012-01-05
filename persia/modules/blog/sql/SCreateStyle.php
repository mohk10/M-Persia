<?php
// ===========================================================================================
//
// SCreateStyle.php
//
// SQL to create support for user defined styles and ability to change styles.
//

$tStyle 		= DBT_Style;

$defaultCharsetAndCollate = "DEFAULT CHARACTER SET utf8 COLLATE utf8_bin";
$charCharsetAndCollate = ""; // Use for datatype CHAR to save space

$query .= <<<EOD

-- -------------------------------------------------------------------------------------------
DROP TABLE IF EXISTS {$tStyle};

--
-- Table for the Style
--
CREATE TABLE {$tStyle} (

  -- Primary key(s)
  idStyle INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  nameStyle VARCHAR(40) NOT NULL UNIQUE,
  hrefStyle VARCHAR(100) NOT NULL
  
) {$defaultCharsetAndCollate};

--
-- Add default style(s) 
--
INSERT INTO {$tStyle} (nameStyle, hrefStyle)
	VALUES ('Plain, square and color', 'style/plain/stylesheet_liquid.css');
INSERT INTO {$tStyle} (nameStyle, hrefStyle)
	VALUES ('Plain, square and color1', 'style/plain/stylesheet_liquid.css');


EOD;


?>