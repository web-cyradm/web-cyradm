-- execute as postgres user in de mail database

ALTER TABLE domain ADD column freenames DEFAULT 'NO' NOT NULL;
ALTER TABLE domain ADD freeaddress DEFAULT 'NO' NOT NULL;

CREATE TABLE log (
  "id" serial PRIMARY KEY,
  "msg" text NOT NULL,
  "user" varchar(255) NOT NULL default '',
  "host" varchar(255) NOT NULL default '',
  "time" datetime NOT NULL default '2000-01-01 00:00:00',
  "pid" varchar(255) NOT NULL default ''
);


GRANT SELECT,INSERT,UPDATE,DELETE ON log TO mail;