<?php
/**
 * WiseBlock AJAX Front Controller
 * Handles dynamic block refresh (cart updates, lazy loading, view tracking)
 *
 * Endpoints:
 *   ?fc=module&module=wiseblock&controller=ajax&action=refreshBlock&id_block=X
 *   ?fc=module&module=wiseblock&controller=ajax&action=refreshHook&hook=hookName
 *   ?fc=module&module=wiseblock&controller=ajax&action=trackView&id_block=X&variant=A
 *   ?fc=module&module=wiseblock&controller=ajax&action=trackClick&id_block=X&variant=A
 */

class WiseBlockAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $action = Tools::getValue('action');

        switch ($action) {
            case 'refreshBlock':
                $this->ajaxRefreshBlock();
                break;
            case 'refreshHook':
                $this->ajaxRefreshHook();
                break;
            case 'trackView':
                $this->ajaxTrackView();
                break;
            case 'trackClick':
                $this->ajaxTrackClick();
                break;
            default:
                $this->ajaxDie(json_encode(['error' => 'Unknown action']));
        }
    }

    /**
     * Refresh a single block by ID - re-renders with current cart/context data
     */
    private function ajaxRefreshBlock()
    {
        $id_block = (int)Tools::getValue('id_block');
        if (!$id_block) {
            $this->ajaxDie(json_encode(['error' => 'Missing id_block']));
        }

        $html = $this->module->renderBlockById($id_block);

        $this->ajaxDie(json_encode([
            'success' => true,
            'id_block' => $id_block,
            'html' => $html,
        ]));
    }

    /**
     * Refresh all blocks for a given hook - re-renders with current context
     */
    private function ajaxRefreshHook()
    {
        $hookName = Tools::getValue('hook');
        if (!$hookName) {
            $this->ajaxDie(json_encode(['error' => 'Missing hook']));
        }

        $html = $this->module->renderHookBlocks(pSQL($hookName));

        $this->ajaxDie(json_encode([
            'success' => true,
            'hook' => $hookName,
            'html' => $html,
        ]));
    }

    /**
     * Track a block view (impression) for stats
     * Rate-limited: max 1 view per block per session via cookie
     */
    private function ajaxTrackView()
    {
        $id_block = (int)Tools::getValue('id_block');
        $variant = Tools::getValue('variant', 'A');
        if (!in_array($variant, ['A', 'B'])) {
            $variant = 'A';
        }

        if (!$id_block) {
            $this->ajaxDie(json_encode(['error' => 'Missing id_block']));
        }

        // Rate limit: one view per block per session
        $cookieKey = 'wb_v_' . $id_block;
        if (isset($_COOKIE[$cookieKey])) {
            $this->ajaxDie(json_encode(['success' => true, 'deduplicated' => true]));
        }
        setcookie($cookieKey, '1', 0, '/'); // session cookie

        $today = date('Y-m-d');
        $db = Db::getInstance();

        // Upsert: increment views or insert new row
        $db->execute('
            INSERT INTO `'._DB_PREFIX_.'wiseblock_stats` (`id_block`, `variant`, `date_stat`, `views`)
            VALUES ('.(int)$id_block.', "'.pSQL($variant).'", "'.pSQL($today).'", 1)
            ON DUPLICATE KEY UPDATE `views` = `views` + 1
        ');

        // Also increment total counter on the block
        $db->execute('
            UPDATE `'._DB_PREFIX_.'wiseblock_block`
            SET `views_count` = `views_count` + 1
            WHERE `id_block` = '.(int)$id_block
        );

        $this->ajaxDie(json_encode(['success' => true]));
    }

    /**
     * Track a click within a block for A/B testing
     * Rate-limited: max 1 click per block per session via cookie
     */
    private function ajaxTrackClick()
    {
        $id_block = (int)Tools::getValue('id_block');
        $variant = Tools::getValue('variant', 'A');
        if (!in_array($variant, ['A', 'B'])) {
            $variant = 'A';
        }

        if (!$id_block) {
            $this->ajaxDie(json_encode(['error' => 'Missing id_block']));
        }

        // Rate limit: one click per block per session
        $cookieKey = 'wb_c_' . $id_block;
        if (isset($_COOKIE[$cookieKey])) {
            $this->ajaxDie(json_encode(['success' => true, 'deduplicated' => true]));
        }
        setcookie($cookieKey, '1', 0, '/'); // session cookie

        $today = date('Y-m-d');
        $db = Db::getInstance();

        // Upsert: increment clicks
        $db->execute('
            INSERT INTO `'._DB_PREFIX_.'wiseblock_stats` (`id_block`, `variant`, `date_stat`, `views`, `clicks`)
            VALUES ('.(int)$id_block.', "'.pSQL($variant).'", "'.pSQL($today).'", 0, 1)
            ON DUPLICATE KEY UPDATE `clicks` = `clicks` + 1
        ');

        $this->ajaxDie(json_encode(['success' => true]));
    }
}
