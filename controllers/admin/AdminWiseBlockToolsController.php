<?php

require_once _PS_MODULE_DIR_.'wiseblock/controllers/admin/WiseBlockAdminHelper.php';

class AdminWiseBlockToolsController extends ModuleAdminController
{
    /**
     * Translation helper - ensures correct source file for translations
     */
    protected function _l($s)
    {
        return $this->module->l($s, 'adminwiseblocktoolscontroller');
    }

    public function __construct()
    {
        $this->module = Module::getInstanceByName('wiseblock');
        $this->bootstrap = true;
        parent::__construct();
        $this->meta_title = 'WiseBlock: Dashboard & Tools';
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(_MODULE_DIR_.'wiseblock/views/css/admin-modern.css?v=4.1');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        // Back button
        $this->page_header_toolbar_btn['back'] = array(
            'href' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => 'wiseblock']),
            'desc' => $this->trans('Back', [], 'Admin.Actions'),
            'icon' => 'process-icon-back',
        );

        // Translate button
        $this->page_header_toolbar_btn['translate'] = array(
            'href' => $this->context->link->getAdminLink('AdminTranslations', true, [], [
                'type' => 'modules',
                'module' => 'wiseblock',
                'lang' => $this->context->language->iso_code,
            ]),
            'desc' => $this->trans('Translate', [], 'Admin.Actions'),
            'icon' => 'process-icon-flag',
        );

        // Manage hooks button
        $this->page_header_toolbar_btn['hooks'] = array(
            'href' => $this->context->link->getAdminLink('AdminModulesPositions', true, [], [
                'show_modules' => $this->module->id,
            ]),
            'desc' => $this->trans('Manage hooks', [], 'Admin.Modules.Feature'),
            'icon' => 'process-icon-anchor',
        );
    }

    public function initContent()
    {
        parent::initContent();

        // All content is now in renderList() - stats + tools
        $content = $this->renderList();

        // Render tabs and prepend to content
        $tabs = WiseBlockAdminHelper::renderTabs($this->context, 'AdminWiseBlockTools');

        // Assign combined content to Smarty
        $this->context->smarty->assign([
            'content' => $tabs . $content,
        ]);
    }

    protected function renderDashboard()
    {
        // SUPER SIMPLE DEBUG - no DB, no logic
        return '<div style="background:orange;color:black;padding:20px;margin:20px 0;font-size:18px;">ORANGE DEBUG - renderDashboard() was called!</div>';

        try {
            $activeBlocks = 0;
            $totalRules = 0;
            $totalHooks = 0;

            // Check if tables exist before querying
            $tables = Db::getInstance()->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'wiseblock%"');
            $tableNames = array();
            foreach ($tables as $t) {
                $tableNames[] = array_values($t)[0];
            }

            if (in_array(_DB_PREFIX_.'wiseblock_block', $tableNames)) {
                $activeBlocks = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_block WHERE active=1');
            }
            if (in_array(_DB_PREFIX_.'wiseblock_rule', $tableNames)) {
                $totalRules = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_rule');
            }
            if (in_array(_DB_PREFIX_.'wiseblock_hook', $tableNames)) {
                $totalHooks = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_hook WHERE enabled=1');
            }
        } catch (Exception $e) {
            $activeBlocks = 0;
            $totalRules = 0;
            $totalHooks = 0;
        }

        // Render stats directly instead of template to avoid caching issues
        $html = '<div style="background:green;color:white;padding:20px;margin:20px 0;font-size:18px;font-weight:bold;">DEBUG OK: Blocks='.$activeBlocks.' | Rules='.$totalRules.' | Hooks='.$totalHooks.'</div>
