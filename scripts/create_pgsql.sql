CREATE TABLE accountuser (
  username varchar(30) NOT NULL UNIQUE default '',
  password varchar(30) NOT NULL default '',
  prefix varchar(30) NOT NULL default '',
  domain_name varchar(255) NOT NULL default ''
);

CREATE TABLE adminuser (
  username varchar(30) NOT NULL default '',
  password varchar(30) NOT NULL default '',
  type int NOT NULL default '0',
  SID varchar(255) NOT NULL default '',
  home varchar(255) NOT NULL default '',
  PRIMARY KEY (username)
);

CREATE TABLE alias (
  alias varchar(255) NOT NULL default '',
  dest text,
  username varchar(30) NOT NULL default '',
  status int NOT NULL default '1',
  PRIMARY KEY (alias)
);

CREATE TABLE domain (
  domain_name varchar(255) NOT NULL default '',
  prefix varchar(30) NOT NULL UNIQUE default '',
  maxaccounts int NOT NULL default '20',
  quota int NOT NULL default '20000',
  PRIMARY KEY (domain_name)
);

CREATE TABLE domainadmin (
  domain_name varchar(255) NOT NULL default '',
  adminuser varchar(255) NOT NULL default ''
);

CREATE TABLE search (
  search_id varchar(255) NOT NULL default '',
  search_sql text NOT NULL,
  perpage int NOT NULL default '0',
  timestamp timestamp(13) NOT NULL,
  PRIMARY KEY (search_id)
);
CREATE INDEX search_search_id_key ON search (search_id);

CREATE TABLE virtual (
  alias varchar(255) NOT NULL UNIQUE default '',
  dest text,
  username varchar(30) NOT NULL default '',
  status int NOT NULL default '1',
  PRIMARY KEY (alias)
);

GRANT SELECT,INSERT,UPDATE,DELETE ON accountuser TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON adminuser TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON alias TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON domain TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON domainadmin TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON search TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON virtual TO mail;
