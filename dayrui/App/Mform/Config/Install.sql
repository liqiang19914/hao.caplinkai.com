CREATE TABLE IF NOT EXISTS `{dbprefix}module_form` (
    `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL COMMENT '表单名称',
    `table` varchar(50) NOT NULL COMMENT '表单表名称',
    `module` varchar(50) NOT NULL COMMENT '模块目录',
    `disabled` tinyint(1) unsigned NOT NULL COMMENT '是否禁用',
    `setting` text NOT NULL COMMENT '表单配置',
    PRIMARY KEY (`id`),
    KEY `table` (`table`),
    KEY `disabled` (`disabled`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT='模块表单表';