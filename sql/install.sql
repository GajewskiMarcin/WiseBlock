-- Install tables for WiseBlock
CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_block` (
  `id_block` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `position` INT UNSIGNED NOT NULL DEFAULT 0,
  `logic_mode` ENUM('OR','AND') NOT NULL DEFAULT 'OR',
  `publish_from` DATETIME NULL,
  `publish_to` DATETIME NULL,
  `time_from` VARCHAR(5) NULL COMMENT 'Time restriction from (HH:MM)',
  `time_to` VARCHAR(5) NULL COMMENT 'Time restriction to (HH:MM)',
  `days_of_week` VARCHAR(20) NULL COMMENT 'Comma-separated day numbers 1=Mon..7=Sun, NULL=all days',
  `auto_refresh` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Auto-refresh via AJAX on cart update',
  `lazy_load` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Load block only when visible in viewport',
  `ab_variant` ENUM('none','A','B') NOT NULL DEFAULT 'none' COMMENT 'A/B test variant selection',
  `ab_auto_optimize` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Auto-optimize A/B after min_views reached',
  `ab_min_views` INT UNSIGNED NOT NULL DEFAULT 500 COMMENT 'Min views per variant before auto-optimize',
  `ab_winner` CHAR(1) NULL COMMENT 'Winning variant after auto-optimize (A or B, NULL=undecided)',
  `views_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total impressions counter',
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_block`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_block_hook` (
  `id_block` INT UNSIGNED NOT NULL,
  `hook_name` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id_block`, `hook_name`),
  KEY `hook_name` (`hook_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_block_lang` (
  `id_block` INT UNSIGNED NOT NULL,
  `id_lang` INT NOT NULL,
  `id_shop` INT NOT NULL,
  `title` VARCHAR(255) NULL,
  `content` LONGTEXT NOT NULL,
  `content_b` LONGTEXT NULL COMMENT 'A/B test variant B content',
  `head_code` LONGTEXT NULL,
  `footer_code` LONGTEXT NULL,
  PRIMARY KEY (`id_block`,`id_lang`,`id_shop`),
  KEY `id_lang` (`id_lang`),
  KEY `id_shop` (`id_shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_rule` (
  `id_rule` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_block` INT UNSIGNED NOT NULL,
  `type` VARCHAR(32) NOT NULL COMMENT 'Rule type: category, tag, customer_group, country, cart_value, manufacturer, supplier, feature, cart_product, currency, utm',
  `id_object` VARCHAR(512) NOT NULL COMMENT 'Object ID or JSON data for complex rules',
  `value_max` DECIMAL(20,6) NULL COMMENT 'Max value for cart_value rules (NULL = no limit)',
  `include` TINYINT(1) NOT NULL DEFAULT 1,
  `with_children` TINYINT(1) NOT NULL DEFAULT 0,
  `id_lang` INT NULL,
  PRIMARY KEY (`id_rule`),
  KEY `id_block` (`id_block`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_hook` (
  `id_wiseblock_hook` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hook_name` VARCHAR(128) NOT NULL UNIQUE,
  `enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `description` VARCHAR(255) NULL,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_wiseblock_hook`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_stats` (
  `id_stat` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_block` INT UNSIGNED NOT NULL,
  `variant` CHAR(1) NOT NULL DEFAULT 'A' COMMENT 'A or B variant',
  `date_stat` DATE NOT NULL,
  `views` INT UNSIGNED NOT NULL DEFAULT 0,
  `clicks` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_stat`),
  UNIQUE KEY `block_variant_date` (`id_block`, `variant`, `date_stat`),
  KEY `id_block` (`id_block`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
