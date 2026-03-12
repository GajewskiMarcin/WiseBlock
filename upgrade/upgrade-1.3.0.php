<?php
if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_1_3_0($module)
{
    $db = Db::getInstance();
    $prefix = _DB_PREFIX_;

    // ---- wiseblock_block: new columns ----
    $blockColumns = $db->executeS('SHOW COLUMNS FROM `'.$prefix.'wiseblock_block`');
    $blockColNames = array_column($blockColumns, 'Field');

    if (!in_array('days_of_week', $blockColNames)) {
        $db->execute('ALTER TABLE `'.$prefix.'wiseblock_block` ADD `days_of_week` VARCHAR(20) NULL AFTER `time_to`');
    }
    if (!in_array('auto_refresh', $blockColNames)) {
        $db->execute('ALTER TABLE `'.$prefix.'wiseblock_block` ADD `auto_refresh` TINYINT(1) NOT NULL DEFAULT 0 AFTER `days_of_week`');
    }
    if (!in_array('lazy_load', $blockColNames)) {
        $db->execute('ALTER TABLE `'.$prefix.'wiseblock_block` ADD `lazy_load` TINYINT(1) NOT NULL DEFAULT 0 AFTER `auto_refresh`');
    }
    if (!in_array('ab_variant', $blockColNames)) {
        $db->execute('ALTER TABLE `'.$prefix.'wiseblock_block` ADD `ab_variant` ENUM(\'none\',\'A\',\'B\') NOT NULL DEFAULT \'none\' AFTER `lazy_load`');
    }
    if (!in_array('views_count', $blockColNames)) {
        $db->execute('ALTER TABLE `'.$prefix.'wiseblock_block` ADD `views_count` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `ab_variant`');
    }

    // ---- wiseblock_block_lang: content_b for A/B testing ----
    $langColumns = $db->executeS('SHOW COLUMNS FROM `'.$prefix.'wiseblock_block_lang`');
    $langColNames = array_column($langColumns, 'Field');

    if (!in_array('content_b', $langColNames)) {
        $db->execute('ALTER TABLE `'.$prefix.'wiseblock_block_lang` ADD `content_b` LONGTEXT NULL AFTER `content`');
    }

    // ---- wiseblock_stats table ----
    $db->execute('CREATE TABLE IF NOT EXISTS `'.$prefix.'wiseblock_stats` (
        `id_stat` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_block` INT UNSIGNED NOT NULL,
        `variant` CHAR(1) NOT NULL DEFAULT \'A\',
        `date_stat` DATE NOT NULL,
        `views` INT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`id_stat`),
        UNIQUE KEY `block_variant_date` (`id_block`, `variant`, `date_stat`),
        KEY `id_block` (`id_block`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    // Register actionFrontControllerSetMedia hook for JS assets
    if (!$module->isRegisteredInHook('actionFrontControllerSetMedia')) {
        $module->registerHook('actionFrontControllerSetMedia');
    }

    return true;
}
