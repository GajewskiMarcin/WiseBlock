<?php

class WiseBlockHook extends ObjectModel
{
    public $id;
    public $hook_name;
    public $enabled = 1;
    public $description;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wiseblock_hook',
        'primary' => 'id_wiseblock_hook',
        'fields' => array(
            'hook_name' => array('type' => self::TYPE_STRING, 'validate' => 'isHookName', 'required' => true, 'size' => 128),
            'enabled' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'description' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        )
    );
}
