# phpMyAdmin MySQL-Dump
# version 2.2.6
# http://phpwizard.net/phpMyAdmin/
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Nov 04, 2002 at 01:44 AM
# Server version: 3.23.52
# PHP Version: 4.2.3
# Database : `mail`
# --------------------------------------------------------

#
# Table structure for table `accountuser`
#

CREATE TABLE accountuser (
  username varchar(50) NOT NULL default '',
  password varchar(30) binary NOT NULL default '',
  prefix varchar(50) NOT NULL default '',
  domain_name varchar(255) NOT NULL default '',
  UNIQUE KEY username (username)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `adminuser`
#

CREATE TABLE adminuser (
  username varchar(50) NOT NULL default '',
  password varchar(50) binary NOT NULL default '',
  type int(11) NOT NULL default '0',
  SID varchar(255) NOT NULL default '',
  home varchar(255) NOT NULL default '',
  PRIMARY KEY  (username)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `alias`
#

CREATE TABLE alias (
  alias varchar(255) NOT NULL default '',
  dest longtext,
  username varchar(50) NOT NULL default '',
  status int(11) NOT NULL default '1',
  PRIMARY KEY  (alias)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `domain`
#

CREATE TABLE domain (
  domain_name varchar(255) NOT NULL default '',
  prefix varchar(50) NOT NULL default '',
  maxaccounts int(11) NOT NULL default '20',
  quota int(10) NOT NULL default '20000',
  transport varchar(255) NOT NULL default 'cyrus',
  PRIMARY KEY  (domain_name),
  UNIQUE KEY prefix (prefix)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `domainadmin`
#

CREATE TABLE domainadmin (
  domain_name varchar(255) NOT NULL default '',
  adminuser varchar(255) NOT NULL default ''
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `search`
#

CREATE TABLE search (
  search_id varchar(255) NOT NULL default '',
  search_sql text NOT NULL,
  perpage int(11) NOT NULL default '0',
  timestamp timestamp(14) NOT NULL,
  PRIMARY KEY  (search_id),
  KEY search_id (search_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `virtual`
#

CREATE TABLE virtual (
  alias varchar(255) NOT NULL default '',
  dest longtext,
  username varchar(50) NOT NULL default '',
  status int(11) NOT NULL default '1',
  PRIMARY KEY  (alias),
  UNIQUE KEY alias (alias)
) TYPE=MyISAM;

