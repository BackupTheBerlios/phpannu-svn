#
# Table structure for table copixcapability
#
CREATE TABLE copixcapability (
  name_ccpb varchar(50) NOT NULL default '',
  description_ccpb varchar(255) NOT NULL default '',
  name_ccpt varchar(255) NOT NULL default '',
  values_ccpb varchar(30) NOT NULL default '',
  PRIMARY KEY  (name_ccpb)
);

#
# Dumping data for table copixcapability
#

INSERT INTO copixcapability (name_ccpb, description_ccpb, name_ccpt, values_ccpb) VALUES ('copixheadings', 'Rubriques', 'modules|copixheadings', '0;10;30');
INSERT INTO copixcapability (name_ccpb, description_ccpb, name_ccpt, values_ccpb) VALUES ('siteAdmin', 'Administration', 'site', '0;70');

# --------------------------------------------------------

#
# Table structure for table copixcapabilitypath
#

CREATE TABLE copixcapabilitypath (
  name_ccpt varchar(255) NOT NULL default '',
  description_ccpt varchar(255) NOT NULL default '',
  PRIMARY KEY  (name_ccpt)
);

#
# Dumping data for table copixcapabilitypath
#


INSERT INTO copixcapabilitypath (name_ccpt, description_ccpt) VALUES ('modules|copixheadings', 'Rubrique principale');
INSERT INTO copixcapabilitypath (name_ccpt, description_ccpt) VALUES ('site', 'Site');

# --------------------------------------------------------

#
# Table structure for table copixgroupcapabilities
#

CREATE TABLE copixgroupcapabilities (
  id_cgrp bigint NOT NULL default '0',
  name_ccpb varchar(50) NOT NULL default '',
  name_ccpt varchar(255) NOT NULL default '',
  value_cgcp int NOT NULL default '0',
  PRIMARY KEY  (id_cgrp,name_ccpb,name_ccpt)
);

#
# Dumping data for table copixgroupcapabilities
#


INSERT INTO copixgroupcapabilities (id_cgrp, name_ccpb, name_ccpt, value_cgcp) VALUES (1, 'siteAdmin', 'site', 70);
INSERT INTO copixgroupcapabilities (id_cgrp, name_ccpb, name_ccpt, value_cgcp) VALUES (1, 'copixheadings', 'modules|copixheadings', 30);


