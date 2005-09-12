#
# Table structure for table `copixgroup`
#
DROP TABLE IF EXISTS `copixgroup`;
CREATE TABLE `copixgroup` (
  `id_cgrp` bigint(20) NOT NULL default '0',
  `name_cgrp` varchar(50) NOT NULL default '',
  `description_cgrp` varchar(255) NULL default '',
  `all_cgrp` tinyint(1) NOT NULL default '0',
  `known_cgrp` tinyint(1) NOT NULL default '0',
  `isadmin_cgrp` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_cgrp`)
) TYPE=MyISAM;
INSERT INTO `copixgroup` (`id_cgrp`, `name_cgrp`, `description_cgrp`, `all_cgrp`, `known_cgrp`, `isadmin_cgrp` ) VALUES (1, 'admin', 'Siteadmin', 0, 0, 1);

#
# Table structure for table `copixcapability`
#
DROP TABLE IF EXISTS `copixcapability`;
CREATE TABLE `copixcapability` (
  `name_ccpb` varchar(50) NOT NULL default '',
  `description_ccpb` varchar(255) NOT NULL default '',
  `name_ccpt` varchar(255) NOT NULL default '',
  `values_ccpb` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`name_ccpb`)
) TYPE=MyISAM;
INSERT INTO `copixcapability` (`name_ccpb`, `description_ccpb`, `name_ccpt`, `values_ccpb`) VALUES ('copixheadings', 'Rubriques', 'modules|copixheadings', '0;10;30');
INSERT INTO `copixcapability` (`name_ccpb`, `description_ccpb`, `name_ccpt`, `values_ccpb`) VALUES ('siteAdmin', 'Administration', 'site', '0;70');

#
# Table structure for table `copixcapabilitypath`
#
DROP TABLE IF EXISTS `copixcapabilitypath`;
CREATE TABLE `copixcapabilitypath` (
  `name_ccpt` varchar(255) NOT NULL default '',
  `description_ccpt` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`name_ccpt`)
) TYPE=MyISAM;
INSERT INTO `copixcapabilitypath` (`name_ccpt`, `description_ccpt`) VALUES ('modules|copixheadings', 'Rubrique principale');
INSERT INTO `copixcapabilitypath` (`name_ccpt`, `description_ccpt`) VALUES ('site', 'Site');

#
# Table structure for table `copixgroupcapabilities`
#
DROP TABLE IF EXISTS `copixgroupcapabilities`;
CREATE TABLE `copixgroupcapabilities` (
  `id_cgrp` bigint(20) NOT NULL default '0',
  `name_ccpb` varchar(50) NOT NULL default '',
  `name_ccpt` varchar(255) NOT NULL default '',
  `value_cgcp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_cgrp`,`name_ccpb`,`name_ccpt`)
) TYPE=MyISAM;


#
# Table structure for table `copixusergroup`
#
DROP TABLE IF EXISTS `copixusergroup`;
CREATE TABLE `copixusergroup` (
  `login_cusr` varchar(50) NOT NULL default '',
  `id_cgrp` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`login_cusr`,`id_cgrp`)
) TYPE=MyISAM;


INSERT INTO `copixusergroup` (`login_cusr`, `id_cgrp`) VALUES ('admin', 1);
