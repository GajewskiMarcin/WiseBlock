<?php
if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_1_2_0($module)
{
    $db = Db::getInstance();

    // Add head_code and footer_code columns to block_lang table
    $columns = $db->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.'wiseblock_block_lang`');
    $columnNames = array_column($columns, 'Field');

    if (!in_array('head_code', $columnNames)) {
        $db->execute('ALTER TABLE `'._DB_PREFIX_.'wiseblock_block_lang` ADD `head_code` LONGTEXT NULL AFTER `content`');
    }
    if (!in_array('footer_code', $columnNames)) {
        $db->execute('ALTER TABLE `'._DB_PREFIX_.'wiseblock_block_lang` ADD `footer_code` LONGTEXT NULL AFTER `head_code`');
    }

    // Register displayBeforeBodyClosingTag hook
    if (!$module->isRegisteredInHook('displayBeforeBodyClosingTag')) {
        $module->registerHook('displayBeforeBodyClosingTag');
    }

    return true;
}
