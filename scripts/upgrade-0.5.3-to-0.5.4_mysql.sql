ALTER TABLE `mail`.`domain` ADD `freenames` ENUM( 'YES', 'NO' ) DEFAULT 'NO' NOT NULL;
ALTER TABLE `mail`.`domain` ADD `freeaddress` ENUM( 'YES', 'NO' ) DEFAULT 'NO' NOT NULL;

CREATE TABLE log (
  id int(11) NOT NULL auto_increment,
  msg text NOT NULL,
  user varchar(255) NOT NULL default '',
  host varchar(255) NOT NULL default '',
  time datetime NOT NULL default '2000-00-00 00:00:00',
  pid varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

