ALTER TABLE `domain` ADD `freenames` ENUM( 'YES', 'NO' ) DEFAULT 'NO' NOT NULL;
ALTER TABLE `domain` ADD `freeaddress` ENUM( 'YES', 'NO' ) DEFAULT 'NO' NOT NULL;

ALTER TABLE `virtual` DROP PRIMARY KEY;
ALTER TABLE `virtual` DROP INDEX `alias`;
ALTER TABLE 'virtual' ADD INDEX (alias);

CREATE TABLE log (
  id int(11) NOT NULL auto_increment,
  msg text NOT NULL,
  user varchar(255) NOT NULL default '',
  host varchar(255) NOT NULL default '',
  time datetime NOT NULL default '2000-00-00 00:00:00',
  pid varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

