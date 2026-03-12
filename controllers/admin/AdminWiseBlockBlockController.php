<?php

require_once _PS_MODULE_DIR_.'wiseblock/classes/WiseBlockBlock.php';
require_once _PS_MODULE_DIR_.'wiseblock/controllers/admin/WiseBlockAdminHelper.php';

class AdminWiseBlockBlockController extends ModuleAdminController
{
    /**
     * Translation helper - ensures correct source file for translations
     */
    protected function _l($s)
    {
        return $this->module->l($s, 'adminwiseblockblockcontroller');
    }

    public function __construct()
    {
        $this->module = Module::getInstanceByName('wiseblock');
        $this->table = 'wiseblock_block';
        $this->className = 'WiseBlockBlock';
        $this->lang = true;
        $this->bootstrap = true;
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->identifier = 'id_block';
        $this->_defaultOrderBy = 'id_block';
        $this->_defaultOrderWay = 'DESC';

        parent::__construct();

        $this->fields_list = array(
            'id_block' => array('title' => 'ID', 'align' => 'center', 'class' => 'fixed-width-xs'),
            'title' => array('title' => 'Title', 'width' => 'auto'),
            'position' => array('title' => 'Position', 'align' => 'center'),
            'logic_mode' => array('title' => 'Logic', 'align' => 'center'),
            'publish_from' => array('title' => 'From', 'type' => 'datetime'),
            'publish_to' => array('title' => 'To', 'type' => 'datetime'),
            'active' => array('title' => 'Active', 'active' => 'status', 'type' => 'bool', 'align' => 'center')
        );

        $this->bulk_actions = array(
            'delete' => array('text' => 'Delete selected', 'confirm' => 'Delete selected items?')
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
        $tabs = WiseBlockAdminHelper::renderTabs($this->context, 'AdminWiseBlockBlock');

        // Assign combined content to Smarty
        $this->context->smarty->assign([
            'content' => $tabs . $this->content,
        ]);
    }

    public function renderList()
    {
        // Get filters
        $search = Tools::getValue('wb_search', '');
        $status_filter = Tools::getValue('wb_status', 'all');

        // Check if any blocks exist at all (for empty state message)
        $totalBlocksInDb = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_block');
        $hasFiltersApplied = ($search !== '' || $status_filter !== 'all');

        // Build query
        $sql = 'SELECT b.*, COALESCE(NULLIF(bl.title, ""), (SELECT bl2.title FROM '._DB_PREFIX_.'wiseblock_block_lang bl2 WHERE bl2.id_block = b.id_block AND bl2.title != "" LIMIT 1)) as title
                FROM '._DB_PREFIX_.'wiseblock_block b
                LEFT JOIN '._DB_PREFIX_.'wiseblock_block_lang bl ON (b.id_block = bl.id_block AND bl.id_lang = '.(int)$this->context->language->id.')
                WHERE 1=1';

        if ($search) {
            $sql .= ' AND bl.title LIKE "%'.pSQL($search).'%"';
        }

        if ($status_filter === 'active') {
            $sql .= ' AND b.active = 1';
        } elseif ($status_filter === 'inactive') {
            $sql .= ' AND b.active = 0';
        }

        $sql .= ' ORDER BY b.id_block DESC';

        $blocks = Db::getInstance()->executeS($sql);
        $total = count($blocks);

        // Pagination
        $page = (int)Tools::getValue('wb_page', 1);
        $per_page = 20;
        $total_pages = max(1, ceil($total / $per_page));
        $offset = ($page - 1) * $per_page;
        $blocks = array_slice($blocks, $offset, $per_page);

        $html = '<div class="wb-list-container">';

        // Search and filter bar - with inline styles as fallback
        $html .= '<form method="get" id="wb-filter-form" class="wb-filter-bar" style="display:flex !important; flex-direction:row !important; align-items:center !important; gap:16px !important; padding:20px 24px !important; background:white !important; border-bottom:1px solid #e5e7eb !important;">
            <input type="hidden" name="controller" value="AdminWiseBlockBlock">
            <input type="hidden" name="token" value="'.Tools::getAdminTokenLite('AdminWiseBlockBlock').'">
            <div class="wb-search-box" style="flex:1 1 auto !important; position:relative !important; min-width:200px !important;">
                <i class="icon-search wb-search-icon" style="position:absolute !important; left:16px !important; top:50% !important; transform:translateY(-50%) !important; color:#9ca3af !important; z-index:1 !important;"></i>
                <input type="text" name="wb_search" class="wb-search-input" placeholder="'.$this->_l('Search blocks...').'" value="'.htmlspecialchars($search).'" style="width:100% !important; height:44px !important; padding:0 16px 0 44px !important; border:1px solid #e5e7eb !important; border-radius:8px !important; font-size:15px !important; box-sizing:border-box !important;">
            </div>
            <select name="wb_status" class="wb-status-select" onchange="document.getElementById(\'wb-filter-form\').submit()" style="flex:0 0 auto !important; width:150px !important; min-width:150px !important; max-width:150px !important; height:44px !important; padding:0 36px 0 14px !important; border:1px solid #e5e7eb !important; border-radius:8px !important; font-size:14px !important; -webkit-appearance:none !important; appearance:none !important; background:white url(\'data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2716%27 height=%2716%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath d=%27M6 9l6 6 6-6%27/%3E%3C/svg%3E\') no-repeat right 12px center !important;">
                <option value="all"'.($status_filter === 'all' ? ' selected' : '').'>'.$this->_l('All Status').'</option>
                <option value="active"'.($status_filter === 'active' ? ' selected' : '').'>'.$this->_l('Active Only').'</option>
                <option value="inactive"'.($status_filter === 'inactive' ? ' selected' : '').'>'.$this->_l('Inactive Only').'</option>
            </select>
            <a href="'.$this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('addwiseblock_block' => 1)).'" class="wb-btn-new-block" style="display:inline-flex !important; align-items:center !important; gap:8px !important; height:44px !important; padding:0 24px !important; background:linear-gradient(135deg, #6366f1 0%, #a855f7 100%) !important; color:#fff !important; border:none !important; border-radius:8px !important; font-size:15px !important; font-weight:600 !important; text-decoration:none !important; white-space:nowrap !important;">
                <i class="icon-plus" style="color:#fff !important;"></i> '.$this->_l('New Block').'
            </a>
        </form>';

        // Bulk actions bar
        $html .= '<div id="wb-bulk-actions" class="wb-bulk-actions">
            <div class="wb-bulk-actions-left">
                <span class="wb-bulk-selected"><span id="wb-selected-count">0</span> '.$this->_l('blocks selected').'</span>
                <div class="wb-bulk-buttons">
                    <button type="button" class="wb-bulk-btn" onclick="bulkAction(\'enable\')">
                        <i class="icon-check"></i> '.$this->_l('Enable').'
                    </button>
                    <button type="button" class="wb-bulk-btn" onclick="bulkAction(\'disable\')">
                        <i class="icon-ban-circle"></i> '.$this->_l('Disable').'
                    </button>
                    <button type="button" class="wb-bulk-btn wb-bulk-btn-delete" onclick="bulkAction(\'delete\')">
                        <i class="icon-trash"></i> '.$this->_l('Delete').'
                    </button>
                </div>
            </div>
            <button type="button" class="wb-bulk-btn wb-bulk-btn-cancel" onclick="clearSelection()">'.$this->_l('Cancel').'</button>
        </div>';

        // Blocks table
        $html .= '<div class="wb-table-container">
            <table class="wb-blocks-table">
                <thead>
                    <tr>
                        <th class="wb-th-checkbox"><input type="checkbox" id="wb-select-all" onchange="toggleSelectAll(this)"></th>
                        <th class="wb-th-id">ID</th>
                        <th class="wb-th-title">'.$this->_l('TITLE').'</th>
                        <th class="wb-th-hooks">'.$this->_l('ASSIGNED HOOKS').'</th>
                        <th class="wb-th-position">'.$this->_l('POSITION').'</th>
                        <th class="wb-th-logic">'.$this->_l('LOGIC').'</th>
                        <th class="wb-th-schedule">'.$this->_l('SCHEDULE').'</th>
                        <th class="wb-th-status">'.$this->_l('STATUS').'</th>
                        <th class="wb-th-actions">'.$this->_l('ACTIONS').'</th>
                    </tr>
                </thead>
                <tbody>';

        if (empty($blocks)) {
            if ($totalBlocksInDb > 0 && $hasFiltersApplied) {
                // Blocks exist but filters don't match any
                $clearFiltersLink = $this->context->link->getAdminLink('AdminWiseBlockBlock', true);
                $html .= '<tr><td colspan="9" class="wb-empty-state">
                    <div class="wb-empty-content">
                        <i class="icon-filter wb-empty-icon"></i>
                        <p class="wb-empty-title">'.$this->_l('No blocks match your filters').'</p>
                        <p class="wb-empty-desc">'.$this->_l('Try adjusting your search or filter criteria, or create a new block').'</p>
                        <div class="wb-empty-actions">
                            <a href="'.$clearFiltersLink.'" class="wb-btn-secondary">
                                <i class="icon-refresh"></i> '.$this->_l('Clear Filters').'
                            </a>
                            <a href="'.$this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('addwiseblock_block' => 1)).'" class="wb-btn-new-block">
                                <i class="icon-plus"></i> '.$this->_l('New Block').'
                            </a>
                        </div>
                    </div>
                </td></tr>';
            } else {
                // No blocks exist at all
                $html .= '<tr><td colspan="9" class="wb-empty-state">
                    <div class="wb-empty-content">
                        <i class="icon-file-text wb-empty-icon"></i>
                        <p class="wb-empty-title">'.$this->_l('No content blocks yet').'</p>
                        <p class="wb-empty-desc">'.$this->_l('Get started by creating your first content block').'</p>
                        <a href="'.$this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('addwiseblock_block' => 1)).'" class="wb-btn-new-block">
                            <i class="icon-plus"></i> '.$this->_l('Create First Block').'
                        </a>
                    </div>
                </td></tr>';
            }
        } else {
            foreach ($blocks as $block) {
                // Get hooks for this block
                $hooks = Db::getInstance()->executeS('SELECT hook_name FROM '._DB_PREFIX_.'wiseblock_block_hook WHERE id_block='.(int)$block['id_block']);
                $hook_badges = '';
                foreach ($hooks as $h) {
                    $hook_badges .= '<span class="wb-hook-badge">'.$h['hook_name'].'</span>';
                }
                if (empty($hook_badges)) {
                    $hook_badges = '<span class="wb-no-hooks">'.$this->_l('No hooks assigned').'</span>';
                }

                // Logic badge
                $logic_badge = $block['logic_mode'] === 'OR'
                    ? '<span class="wb-logic-badge wb-logic-or">OR</span>'
                    : '<span class="wb-logic-badge wb-logic-and">AND</span>';

                // Schedule
                $schedule = '';
                $hasFrom = $block['publish_from'] && $block['publish_from'] !== '0000-00-00 00:00:00';
                $hasTo = $block['publish_to'] && $block['publish_to'] !== '0000-00-00 00:00:00';

                if ($hasFrom || $hasTo) {
                    if ($hasFrom) {
                        $schedule .= date('Y-m-d', strtotime($block['publish_from']));
                    }
                    $schedule .= '<br><span class="wb-schedule-to">'.$this->_l('to').'</span><br>';
                    if ($hasTo) {
                        $schedule .= date('Y-m-d', strtotime($block['publish_to']));
                    } else {
                        $schedule .= '∞';
                    }
                } else {
                    $schedule = '<span class="wb-no-schedule">'.$this->_l('No schedule').'</span>';
                }

                // Toggle switch
                $checked = $block['active'] ? 'checked' : '';

                $editLink = $this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('id_block' => $block['id_block'], 'updatewiseblock_block' => 1));
                $duplicateLink = $this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('id_block' => $block['id_block'], 'duplicatewiseblock_block' => 1));
                $deleteLink = $this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('id_block' => $block['id_block'], 'deletewiseblock_block' => 1));

                $html .= '<tr class="wb-block-row">
                    <td class="wb-td-checkbox"><input type="checkbox" value="'.$block['id_block'].'" class="wb-row-checkbox" onchange="updateBulkBar()"></td>
                    <td class="wb-td-id"><span class="wb-id-badge">'.$block['id_block'].'</span></td>
                    <td class="wb-td-title">'.htmlspecialchars($block['title']).'</td>
                    <td class="wb-td-hooks">'.$hook_badges.'</td>
                    <td class="wb-td-position">'.$block['position'].'</td>
                    <td class="wb-td-logic">'.$logic_badge.'</td>
                    <td class="wb-td-schedule">'.$schedule.'</td>
                    <td class="wb-td-status">
                        <label class="wb-toggle">
                            <input type="checkbox" '.$checked.' onchange="toggleBlockStatus('.$block['id_block'].', this)">
                            <span class="wb-toggle-slider"></span>
                        </label>
                    </td>
                    <td class="wb-td-actions" style="text-align:center;">
                        <div class="wb-actions-group" style="display:inline-flex !important; gap:6px !important;">
                            <a href="'.$editLink.'" class="wb-action-btn wb-action-edit" title="Edit">
                                <i class="icon-pencil"></i>
                            </a>
                            <a href="'.$duplicateLink.'" class="wb-action-btn wb-action-duplicate" title="'.$this->_l('Duplicate').'">
                                <i class="icon-copy"></i>
                            </a>
                            <a href="'.$deleteLink.'" class="wb-action-btn wb-action-delete" title="'.$this->_l('Delete').'" onclick="return confirm(\''.$this->_l('Are you sure you want to delete this block?').'\')">
                                <i class="icon-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>';
            }
        }

        $html .= '</tbody></table></div>';

        // Pagination footer
        $startItem = $total > 0 ? (($page - 1) * $per_page + 1) : 0;
        $endItem = min($page * $per_page, $total);

        $html .= '<div class="wb-pagination">
            <div class="wb-pagination-info">'.$startItem.'-'.$endItem.' '.$this->_l('of').' '.$total.'</div>
            <div class="wb-pagination-buttons">';

        if ($page > 1) {
            $html .= '<a href="'.$this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('wb_page' => ($page - 1), 'wb_search' => $search, 'wb_status' => $status_filter)).'" class="wb-page-btn">'.$this->_l('Previous').'</a>';
        } else {
            $html .= '<span class="wb-page-btn wb-page-btn-disabled">'.$this->_l('Previous').'</span>';
        }

        $html .= '<span class="wb-page-btn wb-page-btn-current">'.$page.'</span>';

        if ($page < $total_pages) {
            $html .= '<a href="'.$this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('wb_page' => ($page + 1), 'wb_search' => $search, 'wb_status' => $status_filter)).'" class="wb-page-btn">'.$this->_l('Next').'</a>';
        } else {
            $html .= '<span class="wb-page-btn wb-page-btn-disabled">'.$this->_l('Next').'</span>';
        }

        $html .= '</div></div></div>';

        // JavaScript
        $html .= '<script>
        function toggleBlockStatus(id, checkbox) {
            var active = checkbox.checked ? 1 : 0;
            $.ajax({
                url: "'.$this->context->link->getAdminLink('AdminWiseBlockBlock', true).'",
                method: "POST",
                data: { ajax: 1, action: "toggleStatus", id_block: id, active: active },
                success: function(response) { console.log("Status updated"); },
                error: function() { checkbox.checked = !checkbox.checked; alert("'.$this->_l('Error updating status').'"); }
            });
        }

        function updateBulkBar() {
            var checked = document.querySelectorAll(".wb-row-checkbox:checked");
            var count = checked.length;
            document.getElementById("wb-selected-count").textContent = count;
            var bar = document.getElementById("wb-bulk-actions");
            if (count > 0) {
                bar.classList.add("active");
            } else {
                bar.classList.remove("active");
            }
        }

        function toggleSelectAll(checkbox) {
            var rows = document.querySelectorAll(".wb-row-checkbox");
            rows.forEach(function(row) { row.checked = checkbox.checked; });
            updateBulkBar();
        }

        function clearSelection() {
            document.querySelectorAll(".wb-row-checkbox").forEach(function(cb) { cb.checked = false; });
            document.getElementById("wb-select-all").checked = false;
            updateBulkBar();
        }

        function getSelectedIds() {
            var ids = [];
            document.querySelectorAll(".wb-row-checkbox:checked").forEach(function(cb) {
                ids.push(cb.value);
            });
            return ids;
        }

        function bulkAction(action) {
            var ids = getSelectedIds();
            if (ids.length === 0) return;

            if (action === "delete" && !confirm("'.$this->_l('Are you sure you want to delete').' " + ids.length + " '.$this->_l('blocks').'?")) {
                return;
            }

            $.ajax({
                url: "'.$this->context->link->getAdminLink('AdminWiseBlockBlock', true).'",
                method: "POST",
                data: { ajax: 1, action: "bulkAction", bulk_action: action, ids: ids },
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert("'.$this->_l('Error performing bulk action').'");
                }
            });
        }

        // Add event listeners to row checkboxes
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".wb-row-checkbox").forEach(function(cb) {
                cb.addEventListener("change", updateBulkBar);
            });
        });
        </script>';

        return $html;
    }

    public function ajaxProcessToggleStatus()
    {
        $id_block = (int)Tools::getValue('id_block');
        $active = (int)Tools::getValue('active');

        if ($id_block) {
            Db::getInstance()->update('wiseblock_block', array('active' => $active), 'id_block='.$id_block);
            die(json_encode(array('success' => true)));
        }

        die(json_encode(array('success' => false)));
    }

    public function ajaxProcessGetFeatureValues()
    {
        $id_feature = (int)Tools::getValue('id_feature');
        if (!$id_feature) {
            die(json_encode(array()));
        }

        $values = Db::getInstance()->executeS('
            SELECT fv.id_feature_value, fvl.value
            FROM '._DB_PREFIX_.'feature_value fv
            LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang = '.(int)$this->context->language->id.')
            WHERE fv.id_feature = '.(int)$id_feature.'
            ORDER BY fvl.value ASC
        ');

        die(json_encode($values ? $values : array()));
    }

    public function ajaxProcessBulkAction()
    {
        $action = Tools::getValue('bulk_action');
        $ids = Tools::getValue('ids');

        if (!is_array($ids) || empty($ids)) {
            die(json_encode(array('success' => false, 'error' => 'No IDs provided')));
        }

        $ids = array_map('intval', $ids);
        $idList = implode(',', $ids);

        switch ($action) {
            case 'enable':
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'wiseblock_block SET active = 1 WHERE id_block IN ('.$idList.')');
                break;
            case 'disable':
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'wiseblock_block SET active = 0 WHERE id_block IN ('.$idList.')');
                break;
            case 'delete':
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'wiseblock_block WHERE id_block IN ('.$idList.')');
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'wiseblock_block_lang WHERE id_block IN ('.$idList.')');
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'wiseblock_block_hook WHERE id_block IN ('.$idList.')');
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'wiseblock_rule WHERE id_block IN ('.$idList.')');
                break;
            default:
                die(json_encode(array('success' => false, 'error' => 'Unknown action')));
        }

        die(json_encode(array('success' => true)));
    }

    protected function duplicateBlock($id_block)
    {
        // Get original block
        $original = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'wiseblock_block WHERE id_block='.(int)$id_block);
        if (!$original) {
            return false;
        }

        // Create new block
        $new_block = $original;
        unset($new_block['id_block']);
        $new_block['date_add'] = date('Y-m-d H:i:s');
        $new_block['date_upd'] = date('Y-m-d H:i:s');

        Db::getInstance()->insert('wiseblock_block', $new_block);
        $new_id = (int)Db::getInstance()->Insert_ID();

        // Copy translations
        $langs = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'wiseblock_block_lang WHERE id_block='.(int)$id_block);
        foreach ($langs as $lang) {
            $lang['id_block'] = $new_id;
            $lang['title'] = $lang['title'].' (Copy)';
            Db::getInstance()->insert('wiseblock_block_lang', $lang);
        }

        // Copy hooks
        $hooks = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'wiseblock_block_hook WHERE id_block='.(int)$id_block);
        foreach ($hooks as $hook) {
            Db::getInstance()->insert('wiseblock_block_hook', array(
                'id_block' => $new_id,
                'hook_name' => $hook['hook_name']
            ));
        }

        // Copy rules
        $rules = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'wiseblock_rule WHERE id_block='.(int)$id_block);
        foreach ($rules as $rule) {
            $rule['id_block'] = $new_id;
            unset($rule['id_rule']);
            Db::getInstance()->insert('wiseblock_rule', $rule);
        }

        return $new_id;
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        // Get available hooks
        $hooks = Db::getInstance()->executeS('SELECT hook_name FROM '._DB_PREFIX_.'wiseblock_hook WHERE enabled = 1 ORDER BY hook_name');
        $selectedHooks = $obj->id ? $obj->getHooks() : array();

        // Get languages
        $languages = Language::getLanguages(true);
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Get block data
        $isEdit = (bool)$obj->id;
        $active = $isEdit ? $obj->active : 1;
        $position = $isEdit ? $obj->position : 0;
        $logicMode = $isEdit ? $obj->logic_mode : 'OR';
        $publishFrom = $isEdit && $obj->publish_from && $obj->publish_from !== '0000-00-00 00:00:00' ? date('Y-m-d', strtotime($obj->publish_from)) : '';
        $publishTo = $isEdit && $obj->publish_to && $obj->publish_to !== '0000-00-00 00:00:00' ? date('Y-m-d', strtotime($obj->publish_to)) : '';
        $timeFrom = $isEdit && !empty($obj->time_from) ? $obj->time_from : '';
        $timeTo = $isEdit && !empty($obj->time_to) ? $obj->time_to : '';
        $daysOfWeek = $isEdit && !empty($obj->days_of_week) ? $obj->days_of_week : '';
        $autoRefresh = $isEdit ? (int)$obj->auto_refresh : 0;
        $lazyLoad = $isEdit ? (int)$obj->lazy_load : 0;
        $abVariant = $isEdit && !empty($obj->ab_variant) ? $obj->ab_variant : 'none';
        $abAutoOptimize = $isEdit ? (int)$obj->ab_auto_optimize : 0;
        $abMinViews = $isEdit && $obj->ab_min_views ? (int)$obj->ab_min_views : 500;
        $abWinner = $isEdit && !empty($obj->ab_winner) ? $obj->ab_winner : null;

        // Get multilang content
        $titles = array();
        $contents = array();
        $contentsB = array();
        $headCodes = array();
        $footerCodes = array();
        if ($isEdit) {
            foreach ($languages as $lang) {
                $langData = Db::getInstance()->getRow('SELECT title, content, content_b, head_code, footer_code FROM '._DB_PREFIX_.'wiseblock_block_lang WHERE id_block='.(int)$obj->id.' AND id_lang='.(int)$lang['id_lang']);
                $titles[$lang['id_lang']] = $langData ? $langData['title'] : '';
                $contents[$lang['id_lang']] = $langData ? $langData['content'] : '';
                $contentsB[$lang['id_lang']] = $langData ? ($langData['content_b'] ?? '') : '';
                $headCodes[$lang['id_lang']] = $langData ? ($langData['head_code'] ?? '') : '';
                $footerCodes[$lang['id_lang']] = $langData ? ($langData['footer_code'] ?? '') : '';
            }
        }

        // Build form token
        $token = Tools::getAdminTokenLite('AdminWiseBlockBlock');
        $formAction = $this->context->link->getAdminLink('AdminWiseBlockBlock');
        $listLink = $this->context->link->getAdminLink('AdminWiseBlockBlock');

        // Start HTML
        $html = '<div class="wb-form-container">';

        // Form Header
        $html .= '<div class="wb-form-header">
            <h2>'.($isEdit ? $this->_l('Edit Block') : $this->_l('New Block')).'</h2>
            <p>'.$this->_l('Configure your content block settings').'</p>
        </div>';

        // Form
        $html .= '<form method="post" action="'.$formAction.'" enctype="multipart/form-data" id="wb-block-form">';
        $html .= '<input type="hidden" name="submitAddwiseblock_block" value="1">';
        if ($isEdit) {
            $html .= '<input type="hidden" name="id_block" value="'.(int)$obj->id.'">';
        }

        $html .= '<div class="wb-form-body">';

        // ========== SECTION 1: Basic Settings ==========
        $html .= '<div class="wb-form-section">
            <div class="wb-form-section-title">'.$this->_l('Basic Settings').'</div>';

        // Active toggle box
        $activeChecked = $active ? 'checked' : '';
        $html .= '<div class="wb-active-toggle-box">
            <div>
                <div class="wb-toggle-label">'.$this->_l('Active').'</div>
                <div class="wb-toggle-desc">'.$this->_l('Enable or disable this block').'</div>
            </div>
            <label class="wb-toggle">
                <input type="checkbox" name="active" value="1" '.$activeChecked.'>
                <span class="wb-toggle-slider"></span>
            </label>
        </div>';

        // Input style (gray background, rounded)
        $inputStyle = 'background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;height:48px;padding:0 16px;font-size:14px;width:100%;box-sizing:border-box;';

        // Block Title
        $html .= '<div class="wb-form-group">
            <label class="wb-form-label">'.$this->_l('Block Title').'</label>
            <input type="text" name="title_'.$defaultLang.'" class="wb-form-input" placeholder="'.$this->_l('Enter block title').'" value="'.htmlspecialchars($titles[$defaultLang] ?? '').'" style="'.$inputStyle.'">
        </div>';

        // Position
        $html .= '<div class="wb-form-group">
            <label class="wb-form-label">'.$this->_l('Position').'</label>
            <input type="number" name="position" class="wb-form-input wb-form-input-sm" value="'.(int)$position.'" min="0" style="'.$inputStyle.'width:120px;">
            <div class="wb-form-hint">'.$this->_l('Lower numbers display first').'</div>
        </div>';

        // Assigned Hooks
        $html .= '<div class="wb-form-group">
            <label class="wb-form-label">'.$this->_l('Assigned Hooks').'</label>
            <div class="wb-hook-tags" id="wb-selected-hooks">';
        foreach ($selectedHooks as $hook) {
            $html .= '<span class="wb-hook-tag" data-hook="'.htmlspecialchars($hook).'">
                '.htmlspecialchars($hook).'
                <button type="button" class="wb-hook-tag-remove" onclick="removeHook(this)">×</button>
                <input type="hidden" name="hooks[]" value="'.htmlspecialchars($hook).'">
            </span>';
        }
        $selectStyle = 'background:#f9fafb url(\"data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236b7280\' stroke-width=\'2\'%3E%3Cpath d=\'M6 9l6 6 6-6\'/%3E%3C/svg%3E\") no-repeat right 14px center;border:1px solid #e5e7eb;border-radius:10px;height:48px;padding:0 40px 0 16px;font-size:14px;width:100%;box-sizing:border-box;appearance:none;-webkit-appearance:none;cursor:pointer;';
        $html .= '</div>
            <select class="wb-form-select" id="wb-hook-select" onchange="addHook(this)" style="margin-top:8px;'.$selectStyle.'">
                <option value="">'.$this->_l('Select a hook to add...').'</option>';
        foreach ($hooks as $h) {
            $html .= '<option value="'.htmlspecialchars($h['hook_name']).'">'.htmlspecialchars($h['hook_name']).'</option>';
        }
        $html .= '</select>
        </div>';

        $html .= '</div>'; // end Basic Settings section

        // ========== SECTION 2: Display Rules ==========
        $html .= '<div class="wb-form-section">
            <div class="wb-form-section-title">'.$this->_l('Display Rules').'</div>';

        // Rule Logic cards
        $html .= '<div class="wb-form-group">
            <label class="wb-form-label">'.$this->_l('Rule Logic').'</label>
            <div class="wb-logic-cards">
                <label class="wb-logic-card '.($logicMode === 'OR' ? 'active' : '').'" onclick="selectLogic(\'OR\', this)">
                    <input type="radio" name="logic_mode" value="OR" '.($logicMode === 'OR' ? 'checked' : '').'>
                    <div class="wb-logic-card-content">
                        <div class="wb-logic-card-title">'.$this->_l('OR Logic').'</div>
                        <div class="wb-logic-card-desc">'.$this->_l('Show if any rule matches').'</div>
                    </div>
                    <div class="wb-logic-card-radio"></div>
                </label>
                <label class="wb-logic-card '.($logicMode === 'AND' ? 'active' : '').'" onclick="selectLogic(\'AND\', this)">
                    <input type="radio" name="logic_mode" value="AND" '.($logicMode === 'AND' ? 'checked' : '').'>
                    <div class="wb-logic-card-content">
                        <div class="wb-logic-card-title">'.$this->_l('AND Logic').'</div>
                        <div class="wb-logic-card-desc">'.$this->_l('Show if all rules match').'</div>
                    </div>
                    <div class="wb-logic-card-radio"></div>
                </label>
            </div>
        </div>';

        // Schedule dates
        $html .= '<div class="wb-date-row">
            <div class="wb-form-group">
                <label class="wb-form-label">'.$this->_l('Schedule Start').'</label>
                <input type="date" name="publish_from" class="wb-form-input" value="'.$publishFrom.'" style="'.$inputStyle.'">
            </div>
            <div class="wb-form-group">
                <label class="wb-form-label">'.$this->_l('Schedule End').'</label>
                <input type="date" name="publish_to" class="wb-form-input" value="'.$publishTo.'" style="'.$inputStyle.'">
            </div>
        </div>';

        // Days of week
        $dayNames = array(
            1 => $this->_l('Mon'), 2 => $this->_l('Tue'), 3 => $this->_l('Wed'),
            4 => $this->_l('Thu'), 5 => $this->_l('Fri'), 6 => $this->_l('Sat'), 7 => $this->_l('Sun')
        );
        $selectedDays = !empty($daysOfWeek) ? array_map('intval', explode(',', $daysOfWeek)) : array();

        $html .= '<div class="wb-form-group" style="margin-top:16px;">
            <label class="wb-form-label">'.$this->_l('Days of Week').'</label>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">';
        foreach ($dayNames as $dayNum => $dayName) {
            $isSelected = empty($selectedDays) || in_array($dayNum, $selectedDays);
            $html .= '<label style="display:flex;align-items:center;gap:4px;padding:6px 12px;border-radius:8px;border:1px solid '.($isSelected ? '#7c3aed' : '#e5e7eb').';background:'.($isSelected ? '#f5f3ff' : '#f9fafb').';cursor:pointer;font-size:13px;transition:all .15s;" onclick="toggleDay(this)">
                <input type="checkbox" name="days_of_week[]" value="'.$dayNum.'" '.($isSelected ? 'checked' : '').' style="display:none;">
                '.$dayName.'
            </label>';
        }
        $html .= '</div>
            <div class="wb-form-hint">'.$this->_l('Leave all selected to show every day').'</div>
        </div>';

        // Schedule hours
        $html .= '<div class="wb-date-row" style="margin-top:12px;">
            <div class="wb-form-group">
                <label class="wb-form-label">'.$this->_l('Time From').'</label>
                <input type="time" name="time_from" class="wb-form-input" value="'.$timeFrom.'" style="'.$inputStyle.'">
            </div>
            <div class="wb-form-group">
                <label class="wb-form-label">'.$this->_l('Time To').'</label>
                <input type="time" name="time_to" class="wb-form-input" value="'.$timeTo.'" style="'.$inputStyle.'">
            </div>
        </div>
        <div class="wb-form-hint" style="margin-top:8px;">
            '.$this->_l('Optional: Show block only during specific hours (e.g. 09:00 - 18:00)').'
        </div>';

        $html .= '</div>'; // end Display Rules section

        // ========== SECTION: Advanced Options ==========
        $html .= '<div class="wb-form-section">
            <div class="wb-form-section-title">'.$this->_l('Advanced Options').'</div>';

        // Auto-refresh toggle
        $html .= '<div class="wb-active-toggle-box">
            <div>
                <div class="wb-toggle-label">'.$this->_l('Auto-refresh on cart update').'</div>
                <div class="wb-toggle-desc">'.$this->_l('Refresh block via AJAX when cart changes (add/remove product)').'</div>
            </div>
            <label class="wb-toggle">
                <input type="checkbox" name="auto_refresh" value="1" '.($autoRefresh ? 'checked' : '').'>
                <span class="wb-toggle-slider"></span>
            </label>
        </div>';

        // Lazy load toggle
        $html .= '<div class="wb-active-toggle-box" style="margin-top:12px;">
            <div>
                <div class="wb-toggle-label">'.$this->_l('Lazy loading').'</div>
                <div class="wb-toggle-desc">'.$this->_l('Load block content only when it becomes visible in viewport').'</div>
            </div>
            <label class="wb-toggle">
                <input type="checkbox" name="lazy_load" value="1" '.($lazyLoad ? 'checked' : '').'>
                <span class="wb-toggle-slider"></span>
            </label>
        </div>';

        // A/B Testing
        $html .= '<div class="wb-form-group" style="margin-top:16px;">
            <label class="wb-form-label">'.$this->_l('A/B Testing').'</label>
            <div class="wb-logic-cards">
                <label class="wb-logic-card '.($abVariant === 'none' ? 'active' : '').'" onclick="selectAB(\'none\', this)">
                    <input type="radio" name="ab_variant" value="none" '.($abVariant === 'none' ? 'checked' : '').'>
                    <div class="wb-logic-card-content">
                        <div class="wb-logic-card-title">'.$this->_l('Off').'</div>
                        <div class="wb-logic-card-desc">'.$this->_l('Show single content').'</div>
                    </div>
                    <div class="wb-logic-card-radio"></div>
                </label>
                <label class="wb-logic-card '.($abVariant !== 'none' ? 'active' : '').'" onclick="selectAB(\'A\', this)">
                    <input type="radio" name="ab_variant" value="A" '.($abVariant !== 'none' ? 'checked' : '').'>
                    <div class="wb-logic-card-content">
                        <div class="wb-logic-card-title">'.$this->_l('A/B Test').'</div>
                        <div class="wb-logic-card-desc">'.$this->_l('50/50 random split').'</div>
                    </div>
                    <div class="wb-logic-card-radio"></div>
                </label>
            </div>
            <div class="wb-form-hint">'.$this->_l('When enabled, visitors randomly see variant A or B. Results tracked in stats.').'</div>
        </div>';

        // A/B Auto-optimize settings (visible only when A/B is enabled)
        $html .= '<div id="wb-ab-optimize-section" style="margin-top:12px;'.($abVariant === 'none' ? 'display:none;' : '').'">
            <div style="padding:14px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <label class="wb-form-label" style="margin:0;">'.$this->_l('Auto-optimize').'</label>
                    <label class="wb-toggle">
                        <input type="checkbox" name="ab_auto_optimize" value="1" '.($abAutoOptimize ? 'checked' : '').'>
                        <span class="wb-toggle-slider"></span>
                    </label>
                </div>
                <div class="wb-form-hint" style="margin-bottom:10px;">'.$this->_l('Automatically select the winning variant after collecting enough data based on CTR (click-through rate).').'</div>
                <div class="wb-form-group" style="margin-bottom:0;">
                    <label class="wb-form-label">'.$this->_l('Minimum views per variant').'</label>
                    <input type="number" name="ab_min_views" class="wb-form-input" value="'.(int)$abMinViews.'" min="100" step="100" style="width:150px;">
                    <div class="wb-form-hint">'.$this->_l('Each variant must reach this number of views before a winner is selected.').'</div>
                </div>
            </div>
        </div>';

        // A/B Stats panel (visible only when editing an existing block with A/B enabled)
        if ($isEdit && $abVariant !== 'none') {
            $abStats = $this->getABStats((int)$obj->id);
            if ($abStats) {
                $html .= '<div style="margin-top:12px;padding:14px 16px;background:#faf5ff;border:1px solid #e9d5ff;border-radius:8px;">';
                $html .= '<label class="wb-form-label" style="margin-bottom:10px;">'.$this->_l('A/B Test Results').'</label>';

                if ($abWinner) {
                    $html .= '<div style="padding:8px 12px;background:#d1fae5;border:1px solid #6ee7b7;border-radius:6px;margin-bottom:12px;font-size:13px;color:#065f46;">
                        <strong>'.$this->_l('Winner').': '.$this->_l('Variant').' '.$abWinner.'</strong> — '.$this->_l('This variant is now shown to all visitors.').'
                        <br><button type="button" onclick="resetABTest('.(int)$obj->id.')" style="margin-top:6px;padding:4px 12px;background:#fff;border:1px solid #d1d5db;border-radius:4px;cursor:pointer;font-size:12px;">'.$this->_l('Reset test').'</button>
                    </div>';
                }

                $html .= '<table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="text-align:left;padding:6px 8px;">'.$this->_l('Variant').'</th>
                        <th style="text-align:right;padding:6px 8px;">'.$this->_l('Views').'</th>
                        <th style="text-align:right;padding:6px 8px;">'.$this->_l('Clicks').'</th>
                        <th style="text-align:right;padding:6px 8px;">CTR</th>
                    </tr>';

                foreach (['A', 'B'] as $v) {
                    $views = isset($abStats[$v]) ? (int)$abStats[$v]['views'] : 0;
                    $clicks = isset($abStats[$v]) ? (int)$abStats[$v]['clicks'] : 0;
                    $ctr = $views > 0 ? round(($clicks / $views) * 100, 2) : 0;
                    $isWinner = $abWinner === $v;

                    $html .= '<tr style="border-bottom:1px solid #f3f4f6;'.($isWinner ? 'background:#d1fae5;font-weight:600;' : '').'">
                        <td style="padding:6px 8px;">'.$this->_l('Variant').' '.$v.($isWinner ? ' ✓' : '').'</td>
                        <td style="text-align:right;padding:6px 8px;">'.number_format($views, 0, ',', ' ').'</td>
                        <td style="text-align:right;padding:6px 8px;">'.number_format($clicks, 0, ',', ' ').'</td>
                        <td style="text-align:right;padding:6px 8px;">'.$ctr.'%</td>
                    </tr>';
                }

                $html .= '</table></div>';
            }
        }

        $html .= '</div>'; // end Advanced Options section

        // ========== SECTION 3: Content ==========
        $html .= '<div class="wb-form-section">
            <div class="wb-form-section-title">'.$this->_l('Content').'</div>';

        // HTML Content with language selector (only if multiple languages)
        $hasMultipleLanguages = count($languages) > 1;

        $html .= '<div class="wb-form-group">';

        $html .= $this->renderLangFieldGroup('content', $this->_l('HTML Content'), $languages, $defaultLang, $hasMultipleLanguages, $contents, array(
            'style' => 'background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;font-family:Courier New,monospace;font-size:13px;line-height:1.6;width:100%;min-height:180px;box-sizing:border-box;resize:vertical;',
            'rows' => 10,
            'placeholderPrefix' => $this->_l('Enter HTML content for'),
        ));

        $html .= '</div>';

        // ---- Content B (A/B variant) ----
        $html .= '<div class="wb-form-group wb-ab-variant-b" id="wb-content-b-section" style="margin-top:16px;'.($abVariant === 'none' ? 'display:none;' : '').'">';
        $html .= '<div style="padding:10px 14px;background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;margin-bottom:12px;font-size:12px;color:#92400e;">
            <strong>Variant B</strong> — '.$this->_l('This content is shown to 50% of visitors when A/B testing is enabled').'
        </div>';
        $html .= $this->renderLangFieldGroup('content_b', $this->_l('HTML Content').' — Variant B', $languages, $defaultLang, $hasMultipleLanguages, $contentsB, array(
            'style' => 'background:#fffbeb;border:1px solid #fcd34d;border-radius:10px;padding:14px 16px;font-family:Courier New,monospace;font-size:13px;line-height:1.6;width:100%;min-height:180px;box-sizing:border-box;resize:vertical;',
            'rows' => 10,
            'placeholderPrefix' => $this->_l('Enter variant B content for'),
        ));
        $html .= '</div>';

        // ---- Head Code (injected into <head>) ----
        $codeTextareaStyle = 'background:#1e1e2e;color:#cdd6f4;border:1px solid #45475a;border-radius:10px;padding:14px 16px;font-family:Courier New,monospace;font-size:13px;line-height:1.6;width:100%;min-height:120px;box-sizing:border-box;resize:vertical;';
        $codeIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>';

        $html .= '<div class="wb-form-group" style="margin-top:24px;">
            <div class="wb-form-hint" style="margin-bottom:8px;">'.$this->_l('Add scripts, styles or meta tags to the page head section.').'</div>';
        $html .= $this->renderLangFieldGroup('head_code', $codeIcon.' '.$this->_l('Head Code').' <span style="font-weight:400;color:#6b7280;font-size:12px;margin-left:6px;">'.$this->_l('Injected into head').'</span>', $languages, $defaultLang, $hasMultipleLanguages, $headCodes, array(
            'style' => $codeTextareaStyle,
            'rows' => 6,
            'placeholderPrefix' => '<!-- '.$this->_l('Head code for'),
            'placeholderSuffix' => ' -->',
        ));
        $html .= '</div>';

        // ---- Footer Code (injected before </body>) ----
        $html .= '<div class="wb-form-group" style="margin-top:16px;">
            <div class="wb-form-hint" style="margin-bottom:8px;">'.$this->_l('Add scripts that should load at the end of the page. Ideal for tracking or analytics.').'</div>';
        $html .= $this->renderLangFieldGroup('footer_code', $codeIcon.' '.$this->_l('Footer Code').' <span style="font-weight:400;color:#6b7280;font-size:12px;margin-left:6px;">'.$this->_l('Injected before closing body').'</span>', $languages, $defaultLang, $hasMultipleLanguages, $footerCodes, array(
            'style' => $codeTextareaStyle,
            'rows' => 6,
            'placeholderPrefix' => '<!-- '.$this->_l('Footer code for'),
            'placeholderSuffix' => ' -->',
        ));
        $html .= '</div>';

        // ---- Available Placeholders (collapsible, after all content fields) ----
        $html .= '<div class="wb-placeholders-section" style="margin-top:24px;">
            <div class="wb-placeholders-toggle" onclick="togglePlaceholders(this)">
                <span>'.$this->_l('Available placeholders').'</span>
                <i class="icon-chevron-down"></i>
            </div>
            <div class="wb-placeholders-content" style="display:none;">
                <div class="wb-placeholders-group">
                    <div class="wb-placeholders-group-title">'.$this->_l('Product').'</div>
                    <code onclick="insertPlaceholder(\'{{product_name}}\')">{{product_name}}</code>
                    <code onclick="insertPlaceholder(\'{{price}}\')">{{price}}</code>
                    <code onclick="insertPlaceholder(\'{{price_without_tax}}\')">{{price_without_tax}}</code>
                    <code onclick="insertPlaceholder(\'{{reference}}\')">{{reference}}</code>
                    <code onclick="insertPlaceholder(\'{{ean13}}\')">{{ean13}}</code>
                    <code onclick="insertPlaceholder(\'{{manufacturer}}\')">{{manufacturer}}</code>
                    <code onclick="insertPlaceholder(\'{{stock_status}}\')">{{stock_status}}</code>
                    <code onclick="insertPlaceholder(\'{{stock_quantity}}\')">{{stock_quantity}}</code>
                    <code onclick="insertPlaceholder(\'{{weight}}\')">{{weight}}</code>
                    <code onclick="insertPlaceholder(\'{{product_url}}\')">{{product_url}}</code>
                    <code onclick="insertPlaceholder(\'{{add_to_cart_url}}\')">{{add_to_cart_url}}</code>
                </div>
                <div class="wb-placeholders-group">
                    <div class="wb-placeholders-group-title">'.$this->_l('Category').'</div>
                    <code onclick="insertPlaceholder(\'{{category_name}}\')">{{category_name}}</code>
                    <code onclick="insertPlaceholder(\'{{category_description}}\')">{{category_description}}</code>
                </div>
                <div class="wb-placeholders-group">
                    <div class="wb-placeholders-group-title">'.$this->_l('Shop & Customer').'</div>
                    <code onclick="insertPlaceholder(\'{{shop_name}}\')">{{shop_name}}</code>
                    <code onclick="insertPlaceholder(\'{{customer_name}}\')">{{customer_name}}</code>
                    <code onclick="insertPlaceholder(\'{{search_query}}\')">{{search_query}}</code>
                </div>
                <div class="wb-placeholders-group">
                    <div class="wb-placeholders-group-title">'.$this->_l('Cart').'</div>
                    <code onclick="insertPlaceholder(\'{{cart_total}}\')">{{cart_total}}</code>
                    <code onclick="insertPlaceholder(\'{{cart_total_with_shipping}}\')">{{cart_total_with_shipping}}</code>
                    <code onclick="insertPlaceholder(\'{{cart_products_count}}\')">{{cart_products_count}}</code>
                </div>
                <div class="wb-placeholders-group">
                    <div class="wb-placeholders-group-title">'.$this->_l('Free Shipping').'</div>
                    <code onclick="insertPlaceholder(\'{{free_shipping_threshold}}\')">{{free_shipping_threshold}}</code>
                    <code onclick="insertPlaceholder(\'{{free_shipping_remaining}}\')">{{free_shipping_remaining}}</code>
                    <code onclick="insertPlaceholder(\'{{free_shipping_remaining_raw}}\')">{{free_shipping_remaining_raw}}</code>
                    <code onclick="insertPlaceholder(\'{{free_shipping_progress}}\')">{{free_shipping_progress}}</code>
                    <code onclick="insertPlaceholder(\'{{free_shipping_achieved}}\')">{{free_shipping_achieved}}</code>
                </div>
                <div class="wb-placeholders-group">
                    <div class="wb-placeholders-group-title">'.$this->_l('Conditional Blocks').'</div>
                    <code onclick="insertPlaceholder(\'{{#if_free_shipping_not_achieved}}...{{/if_free_shipping_not_achieved}}\')">{{#if_free_shipping_not_achieved}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_free_shipping_achieved}}...{{/if_free_shipping_achieved}}\')">{{#if_free_shipping_achieved}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_in_stock}}...{{/if_in_stock}}\')">{{#if_in_stock}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_out_of_stock}}...{{/if_out_of_stock}}\')">{{#if_out_of_stock}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_low_stock}}...{{/if_low_stock}}\')">{{#if_low_stock}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_on_sale}}...{{/if_on_sale}}\')">{{#if_on_sale}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_cart_empty}}...{{/if_cart_empty}}\')">{{#if_cart_empty}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_cart_not_empty}}...{{/if_cart_not_empty}}\')">{{#if_cart_not_empty}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_logged_in}}...{{/if_logged_in}}\')">{{#if_logged_in}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_guest}}...{{/if_guest}}\')">{{#if_guest}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_has_manufacturer}}...{{/if_has_manufacturer}}\')">{{#if_has_manufacturer}}</code>
                    <code onclick="insertPlaceholder(\'{{#if_new_product}}...{{/if_new_product}}\')">{{#if_new_product}}</code>
                </div>
                <div class="wb-placeholders-hint">
                    <i class="icon-info-circle"></i> '.$this->_l('Click on a placeholder to insert it into the content area').'
                </div>
            </div>
        </div>';

        $html .= '</div>'; // end Content section

        $html .= '</form>'; // close main form BEFORE targeting rules to avoid nested forms

        // ========== SECTION 4: Targeting Rules (only for edit) ==========
        if ($isEdit) {
            $html .= $this->renderTargetingRulesSection((int)$obj->id);
        } else {
            $html .= '<div class="wb-form-section">
                <div class="alert alert-info" style="margin:0;background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;border-radius:8px;padding:16px 16px 16px 42px;">
                    '.$this->_l('Save the block first to configure targeting rules.').'
                </div>
            </div>';
        }

        $html .= '</div>'; // end form body

        // Form Footer (button uses form attribute to submit main form)
        $html .= '<div class="wb-form-footer">
            <a href="'.$listLink.'" class="wb-btn-cancel">'.$this->_l('Cancel').'</a>
            <button type="submit" name="submitAddwiseblock_block" value="1" class="wb-btn-save" form="wb-block-form">'.($isEdit ? $this->_l('Save Changes') : $this->_l('Create Block')).'</button>
        </div>';

        $html .= '</div>'; // end wb-form-container

        // JavaScript
        $html .= $this->renderFormJavaScript();

        return $html;
    }

    protected function renderFormJavaScript()
    {
        return '<script>
        function addHook(select) {
            var hook = select.value;
            if (!hook) return;

            // Check if already added
            var existing = document.querySelectorAll("#wb-selected-hooks .wb-hook-tag");
            for (var i = 0; i < existing.length; i++) {
                if (existing[i].dataset.hook === hook) {
                    select.value = "";
                    return;
                }
            }

            // Add tag
            var container = document.getElementById("wb-selected-hooks");
            var tag = document.createElement("span");
            tag.className = "wb-hook-tag";
            tag.dataset.hook = hook;
            tag.innerHTML = hook +
                \'<button type="button" class="wb-hook-tag-remove" onclick="removeHook(this)">×</button>\' +
                \'<input type="hidden" name="hooks[]" value="\' + hook + \'">\';
            container.appendChild(tag);

            select.value = "";
        }

        function removeHook(btn) {
            btn.parentElement.remove();
        }

        function selectLogic(value, card) {
            // Only toggle cards within same parent group
            card.closest(".wb-logic-cards").querySelectorAll(".wb-logic-card").forEach(function(c) {
                c.classList.remove("active");
            });
            card.classList.add("active");
            card.querySelector("input").checked = true;
        }

        function selectAB(value, card) {
            card.closest(".wb-logic-cards").querySelectorAll(".wb-logic-card").forEach(function(c) {
                c.classList.remove("active");
            });
            card.classList.add("active");
            card.querySelector("input").checked = true;
            // Show/hide content B section
            var section = document.getElementById("wb-content-b-section");
            if (section) {
                section.style.display = (value !== "none") ? "block" : "none";
            }
            var optSection = document.getElementById("wb-ab-optimize-section");
            if (optSection) {
                optSection.style.display = (value !== "none") ? "block" : "none";
            }
        }

        function resetABTest(idBlock) {
            if (!confirm("' . $this->_l('Are you sure? This will clear all A/B test statistics and reset the winner.') . '")) return;
            var url = "' . $this->context->link->getAdminLink('AdminWiseBlockBlock') . '&action=resetABTest&id_block=" + idBlock;
            fetch(url, { method: "GET", headers: { "X-Requested-With": "XMLHttpRequest" } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) { location.reload(); }
                    else { alert(data.error || "Error"); }
                });
        }

        function toggleDay(label) {
            var cb = label.querySelector("input[type=checkbox]");
            // Toggle is handled by browser, just update styling
            setTimeout(function() {
                if (cb.checked) {
                    label.style.borderColor = "#7c3aed";
                    label.style.background = "#f5f3ff";
                } else {
                    label.style.borderColor = "#e5e7eb";
                    label.style.background = "#f9fafb";
                }
            }, 10);
        }

        function toggleLangDropdown(event) {
            toggleLangDropdownFor("content", event);
        }

        function switchLang(group, langId, langCode, btn) {
            // Update button display
            var codeEl = document.getElementById("wb-lang-code-" + group);
            var flagEl = document.getElementById("wb-lang-flag-" + group);
            if (codeEl) codeEl.textContent = langCode;
            if (flagEl) flagEl.src = "../img/l/" + langId + ".jpg";

            // Update active states in dropdown
            var container = document.getElementById("wb-lang-selector-" + group);
            if (container) {
                container.querySelectorAll(".wb-lang-option").forEach(function(opt) {
                    opt.style.background = "white";
                    opt.style.color = "#374151";
                });
            }
            btn.style.background = "rgba(99,102,241,0.08)";
            btn.style.color = "#6366f1";

            // Switch visible textarea for this group
            document.querySelectorAll(".wb-lang-field-" + group).forEach(function(field) {
                field.classList.remove("active");
            });
            document.querySelectorAll(".wb-lang-field-" + group + "[data-lang=\'" + langId + "\']").forEach(function(field) {
                field.classList.add("active");
            });

            // Close dropdown
            var dropdown = document.getElementById("wb-lang-dropdown-" + group);
            if (dropdown) dropdown.style.display = "none";
        }

        // Backward compat wrapper
        function switchLanguage(langId, langCode, btn) {
            switchLang("content", langId, langCode, btn);
        }

        function toggleLangDropdownFor(group, event) {
            event.stopPropagation();
            var dropdown = document.getElementById("wb-lang-dropdown-" + group);
            if (!dropdown) return;
            var isVisible = dropdown.style.display === "block";
            // Close all dropdowns first
            document.querySelectorAll("[id^=wb-lang-dropdown-]").forEach(function(d) { d.style.display = "none"; });
            if (!isVisible) dropdown.style.display = "block";
        }

        // Close all lang dropdowns when clicking outside
        document.addEventListener("click", function(e) {
            document.querySelectorAll("[id^=wb-lang-selector-]").forEach(function(selector) {
                var dropdown = selector.querySelector("[id^=wb-lang-dropdown-]");
                if (dropdown && !selector.contains(e.target)) {
                    dropdown.style.display = "none";
                }
            });
        });

        function toggleTargeting() {
            var accordion = document.getElementById("wb-targeting-accordion");
            accordion.classList.toggle("open");
        }

        function togglePlaceholders(el) {
            var content = el.nextElementSibling;
            var icon = el.querySelector("i");
            if (content.style.display === "none") {
                content.style.display = "block";
                icon.style.transform = "rotate(180deg)";
            } else {
                content.style.display = "none";
                icon.style.transform = "rotate(0deg)";
            }
        }

        function insertPlaceholder(placeholder) {
            var activeTextarea = document.querySelector(".wb-lang-field-content.active");
            if (!activeTextarea) {
                activeTextarea = document.querySelector(".wb-lang-field-content");
            }
            if (activeTextarea) {
                var start = activeTextarea.selectionStart;
                var end = activeTextarea.selectionEnd;
                var text = activeTextarea.value;
                activeTextarea.value = text.substring(0, start) + placeholder + text.substring(end);
                activeTextarea.selectionStart = activeTextarea.selectionEnd = start + placeholder.length;
                activeTextarea.focus();
            }
        }
        </script>';
    }

    /**
     * Render a multilang textarea field group with its own language switcher.
     * @param string $group      Field name prefix (e.g. 'content', 'head_code', 'footer_code')
     * @param string $label      Label HTML
     * @param array  $languages  PS languages array
     * @param int    $defaultLang Default language ID
     * @param bool   $hasMultipleLanguages
     * @param array  $values     Values indexed by id_lang
     * @param array  $opts       Options: style, rows, placeholderPrefix, placeholderSuffix
     */
    private function renderLangFieldGroup($group, $label, $languages, $defaultLang, $hasMultipleLanguages, $values, $opts = array())
    {
        $style = isset($opts['style']) ? $opts['style'] : '';
        $rows = isset($opts['rows']) ? (int)$opts['rows'] : 6;
        $phPrefix = isset($opts['placeholderPrefix']) ? $opts['placeholderPrefix'] : '';
        $phSuffix = isset($opts['placeholderSuffix']) ? $opts['placeholderSuffix'] : '...';

        $html = '';

        if ($hasMultipleLanguages) {
            $defaultIso = 'EN';
            foreach ($languages as $l) {
                if ($l['id_lang'] == $defaultLang) { $defaultIso = strtoupper($l['iso_code']); break; }
            }
            $html .= '<div class="wb-form-label-row" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <label class="wb-form-label" style="margin-bottom:0;">'.$label.'</label>
                <div class="wb-lang-selector" id="wb-lang-selector-'.$group.'" style="position:relative;display:inline-block;">
                    <button type="button" class="wb-lang-selector-btn" onclick="toggleLangDropdownFor(\''.$group.'\', event)" style="display:inline-flex;align-items:center;gap:8px;padding:6px 12px;background:white;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-weight:500;color:#374151;cursor:pointer;">
                        <img src="../img/l/'.$defaultLang.'.jpg" alt="" id="wb-lang-flag-'.$group.'" style="width:20px;height:14px;border-radius:2px;">
                        <span id="wb-lang-code-'.$group.'">'.$defaultIso.'</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="wb-lang-dropdown" id="wb-lang-dropdown-'.$group.'" style="position:absolute;top:calc(100% + 4px);right:0;min-width:180px;background:white;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 10px 40px rgba(0,0,0,0.12);z-index:1000;display:none;">';

            foreach ($languages as $lang) {
                $isActive = $lang['id_lang'] == $defaultLang;
                $html .= '<button type="button" class="wb-lang-option '.($isActive ? 'active' : '').'" onclick="switchLang(\''.$group.'\', '.$lang['id_lang'].', \''.strtoupper($lang['iso_code']).'\', this)" style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 14px;background:'.($isActive ? 'rgba(99,102,241,0.08)' : 'white').';border:none;font-size:14px;color:'.($isActive ? '#6366f1' : '#374151').';cursor:pointer;text-align:left;">
                    <img src="../img/l/'.$lang['id_lang'].'.jpg" alt="" style="width:20px;height:14px;border-radius:2px;">
                    <span>'.$lang['name'].'</span>
                </button>';
            }

            $html .= '</div>
                </div>
            </div>';
        } else {
            $html .= '<label class="wb-form-label">'.$label.'</label>';
        }

        foreach ($languages as $lang) {
            $isActive = $lang['id_lang'] == $defaultLang;
            $activeClass = $isActive ? ' active' : '';
            $placeholder = $phPrefix ? htmlspecialchars($phPrefix.' '.$lang['name'].$phSuffix) : '';
            $html .= '<textarea name="'.$group.'_'.$lang['id_lang'].'" class="wb-form-textarea wb-lang-field-'.$group.$activeClass.'" data-lang="'.$lang['id_lang'].'" rows="'.$rows.'" placeholder="'.$placeholder.'" style="'.$style.'">'.htmlspecialchars($values[$lang['id_lang']] ?? '').'</textarea>';
        }

        return $html;
    }

    protected function renderTargetingRulesSection($id_block)
    {
        $rules = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'wiseblock_rule WHERE id_block='.(int)$id_block.' ORDER BY id_rule DESC');
        $ruleCount = count($rules);

        // Get data for selects
        $groups = \Group::getGroups($this->context->language->id);
        $countries = \Country::getCountries($this->context->language->id, true);
        $currency = \Context::getContext()->currency->sign;
        $manufacturers = \Manufacturer::getManufacturers(false, $this->context->language->id, true);
        $suppliers = \Supplier::getSuppliers(false, $this->context->language->id, true);
        $features = \Feature::getFeatures($this->context->language->id);
        $languages = \Language::getLanguages(true);

        // Form action URL for rule forms
        $ruleFormAction = $this->context->link->getAdminLink('AdminWiseBlockBlock');

        $html = '<div class="wb-form-section">
            <div class="wb-targeting-accordion '.($ruleCount > 0 ? 'open' : '').'" id="wb-targeting-accordion">
                <div class="wb-targeting-header" onclick="toggleTargeting()">
                    <div class="wb-targeting-header-left">
                        <span class="wb-targeting-title">'.$this->_l('Targeting Rules').'</span>
                        <span class="wb-targeting-count">'.$ruleCount.'</span>
                    </div>
                    <i class="icon-chevron-down wb-targeting-chevron"></i>
                </div>
                <div class="wb-targeting-body">';

        // Rule type buttons
        $html .= '<div class="wb-form-group">
            <label class="wb-form-label">'.$this->_l('Add New Rule').'</label>
            <div class="wb-rule-types">
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'category\', this)">
                    <span class="wb-rule-type-icon">📁</span> '.$this->_l('Category').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'manufacturer\', this)">
                    <span class="wb-rule-type-icon">🏭</span> '.$this->_l('Manufacturer').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'supplier\', this)">
                    <span class="wb-rule-type-icon">📦</span> '.$this->_l('Supplier').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'feature\', this)">
                    <span class="wb-rule-type-icon">⚙️</span> '.$this->_l('Feature').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'tag\', this)">
                    <span class="wb-rule-type-icon">🏷️</span> '.$this->_l('Tag').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'customer\', this)">
                    <span class="wb-rule-type-icon">👥</span> '.$this->_l('Customer').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'country\', this)">
                    <span class="wb-rule-type-icon">🌍</span> '.$this->_l('Country').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'cart\', this)">
                    <span class="wb-rule-type-icon">🛒</span> '.$this->_l('Cart').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'cart_product\', this)">
                    <span class="wb-rule-type-icon">📦</span> '.$this->_l('In Cart').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'currency\', this)">
                    <span class="wb-rule-type-icon">💱</span> '.$this->_l('Currency').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'utm\', this)">
                    <span class="wb-rule-type-icon">🔗</span> '.$this->_l('UTM').'
                </button>
                <button type="button" class="wb-rule-type-btn" onclick="showRuleForm(\'search_query\', this)">
                    <span class="wb-rule-type-icon">🔍</span> '.$this->_l('Query').'
                </button>
            </div>
        </div>';

        // Rule forms (hidden by default)
        // Category form
        $html .= '<div class="wb-rule-form" id="rule-form-category" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_category">
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Category ID').'</label>
                    <input type="number" name="id_category" class="wb-form-input" required>
                </div>
                <div class="wb-form-group" style="display:flex;align-items:center;gap:12px;">
                    <input type="checkbox" name="with_children" value="1" id="cat_subcats" style="width:18px;height:18px;">
                    <label for="cat_subcats" style="margin:0;font-size:14px;">'.$this->_l('Include subcategories').'</label>
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleCategory">
                    <i class="icon-plus"></i> '.$this->_l('Add Category Rule').'
                </button>
            </form>
        </div>';

        // Manufacturer form
        $html .= '<div class="wb-rule-form" id="rule-form-manufacturer" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_manufacturer">
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Manufacturer').'</label>
                    <select name="id_manufacturer" class="wb-form-select">';
        foreach ($manufacturers as $m) {
            $html .= '<option value="'.$m['id_manufacturer'].'">'.htmlspecialchars($m['name']).'</option>';
        }
        $html .= '</select>
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleManufacturer">
                    <i class="icon-plus"></i> '.$this->_l('Add Manufacturer Rule').'
                </button>
            </form>
        </div>';

        // Supplier form
        $html .= '<div class="wb-rule-form" id="rule-form-supplier" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_supplier">
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Supplier').'</label>
                    <select name="id_supplier" class="wb-form-select">';
        foreach ($suppliers as $s) {
            $html .= '<option value="'.$s['id_supplier'].'">'.htmlspecialchars($s['name']).'</option>';
        }
        $html .= '</select>
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleSupplier">
                    <i class="icon-plus"></i> '.$this->_l('Add Supplier Rule').'
                </button>
            </form>
        </div>';

        // Feature form
        $html .= '<div class="wb-rule-form" id="rule-form-feature" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_feature">
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Feature').'</label>
                    <select name="id_feature" class="wb-form-select" onchange="loadFeatureValues(this.value)">';
        foreach ($features as $f) {
            $html .= '<option value="'.$f['id_feature'].'">'.htmlspecialchars($f['name']).'</option>';
        }
        $html .= '</select>
                </div>
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Feature Value').'</label>
                    <select name="id_feature_value" class="wb-form-select" id="feature-value-select">
                        <option value="">-- '.$this->_l('Select feature first').' --</option>
                    </select>
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleFeature">
                    <i class="icon-plus"></i> '.$this->_l('Add Feature Rule').'
                </button>
            </form>
        </div>';

        // Tag form
        $html .= '<div class="wb-rule-form" id="rule-form-tag" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_tag">
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Tag ID').'</label>
                    <input type="number" name="id_tag" class="wb-form-input" required>
                </div>
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Language').'</label>
                    <select name="id_lang" class="wb-form-select">';
        foreach ($languages as $lang) {
            $selected = $lang['id_lang'] == $this->context->language->id ? ' selected' : '';
            $html .= '<option value="'.$lang['id_lang'].'"'.$selected.'>'.htmlspecialchars($lang['name']).'</option>';
        }
        $html .= '</select>
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleTag">
                    <i class="icon-plus"></i> '.$this->_l('Add Tag Rule').'
                </button>
            </form>
        </div>';

        // Customer Group form
        $html .= '<div class="wb-rule-form" id="rule-form-customer" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_group">
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Customer Group').'</label>
                    <select name="id_group" class="wb-form-select">';
        foreach ($groups as $g) {
            $html .= '<option value="'.$g['id_group'].'">'.htmlspecialchars($g['name']).'</option>';
        }
        $html .= '</select>
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleGroup">
                    <i class="icon-plus"></i> '.$this->_l('Add Group Rule').'
                </button>
            </form>
        </div>';

        // Country form
        $html .= '<div class="wb-rule-form" id="rule-form-country" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_country">
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Country').'</label>
                    <select name="id_country" class="wb-form-select">';
        foreach ($countries as $c) {
            $html .= '<option value="'.$c['id_country'].'">'.htmlspecialchars($c['name']).'</option>';
        }
        $html .= '</select>
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleCountry">
                    <i class="icon-plus"></i> '.$this->_l('Add Country Rule').'
                </button>
            </form>
        </div>';

        // Cart Value form
        $html .= '<div class="wb-rule-form" id="rule-form-cart" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_cart_value">
                <div style="display:flex;gap:16px;">
                    <div class="wb-form-group" style="flex:1;">
                        <label class="wb-form-label">'.$this->_l('Min Cart Value').' ('.$currency.')</label>
                        <input type="number" name="cart_min" class="wb-form-input" step="0.01" min="0" value="0" placeholder="0 = '.$this->_l('no minimum').'">
                    </div>
                    <div class="wb-form-group" style="flex:1;">
                        <label class="wb-form-label">'.$this->_l('Max Cart Value').' ('.$currency.')</label>
                        <input type="number" name="cart_max" class="wb-form-input" step="0.01" min="0" placeholder="'.$this->_l('Empty = no limit').'">
                    </div>
                </div>
                <div class="wb-form-hint" style="margin-bottom:12px;">
                    '.$this->_l('Examples: 0-200 (show when cart < 200), 200+ (show when cart >= 200), 100-500 (show in range)').'
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleCartValue">
                    <i class="icon-plus"></i> '.$this->_l('Add Cart Rule').'
                </button>
            </form>
        </div>';

        // Cart Products form
        $html .= '<div class="wb-rule-form" id="rule-form-cart_product" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_cart_product">
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Product IDs').'</label>
                    <input type="text" name="product_ids" class="wb-form-input" required placeholder="'.$this->_l('e.g. 15, 27, 42').'">
                </div>
                <div class="wb-form-hint" style="margin-bottom:12px;">
                    '.$this->_l('Enter product IDs separated by commas. Rule matches if ANY of these products is in cart.').'
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleCartProduct">
                    <i class="icon-plus"></i> '.$this->_l('Add In Cart Rule').'
                </button>
            </form>
        </div>';

        // Currency form
        $currencies = Currency::getCurrencies();
        $html .= '<div class="wb-rule-form" id="rule-form-currency" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_currency">
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Currency').'</label>
                    <select name="id_currency" class="wb-form-input" required>';
        foreach ($currencies as $curr) {
            $html .= '<option value="'.(int)$curr['id_currency'].'">'.htmlspecialchars($curr['name']).' ('.$curr['iso_code'].')</option>';
        }
        $html .= '</select>
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleCurrency">
                    <i class="icon-plus"></i> '.$this->_l('Add Rule').'
                </button>
            </form>
        </div>';

        // UTM form
        $html .= '<div class="wb-rule-form" id="rule-form-utm" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_utm">
                <div style="display:flex;gap:16px;">
                    <div class="wb-form-group" style="flex:1;">
                        <label class="wb-form-label">utm_source</label>
                        <input type="text" name="utm_source" class="wb-form-input" placeholder="'.$this->_l('e.g. google, facebook').'">
                    </div>
                    <div class="wb-form-group" style="flex:1;">
                        <label class="wb-form-label">utm_campaign</label>
                        <input type="text" name="utm_campaign" class="wb-form-input" placeholder="'.$this->_l('e.g. summer_sale').'">
                    </div>
                </div>
                <div class="wb-form-hint" style="margin-bottom:12px;">
                    '.$this->_l('Fill one or both fields. If both are filled, both must match.').'
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleUtm">
                    <i class="icon-plus"></i> '.$this->_l('Add Rule').'
                </button>
            </form>
        </div>';

        // Search Query form
        $html .= '<div class="wb-rule-form" id="rule-form-search_query" style="display:none;padding:16px;background:#f9fafb;border-radius:8px;margin-bottom:16px;">
            <form method="post" action="'.$ruleFormAction.'">
                <input type="hidden" name="id_block" value="'.(int)$id_block.'">
                <input type="hidden" name="wiseblock_action" value="add_rule_search_query">
                <div class="wb-form-group">
                    <label class="wb-form-label">'.$this->_l('Search query contains').'</label>
                    <input type="text" name="search_query_text" class="wb-form-input" required placeholder="'.$this->_l('e.g. guitar, acoustic').'">
                </div>
                <div class="wb-form-hint" style="margin-bottom:12px;">
                    '.$this->_l('Matches if the visitor\'s search query contains this text (case-insensitive). Only works on search results pages.').'
                </div>
                <div class="wb-include-exclude">
                    <label class="include"><input type="radio" name="include" value="1" checked> '.$this->_l('Include').'</label>
                    <label class="exclude"><input type="radio" name="include" value="0"> '.$this->_l('Exclude').'</label>
                </div>
                <button type="submit" class="wb-btn-save" style="margin-top:12px;" name="submitAddRuleSearchQuery">
                    <i class="icon-plus"></i> '.$this->_l('Add Search Query Rule').'
                </button>
            </form>
        </div>';

        // Active Rules List
        if ($ruleCount > 0) {
            $html .= '<div class="wb-form-group" style="margin-top:20px;">
                <label class="wb-form-label">'.$this->_l('Active Rules').' ('.$ruleCount.')</label>';

            foreach ($rules as $r) {
                // Handle empty type (legacy rules)
                $ruleType = $r['type'] ? $r['type'] : 'unknown';

                $typeClass = 'wb-rule-type-'.$ruleType;
                if ($ruleType === 'customer_group') $typeClass = 'wb-rule-type-customer';
                if ($ruleType === 'cart_value') $typeClass = 'wb-rule-type-cart';
                if ($ruleType === 'cart_product') $typeClass = 'wb-rule-type-cart-product';
                if ($ruleType === 'currency') $typeClass = 'wb-rule-type-currency';
                if ($ruleType === 'utm') $typeClass = 'wb-rule-type-utm';
                if ($ruleType === 'search_query') $typeClass = 'wb-rule-type-search';
                if ($ruleType === 'unknown') $typeClass = 'wb-rule-type-unknown';

                $typeName = $ruleType === 'unknown' ? 'UNKNOWN' : ucfirst(str_replace('_', ' ', $ruleType));
                $modeClass = $r['include'] ? 'wb-rule-mode-include' : 'wb-rule-mode-exclude';
                $modeText = $r['include'] ? $this->_l('Include') : $this->_l('Exclude');

                // Get human-readable value based on type
                $valueDisplay = 'ID: '.$r['id_object'];
                switch ($ruleType) {
                    case 'manufacturer':
                        $mName = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'manufacturer WHERE id_manufacturer='.(int)$r['id_object']);
                        $valueDisplay = $mName ?: 'ID: '.$r['id_object'];
                        break;
                    case 'supplier':
                        $sName = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'supplier WHERE id_supplier='.(int)$r['id_object']);
                        $valueDisplay = $sName ?: 'ID: '.$r['id_object'];
                        break;
                    case 'feature':
                        $fvData = Db::getInstance()->getRow('
                            SELECT f.id_feature, fl.name as feature_name, fvl.value as feature_value
                            FROM '._DB_PREFIX_.'feature_value fv
                            LEFT JOIN '._DB_PREFIX_.'feature f ON f.id_feature = fv.id_feature
                            LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = f.id_feature AND fl.id_lang='.(int)$this->context->language->id.')
                            LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang='.(int)$this->context->language->id.')
                            WHERE fv.id_feature_value='.(int)$r['id_object']
                        );
                        $valueDisplay = $fvData ? htmlspecialchars($fvData['feature_name'].': '.$fvData['feature_value']) : 'ID: '.$r['id_object'];
                        break;
                    case 'customer_group':
                        $gName = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'group_lang WHERE id_group='.(int)$r['id_object'].' AND id_lang='.(int)$this->context->language->id);
                        $valueDisplay = $gName ?: 'ID: '.$r['id_object'];
                        break;
                    case 'country':
                        $cName = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'country_lang WHERE id_country='.(int)$r['id_object'].' AND id_lang='.(int)$this->context->language->id);
                        $valueDisplay = $cName ?: 'ID: '.$r['id_object'];
                        break;
                    case 'category':
                        $catName = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'category_lang WHERE id_category='.(int)$r['id_object'].' AND id_lang='.(int)$this->context->language->id);
                        $valueDisplay = ($catName ?: 'ID: '.$r['id_object']).($r['with_children'] ? ' ('.$this->_l('+ subcategories').')' : '');
                        break;
                    case 'cart_value':
                        $cartMin = $r['id_object'] / 100; // Stored as cents
                        $cartMax = isset($r['value_max']) ? $r['value_max'] : null;
                        if ($cartMin == 0 && $cartMax !== null) {
                            $valueDisplay = $this->_l('Cart').' < '.$currency.number_format($cartMax, 2);
                        } elseif ($cartMax === null) {
                            $valueDisplay = $this->_l('Cart').' >= '.$currency.number_format($cartMin, 2);
                        } else {
                            $valueDisplay = $currency.number_format($cartMin, 2).' - '.$currency.number_format($cartMax, 2);
                        }
                        break;
                    case 'tag':
                        $tName = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'tag WHERE id_tag='.(int)$r['id_object']);
                        $valueDisplay = $tName ?: 'ID: '.$r['id_object'];
                        break;
                    case 'cart_product':
                        // id_object stores comma-separated product IDs
                        $productIds = explode(',', $r['id_object']);
                        $productNames = array();
                        foreach ($productIds as $pid) {
                            $pName = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'product_lang WHERE id_product='.(int)$pid.' AND id_lang='.(int)$this->context->language->id);
                            $productNames[] = $pName ?: 'ID:'.$pid;
                        }
                        $valueDisplay = implode(', ', array_slice($productNames, 0, 3));
                        if (count($productNames) > 3) {
                            $valueDisplay .= ' (+'.(count($productNames) - 3).')';
                        }
                        break;
                    case 'currency':
                        $currName = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'currency WHERE id_currency='.(int)$r['id_object']);
                        $currIso = Db::getInstance()->getValue('SELECT iso_code FROM '._DB_PREFIX_.'currency WHERE id_currency='.(int)$r['id_object']);
                        $valueDisplay = $currName ? htmlspecialchars($currName).' ('.$currIso.')' : 'ID: '.$r['id_object'];
                        break;
                    case 'utm':
                        $utmData = json_decode($r['id_object'], true);
                        $parts = array();
                        if (!empty($utmData['source'])) {
                            $parts[] = 'source: '.htmlspecialchars($utmData['source']);
                        }
                        if (!empty($utmData['campaign'])) {
                            $parts[] = 'campaign: '.htmlspecialchars($utmData['campaign']);
                        }
                        $valueDisplay = implode(', ', $parts) ?: '(empty)';
                        break;
                    case 'search_query':
                        $valueDisplay = $this->_l('Contains').': "'.htmlspecialchars($r['id_object']).'"';
                        break;
                }

                $deleteLink = $this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array(
                    'wiseblock_action' => 'delete_rule',
                    'id_rule' => $r['id_rule'],
                    'id_block' => $id_block
                ));

                $html .= '<div class="wb-rule-item">
                    <span class="wb-rule-grip"><i class="icon-reorder"></i></span>
                    <span class="wb-rule-type-badge '.$typeClass.'">'.$typeName.'</span>
                    <span class="wb-rule-value">'.$valueDisplay.'</span>
                    <span class="wb-rule-mode '.$modeClass.'">'.$modeText.'</span>
                    <a href="'.$deleteLink.'" class="wb-rule-delete" onclick="return confirm(\''.$this->_l('Delete this rule?').'\')">
                        <i class="icon-trash"></i>
                    </a>
                </div>';
            }

            $html .= '</div>';
        } else {
            $html .= '<div style="text-align:center;padding:20px;color:#6b7280;font-size:14px;">
                <i class="icon-info-circle"></i> '.$this->_l('No targeting rules yet. Click a rule type above to add one.').'
            </div>';
        }

        $html .= '</div></div></div>';

        // JavaScript for rule forms
        $html .= '<script>
        function showRuleForm(type, btn) {
            // Hide all forms
            document.querySelectorAll(".wb-rule-form").forEach(function(f) {
                f.style.display = "none";
            });
            // Remove active from all buttons
            document.querySelectorAll(".wb-rule-type-btn").forEach(function(b) {
                b.classList.remove("active");
            });
            // Show selected form and mark button active
            document.getElementById("rule-form-" + type).style.display = "block";
            btn.classList.add("active");

            // Auto-load feature values if feature form
            if (type === "feature") {
                var featureSelect = document.querySelector("#rule-form-feature select[name=id_feature]");
                if (featureSelect && featureSelect.value) {
                    loadFeatureValues(featureSelect.value);
                }
            }
        }

        function loadFeatureValues(idFeature) {
            if (!idFeature) return;
            var select = document.getElementById("feature-value-select");
            select.innerHTML = "<option value=\"\">'.$this->_l('Loading...').'</option>";

            $.ajax({
                url: "'.$this->context->link->getAdminLink('AdminWiseBlockBlock', true).'",
                method: "POST",
                data: { ajax: 1, action: "getFeatureValues", id_feature: idFeature },
                success: function(response) {
                    try {
                        var data = JSON.parse(response);
                        select.innerHTML = "";
                        if (data.length === 0) {
                            select.innerHTML = "<option value=\"\">'.$this->_l('No values found').'</option>";
                        } else {
                            data.forEach(function(item) {
                                var opt = document.createElement("option");
                                opt.value = item.id_feature_value;
                                opt.textContent = item.value;
                                select.appendChild(opt);
                            });
                        }
                    } catch(e) {
                        select.innerHTML = "<option value=\"\">'.$this->_l('Error loading values').'</option>";
                    }
                },
                error: function() {
                    select.innerHTML = "<option value=\"\">'.$this->_l('Error loading values').'</option>";
                }
            });
        }
        </script>';

        return $html;
    }

    public function postProcess()
    {
        // Handle A/B test reset
        if (Tools::getValue('action') === 'resetABTest') {
            $id_block = (int)Tools::getValue('id_block');
            if ($id_block) {
                $db = Db::getInstance();
                $db->delete('wiseblock_stats', 'id_block='.(int)$id_block);
                $db->execute('UPDATE `'._DB_PREFIX_.'wiseblock_block` SET `ab_winner` = NULL, `views_count` = 0 WHERE `id_block` = '.(int)$id_block);
                die(json_encode(array('success' => true)));
            }
            die(json_encode(array('error' => 'Missing id_block')));
        }

        // Handle duplicate
        if (Tools::isSubmit('duplicatewiseblock_block')) {
            $id_block = (int)Tools::getValue('id_block');
            if ($id_block) {
                $this->duplicateBlock($id_block);
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock'));
            }
        }

        // handle rule ops - using submit button names for reliable detection
        if (Tools::isSubmit('submitAddRuleCategory')) {
            $id_block = (int)Tools::getValue('id_block');
            $id_cat = (int)Tools::getValue('id_category');
            $with_children = (int)Tools::getValue('with_children');
            $include = (int)Tools::getValue('include', 1);
            Db::getInstance()->insert('wiseblock_rule', array(
                'id_block' => $id_block,
                'type' => 'category',
                'id_object' => $id_cat,
                'include' => $include,
                'with_children' => $with_children,
                'id_lang' => null
            ));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleTag')) {
            $id_block = (int)Tools::getValue('id_block');
            $id_tag = (int)Tools::getValue('id_tag');
            $id_lang = (int)Tools::getValue('id_lang');
            $include = (int)Tools::getValue('include', 1);
            Db::getInstance()->insert('wiseblock_rule', array(
                'id_block' => $id_block,
                'type' => 'tag',
                'id_object' => $id_tag,
                'include' => $include,
                'with_children' => 0,
                'id_lang' => $id_lang
            ));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleGroup')) {
            $id_block = (int)Tools::getValue('id_block');
            $id_group = (int)Tools::getValue('id_group');
            $include = (int)Tools::getValue('include', 1);
            Db::getInstance()->insert('wiseblock_rule', array(
                'id_block' => $id_block,
                'type' => 'customer_group',
                'id_object' => $id_group,
                'include' => $include,
                'with_children' => 0,
                'id_lang' => null
            ));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleCountry')) {
            $id_block = (int)Tools::getValue('id_block');
            $id_country = (int)Tools::getValue('id_country');
            $include = (int)Tools::getValue('include', 1);
            Db::getInstance()->insert('wiseblock_rule', array(
                'id_block' => $id_block,
                'type' => 'country',
                'id_object' => $id_country,
                'include' => $include,
                'with_children' => 0,
                'id_lang' => null
            ));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleCartValue')) {
            $id_block = (int)Tools::getValue('id_block');
            $cart_min = (float)Tools::getValue('cart_min', 0);
            $cart_max = Tools::getValue('cart_max');
            $cart_max = ($cart_max !== '' && $cart_max !== null) ? (float)$cart_max : null;
            $include = (int)Tools::getValue('include', 1);
            Db::getInstance()->insert('wiseblock_rule', array(
                'id_block' => $id_block,
                'type' => 'cart_value',
                'id_object' => (int)($cart_min * 100), // Store as cents for precision
                'value_max' => $cart_max,
                'include' => $include,
                'with_children' => 0,
                'id_lang' => null
            ));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleCartProduct')) {
            $id_block = (int)Tools::getValue('id_block');
            $productIds = Tools::getValue('product_ids', '');
            // Clean and validate product IDs
            $productIds = preg_replace('/[^0-9,]/', '', $productIds);
            $productIds = implode(',', array_filter(array_map('intval', explode(',', $productIds))));
            $include = (int)Tools::getValue('include', 1);
            if (!empty($productIds)) {
                Db::getInstance()->insert('wiseblock_rule', array(
                    'id_block' => $id_block,
                    'type' => 'cart_product',
                    'id_object' => pSQL($productIds), // Store comma-separated IDs
                    'include' => $include,
                    'with_children' => 0,
                    'id_lang' => null
                ));
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleCurrency')) {
            $id_block = (int)Tools::getValue('id_block');
            $id_currency = (int)Tools::getValue('id_currency');
            $include = (int)Tools::getValue('include', 1);
            Db::getInstance()->insert('wiseblock_rule', array(
                'id_block' => $id_block,
                'type' => 'currency',
                'id_object' => $id_currency,
                'include' => $include,
                'with_children' => 0,
                'id_lang' => null
            ));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleUtm')) {
            $id_block = (int)Tools::getValue('id_block');
            $utmSource = trim(Tools::getValue('utm_source', ''));
            $utmCampaign = trim(Tools::getValue('utm_campaign', ''));
            $include = (int)Tools::getValue('include', 1);
            // At least one must be filled
            if (!empty($utmSource) || !empty($utmCampaign)) {
                $utmData = json_encode(array(
                    'source' => $utmSource,
                    'campaign' => $utmCampaign
                ));
                Db::getInstance()->insert('wiseblock_rule', array(
                    'id_block' => $id_block,
                    'type' => 'utm',
                    'id_object' => pSQL($utmData),
                    'include' => $include,
                    'with_children' => 0,
                    'id_lang' => null
                ));
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleSearchQuery')) {
            $id_block = (int)Tools::getValue('id_block');
            $searchText = trim(Tools::getValue('search_query_text', ''));
            $include = (int)Tools::getValue('include', 1);
            if (!empty($searchText)) {
                Db::getInstance()->insert('wiseblock_rule', array(
                    'id_block' => $id_block,
                    'type' => 'search_query',
                    'id_object' => pSQL($searchText),
                    'include' => $include,
                    'with_children' => 0,
                    'id_lang' => null
                ));
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleManufacturer')) {
            $id_block = (int)Tools::getValue('id_block');
            $id_manufacturer = (int)Tools::getValue('id_manufacturer');
            $include = (int)Tools::getValue('include', 1);
            Db::getInstance()->insert('wiseblock_rule', array(
                'id_block' => $id_block,
                'type' => 'manufacturer',
                'id_object' => $id_manufacturer,
                'include' => $include,
                'with_children' => 0,
                'id_lang' => null
            ));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleSupplier')) {
            $id_block = (int)Tools::getValue('id_block');
            $id_supplier = (int)Tools::getValue('id_supplier');
            $include = (int)Tools::getValue('include', 1);
            Db::getInstance()->insert('wiseblock_rule', array(
                'id_block' => $id_block,
                'type' => 'supplier',
                'id_object' => $id_supplier,
                'include' => $include,
                'with_children' => 0,
                'id_lang' => null
            ));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddRuleFeature')) {
            $id_block = (int)Tools::getValue('id_block');
            $id_feature_value = (int)Tools::getValue('id_feature_value');
            $include = (int)Tools::getValue('include', 1);
            Db::getInstance()->insert('wiseblock_rule', array(
                'id_block' => $id_block,
                'type' => 'feature',
                'id_object' => $id_feature_value,
                'include' => $include,
                'with_children' => 0,
                'id_lang' => null
            ));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        // Delete rule (via GET parameter)
        if (Tools::getIsset('wiseblock_action') && Tools::getValue('wiseblock_action') === 'delete_rule') {
            $id_rule = (int)Tools::getValue('id_rule');
            $id_block = (int)Tools::getValue('id_block');
            Db::getInstance()->delete('wiseblock_rule', 'id_rule='.(int)$id_rule);
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array('update'.$this->table => 1, 'id_block' => $id_block)));
        }

        if (Tools::isSubmit('submitAddwiseblock_block')) {
            return $this->processSaveBlock();
        }

        parent::postProcess();
    }

    /**
     * Custom save handler for block form
     */
    protected function processSaveBlock()
    {
        $id_block = (int)Tools::getValue('id_block');
        $languages = Language::getLanguages(true);

        // Create or load object
        if ($id_block) {
            $block = new WiseBlockBlock($id_block);
        } else {
            $block = new WiseBlockBlock();
        }

        // Set basic fields
        $block->active = (int)Tools::getValue('active', 0);
        $block->position = (int)Tools::getValue('position', 0);
        $block->logic_mode = Tools::getValue('logic_mode', 'OR');

        // Handle dates
        $publish_from = Tools::getValue('publish_from');
        $publish_to = Tools::getValue('publish_to');
        $block->publish_from = $publish_from ? $publish_from.' 00:00:00' : null;
        $block->publish_to = $publish_to ? $publish_to.' 23:59:59' : null;

        // Handle time restrictions
        $time_from = Tools::getValue('time_from', '');
        $time_to = Tools::getValue('time_to', '');
        $block->time_from = !empty($time_from) ? $time_from : null;
        $block->time_to = !empty($time_to) ? $time_to : null;

        // Days of week
        $daysArr = Tools::getValue('days_of_week');
        if (is_array($daysArr) && count($daysArr) < 7) {
            $block->days_of_week = implode(',', array_map('intval', $daysArr));
        } else {
            $block->days_of_week = null; // all days
        }

        // Advanced options
        $block->auto_refresh = (int)Tools::getValue('auto_refresh', 0);
        $block->lazy_load = (int)Tools::getValue('lazy_load', 0);
        $block->ab_variant = Tools::getValue('ab_variant', 'none');
        if (!in_array($block->ab_variant, array('none', 'A', 'B'))) {
            $block->ab_variant = 'none';
        }
        $block->ab_auto_optimize = (int)Tools::getValue('ab_auto_optimize', 0);
        $block->ab_min_views = max(100, (int)Tools::getValue('ab_min_views', 500));
        // Don't overwrite ab_winner - it's set by auto-optimize logic

        // Set multilang fields
        foreach ($languages as $lang) {
            $id_lang = (int)$lang['id_lang'];
            $block->title[$id_lang] = Tools::getValue('title_'.$id_lang, '');
            $block->content[$id_lang] = Tools::getValue('content_'.$id_lang, '');
            $block->content_b[$id_lang] = Tools::getValue('content_b_'.$id_lang, '');
            $block->head_code[$id_lang] = Tools::getValue('head_code_'.$id_lang, '');
            $block->footer_code[$id_lang] = Tools::getValue('footer_code_'.$id_lang, '');
        }

        // Save
        try {
            if ($id_block) {
                $result = $block->update();
            } else {
                $result = $block->add();
            }

            if ($result) {
                // Save hooks
                $hooks = Tools::getValue('hooks');
                if (is_array($hooks)) {
                    $block->setHooks($hooks);
                } else {
                    $block->setHooks(array());
                }

                // Redirect back to edit page (so user can add rules immediately)
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock', true, array(), array(
                    'updatewiseblock_block' => 1,
                    'id_block' => $block->id,
                    'conf' => 4
                )));
                exit; // Ensure redirect happens
            } else {
                $this->errors[] = $this->trans('An error occurred while saving the block.', array(), 'Admin.Notifications.Error');
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        return false;
    }

    /**
     * Get aggregated A/B test stats for a block
     */
    private function getABStats($id_block)
    {
        $rows = Db::getInstance()->executeS('
            SELECT `variant`, SUM(`views`) as total_views, SUM(`clicks`) as total_clicks
            FROM `'._DB_PREFIX_.'wiseblock_stats`
            WHERE `id_block` = '.(int)$id_block.'
            GROUP BY `variant`
        ');

        if (!$rows) {
            return null;
        }

        $stats = array();
        foreach ($rows as $row) {
            $stats[$row['variant']] = array(
                'views' => (int)$row['total_views'],
                'clicks' => (int)$row['total_clicks'],
            );
        }

        return !empty($stats) ? $stats : null;
    }
}
