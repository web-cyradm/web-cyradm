ALTER TABLE `domain` ADD `domainquota` int(10) DEFAULT '0' NOT NULL AFTER `quota`;

ALTER TABLE `accountuser` ADD `imap` int(10) DEFAULT '0' NOT NULL AFTER `domain_name`;
ALTER TABLE `accountuser` ADD `pop` int(10) DEFAULT '0' NOT NULL AFTER `imap`;
ALTER TABLE `accountuser` ADD `sieve` int(10) DEFAULT '0' NOT NULL AFTER `pop`;
ALTER TABLE `accountuser` ADD `smtpauth` int(10) DEFAULT '0' NOT NULL AFTER `sieve`;
ALTER TABLE `accountuser` ADD `status` int(10) DEFAULT '0' NOT NULL AFTER `smtpauth`;

CREATE TABLE `settings` (
  `username` varchar(50) binary NOT NULL default '',
  `style` varchar(50) NOT NULL default 'default',
  `maxdisplay` int(4) NOT NULL default '15',
  `warnlevel` int(3) NOT NULL default '90',
  PRIMARY KEY  (username)
) TYPE=MyISAM;

INSERT INTO `settings` (username) SELECT `username` FROM adminuser;
