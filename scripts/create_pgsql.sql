DROP INDEX virtual_username_ndx;
DROP INDEX virtual_unique_ndx;
DROP INDEX domain_unique_ndx;
DROP INDEX accountuser_unique_ndx;
DROP TABLE search;
DROP TABLE virtual;
DROP TABLE domainadmin;
DROP TABLE "domain";
DROP TABLE "log";
DROP TABLE alias;
DROP TABLE adminuser;
DROP TABLE accountuser;

CREATE TABLE accountuser (
  username varchar(255) NOT NULL default '',
  password varchar(50) NOT NULL default '',
  prefix varchar(50) NOT NULL default '',
  domain_name varchar(255) NOT NULL default ''
);

CREATE UNIQUE INDEX accountuser_ndx ON accountuser(username);

CREATE TABLE adminuser (
  username varchar(50) PRIMARY KEY,
  password varchar(50) NOT NULL default '',
  type int NOT NULL default 0,
  SID varchar(255) NOT NULL default '',
  home varchar(255) NOT NULL default ''
);

CREATE TABLE alias (
  alias varchar(255) PRIMARY KEY,
  dest varchar(255),
  username varchar(255) NOT NULL default '',
  status int NOT NULL default 1
);

CREATE TABLE domain (
  domain_name varchar(255) PRIMARY KEY ,
  prefix varchar(255) UNIQUE NOT NULL default '',
  maxaccounts int NOT NULL default 20,
  quota int NOT NULL default '20000',
  freenames varchar(3) CHECK (freenames='YES' OR freenames='NO' ) DEFAULT 'NO' NOT NULL,
  freeaddress varchar(3) CHECK (freeaddress='YES' OR freeaddress='NO' ) DEFAULT 'NO' NOT NULL,
  transport varchar(255) NOT NULL default 'cyrus'
);


CREATE TABLE domainadmin (
  domain_name varchar(255) NOT NULL default '',
  adminuser varchar(255) NOT NULL default ''
);

CREATE TABLE search (
  search_id varchar(255) PRIMARY KEY,
  search_sql text NOT NULL,
  perpage int NOT NULL default '0',
  timestamp timestamp NOT NULL
);

CREATE TABLE virtual (
  alias varchar(255) PRIMARY KEY ,
  dest varchar(255),
  username varchar(255) NOT NULL default '',
  status int NOT NULL default '1'
);
CREATE UNIQUE INDEX virtual_unique_ndx ON virtual(alias,dest);

CREATE TABLE log (
  id serial PRIMARY KEY,
  msg text NOT NULL,
  "user" varchar(255) NOT NULL default '',
  host varchar(255) NOT NULL default '',
  time timestamp  NOT NULL,
  pid varchar(255) NOT NULL default ''
);

GRANT SELECT,INSERT,UPDATE,DELETE ON accountuser TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON adminuser TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON alias TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON domain TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON domainadmin TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON search TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON virtual TO mail;
GRANT SELECT,INSERT,UPDATE,DELETE ON log TO mail;
