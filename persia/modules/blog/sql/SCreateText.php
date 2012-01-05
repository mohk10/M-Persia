<?php
// ===========================================================================================
//
// SCreateText.php
//
// SQL to create general support for storing text.
//

$tText = DBT_Text;

$defaultCharsetAndCollate = "DEFAULT CHARACTER SET utf8 COLLATE utf8_bin";
$charCharsetAndCollate = ""; // Use for datatype CHAR to save space

$query .= <<<EOD

-- -------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS {$tText};

--
-- General table for storing texts
--
CREATE TABLE {$tText} (

  -- Primary key(s)
  idText INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  titleText VARCHAR(256) NOT NULL,
  ingressText VARCHAR(256) NOT NULL,
  contentText TEXT NOT NULL,
  keyText VARCHAR(256) NOT NULL

) {$defaultCharsetAndCollate};


EOD;

?>