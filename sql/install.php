<?php

$sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'psmoduleexpirydate` (
    `id_product` INT UNSIGNED NOT NULL,
    `expiration_date` DATE NULL,
    PRIMARY KEY (`id_product`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

return Db::getInstance()->execute($sql);
