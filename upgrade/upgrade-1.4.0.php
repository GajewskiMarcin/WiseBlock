<?php
if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_1_4_0($module)
{
    $db = Db::getInstance();
    $prefix = _DB_PREFIX_;

    // ---- wiseblock_block: A/B auto-optimize columns ----
    $blockColumns = $db->executeS('SHOW COLUMNS FROM `'.$prefix.'wiseblock_block`');
    $blockColNames = array_column($blockColumns, 'Field');

    if (!in_array('ab_auto_optimize', $blockColNames)) {
        $db->execute('ALTER TABLE `'.$prefix.'wiseblock_block` ADD `ab_auto_optimize` TINYINT(1) NOT NULL DEFAULT 0 AFTER `ab_variant`');
    }
    if (!in_array('ab_min_views', $blockColNames)) {
        $db->execute('ALTER TABLE `'.$prefix.'wiseblock_block` ADD `ab_min_views` INT UNSIGNED NOT NULL DEFAULT 500 AFTER `ab_auto_optimize`');
    }
    if (!in_array('ab_winner', $blockColNames)) {
        $db->execute('ALTER TABLE `'.$prefix.'wiseblock_block` ADD `ab_winner` CHAR(1) NULL AFTER `ab_min_views`');
    }

    // ---- wiseblock_stats: clicks column ----
    $statsColumns = $db->executeS('SHOW COLUMNS FROM `'.$prefix.'wiseblock_stats`');
    $statsColNames = array_column($statsColumns, 'Field');

    if (!in_array('clicks', $statsColNames)) {
        $db->execute('ALTER TABLE `'.$prefix.'wiseblock_stats` ADD `clicks` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `views`');
    }

    return true;
}
