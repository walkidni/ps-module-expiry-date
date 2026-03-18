<?php

$sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'psmoduleexpirydate`';

return Db::getInstance()->execute($sql);
