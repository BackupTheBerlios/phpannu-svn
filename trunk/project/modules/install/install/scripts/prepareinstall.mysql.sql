#
# Table structure for table `installedmodules`
#
DROP TABLE IF EXISTS  copixmodule;
CREATE TABLE copixmodule (
  name_cpm varchar(255) NOT NULL default '',
  PRIMARY KEY  (name_cpm)
);

DROP TABLE IF EXISTS `copixconfig`;
CREATE TABLE `copixconfig` (
  `id_ccfg` varchar(255) NOT NULL default '',
  `group_ccfg` varchar(255) NOT NULL default '',
  `value_ccfg` varchar(255) default NULL,
  PRIMARY KEY  (`id_ccfg`)
);