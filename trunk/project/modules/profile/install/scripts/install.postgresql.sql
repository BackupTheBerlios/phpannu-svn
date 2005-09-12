
#
# Table structure for table `copixgroup`
#
DROP TABLE copixgroup;
CREATE TABLE copixgroup (
id_cgrp             bigint NOT NULL default 0,
name_cgrp           varchar(50) NOT NULL default '',
description_cgrp    varchar(255) NULL default '',
all_cgrp            char(1) NOT NULL default '0',
known_cgrp          char(1) NOT NULL default '0',
isadmin_cgrp        char(1) NOT NULL default '0',
PRIMARY KEY (id_cgrp)
);

INSERT INTO copixgroup (id_cgrp, name_cgrp, description_cgrp, all_cgrp, known_cgrp, isadmin_cgrp) VALUES (1, 'admin', 'Siteadmin', 0, 0, 1);


#
# Table structure for table `copixcapability`
#

DROP TABLE  copixcapability;
CREATE TABLE copixcapability (
  name_ccpb varchar(50) NOT NULL default '',
  description_ccpb varchar(255) NOT NULL default '',
  name_ccpt varchar(255) NOT NULL default '',
  values_ccpb varchar(30) NOT NULL default '',
  PRIMARY KEY  (name_ccpb)
) ;

INSERT INTO copixcapability (name_ccpb, description_ccpb, name_ccpt, values_ccpb) VALUES ('copixheadings', 'Rubriques', 'modules|copixheadings', '0;10;30');
INSERT INTO copixcapability (name_ccpb, description_ccpb, name_ccpt, values_ccpb) VALUES ('siteAdmin', 'Administration', 'site', '0;70');

#
# Table structure for table `copixcapabilitypath`
#

DROP TABLE  copixcapabilitypath;
CREATE TABLE copixcapabilitypath (
  name_ccpt varchar(255) NOT NULL default '',
  description_ccpt varchar(255) NOT NULL default '',
  PRIMARY KEY  (name_ccpt)
) ;

INSERT INTO copixcapabilitypath (name_ccpt, description_ccpt) VALUES ('modules|copixheadings', 'Rubrique principale');
INSERT INTO copixcapabilitypath (name_ccpt, description_ccpt) VALUES ('site', 'Site');

#
# Table structure for table `copixgroupcapabilities`
#

DROP TABLE  copixgroupcapabilities;
CREATE TABLE copixgroupcapabilities (
  id_cgrp bigint NOT NULL default '0',
  name_ccpb varchar(50) NOT NULL default '',
  name_ccpt varchar(255) NOT NULL default '',
  value_cgcp integer NOT NULL default '0',
  PRIMARY KEY  (id_cgrp,name_ccpb,name_ccpt)
) ;

INSERT INTO copixgroupcapabilities (id_cgrp, name_ccpb, name_ccpt, value_cgcp) VALUES (1, 'siteAdmin', 'site', 70);
INSERT INTO copixgroupcapabilities (id_cgrp, name_ccpb, name_ccpt, value_cgcp) VALUES (1, 'copixheadings', 'modules|copixheadings', 30);

#
# Table structure for table `copixusergroup`
#
DROP TABLE copixusergroup;
CREATE TABLE copixusergroup (
login_cusr      varchar(50) NOT NULL default '',
id_cgrp         bigint NOT NULL default 0,
PRIMARY KEY (login_cusr, id_cgrp)
);
INSERT INTO copixusergroup (login_cusr, id_cgrp) VALUES ('admin',1);


