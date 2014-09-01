<?php
/**
 * Created by PhpStorm.
 * User: squiaios
 * Date: 26/06/14
 * Time: 15:25
 */

$sql = "
CREATE TABLE `chatMessage` (
  `idChatMessage` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idSender` smallint(5) unsigned NOT NULL,
  `content` text NOT NULL,
  `date_insert` datetime NOT NULL,
  `date_edit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idChatMessage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
";
$handler->query($sql);
