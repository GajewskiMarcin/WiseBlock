<?php
/**
 * Secret Sauce - Parent menu controller
 * This is a container tab for custom modules
 */

class AdminSecretSauceController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        // Redirect to first child module (WiseBlock)
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminWiseBlockBlock'));
    }
}
