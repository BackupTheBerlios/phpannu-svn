#
# Table structure for table `installedmodules`
#
CREATE TABLE copixmodule (
  name_cpm varchar(255) NOT NULL default '',
  PRIMARY KEY  (name_cpm)
);


CREATE TABLE copixconfig (
  id_ccfg varchar(255) NOT NULL default '',
  group_ccfg varchar(255) NOT NULL default '',
  value_ccfg varchar(255) default NULL NULL,
  PRIMARY KEY  (id_ccfg)
);
