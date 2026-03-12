<?php

require_once _PS_MODULE_DIR_.'wiseblock/controllers/admin/WiseBlockAdminHelper.php';

class AdminWiseBlockAboutController extends ModuleAdminController
{
    public function __construct()
    {
        $this->module = Module::getInstanceByName('wiseblock');
        $this->bootstrap = true;
        parent::__construct();
        $this->meta_title = 'WiseBlock: About & Help';
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

        // All content is rendered in renderList()
        $content = $this->renderList();

        // Render tabs and prepend to content
        $tabs = WiseBlockAdminHelper::renderTabs($this->context, 'AdminWiseBlockAbout');

        // Assign combined content to Smarty
        $this->context->smarty->assign([
            'content' => $tabs . $content,
        ]);
    }

    public function renderList()
    {
        $module = Module::getInstanceByName('wiseblock');

        // GitHub URLs
        $github_base = 'https://github.com/GajewskiMarcin/WiseBlock';

        $this->context->smarty->assign(array(
            'module_version' => $module ? $module->version : '1.0.0',
            'module_author' => $module ? $module->author : 'marcingajewski.pl',
            'ps_version' => _PS_VERSION_,
            'php_version' => phpversion(),
            'php_os' => PHP_OS,
            'current_year' => date('Y'),
            'module_dir' => $module ? $module->getPathUri() : '',
            // GitHub links
            'github_repo_url' => $github_base,
            'github_wiki_url' => $github_base . '/wiki',
            'github_issues_url' => $github_base . '/issues',
            'github_discussions_url' => $github_base . '/discussions',
            // Support link
            'buymeacoffee_url' => 'https://buymeacoffee.com/marcingajewski',
        ));

        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'wiseblock/views/templates/admin/about.tpl');
    }
}
