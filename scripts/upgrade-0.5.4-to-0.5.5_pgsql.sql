-- From PostgreSQL documentation: "In the current implementation of ADD COLUMN, default and NOT NULL clauses for the new column are not supported. The new column always comes into being with all values null."
-- How to achive "AFTER quota"?
ALTER TABLE domain ADD domainquota int;
ALTER TABLE domain SET DEFAULT '0';
UPDATE domain SET domainquota = DEFAULT;
ALTER TABLE domain ALTER COLUMN domainquota SET NOT NULL;

ALTER TABLE domain ADD folders varchar(255);
ALTER TABLE domain SET DEFAULT '';
UPDATE domain SET folders = DEFAULT;
ALTER TABLE domain ALTER COLUMN folders SET NOT NULL;

ALTER TABLE accountuser ADD imap int;
ALTER TABLE accountuser ADD pop int;
ALTER TABLE accountuser ADD sieve int;
ALTER TABLE accountuser ADD smtpauth int;

CREATE TABLE settings (
  username varchar(50) PRIMARY KEY,
  style varchar(50) NOT NULL default 'default',
  maxdisplay int NOT NULL default 15,
  warnlevel int NOT NULL default 90
);

INSERT INTO settings (username) SELECT username FROM adminuser;
UPDATE settings SET style = DEFAULT, maxdisplay = DEFAULT, warnlevel = DEFAULT;
