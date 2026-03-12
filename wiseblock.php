<?php
if (!defined('_PS_VERSION_')) { exit; }

require_once __DIR__.'/classes/WiseBlockBlock.php';
require_once __DIR__.'/classes/WiseBlockHook.php';

class WiseBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wiseblock';
        $this->tab = 'front_office_features';
        $this->version = '1.4.0';
        $this->author = 'marcingajewski.pl';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '8.0.0', 'max' => '9.99.99');
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('WiseBlock - Contextual HTML Block');
        $this->description = $this->l('Create and manage contextual content blocks with flexible rules.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall WiseBlock? All blocks and settings will be deleted.');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // create tables
        $sql = file_get_contents(__DIR__.'/sql/install.sql');
        if (!$sql) {
            return false;
        }
        $sql = str_replace('{prefix}', _DB_PREFIX_, $sql);
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $q) {
            if ($q) {
                if (!Db::getInstance()->execute($q)) {
                    return false;
                }
            }
        }

        // create BO tabs
        if (!$this->installTabs()) { return false; }

        // register default hooks
        $defaultHooks = array(
            'displayWiseBlockUnderCart' => 'WiseBlock: Content under Add to cart button',
            'displayWiseBlockUnderDescription' => 'WiseBlock: Content under product description',
        );
        foreach ($defaultHooks as $hook => $desc) {
            $this->ensureHook($hook, $desc);
        }

        // Register as precaution for common product hooks (optional)
        $this->registerHook('displayHeader'); // for assets if needed
        $this->registerHook('actionFrontControllerSetMedia'); // for JS assets
        $this->registerHook('displayBeforeBodyClosingTag'); // for footer code injection
        $this->registerHook('displayCMSContent'); // for shortcodes in CMS
        $this->registerHook('displayProductDescription'); // for shortcodes in product descriptions
        return true;
    }

    public function uninstall()
    {
        $this->uninstallTabs();
        $this->dropTables();
        return parent::uninstall();
    }

    private function dropTables()
    {
        $sql = array(
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'wiseblock_block`',
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'wiseblock_block_lang`',
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'wiseblock_block_hook`',
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'wiseblock_rule`',
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'wiseblock_hook`',
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'wiseblock_stats`'
        );
        foreach ($sql as $q) {
            Db::getInstance()->execute($q);
        }
    }

    private function installTabs()
    {
        // Create "Secret Sauce" category under IMPROVE section
        $improve_id = (int)Tab::getIdFromClassName('IMPROVE');
        if (!$improve_id) {
            // Fallback for older PS versions
            $improve_id = (int)Tab::getIdFromClassName('AdminParentModulesSf');
        }
        if (!$improve_id) {
            $improve_id = 0;
        }

        // Create "Secret Sauce" parent category if not exists
        // This is a shared category - multiple modules can add their tabs here
        // module = '' means no single module owns it, so it won't be deleted when one module is uninstalled
        $secret_sauce_class = 'AdminSecretSauce';
        $secret_sauce_id = (int)Tab::getIdFromClassName($secret_sauce_class);
        if (!$secret_sauce_id) {
            $secretSauce = new Tab();
            $secretSauce->active = 1;
            $secretSauce->class_name = $secret_sauce_class;
            $secretSauce->id_parent = $improve_id;
            $secretSauce->module = ''; // No owner - shared category for multiple modules
            if (property_exists($secretSauce, 'icon')) {
                $secretSauce->icon = 'science'; // Material icon (flask)
            }
            foreach (Language::getLanguages(false) as $lang) {
                $secretSauce->name[(int)$lang['id_lang']] = 'Secret Sauce';
            }
            if (!$secretSauce->add()) {
                return false;
            }
            $secret_sauce_id = (int)$secretSauce->id;
        }

        // Single "WiseBlock" entry in menu (other tabs are internal, no menu items)
        // Tabs with id_parent = -1 are accessible but not visible in sidebar menu
        $tabs = array(
            // Main visible menu entry - under Secret Sauce
            array(
                'class_name' => 'AdminWiseBlockBlock',
                'name' => 'WiseBlock',
                'id_parent' => $secret_sauce_id, // Visible under Secret Sauce
            ),
            // Hidden tabs (accessible via internal navigation, not in side menu)
            // id_parent = -1 means: accessible but hidden from menu
            array(
                'class_name' => 'AdminWiseBlockHook',
                'name' => 'WiseBlock Hooks',
                'id_parent' => -1,
            ),
            array(
                'class_name' => 'AdminWiseBlockTools',
                'name' => 'WiseBlock Dashboard',
                'id_parent' => -1,
            ),
            array(
                'class_name' => 'AdminWiseBlockAbout',
                'name' => 'WiseBlock About',
                'id_parent' => -1,
            ),
        );
        foreach ($tabs as $t) {
            $existingId = Tab::getIdFromClassName($t['class_name']);
            if ($existingId) {
                // Update existing tab to fix parent if needed
                $tab = new Tab($existingId);
                $tab->id_parent = (int)$t['id_parent'];
                $tab->active = 1;
                $tab->save();
                continue;
            }
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = $t['class_name'];
            $tab->module = $this->name;
            $tab->id_parent = (int)$t['id_parent'];

            foreach (Language::getLanguages(false) as $lang) {
                $tab->name[(int)$lang['id_lang']] = $t['name'];
            }

            if (!$tab->add()) {
                return false;
            }
        }
        return true;
    }

    private function uninstallTabs()
    {
        // Remove WiseBlock tabs
        foreach (array('AdminWiseBlockBlock', 'AdminWiseBlockHook', 'AdminWiseBlockTools', 'AdminWiseBlockAbout', 'AdminWiseBlock') as $class) {
            $id = (int)Tab::getIdFromClassName($class);
            if ($id) {
                $tab = new Tab($id);
                $tab->delete();
            }
        }

        // Remove "Secret Sauce" category (only if no other tabs use it)
        $id_secret_sauce = (int)Tab::getIdFromClassName('AdminSecretSauce');
        if ($id_secret_sauce) {
            // Check if there are any other child tabs under Secret Sauce
            $children = Tab::getTabs(Context::getContext()->language->id, $id_secret_sauce);
            if (empty($children)) {
                $ss = new Tab($id_secret_sauce);
                $ss->delete();
            }
        }

        return true;
    }

    public function getContent()
    {
        // Redirect to Blocks list by default
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminWiseBlockBlock'));
    }

    /** Dynamic hook proxy: handle any hookDisplayWiseBlock* */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'hookDisplayWiseBlock') === 0) {
            $hookName = lcfirst(substr($name, 4)); // hookDisplayWiseBlock* -> displayWiseBlock*
            $params = isset($arguments[0]) ? $arguments[0] : array();
            return $this->renderForHook($hookName, $params);
        }
        return '';
    }

    /** Render blocks for given hook — works with or without product context */
    private function renderForHook($hookName, $params)
    {
        // enabled?
        $enabled = (int)Db::getInstance()->getValue('SELECT enabled FROM '._DB_PREFIX_.'wiseblock_hook WHERE hook_name="'.pSQL($hookName).'"');
        if (!$enabled) { return ''; }

        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $id_lang = (int)$context->language->id;

        // product id (optional — blocks work without it)
        $id_product = 0;
        if (!empty($params['product']) && is_array($params['product']) && !empty($params['product']['id_product'])) {
            $id_product = (int)$params['product']['id_product'];
        } elseif (isset($context->controller) && method_exists($context->controller, 'getProduct') && $context->controller->getProduct()) {
            $id_product = (int)$context->controller->getProduct()->id;
        }

        $cacheKey = 'wiseblock_'.$hookName.'_'.$id_shop.'_'.$id_lang.'_'.$id_product;
        if (Cache::isStored($cacheKey)) {
            return Cache::retrieve($cacheKey);
        }

        // get product categories (with tree) and tags — only if product context exists
        $categoryIds = $id_product ? $this->getProductCategoryTreeIds($id_product) : array();
        $tagIds = $id_product ? $this->getProductTagIds($id_product, $id_lang) : array();

        // find active blocks for hook
        $now = date('Y-m-d H:i:00');
        $blocks = Db::getInstance()->executeS('
            SELECT b.*, bl.title, bl.content, bl.content_b, bl.head_code, bl.footer_code
            FROM '._DB_PREFIX_.'wiseblock_block b
            JOIN '._DB_PREFIX_.'wiseblock_block_lang bl ON (b.id_block=bl.id_block AND bl.id_lang='.(int)$id_lang.' AND bl.id_shop='.(int)$id_shop.')
            JOIN '._DB_PREFIX_.'wiseblock_block_hook bh ON (b.id_block=bh.id_block)
            WHERE b.active=1
              AND bh.hook_name="'.pSQL($hookName).'"
              AND (b.publish_from IS NULL OR b.publish_from = "0000-00-00 00:00:00" OR b.publish_from <= "'.pSQL($now).'")
              AND (b.publish_to IS NULL OR b.publish_to = "0000-00-00 00:00:00" OR b.publish_to >= "'.pSQL($now).'")
            ORDER BY b.position ASC, b.id_block ASC
        ');

        $out = '';
        foreach ($blocks as $b) {
            // Check time restrictions (if set)
            if (!$this->isWithinTimeRange($b)) {
                continue;
            }
            if ($this->matchesRules((int)$b['id_block'], $categoryIds, $tagIds, $id_lang, $b['logic_mode'], $id_product)) {
                // A/B variant selection
                $variant = $this->getABVariant($b);
                $contentSource = ($variant === 'B' && !empty($b['content_b'])) ? $b['content_b'] : $b['content'];

                $content = $this->renderPlaceholders($contentSource, $id_product, $id_lang, $id_shop);

                // Pass extra flags to template
                $b['_auto_refresh'] = !empty($b['auto_refresh']);
                $b['_lazy_load'] = !empty($b['lazy_load']);
                $b['_variant'] = ($b['ab_variant'] !== 'none') ? $variant : '';

                $context->smarty->assign(array('content' => $content, 'block' => $b));
                $out .= $this->fetch('module:'.$this->name.'/views/templates/hook/block.tpl');

                // Auto-optimize check (after rendering)
                if (!empty($b['ab_auto_optimize']) && empty($b['ab_winner'])) {
                    $this->checkAutoOptimize((int)$b['id_block'], (int)$b['ab_min_views']);
                }
            }
        }

        Cache::store($cacheKey, $out);
        return $out;
    }

    /** Rule matching: categories + tags + manufacturer + supplier + feature with include/exclude and OR/AND */
    private function matchesRules($id_block, array $categoryIds, array $tagIds, $id_lang, $logicMode, $id_product = 0)
    {
        $rules = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'wiseblock_rule WHERE id_block='.(int)$id_block);
        if (!$rules || !count($rules)) {
            return true; // no rules -> always show
        }
        $includes = array(); $excludes = array();

        // Cache product data for manufacturer/supplier/feature checks
        $productManufacturer = null;
        $productSupplier = null;
        $productFeatureValues = null;

        foreach ($rules as $r) {
            $isInclude = (bool)$r['include'];
            $hit = false;
            if ($r['type'] === 'category') {
                $hit = in_array((int)$r['id_object'], $categoryIds);
            } elseif ($r['type'] === 'tag') {
                if ($r['id_lang'] && (int)$r['id_lang'] !== (int)$id_lang) {
                    $hit = false;
                } else {
                    $hit = in_array((int)$r['id_object'], $tagIds);
                }
            } elseif ($r['type'] === 'customer_group') {
                $hit = (int)Context::getContext()->customer->id_default_group == (int)$r['id_object']
                       || Context::getContext()->customer->isMemberOfGroup((int)$r['id_object']);
            } elseif ($r['type'] === 'country') {
                $hit = (int)Context::getContext()->country->id == (int)$r['id_object'];
            } elseif ($r['type'] === 'cart_value') {
                $cart = Context::getContext()->cart;
                if ($cart) {
                    $cartTotal = $cart->getOrderTotal(true);
                    $minValue = (float)$r['id_object'] / 100; // Stored as cents
                    $maxValue = isset($r['value_max']) && $r['value_max'] !== null ? (float)$r['value_max'] : null;
                    $hit = $cartTotal >= $minValue && ($maxValue === null || $cartTotal <= $maxValue);
                } else {
                    $hit = false;
                }
            } elseif ($r['type'] === 'manufacturer') {
                if ($id_product && $productManufacturer === null) {
                    $productManufacturer = (int)Db::getInstance()->getValue('SELECT id_manufacturer FROM '._DB_PREFIX_.'product WHERE id_product='.(int)$id_product);
                }
                $hit = $productManufacturer == (int)$r['id_object'];
            } elseif ($r['type'] === 'supplier') {
                if ($id_product && $productSupplier === null) {
                    $productSupplier = (int)Db::getInstance()->getValue('SELECT id_supplier FROM '._DB_PREFIX_.'product WHERE id_product='.(int)$id_product);
                }
                $hit = $productSupplier == (int)$r['id_object'];
            } elseif ($r['type'] === 'feature') {
                if ($id_product && $productFeatureValues === null) {
                    $productFeatureValues = array();
                    $fvRows = Db::getInstance()->executeS('SELECT id_feature_value FROM '._DB_PREFIX_.'feature_product WHERE id_product='.(int)$id_product);
                    foreach ($fvRows as $fv) {
                        $productFeatureValues[] = (int)$fv['id_feature_value'];
                    }
                }
                $hit = in_array((int)$r['id_object'], $productFeatureValues);
            } elseif ($r['type'] === 'cart_product') {
                // Check if specific product(s) are in the cart
                $cart = Context::getContext()->cart;
                if ($cart) {
                    $cartProducts = $cart->getProducts();
                    $cartProductIds = array_column($cartProducts, 'id_product');
                    // id_object stores comma-separated product IDs
                    $targetProductIds = array_map('intval', explode(',', $r['id_object']));
                    // Check if ANY of the target products is in the cart
                    $hit = !empty(array_intersect($targetProductIds, $cartProductIds));
                } else {
                    $hit = false;
                }
            } elseif ($r['type'] === 'currency') {
                // Check if current currency matches
                $hit = (int)Context::getContext()->currency->id == (int)$r['id_object'];
            } elseif ($r['type'] === 'utm') {
                // UTM tracking rule - id_object stores JSON with utm_source and utm_campaign
                $utmData = json_decode($r['id_object'], true);
                if (!$utmData) {
                    $hit = false;
                } else {
                    $utmSource = isset($utmData['source']) ? $utmData['source'] : '';
                    $utmCampaign = isset($utmData['campaign']) ? $utmData['campaign'] : '';

                    $currentSource = Tools::getValue('utm_source', '');
                    $currentCampaign = Tools::getValue('utm_campaign', '');

                    // Also check cookies/session for UTM persistence
                    if (empty($currentSource) && isset($_COOKIE['wb_utm_source'])) {
                        $currentSource = $_COOKIE['wb_utm_source'];
                    }
                    if (empty($currentCampaign) && isset($_COOKIE['wb_utm_campaign'])) {
                        $currentCampaign = $_COOKIE['wb_utm_campaign'];
                    }

                    // Logic: if both defined, both must match; if only one, only that one
                    $sourceMatch = empty($utmSource) || (strtolower($currentSource) === strtolower($utmSource));
                    $campaignMatch = empty($utmCampaign) || (strtolower($currentCampaign) === strtolower($utmCampaign));

                    // At least one must be defined and all defined must match
                    $hit = (!empty($utmSource) || !empty($utmCampaign)) && $sourceMatch && $campaignMatch;
                }
            } elseif ($r['type'] === 'search_query') {
                // Search query rule - id_object stores the search text to match
                $searchQuery = Tools::getValue('s', Tools::getValue('search_query', ''));
                if (!empty($searchQuery) && !empty($r['id_object'])) {
                    $hit = stripos($searchQuery, $r['id_object']) !== false;
                } else {
                    $hit = false;
                }
            }
            if ($isInclude) { $includes[] = $hit; } else { $excludes[] = $hit; }
        }

        // any exclusion hit -> reject
        if (in_array(true, $excludes, true)) {
            return false;
        }

        // include logic
        if (!count($includes)) { return true; }
        if ($logicMode === 'AND') {
            return !in_array(false, $includes, true);
        } else {
            return in_array(true, $includes, true);
        }
    }

    public function hookDisplayCMSContent($params)
    {
        return $this->parseShortcodes($params['content']);
    }

    public function hookDisplayProductDescription($params)
    {
        if (isset($params['product']['description'])) {
            $params['product']['description'] = $this->parseShortcodes($params['product']['description']);
        }
        return '';
    }

    private function parseShortcodes($content)
    {
        if (empty($content)) { return $content; }
        return preg_replace_callback('/\[wiseblock\s+id="(\d+)"\]/', function($m) {
            $id_block = (int)$m[1];
            $block = new WiseBlockBlock($id_block, (int)$this->context->language->id);
            if ($block->id && $block->active) {
                return $block->content;
            }
            return '';
        }, $content);
    }

    /** Check if block should be shown based on days_of_week, time_from and time_to */
    private function isWithinTimeRange($block)
    {
        // Check days of week first
        $daysOfWeek = isset($block['days_of_week']) ? trim($block['days_of_week']) : '';
        if (!empty($daysOfWeek)) {
            $allowedDays = array_map('intval', explode(',', $daysOfWeek));
            $currentDay = (int)date('N'); // 1=Mon, 7=Sun
            if (!in_array($currentDay, $allowedDays)) {
                return false;
            }
        }

        $timeFrom = isset($block['time_from']) ? trim($block['time_from']) : '';
        $timeTo = isset($block['time_to']) ? trim($block['time_to']) : '';

        // If no time restrictions, allow
        if (empty($timeFrom) && empty($timeTo)) {
            return true;
        }

        $currentTime = date('H:i');

        // Handle overnight ranges (e.g., 22:00 to 06:00)
        if (!empty($timeFrom) && !empty($timeTo)) {
            if ($timeFrom <= $timeTo) {
                // Normal range (e.g., 09:00 to 18:00)
                return $currentTime >= $timeFrom && $currentTime <= $timeTo;
            } else {
                // Overnight range (e.g., 22:00 to 06:00)
                return $currentTime >= $timeFrom || $currentTime <= $timeTo;
            }
        }

        // Only from set - must be after
        if (!empty($timeFrom) && empty($timeTo)) {
            return $currentTime >= $timeFrom;
        }

        // Only to set - must be before
        if (empty($timeFrom) && !empty($timeTo)) {
            return $currentTime <= $timeTo;
        }

        return true;
    }

    /** Return all category ids including parents for a product */
    private function getProductCategoryTreeIds($id_product)
    {
        $ids = array();
        $rows = Db::getInstance()->executeS('SELECT id_category FROM '._DB_PREFIX_.'category_product WHERE id_product='.(int)$id_product);
        foreach ($rows as $r) {
            $cid = (int)$r['id_category'];
            $ids[] = $cid;
            // add parents up to root
            $ids = array_merge($ids, $this->getParentCategoryIds($cid));
        }
        $ids = array_values(array_unique($ids));
        return $ids;
    }

    private function getParentCategoryIds($id_category)
    {
        $ids = array();
        $cat = new Category((int)$id_category, (int)Context::getContext()->language->id);
        while ($cat && $cat->id_parent && $cat->id_parent != $cat->id) {
            $ids[] = (int)$cat->id_parent;
            $cat = new Category((int)$cat->id_parent, (int)Context::getContext()->language->id);
            if (!$cat->id) break;
        }
        return $ids;
    }

    /** Get tag ids for product and language */
    private function getProductTagIds($id_product, $id_lang)
    {
        $ids = array();
        $rows = Db::getInstance()->executeS('SELECT pt.id_tag FROM '._DB_PREFIX_.'product_tag pt
            INNER JOIN '._DB_PREFIX_.'tag t ON (pt.id_tag=t.id_tag)
            WHERE pt.id_product='.(int)$id_product.' AND t.id_lang='.(int)$id_lang);
        foreach ($rows as $r) { $ids[] = (int)$r['id_tag']; }
        return array_values(array_unique($ids));
    }

    /** Placeholder renderer with whitelist — works with or without product context */
    private function renderPlaceholders($html, $id_product, $id_lang, $id_shop)
    {
        $context = Context::getContext();
        $locale = $context->getCurrentLocale();
        $currency = $context->currency->iso_code;

        $replacements = array(
            '{{shop_name}}' => pSQL(Configuration::get('PS_SHOP_NAME'), true),
        );

        // Customer placeholder
        $customer = $context->customer;
        $replacements['{{customer_name}}'] = ($customer && $customer->isLogged()) ? htmlspecialchars($customer->firstname) : '';

        // Search query placeholder
        $replacements['{{search_query}}'] = htmlspecialchars(Tools::getValue('s', Tools::getValue('search_query', '')));

        // Product-based placeholders (only when product context exists)
        if ($id_product) {
            $product = new Product((int)$id_product, true, (int)$id_lang, (int)$id_shop);
            $replacements['{{product_name}}'] = pSQL($product->name, true);
            $replacements['{{reference}}'] = pSQL($product->reference, true);
            $replacements['{{ean13}}'] = pSQL($product->ean13, true);
            $replacements['{{manufacturer}}'] = pSQL(Manufacturer::getNameById((int)$product->id_manufacturer), true);
            $replacements['{{category_name}}'] = pSQL($this->getDefaultCategoryName($product, $id_lang), true);
            $replacements['{{category_description}}'] = $this->getDefaultCategoryDescription($product, $id_lang);
            $replacements['{{product_url}}'] = $context->link->getProductLink($product);
            $replacements['{{add_to_cart_url}}'] = $this->getAddToCartUrl($product);
            $replacements['{{price}}'] = $locale->formatPrice($product->getPrice(true), $currency);
            $replacements['{{price_without_tax}}'] = $locale->formatPrice($product->getPrice(false), $currency);
            $replacements['{{stock_status}}'] = $product->quantity > 0 ? $this->l('In Stock') : $this->l('Out of Stock');
            $replacements['{{stock_quantity}}'] = (int)$product->quantity;
            $replacements['{{weight}}'] = $product->weight > 0 ? number_format($product->weight, 2) . ' ' . Configuration::get('PS_WEIGHT_UNIT') : '';
        }

        // Cart-based placeholders
        $cart = $context->cart;
        $cartTotal = $cart ? $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING) : 0;
        $cartTotalWithShipping = $cart ? $cart->getOrderTotal(true) : 0;
        $cartProductsCount = $cart ? $cart->nbProducts() : 0;

        $replacements['{{cart_total}}'] = $locale->formatPrice($cartTotal, $currency);
        $replacements['{{cart_total_with_shipping}}'] = $locale->formatPrice($cartTotalWithShipping, $currency);
        $replacements['{{cart_products_count}}'] = (int)$cartProductsCount;

        // Free shipping placeholders
        $freeShippingData = $this->getFreeShippingData($cart);
        $replacements['{{free_shipping_threshold}}'] = $freeShippingData['threshold'] > 0
            ? $locale->formatPrice($freeShippingData['threshold'], $currency)
            : '';
        $replacements['{{free_shipping_remaining}}'] = $freeShippingData['remaining'] > 0
            ? $locale->formatPrice($freeShippingData['remaining'], $currency)
            : '';
        $replacements['{{free_shipping_remaining_raw}}'] = number_format($freeShippingData['remaining'], 2, '.', '');
        $replacements['{{free_shipping_progress}}'] = (int)$freeShippingData['progress'];
        $replacements['{{free_shipping_achieved}}'] = $freeShippingData['achieved'] ? '1' : '0';

        // Conditional blocks: {{#if_free_shipping_not_achieved}}...{{/if_free_shipping_not_achieved}}
        if ($freeShippingData['achieved'] || $freeShippingData['threshold'] <= 0) {
            $html = preg_replace('/\{\{#if_free_shipping_not_achieved\}\}.*?\{\{\/if_free_shipping_not_achieved\}\}/s', '', $html);
        } else {
            $html = str_replace(array('{{#if_free_shipping_not_achieved}}', '{{/if_free_shipping_not_achieved}}'), '', $html);
        }

        // Conditional: {{#if_free_shipping_achieved}}...{{/if_free_shipping_achieved}}
        if (!$freeShippingData['achieved']) {
            $html = preg_replace('/\{\{#if_free_shipping_achieved\}\}.*?\{\{\/if_free_shipping_achieved\}\}/s', '', $html);
        } else {
            $html = str_replace(array('{{#if_free_shipping_achieved}}', '{{/if_free_shipping_achieved}}'), '', $html);
        }

        // ========== STOCK CONDITIONS (product-only) ==========
        if ($id_product && isset($product)) {
            // {{#if_in_stock}}...{{/if_in_stock}}
            if ($product->quantity <= 0) {
                $html = preg_replace('/\{\{#if_in_stock\}\}.*?\{\{\/if_in_stock\}\}/s', '', $html);
            } else {
                $html = str_replace(array('{{#if_in_stock}}', '{{/if_in_stock}}'), '', $html);
            }

            // {{#if_out_of_stock}}...{{/if_out_of_stock}}
            if ($product->quantity > 0) {
                $html = preg_replace('/\{\{#if_out_of_stock\}\}.*?\{\{\/if_out_of_stock\}\}/s', '', $html);
            } else {
                $html = str_replace(array('{{#if_out_of_stock}}', '{{/if_out_of_stock}}'), '', $html);
            }

            // {{#if_low_stock}}...{{/if_low_stock}} - shows when stock <= 5
            $lowStockThreshold = (int)Configuration::get('WISEBLOCK_LOW_STOCK_THRESHOLD');
            if ($lowStockThreshold <= 0) {
                $lowStockThreshold = 5; // default
            }
            if ($product->quantity <= 0 || $product->quantity > $lowStockThreshold) {
                $html = preg_replace('/\{\{#if_low_stock\}\}.*?\{\{\/if_low_stock\}\}/s', '', $html);
            } else {
                $html = str_replace(array('{{#if_low_stock}}', '{{/if_low_stock}}'), '', $html);
            }

            // {{#if_on_sale}}...{{/if_on_sale}} - shows when product has reduction
            $hasReduction = $product->specificPrice || $product->getPrice(true) < $product->getPrice(true, null, 6, null, false, false);
            if (!$hasReduction) {
                $html = preg_replace('/\{\{#if_on_sale\}\}.*?\{\{\/if_on_sale\}\}/s', '', $html);
            } else {
                $html = str_replace(array('{{#if_on_sale}}', '{{/if_on_sale}}'), '', $html);
            }

            // {{#if_has_manufacturer}}...{{/if_has_manufacturer}}
            if (!$product->id_manufacturer) {
                $html = preg_replace('/\{\{#if_has_manufacturer\}\}.*?\{\{\/if_has_manufacturer\}\}/s', '', $html);
            } else {
                $html = str_replace(array('{{#if_has_manufacturer}}', '{{/if_has_manufacturer}}'), '', $html);
            }

            // {{#if_new_product}}...{{/if_new_product}}
            $isNew = $product->isNew();
            if (!$isNew) {
                $html = preg_replace('/\{\{#if_new_product\}\}.*?\{\{\/if_new_product\}\}/s', '', $html);
            } else {
                $html = str_replace(array('{{#if_new_product}}', '{{/if_new_product}}'), '', $html);
            }
        } else {
            // No product context — remove all product-conditional blocks
            $html = preg_replace('/\{\{#if_in_stock\}\}.*?\{\{\/if_in_stock\}\}/s', '', $html);
            $html = preg_replace('/\{\{#if_out_of_stock\}\}.*?\{\{\/if_out_of_stock\}\}/s', '', $html);
            $html = preg_replace('/\{\{#if_low_stock\}\}.*?\{\{\/if_low_stock\}\}/s', '', $html);
            $html = preg_replace('/\{\{#if_on_sale\}\}.*?\{\{\/if_on_sale\}\}/s', '', $html);
            $html = preg_replace('/\{\{#if_has_manufacturer\}\}.*?\{\{\/if_has_manufacturer\}\}/s', '', $html);
            $html = preg_replace('/\{\{#if_new_product\}\}.*?\{\{\/if_new_product\}\}/s', '', $html);
        }

        // ========== CART CONDITIONS ==========
        // {{#if_cart_empty}}...{{/if_cart_empty}}
        if ($cartProductsCount > 0) {
            $html = preg_replace('/\{\{#if_cart_empty\}\}.*?\{\{\/if_cart_empty\}\}/s', '', $html);
        } else {
            $html = str_replace(array('{{#if_cart_empty}}', '{{/if_cart_empty}}'), '', $html);
        }

        // {{#if_cart_not_empty}}...{{/if_cart_not_empty}}
        if ($cartProductsCount <= 0) {
            $html = preg_replace('/\{\{#if_cart_not_empty\}\}.*?\{\{\/if_cart_not_empty\}\}/s', '', $html);
        } else {
            $html = str_replace(array('{{#if_cart_not_empty}}', '{{/if_cart_not_empty}}'), '', $html);
        }

        // ========== CUSTOMER CONDITIONS ==========
        // {{#if_logged_in}}...{{/if_logged_in}}
        $isLoggedIn = $context->customer && $context->customer->isLogged();
        if (!$isLoggedIn) {
            $html = preg_replace('/\{\{#if_logged_in\}\}.*?\{\{\/if_logged_in\}\}/s', '', $html);
        } else {
            $html = str_replace(array('{{#if_logged_in}}', '{{/if_logged_in}}'), '', $html);
        }

        // {{#if_guest}}...{{/if_guest}}
        if ($isLoggedIn) {
            $html = preg_replace('/\{\{#if_guest\}\}.*?\{\{\/if_guest\}\}/s', '', $html);
        } else {
            $html = str_replace(array('{{#if_guest}}', '{{/if_guest}}'), '', $html);
        }

        return strtr($html, $replacements);
    }

    /** Get free shipping threshold and remaining amount */
    private function getFreeShippingData($cart)
    {
        $result = array(
            'threshold' => 0,
            'remaining' => 0,
            'progress' => 0,
            'achieved' => false,
        );

        if (!$cart) {
            return $result;
        }

        $context = Context::getContext();
        $id_zone = (int)Address::getZoneById($cart->id_address_delivery);
        if (!$id_zone) {
            $id_zone = (int)Country::getIdZone($context->country->id);
        }

        // Method 1: Check for cart rules with free shipping
        $freeShippingThreshold = $this->getFreeShippingFromCartRules();

        // Method 2: Check carrier free shipping ranges (if no cart rule found)
        if ($freeShippingThreshold <= 0) {
            $freeShippingThreshold = $this->getFreeShippingFromCarriers($id_zone);
        }

        // Method 3: Check PrestaShop native free shipping setting (Preferences > Shipping)
        if ($freeShippingThreshold <= 0) {
            $psThreshold = (float)Configuration::get('PS_SHIPPING_FREE_PRICE');
            if ($psThreshold > 0) {
                $freeShippingThreshold = $psThreshold;
            }
        }

        if ($freeShippingThreshold > 0) {
            $cartTotal = $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
            $result['threshold'] = $freeShippingThreshold;
            $result['remaining'] = max(0, $freeShippingThreshold - $cartTotal);
            $result['progress'] = min(100, round(($cartTotal / $freeShippingThreshold) * 100));
            $result['achieved'] = $cartTotal >= $freeShippingThreshold;
        }

        return $result;
    }

    /** Get free shipping threshold from active cart rules */
    private function getFreeShippingFromCartRules()
    {
        $now = date('Y-m-d H:i:s');
        $threshold = Db::getInstance()->getValue('
            SELECT MIN(minimum_amount)
            FROM '._DB_PREFIX_.'cart_rule
            WHERE free_shipping = 1
              AND active = 1
              AND minimum_amount > 0
              AND (date_from <= "'.pSQL($now).'" OR date_from = "0000-00-00 00:00:00")
              AND (date_to >= "'.pSQL($now).'" OR date_to = "0000-00-00 00:00:00")
        ');
        return (float)$threshold;
    }

    /** Get free shipping threshold from carriers (price ranges) */
    private function getFreeShippingFromCarriers($id_zone)
    {
        // PS9 renamed price1/price2 to delimiter1/delimiter2 in range_price
        $colLow = 'delimiter1';
        $colHigh = 'delimiter2';
        $cols = Db::getInstance()->executeS('SHOW COLUMNS FROM '._DB_PREFIX_.'range_price');
        $colNames = array_column($cols, 'Field');
        if (in_array('price1', $colNames)) {
            $colLow = 'price1';
            $colHigh = 'price2';
        }

        // Find carriers that have free shipping above certain price
        $carriers = Carrier::getCarriers(Context::getContext()->language->id, true, false, $id_zone);
        $minThreshold = 0;

        foreach ($carriers as $carrier) {
            if (!$carrier['active']) continue;

            // Check if carrier has price-based ranges with 0 cost (free shipping)
            $ranges = Db::getInstance()->executeS('
                SELECT dr.`'.$colHigh.'` as max_price
                FROM '._DB_PREFIX_.'delivery d
                JOIN '._DB_PREFIX_.'range_price dr ON (d.id_range_price = dr.id_range_price)
                WHERE d.id_carrier = '.(int)$carrier['id_carrier'].'
                  AND d.id_zone = '.(int)$id_zone.'
                  AND d.price = 0
                ORDER BY dr.`'.$colHigh.'` DESC
                LIMIT 1
            ');

            if ($ranges && count($ranges) > 0) {
                // There's a free shipping range, find the minimum price to qualify
                $freeAbove = Db::getInstance()->getValue('
                    SELECT MIN(dr.`'.$colLow.'`) as min_price
                    FROM '._DB_PREFIX_.'delivery d
                    JOIN '._DB_PREFIX_.'range_price dr ON (d.id_range_price = dr.id_range_price)
                    WHERE d.id_carrier = '.(int)$carrier['id_carrier'].'
                      AND d.id_zone = '.(int)$id_zone.'
                      AND d.price = 0
                ');

                if ($freeAbove > 0 && ($minThreshold == 0 || $freeAbove < $minThreshold)) {
                    $minThreshold = (float)$freeAbove;
                }
            }
        }

        return $minThreshold;
    }

    private function getDefaultCategoryName(Product $product, $id_lang)
    {
        if ($product->id_category_default) {
            $cat = new Category((int)$product->id_category_default, (int)$id_lang);
            if ($cat && $cat->id) { return $cat->name; }
        }
        return '';
    }

    private function getDefaultCategoryDescription(Product $product, $id_lang)
    {
        if ($product->id_category_default) {
            $cat = new Category((int)$product->id_category_default, (int)$id_lang);
            if ($cat && $cat->id) { return $cat->description; }
        }
        return '';
    }

    private function getAddToCartUrl(Product $product)
    {
        $context = Context::getContext();
        return $context->link->getPageLink('cart', null, $context->language->id, array(
            'add' => 1,
            'id_product' => (int)$product->id,
            'token' => Tools::getToken(false)
        ));
    }

    /** Helper: ensure hook exists and module is registered; store in mgcc_hook */
    private function ensureHook($hookName, $desc='')
    {
        $id_hook = (int)Hook::getIdByName($hookName);
        if (!$id_hook) {
            $hook = new Hook();
            $hook->name = $hookName;
            $hook->title = $hookName;
            $hook->description = $desc;
            $hook->add();
        }
        if (!$this->isRegisteredInHook($hookName)) {
            $this->registerHook($hookName);
        }
        $exists = Db::getInstance()->getValue('SELECT id_wiseblock_hook FROM '._DB_PREFIX_.'wiseblock_hook WHERE hook_name="'.pSQL($hookName).'"');
        $row = array(
            'hook_name' => pSQL($hookName),
            'enabled' => 1,
            'description' => pSQL($desc),
            'date_upd' => date('Y-m-d H:i:s')
        );
        if ($exists) {
            Db::getInstance()->update('wiseblock_hook', $row, 'id_wiseblock_hook='.(int)$exists);
        } else {
            $row['date_add'] = date('Y-m-d H:i:s');
            Db::getInstance()->insert('wiseblock_hook', $row);
        }
    }

    /** Hook displayHeader - inject custom CSS, head code, and save UTM params */
    public function hookDisplayHeader($params)
    {
        // Save UTM parameters to cookies for later use in targeting rules
        $this->saveUtmToCookies();

        $out = '';

        $customCSS = Configuration::get('WISEBLOCK_CUSTOM_CSS');
        if (!empty($customCSS)) {
            $out .= '<style type="text/css">' . $customCSS . '</style>';
        }

        // Inject head_code from active blocks for current page
        $out .= $this->getActiveBlocksCode('head_code');

        return $out;
    }

    /** Hook actionFrontControllerSetMedia - register JS assets */
    public function hookActionFrontControllerSetMedia($params)
    {
        // Check if any blocks need refresh or lazy loading
        $hasRefreshOrLazy = (int)Db::getInstance()->getValue('
            SELECT COUNT(*) FROM '._DB_PREFIX_.'wiseblock_block
            WHERE active = 1 AND (auto_refresh = 1 OR lazy_load = 1 OR ab_variant != "none")
        ');

        if ($hasRefreshOrLazy) {
            $this->context->controller->registerJavascript(
                'wiseblock-refresh',
                'modules/'.$this->name.'/views/js/wiseblock-refresh.js',
                array('position' => 'bottom', 'priority' => 200)
            );
        }
    }

    /** Get head_code or footer_code from active blocks */
    private function getActiveBlocksCode($field)
    {
        $context = Context::getContext();
        $id_lang = (int)$context->language->id;
        $id_shop = (int)$context->shop->id;
        $now = date('Y-m-d H:i:00');

        $codes = Db::getInstance()->executeS('
            SELECT bl.'.$field.'
            FROM '._DB_PREFIX_.'wiseblock_block b
            JOIN '._DB_PREFIX_.'wiseblock_block_lang bl ON (b.id_block=bl.id_block AND bl.id_lang='.(int)$id_lang.' AND bl.id_shop='.(int)$id_shop.')
            WHERE b.active=1
              AND bl.'.$field.' IS NOT NULL AND bl.'.$field.' != ""
              AND (b.publish_from IS NULL OR b.publish_from = "0000-00-00 00:00:00" OR b.publish_from <= "'.pSQL($now).'")
              AND (b.publish_to IS NULL OR b.publish_to = "0000-00-00 00:00:00" OR b.publish_to >= "'.pSQL($now).'")
        ');

        $out = '';
        if ($codes) {
            foreach ($codes as $row) {
                if (!empty($row[$field])) {
                    $out .= $row[$field] . "\n";
                }
            }
        }
        return $out;
    }

    /** Inject footer code before </body> via displayBeforeBodyClosingTag or displayFooter */
    public function hookDisplayBeforeBodyClosingTag($params)
    {
        return $this->getActiveBlocksCode('footer_code');
    }

    /** Save UTM parameters to cookies (30 days expiry) */
    private function saveUtmToCookies()
    {
        $utmSource = Tools::getValue('utm_source', '');
        $utmCampaign = Tools::getValue('utm_campaign', '');

        $expiry = time() + (30 * 24 * 60 * 60); // 30 days

        if (!empty($utmSource)) {
            setcookie('wb_utm_source', $utmSource, $expiry, '/');
        }
        if (!empty($utmCampaign)) {
            setcookie('wb_utm_campaign', $utmCampaign, $expiry, '/');
        }
    }

    /** A/B variant selection: returns 'A' or 'B' based on winner or random split */
    private function getABVariant($block)
    {
        if ($block['ab_variant'] === 'none') {
            return 'A';
        }

        // If winner selected, always show winner
        if (!empty($block['ab_winner'])) {
            return $block['ab_winner'];
        }

        // 50/50 random split, persisted in session
        $sessionKey = 'wb_ab_' . (int)$block['id_block'];
        if (isset($_COOKIE[$sessionKey])) {
            return $_COOKIE[$sessionKey] === 'B' ? 'B' : 'A';
        }

        $variant = mt_rand(0, 1) === 0 ? 'A' : 'B';
        setcookie($sessionKey, $variant, time() + (30 * 24 * 3600), '/');
        return $variant;
    }

    /** Check if auto-optimize conditions are met and select winner */
    private function checkAutoOptimize($id_block, $minViews)
    {
        $db = Db::getInstance();
        $stats = $db->executeS('
            SELECT `variant`, SUM(`views`) as total_views, SUM(`clicks`) as total_clicks
            FROM `'._DB_PREFIX_.'wiseblock_stats`
            WHERE `id_block` = '.(int)$id_block.'
            GROUP BY `variant`
        ');

        if (!$stats || count($stats) < 2) {
            return; // Need data for both variants
        }

        $data = array();
        foreach ($stats as $row) {
            $data[$row['variant']] = array(
                'views' => (int)$row['total_views'],
                'clicks' => (int)$row['total_clicks'],
            );
        }

        if (!isset($data['A']) || !isset($data['B'])) {
            return;
        }

        // Both variants must reach minimum views
        if ($data['A']['views'] < $minViews || $data['B']['views'] < $minViews) {
            return;
        }

        // Calculate CTR
        $ctrA = $data['A']['views'] > 0 ? $data['A']['clicks'] / $data['A']['views'] : 0;
        $ctrB = $data['B']['views'] > 0 ? $data['B']['clicks'] / $data['B']['views'] : 0;

        // Select winner (higher CTR wins, A wins ties)
        $winner = $ctrB > $ctrA ? 'B' : 'A';

        $db->execute('UPDATE `'._DB_PREFIX_.'wiseblock_block` SET `ab_winner` = "'.pSQL($winner).'" WHERE `id_block` = '.(int)$id_block);
    }

    /** Render a single block by ID (used by AJAX refresh controller) */
    public function renderBlockById($id_block)
    {
        $context = Context::getContext();
        $id_lang = (int)$context->language->id;
        $id_shop = (int)$context->shop->id;

        $block = Db::getInstance()->getRow('
            SELECT b.*, bl.content, bl.content_b
            FROM '._DB_PREFIX_.'wiseblock_block b
            JOIN '._DB_PREFIX_.'wiseblock_block_lang bl ON (b.id_block=bl.id_block AND bl.id_lang='.(int)$id_lang.' AND bl.id_shop='.(int)$id_shop.')
            WHERE b.id_block='.(int)$id_block.' AND b.active=1
        ');

        if (!$block) {
            return '';
        }

        // A/B variant selection
        $variant = $this->getABVariant($block);
        $contentSource = ($variant === 'B' && !empty($block['content_b'])) ? $block['content_b'] : $block['content'];

        return $this->renderPlaceholders($contentSource, 0, $id_lang, $id_shop);
    }

    /** Render all blocks for a hook (used by AJAX refresh controller) */
    public function renderHookBlocks($hookName)
    {
        return $this->renderForHook($hookName, array());
    }

    /** Debug tester used by Tools controller */
    public function debugTestMatching($id_product, $hookName, $id_lang, $id_shop)
    {
        $categoryIds = $this->getProductCategoryTreeIds($id_product);
        $tagIds = $this->getProductTagIds($id_product, $id_lang);

        $now = date('Y-m-d H:i:00');
        $blocks = Db::getInstance()->executeS('
            SELECT b.*, bl.title
            FROM '._DB_PREFIX_.'wiseblock_block b
            JOIN '._DB_PREFIX_.'wiseblock_block_lang bl ON (b.id_block=bl.id_block AND bl.id_lang='.(int)$id_lang.' AND bl.id_shop='.(int)$id_shop.')
            JOIN '._DB_PREFIX_.'wiseblock_block_hook bh ON (b.id_block=bh.id_block)
            WHERE b.active=1 AND bh.hook_name="'.pSQL($hookName).'"
              AND (b.publish_from IS NULL OR b.publish_from = "0000-00-00 00:00:00" OR b.publish_from <= "'.pSQL($now).'")
              AND (b.publish_to IS NULL OR b.publish_to = "0000-00-00 00:00:00" OR b.publish_to >= "'.pSQL($now).'")
            ORDER BY b.position ASC, b.id_block ASC
        ');

        $report = array(
            'product' => (int)$id_product,
            'hook' => $hookName,
            'categories' => $categoryIds,
            'tags' => $tagIds,
            'now' => $now,
            'blocks_checked' => array(),
        );
        foreach ($blocks as $b) {
            $ok = $this->matchesRules((int)$b['id_block'], $categoryIds, $tagIds, $id_lang, $b['logic_mode'], $id_product);
            $report['blocks_checked'][] = array('id_block' => (int)$b['id_block'], 'title' => $b['title'], 'matched' => $ok, 'logic' => $b['logic_mode']);
        }

        return json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
