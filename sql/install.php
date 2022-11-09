<?php
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'vacances` (
    `text` TEXT,
    `date_from` datetime,
    `date_to` datetime,
    `ip_list` text 
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
$sql[] = 'INSERT INTO ' . _DB_PREFIX_ . 'vacances (text, date_from, date_to, ip_list) values (\'\', now(), now(), \'\')';
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}