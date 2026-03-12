<?php

require_once _PS_MODULE_DIR_.'wiseblock/classes/WiseBlockHook.php';
require_once _PS_MODULE_DIR_.'wiseblock/controllers/admin/WiseBlockAdminHelper.php';

class AdminWiseBlockHookController extends ModuleAdminController
{
    /**
     * Translation helper - ensures correct source file for translations
     */
    protected function _l($s)
    {
        return $this->module->l($s, 'adminwiseblockhookcontroller');
    }

    public function __construct()
    {
        $this->module = Module::getInstanceByName('wiseblock');
        $this->table = 'wiseblock_hook';
        $this->className = 'WiseBlockHook';
        $this->bootstrap = true;
        $this->identifier = 'id_wiseblock_hook';

        parent::__construct();

        $this->fields_list = array(
            'id_wiseblock_hook' => array('title' => 'ID', 'align' => 'center', 'class' => 'fixed-width-xs'),
            'hook_name' => array('title' => 'Hook'),
            'description' => array('title' => 'Description'),
            'enabled' => array('title' => 'Enabled', 'type' => 'bool', 'active' => 'status', 'align' => 'center'),
            'date_add' => array('title' => 'Created'),
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');
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

        // Render tabs and prepend to content
        $tabs = WiseBlockAdminHelper::renderTabs($this->context, 'AdminWiseBlockHook');

        // Assign combined content to Smarty
        $this->context->smarty->assign([
            'content' => $tabs . $this->content,
        ]);
    }

    public function renderList()
    {
        // Get all hooks
        $hooks = Db::getInstance()->executeS('
            SELECT h.*,
                   (SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_block_hook bh WHERE bh.hook_name = h.hook_name) as block_count
            FROM '._DB_PREFIX_.'wiseblock_hook h
            ORDER BY h.hook_name ASC
        ');

        $html = '<div class="wb-hooks-container">';

        // Header
        $html .= '<div class="wb-hooks-header">
            <div class="wb-hooks-header-left">
                <h2 class="wb-hooks-title">'.$this->_l('Hooks').'</h2>
                <p class="wb-hooks-subtitle">'.$this->_l('Manage custom hooks for your content blocks').'</p>
            </div>
            <a href="'.$this->context->link->getAdminLink('AdminWiseBlockHook', true, array(), array('addwiseblock_hook' => 1)).'" class="wb-btn-create-hook">
                <i class="icon-plus"></i> '.$this->_l('Create Hook').'
            </a>
        </div>';

        // Info alert
        $html .= '<div class="wb-hooks-alert">
            <i class="icon-info-circle"></i>
            <span>'.$this->_l('Custom hooks must start with').' \'<strong>displayWiseBlock</strong>\' '.$this->_l('prefix to ensure compatibility with the module.').'</span>
        </div>';

        // Hooks grid
        $html .= '<div class="wb-hooks-grid">';

        if (empty($hooks)) {
            $html .= '<div class="wb-hooks-empty">
                <i class="icon-anchor wb-empty-icon"></i>
                <p class="wb-empty-title">'.$this->_l('No custom hooks yet').'</p>
                <p class="wb-empty-desc">'.$this->_l('Create your first custom hook to display content in specific locations').'</p>
                <a href="'.$this->context->link->getAdminLink('AdminWiseBlockHook', true, array(), array('addwiseblock_hook' => 1)).'" class="wb-btn-create-hook">
                    <i class="icon-plus"></i> '.$this->_l('Create First Hook').'
                </a>
            </div>';
        } else {
            foreach ($hooks as $hook) {
                $editLink = $this->context->link->getAdminLink('AdminWiseBlockHook', true, array(), array(
                    'updatewiseblock_hook' => 1,
                    'id_wiseblock_hook' => $hook['id_wiseblock_hook']
                ));
                $toggleLink = $this->context->link->getAdminLink('AdminWiseBlockHook', true, array(), array(
                    'wiseblock_toggle_hook' => 1,
                    'id_wiseblock_hook' => $hook['id_wiseblock_hook']
                ));
                $deleteLink = $this->context->link->getAdminLink('AdminWiseBlockHook', true, array(), array(
                    'deletewiseblock_hook' => 1,
                    'id_wiseblock_hook' => $hook['id_wiseblock_hook']
                ));

                $isEnabled = (int)$hook['enabled'];
                $blockCount = (int)$hook['block_count'];
                $dateFormatted = date('d.m.Y', strtotime($hook['date_add']));

                $html .= '<div class="wb-hook-card'.($isEnabled ? '' : ' wb-hook-disabled').'">
                    <div class="wb-hook-card-header">
                        <span class="wb-hook-name">'.htmlspecialchars($hook['hook_name']).'</span>
                        <label class="wb-hook-toggle">
                            <input type="checkbox" '.($isEnabled ? 'checked' : '').' onchange="toggleHook('.$hook['id_wiseblock_hook'].', this.checked)">
                            <span class="wb-toggle-slider"></span>
                        </label>
                    </div>
                    <p class="wb-hook-description">'.htmlspecialchars($hook['description'] ?: $this->_l('No description')).'</p>
                    <div class="wb-hook-card-footer">
                        <span class="wb-hook-blocks">'.$blockCount.' '.($blockCount !== 1 ? $this->_l('blocks') : $this->_l('block')).'</span>
                        <span class="wb-hook-date">'.$dateFormatted.'</span>
                        <div class="wb-hook-actions">
                            <a href="'.$editLink.'" class="wb-hook-action-btn" title="'.$this->_l('Edit').'">
                                <i class="icon-pencil"></i>
                            </a>
                            <a href="'.$deleteLink.'" class="wb-hook-action-btn wb-hook-action-delete" title="'.$this->_l('Delete').'" onclick="return confirm(\''.$this->_l('Delete this hook?').'\')">
                                <i class="icon-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>';
            }
        }

        $html .= '</div>'; // end grid
        $html .= '</div>'; // end container

        // JavaScript for toggle
        $html .= '<script>
        function toggleHook(id, enabled) {
            var url = "'.$this->context->link->getAdminLink('AdminWiseBlockHook', true).'&wiseblock_toggle_hook=1&id_wiseblock_hook=" + id + "&new_status=" + (enabled ? 1 : 0);
            window.location.href = url;
        }
        </script>';

        return $html;
    }

    public function renderForm()
    {
        $obj = $this->loadObject(true);
        $isEdit = Validate::isLoadedObject($obj);

        $listLink = $this->context->link->getAdminLink('AdminWiseBlockHook');

        // Get current values
        $hookName = '';
        $hookSuffix = '';
        $description = '';
        $enabled = 1;

        if ($isEdit) {
            $hookName = $obj->hook_name;
            // Extract suffix (part after displayWiseBlock)
            if (strpos($hookName, 'displayWiseBlock') === 0) {
                $hookSuffix = substr($hookName, strlen('displayWiseBlock'));
            } else {
                $hookSuffix = $hookName;
            }
            $description = $obj->description;
            $enabled = (int)$obj->enabled;
        }

        $html = '<div class="wb-form-container" style="max-width:600px;">
            <form method="post" action="'.$this->context->link->getAdminLink('AdminWiseBlockHook').'" id="wb-hook-form">
                <input type="hidden" name="id_wiseblock_hook" value="'.($isEdit ? (int)$obj->id : '').'">
                <input type="hidden" name="submitSaveHook" value="1">';

        // Form Header
        $html .= '<div class="wb-form-header">
            <h2>'.($isEdit ? $this->_l('Edit Hook') : $this->_l('Create Hook')).'</h2>
            <p>'.$this->_l('Configure your custom hook settings').'</p>
        </div>';

        // Form Body
        $html .= '<div class="wb-form-body">';

        // Hook Name with prefix
        $html .= '<div class="wb-form-section">
            <div class="wb-form-group">
                <label class="wb-form-label">'.$this->_l('Hook Name').'</label>
                <div class="wb-hook-name-input">
                    <span class="wb-hook-prefix">displayWiseBlock</span>
                    <input type="text" name="hook_suffix" class="wb-form-input wb-hook-suffix" value="'.htmlspecialchars($hookSuffix).'" placeholder="'.$this->_l('Suffix').'" required>
                </div>
                <div class="wb-form-hint">'.$this->_l('The full hook name will be:').' displayWiseBlock<span id="hook-preview">'.htmlspecialchars($hookSuffix).'</span></div>
            </div>
        </div>';

        // Description
        $html .= '<div class="wb-form-section">
            <div class="wb-form-group">
                <label class="wb-form-label">'.$this->_l('Description').'</label>
                <textarea name="description" class="wb-form-textarea" rows="3" placeholder="'.$this->_l('Describe the purpose of this hook...').'">'.htmlspecialchars($description).'</textarea>
            </div>
        </div>';

        // Enabled Toggle
        $html .= '<div class="wb-form-section">
            <div class="wb-active-toggle-box">
                <div>
                    <div class="wb-toggle-label">'.$this->_l('Enabled').'</div>
                    <div class="wb-toggle-desc">'.$this->_l('Activate this hook').'</div>
                </div>
                <label class="wb-toggle">
                    <input type="checkbox" name="enabled" value="1"'.($enabled ? ' checked' : '').'>
                    <span class="wb-toggle-slider"></span>
                </label>
            </div>
        </div>';

        $html .= '</div>'; // end form body

        // Form Footer
        $html .= '<div class="wb-form-footer">
            <a href="'.$listLink.'" class="wb-btn-cancel">'.$this->_l('Cancel').'</a>
            <button type="submit" class="wb-btn-save">'.($isEdit ? $this->_l('Save Changes') : $this->_l('Create Hook')).'</button>
        </div>';

        $html .= '</form></div>';

        // JavaScript for live preview
        $html .= '<script>
        document.querySelector(\'.wb-hook-suffix\').addEventListener(\'input\', function() {
            document.getElementById(\'hook-preview\').textContent = this.value;
        });
        </script>';

        return $html;
    }


    public function postProcess()
    {
        // Handle toggle hook enabled/disabled
        if (Tools::getIsset('wiseblock_toggle_hook')) {
            $id = (int)Tools::getValue('id_wiseblock_hook');
            $newStatus = (int)Tools::getValue('new_status');
            if ($id) {
                Db::getInstance()->update('wiseblock_hook', array(
                    'enabled' => $newStatus,
                    'date_upd' => date('Y-m-d H:i:s')
                ), 'id_wiseblock_hook='.(int)$id);
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockHook'));
        }

        // Handle custom form submission
        if (Tools::isSubmit('submitSaveHook')) {
            $id = (int)Tools::getValue('id_wiseblock_hook');
            $suffix = trim(Tools::getValue('hook_suffix', ''));
            $hookName = 'displayWiseBlock' . $suffix;
            $description = Tools::getValue('description', '');
            $enabled = (int)Tools::getValue('enabled', 0);

            // Validate
            if (empty($suffix)) {
                $this->errors[] = $this->_l('Hook suffix is required.');
                return;
            }

            // Ensure core hook exists in PrestaShop
            $idHook = (int)Hook::getIdByName($hookName);
            if (!$idHook) {
                $hook = new Hook();
                $hook->name = pSQL($hookName);
                $hook->title = pSQL($hookName);
                $hook->description = pSQL($description);
                $hook->position = 1;
                $hook->add();
                $idHook = (int)$hook->id;
            }

            // Ensure module is registered to this hook
            if ($idHook > 0 && isset($this->module) && $this->module instanceof Module) {
                if (!$this->module->isRegisteredInHook($hookName)) {
                    $this->module->registerHook($hookName);
                }
            }

            $now = date('Y-m-d H:i:s');

            if ($id) {
                // Get old hook name before updating
                $oldHookName = Db::getInstance()->getValue('SELECT hook_name FROM '._DB_PREFIX_.'wiseblock_hook WHERE id_wiseblock_hook='.(int)$id);

                // Update existing
                Db::getInstance()->update('wiseblock_hook', array(
                    'hook_name' => pSQL($hookName),
                    'description' => pSQL($description),
                    'enabled' => $enabled,
                    'date_upd' => $now
                ), 'id_wiseblock_hook='.(int)$id);

                // Propagate name change to block-hook associations
                if ($oldHookName && $oldHookName !== $hookName) {
                    Db::getInstance()->execute('
                        UPDATE `'._DB_PREFIX_.'wiseblock_block_hook`
                        SET `hook_name` = "'.pSQL($hookName).'"
                        WHERE `hook_name` = "'.pSQL($oldHookName).'"
                    ');
                }
            } else {
                // Check for duplicate
                $existing = Db::getInstance()->getValue('SELECT id_wiseblock_hook FROM '._DB_PREFIX_.'wiseblock_hook WHERE hook_name="'.pSQL($hookName).'"');
                if ($existing) {
                    $this->errors[] = $this->_l('A hook with this name already exists.');
                    return;
                }

                // Insert new
                Db::getInstance()->insert('wiseblock_hook', array(
                    'hook_name' => pSQL($hookName),
                    'description' => pSQL($description),
                    'enabled' => $enabled,
                    'date_add' => $now,
                    'date_upd' => $now
                ));
            }

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockHook', true, array(), array('conf' => 4)));
        }

        parent::postProcess();
    }

    public function processDelete()
    {
        $id = (int)Tools::getValue('id_wiseblock_hook');
        if ($id) {
            $row = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'wiseblock_hook WHERE id_wiseblock_hook='.(int)$id);
            if ($row) {
                // only remove from our table; do not drop from ps_hook (safer)
                Db::getInstance()->delete('wiseblock_hook', 'id_wiseblock_hook='.(int)$id);
            }
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockHook'));
    }

    public function processSave()
    {
        // Normalize name
        $name = Tools::getValue('hook_name');
        if ($name !== null) {
            $name = trim($name);
            $_POST['hook_name'] = $name;

            // Ensure core hook exists
            $idHook = (int)Hook::getIdByName($name);
            if (!$idHook) {
                $hook = new Hook();
                $hook->name = pSQL($name);
                $hook->title = pSQL($name);
                $hook->description = pSQL($name);
                $hook->position = 1;
                $hook->add();
                $idHook = (int)$hook->id;
            }

            // Ensure module is registered to this hook
            if ($idHook > 0 && isset($this->module) && $this->module instanceof Module) {
                $isReg = method_exists($this->module, 'isRegisteredInHook')
                    ? ($this->module->isRegisteredInHook($name) || $this->module->isRegisteredInHook($idHook))
                    : false;
                if (!$isReg) {
                    $this->module->registerHook($name);
                }
            }

            // Avoid duplicate insert in our own table
            $sql = 'SELECT id_wiseblock_hook FROM '._DB_PREFIX_.'wiseblock_hook WHERE hook_name="'.pSQL($name).'"';
            $id_existing = (int)Db::getInstance()->getValue($sql);
            if ($id_existing && !Tools::getValue($this->identifier)) {
                $_POST[$this->identifier] = $id_existing; // switch to UPDATE
            }
        }

        return parent::processSave();
    }
}