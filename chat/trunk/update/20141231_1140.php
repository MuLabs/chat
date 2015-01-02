<?php
$sql = "
ALTER TABLE `chatMessage`
CHANGE COLUMN `date_insert` `dateInsert` DATETIME NOT NULL ,
CHANGE COLUMN `date_edit` `dateEdit` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ;
";
$handler->query($sql);
