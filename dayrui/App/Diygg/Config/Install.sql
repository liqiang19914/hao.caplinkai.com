DROP TABLE IF EXISTS `{dbprefix}diygg_type`;
CREATE TABLE IF NOT EXISTS `{dbprefix}diygg_type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `typename` varchar(20) NOT NULL DEFAULT '' COMMENT '分类名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='广告分类';

DROP TABLE IF EXISTS `{dbprefix}diygg`;
CREATE TABLE IF NOT EXISTS `{dbprefix}diygg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adstyle` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '广告类型',
  `adbody` text COLLATE utf8mb4_unicode_ci COMMENT '广告内容',
  `typeid` smallint(5) NOT NULL DEFAULT '0' COMMENT '广告分类',
  `adname` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '广告位名称',
  `timeset` tinyint(1) NOT NULL DEFAULT '0' COMMENT '时间限制',
  `starttime` int(11) DEFAULT NULL COMMENT '开始时间',
  `endtime` int(11) DEFAULT NULL COMMENT '结束时间',
  `overpic` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '过期占位图',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='广告列表';
