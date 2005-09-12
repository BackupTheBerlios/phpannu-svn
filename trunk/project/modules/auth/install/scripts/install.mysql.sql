CREATE TABLE `copixuser` (
  `id_cusr` int(11) NOT NULL auto_increment,
  `login_cusr` varchar(50) NOT NULL default '',
  `password_cusr` varchar(50) NOT NULL default '',
  `enabled_cusr` tinyint(1) NOT NULL default '1',
  `lostpassword_cusr` varchar(10) NOT NULL default '',
  `email_cusr` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_cusr`)
) AUTO_INCREMENT=2 ;

INSERT INTO `copixuser` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 'f11a8698ae', 'no@mail.com');
