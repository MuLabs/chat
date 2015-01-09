<?php
$sql = "
CREATE TABLE `chatMessageUser` (
  `idUser` INT UNSIGNED NOT NULL,
  `idChatMessage` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idUser`)
) ENGINE=MEMORY;
";
$handler->query($sql);
