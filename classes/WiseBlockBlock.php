<?php

class WiseBlockBlock extends ObjectModel
{
    public $id;
    public $active = 1;
    public $position = 0;
    public $logic_mode = 'OR';
    public $publish_from;
    public $publish_to;
    public $time_from;
    public $time_to;
    public $days_of_week;
    public $auto_refresh = 0;
    public $lazy_load = 0;
    public $ab_variant = 'none';
    public $ab_auto_optimize = 0;
    public $ab_min_views = 500;
    public $ab_winner;
    public $views_count = 0;
    public $date_add;
    public $date_upd;

    // lang
    public $title;
    public $content;
    public $content_b;
    public $head_code;
    public $footer_code;

    public static $definition = array(
        'table' => 'wiseblock_block',
        'primary' => 'id_block',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'logic_mode' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 3),
            'publish_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'publish_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'time_from' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 5),
            'time_to' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 5),
            'days_of_week' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 20, 'required' => false),
            'auto_refresh' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'lazy_load' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'ab_variant' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 4),
            'ab_auto_optimize' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'ab_min_views' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'ab_winner' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 1, 'required' => false),
            'views_count' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            // lang
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'content_b' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => false),
            'head_code' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => false),
            'footer_code' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => false),
        )
    );

    /** Get hooks assigned to this block */
    public function getHooks()
    {
        if (!$this->id) { return array(); }
        $rows = Db::getInstance()->executeS('SELECT hook_name FROM '._DB_PREFIX_.'wiseblock_block_hook WHERE id_block='.(int)$this->id);
        return array_column($rows, 'hook_name');
    }

    /** Set hooks for this block */
    public function setHooks(array $hooks)
    {
        if (!$this->id) { return false; }
        Db::getInstance()->delete('wiseblock_block_hook', 'id_block='.(int)$this->id);
        foreach ($hooks as $hook) {
            Db::getInstance()->insert('wiseblock_block_hook', array(
                'id_block' => (int)$this->id,
                'hook_name' => pSQL($hook)
            ));
        }
        return true;
    }
}
