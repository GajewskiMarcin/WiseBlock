<?php
/**
 * Helper class for WiseBlock admin controllers
 * Provides shared navigation rendering
 */

class WiseBlockAdminHelper
{
    /**
     * Render navigation tabs using Smarty template
     *
     * @param object $context PrestaShop context
     * @param string $currentTab Current active tab identifier
     * @return string Tabs HTML
     */
    public static function renderTabs($context, $currentTab)
    {
        $context->smarty->assign([
            'current_tab' => $currentTab,
            'link_blocks' => $context->link->getAdminLink('AdminWiseBlockBlock'),
            'link_hooks' => $context->link->getAdminLink('AdminWiseBlockHook'),
            'link_tools' => $context->link->getAdminLink('AdminWiseBlockTools'),
            'link_about' => $context->link->getAdminLink('AdminWiseBlockAbout'),
        ]);

        return $context->smarty->fetch(_PS_MODULE_DIR_.'wiseblock/views/templates/admin/tabs.tpl');
    }

    /**
     * Render wrapper - kept for backward compatibility
     * Now just prepends tabs to content
     */
    public static function renderWrapper($context, $currentTab, $pageTitle, $pageDescription, $content)
    {
        $tabs = self::renderTabs($context, $currentTab);
        return '<div class="wb-module-wrapper">' . $tabs . '<div class="wb-content-area">' . $content . '</div></div>';
    }
}