<!-- Stats Cards -->
<div class="wb-stats-grid">
    <!-- Active Blocks Card -->
    <div class="wb-stat-card-new wb-stat-blocks">
        <div class="wb-stat-card-top">
            <div class="wb-stat-icon wb-stat-icon-blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
            </div>
            <div class="wb-stat-trend wb-trend-up">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                <span>+12%</span>
            </div>
        </div>
        <div class="wb-stat-card-bottom">
            <div class="wb-stat-number">'.$activeBlocks.'</div>
            <div class="wb-stat-label">Active Blocks</div>
        </div>
        <div class="wb-stat-blob wb-blob-blue"></div>
    </div>

    <!-- Total Rules Card -->
    <div class="wb-stat-card-new wb-stat-rules">
        <div class="wb-stat-card-top">
            <div class="wb-stat-icon wb-stat-icon-purple">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            </div>
            <div class="wb-stat-trend wb-trend-up">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                <span>+8%</span>
            </div>
        </div>
        <div class="wb-stat-card-bottom">
            <div class="wb-stat-number">'.$totalRules.'</div>
            <div class="wb-stat-label">Total Rules</div>
        </div>
        <div class="wb-stat-blob wb-blob-purple"></div>
    </div>

    <!-- Active Hooks Card -->
    <div class="wb-stat-card-new wb-stat-hooks">
        <div class="wb-stat-card-top">
            <div class="wb-stat-icon wb-stat-icon-orange">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
            </div>
            <div class="wb-stat-trend wb-trend-up wb-trend-orange">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                <span>+3</span>
            </div>
        </div>
        <div class="wb-stat-card-bottom">
            <div class="wb-stat-number">'.$totalHooks.'</div>
            <div class="wb-stat-label">Active Hooks</div>
        </div>
        <div class="wb-stat-blob wb-blob-orange"></div>
    </div>
