-- From PostgreSQL documentation: "In the current implementation of ADD COLUMN, default and NOT NULL clauses for the new column are not supported. The new column always comes into being with all values null."
-- How to achive "AFTER quota"?
ALTER TABLE domain ADD domainquota int;
ALTER TABLE domain SET DEFAULT '0';
ALTER TABLE accountuser ADD imap int;
ALTER TABLE accountuser ADD pop int;
ALTER TABLE accountuser ADD sieve int;
UPDATE domain SET domainquota = DEFAULT;
ALTER TABLE domain ALTER COLUMN domainquota SET NOT NULL;

CREATE TABLE settings (
  username varchar(50) PRIMARY KEY,
  style varchar(50) NOT NULL default 'default',
  maxdisplay int NOT NULL default 15,
  warnlevel int NOT NULL default 90
);

INSERT INTO settings (username) SELECT username FROM adminuser;
INSERT INTO settings (username, style , maxdisplay , warnlevel ) VALUES ( 'admin', 'default', '15', '90');
