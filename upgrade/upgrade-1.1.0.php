<?php
if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_1_1_0($module)
{
    // Register new hooks for multi-page support
    $module->registerHook('displayHome');

    // Add new default WiseBlock hooks
    $newHooks = array(
        'displayWiseBlockHomeBanner' => 'WiseBlock: Homepage banner area',
        'displayWiseBlockCategoryTop' => 'WiseBlock: Top of category page',
        'displayWiseBlockCategoryBottom' => 'WiseBlock: Bottom of category page',
        'displayWiseBlockSearchResults' => 'WiseBlock: Search results page',
        'displayWiseBlockManufacturer' => 'WiseBlock: Manufacturer page',
    );

    foreach ($newHooks as $hookName => $desc) {
        // Create PS hook if needed
        $id_hook = (int)Hook::getIdByName($hookName);
        if (!$id_hook) {
            $hook = new Hook();
            $hook->name = $hookName;
            $hook->title = $hookName;
            $hook->description = $desc;
            $hook->add();
        }
        if (!$module->isRegisteredInHook($hookName)) {
            $module->registerHook($hookName);
        }

        // Add to wiseblock_hook table
        $exists = Db::getInstance()->getValue('SELECT id_wiseblock_hook FROM '._DB_PREFIX_.'wiseblock_hook WHERE hook_name="'.pSQL($hookName).'"');
        if (!$exists) {
            Db::getInstance()->insert('wiseblock_hook', array(
                'hook_name' => pSQL($hookName),
                'enabled' => 1,
                'description' => pSQL($desc),
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s'),
            ));
        }
    }

    return true;
}