</div>';

        return $html;
    }

    public function renderList()
    {
        // Stats - get both active and inactive counts
        $activeBlocks = 0;
        $inactiveBlocks = 0;
        $totalRules = 0;
        $blocksWithRules = 0;
        $activeHooks = 0;
        $disabledHooks = 0;
        try {
            $activeBlocks = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_block WHERE active=1');
            $inactiveBlocks = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_block WHERE active=0');
            $totalRules = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_rule');
            // Count how many distinct blocks have at least one rule
            // First check what column exists in the rule table
            $ruleColumns = Db::getInstance()->executeS('SHOW COLUMNS FROM '._DB_PREFIX_.'wiseblock_rule');
            $blockIdColumn = 'id_wiseblock_block'; // default
            foreach ($ruleColumns as $col) {
                if ($col['Field'] === 'id_block') {
                    $blockIdColumn = 'id_block';
                    break;
                }
            }
            $blocksWithRules = (int)Db::getInstance()->getValue('
                SELECT COUNT(DISTINCT '.$blockIdColumn.') FROM '._DB_PREFIX_.'wiseblock_rule WHERE '.$blockIdColumn.' > 0
            ');
            $activeHooks = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_hook WHERE enabled=1');
            $disabledHooks = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_hook WHERE enabled=0');
        } catch (Exception $e) {
            // ignore
        }

        $totalBlocks = $activeBlocks + $inactiveBlocks;
        $totalHooksCount = $activeHooks + $disabledHooks;

        // Prepare translated strings for stats
        $lblBlocks = $this->_l('Blocks');
        $lblActive = $this->_l('active');
        $lblInactive = $this->_l('inactive');
        $lblTargetingRules = $this->_l('Targeting Rules');
        $lblInBlocks = ($blocksWithRules != 1)
            ? sprintf($this->_l('in %d blocks'), $blocksWithRules)
            : sprintf($this->_l('in %d block'), $blocksWithRules);
        $lblHooks = $this->_l('Hooks');
        $lblEnabled = $this->_l('enabled');
        $lblDisabled = $this->_l('disabled');

        $statsHtml = '<!-- Stats Cards -->
<div class="wb-stats-grid">
    <div class="wb-stat-card-new wb-stat-blocks">
        <div class="wb-stat-card-content">
            <div class="wb-stat-icon wb-stat-icon-blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
            </div>
            <div class="wb-stat-info">
                <div class="wb-stat-number">'.$totalBlocks.'</div>
                <div class="wb-stat-label">'.$lblBlocks.'</div>
                <div class="wb-stat-details">
                    <span class="wb-stat-active"><i class="icon-check"></i> '.$activeBlocks.' '.$lblActive.'</span>
                    <span class="wb-stat-inactive"><i class="icon-ban"></i> '.$inactiveBlocks.' '.$lblInactive.'</span>
                </div>
            </div>
        </div>
        <div class="wb-stat-blob wb-blob-blue"></div>
    </div>
    <div class="wb-stat-card-new wb-stat-rules">
        <div class="wb-stat-card-content">
            <div class="wb-stat-icon wb-stat-icon-purple">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            </div>
            <div class="wb-stat-info">
                <div class="wb-stat-number">'.$totalRules.'</div>
                <div class="wb-stat-label">'.$lblTargetingRules.'</div>
                <div class="wb-stat-details">
                    <span class="wb-stat-neutral"><i class="icon-th-large"></i> '.$lblInBlocks.'</span>
                </div>
            </div>
        </div>
        <div class="wb-stat-blob wb-blob-purple"></div>
    </div>
    <div class="wb-stat-card-new wb-stat-hooks">
        <div class="wb-stat-card-content">
            <div class="wb-stat-icon wb-stat-icon-orange">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
            </div>
            <div class="wb-stat-info">
                <div class="wb-stat-number">'.$totalHooksCount.'</div>
                <div class="wb-stat-label">'.$lblHooks.'</div>
                <div class="wb-stat-details">
                    <span class="wb-stat-active"><i class="icon-check"></i> '.$activeHooks.' '.$lblEnabled.'</span>
                    <span class="wb-stat-inactive"><i class="icon-ban"></i> '.$disabledHooks.' '.$lblDisabled.'</span>
                </div>
            </div>
        </div>
        <div class="wb-stat-blob wb-blob-orange"></div>
    </div>
</div>';

        // Handle form submissions first
        $this->processFormSubmissions();

        // Get available hooks for dropdown
        $availableHooks = Db::getInstance()->executeS('
            SELECT hook_name FROM '._DB_PREFIX_.'wiseblock_hook WHERE enabled=1
            UNION
            SELECT DISTINCT hook_name FROM '._DB_PREFIX_.'wiseblock_block_hook
            ORDER BY hook_name ASC
        ');

        // Get cache last cleared timestamp
        $cacheLastCleared = Configuration::get('WISEBLOCK_CACHE_CLEARED');
        $cacheTimeAgo = '';
        if ($cacheLastCleared) {
            $cacheTimeAgo = $this->getTimeAgo((int)$cacheLastCleared);
        }

        // Get custom CSS
        $customCSS = Configuration::get('WISEBLOCK_CUSTOM_CSS');

        $this->context->smarty->assign([
            'available_hooks' => $availableHooks,
            'cache_last_cleared' => $cacheTimeAgo,
            'custom_css' => $customCSS
        ]);

        $html = $statsHtml;
        $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.'wiseblock/views/templates/admin/tools/tester.tpl');
        $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.'wiseblock/views/templates/admin/tools/import_export.tpl');
        return $html;
    }

    protected function processFormSubmissions()
    {
        // Fix Tabs
        if (Tools::isSubmit('submitWiseBlockFixTabs')) {
            $this->fixModuleTabs();
        }

        // Tester
        if (Tools::isSubmit('submitWiseBlockTester')) {
            $id_product = (int)Tools::getValue('wiseblock_test_id_product');
            $hook = Tools::getValue('wiseblock_test_hook');
            $module = Module::getInstanceByName('wiseblock');
            if ($module && $id_product && $hook) {
                $result = $module->debugTestMatching($id_product, $hook, (int)$this->context->language->id, (int)$this->context->shop->id);
                $this->context->smarty->assign('wiseblock_result', $result);
            }
        }

        // Export
        if (Tools::isSubmit('submitWiseBlockExport')) {
            $json = $this->exportConfig();
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="wiseblock-config-'.date('Y-m-d').'.json"');
            echo $json;
            exit;
        }

        // Import
        if (Tools::isSubmit('submitWiseBlockImport')) {
            if (!empty($_FILES['wiseblock_import_file']['tmp_name'])) {
                $json = Tools::file_get_contents($_FILES['wiseblock_import_file']['tmp_name']);
                $this->importConfig($json);
            }
        }

        // Clear cache
        if (Tools::isSubmit('submitWiseBlockClearCache')) {
            Cache::clean('wiseblock_*');
            Configuration::updateValue('WISEBLOCK_CACHE_CLEARED', time());
            $this->confirmations[] = $this->_l('Module cache has been cleared.');
        }

        // Save custom CSS
        if (Tools::isSubmit('submitWiseBlockCustomCSS')) {
            $customCSS = Tools::getValue('wiseblock_custom_css', '');
            Configuration::updateValue('WISEBLOCK_CUSTOM_CSS', $customCSS, true);
            $this->confirmations[] = $this->_l('Custom CSS has been saved.');
        }
    }

    /**
     * Get human-readable time ago string
     */
    protected function getTimeAgo($timestamp)
    {
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return $this->_l('just now');
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return ($mins > 1)
                ? sprintf($this->_l('%d minutes ago'), $mins)
                : sprintf($this->_l('%d minute ago'), $mins);
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return ($hours > 1)
                ? sprintf($this->_l('%d hours ago'), $hours)
                : sprintf($this->_l('%d hour ago'), $hours);
        } else {
            $days = floor($diff / 86400);
            return ($days > 1)
                ? sprintf($this->_l('%d days ago'), $days)
                : sprintf($this->_l('%d day ago'), $days);
        }
    }

    private function exportConfig()
    {
        $prefix = _DB_PREFIX_;
        $data = array(
            'version' => '1.0.0',
            'exported_at' => date('Y-m-d H:i:s'),
            'hooks' => Db::getInstance()->executeS('SELECT * FROM '.$prefix.'wiseblock_hook'),
            'blocks' => Db::getInstance()->executeS('SELECT * FROM '.$prefix.'wiseblock_block'),
            'block_lang' => Db::getInstance()->executeS('SELECT * FROM '.$prefix.'wiseblock_block_lang'),
            'block_hook' => Db::getInstance()->executeS('SELECT * FROM '.$prefix.'wiseblock_block_hook'),
            'rules' => Db::getInstance()->executeS('SELECT * FROM '.$prefix.'wiseblock_rule'),
        );
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Fix module tabs - repair parent relationships
     */
    protected function fixModuleTabs()
    {
        // First, ensure Secret Sauce category exists
        $improve_id = (int)Tab::getIdFromClassName('IMPROVE');
        if (!$improve_id) {
            $improve_id = (int)Tab::getIdFromClassName('AdminParentModulesSf');
        }
        if (!$improve_id) {
            $improve_id = 0;
        }

        $secret_sauce_id = (int)Tab::getIdFromClassName('AdminSecretSauce');
        if (!$secret_sauce_id) {
            // Create Secret Sauce
            $secretSauce = new Tab();
            $secretSauce->active = 1;
            $secretSauce->class_name = 'AdminSecretSauce';
            $secretSauce->id_parent = $improve_id;
            $secretSauce->module = 'wiseblock';
            if (property_exists($secretSauce, 'icon')) {
                $secretSauce->icon = 'science';
            }
            foreach (Language::getLanguages(false) as $lang) {
                $secretSauce->name[(int)$lang['id_lang']] = 'Secret Sauce';
            }
            if ($secretSauce->add()) {
                $secret_sauce_id = (int)$secretSauce->id;
            }
        }

        if (!$secret_sauce_id) {
            $this->errors[] = $this->_l('Could not create Secret Sauce category.');
            return;
        }

        // Fix all WiseBlock tabs - set proper parent
        $tabsToFix = ['AdminWiseBlockBlock', 'AdminWiseBlockHook', 'AdminWiseBlockTools', 'AdminWiseBlockAbout'];
        $tabNames = [
            'AdminWiseBlockBlock' => 'WiseBlock',
            'AdminWiseBlockHook' => 'WiseBlock Hooks',
            'AdminWiseBlockTools' => 'WiseBlock Dashboard',
            'AdminWiseBlockAbout' => 'WiseBlock About'
        ];

        $fixed = 0;
        $created = 0;

        foreach ($tabsToFix as $className) {
            $tabId = (int)Tab::getIdFromClassName($className);

            if ($tabId) {
                // Update existing tab
                $tab = new Tab($tabId);
                $tab->id_parent = $secret_sauce_id;
                $tab->active = 1;
                $tab->module = 'wiseblock';
                if ($tab->save()) {
                    $fixed++;
                }
            } else {
                // Create missing tab
                $tab = new Tab();
                $tab->active = 1;
                $tab->class_name = $className;
                $tab->module = 'wiseblock';
                $tab->id_parent = $secret_sauce_id;
                foreach (Language::getLanguages(false) as $lang) {
                    $tab->name[(int)$lang['id_lang']] = $tabNames[$className];
                }
                if ($tab->add()) {
                    $created++;
                }
            }
        }

        $this->confirmations[] = sprintf($this->_l('Menu tabs repaired: %d fixed, %d created. All tabs now under Secret Sauce category.'), $fixed, $created);
    }

    private function importConfig($json)
    {
        $data = json_decode($json, true);
        if (!$data || empty($data['version'])) {
            $this->errors[] = $this->_l('Invalid JSON file.');
            return;
        }

        $prefix = _DB_PREFIX_;

        // Hooks upsert
        if (!empty($data['hooks'])) {
            foreach ($data['hooks'] as $h) {
                $hook_name = pSQL($h['hook_name']);
                $id_hook = (int)Hook::getIdByName($hook_name);
                if (!$id_hook) {
                    $hook = new Hook();
                    $hook->name = $hook_name;
                    $hook->title = $hook_name;
                    $hook->description = pSQL($h['description']);
                    $hook->add();
                }
                $exists = Db::getInstance()->getValue('SELECT id_wiseblock_hook FROM '.$prefix.'wiseblock_hook WHERE hook_name="'.$hook_name.'"');
                $row = array(
                    'hook_name' => $hook_name,
                    'enabled' => (int)$h['enabled'],
                    'description' => pSQL($h['description']),
                    'date_add' => pSQL($h['date_add'] ?: date('Y-m-d H:i:s')),
                    'date_upd' => pSQL($h['date_upd'] ?: date('Y-m-d H:i:s'))
                );
                if ($exists) {
                    Db::getInstance()->update('wiseblock_hook', $row, 'id_wiseblock_hook='.(int)$exists);
                } else {
                    Db::getInstance()->insert('wiseblock_hook', $row);
                }
            }
        }

        // Blocks, langs, hooks, rules (replace)
        if (!empty($data['blocks'])) {
            Db::getInstance()->execute('TRUNCATE TABLE '.$prefix.'wiseblock_block');
            foreach ($data['blocks'] as $b) {
                Db::getInstance()->insert('wiseblock_block', $b);
            }
        }
        if (!empty($data['block_lang'])) {
            Db::getInstance()->execute('TRUNCATE TABLE '.$prefix.'wiseblock_block_lang');
            foreach ($data['block_lang'] as $l) {
                Db::getInstance()->insert('wiseblock_block_lang', $l);
            }
        }
        if (!empty($data['block_hook'])) {
            Db::getInstance()->execute('TRUNCATE TABLE '.$prefix.'wiseblock_block_hook');
            foreach ($data['block_hook'] as $bh) {
                Db::getInstance()->insert('wiseblock_block_hook', $bh);
            }
        }
        if (!empty($data['rules'])) {
            Db::getInstance()->execute('TRUNCATE TABLE '.$prefix.'wiseblock_rule');
            foreach ($data['rules'] as $r) {
                Db::getInstance()->insert('wiseblock_rule', $r);
            }
        }

        $this->confirmations[] = $this->_l('Import completed successfully.');
    }
}
