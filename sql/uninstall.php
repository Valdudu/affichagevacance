<?
$sql = array();
$sql[] = 'drop table if exists `' . _DB_PREFIX_ . 'vacances`';
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}