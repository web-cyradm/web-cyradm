connect mysql;
INSERT INTO user (Host, User, Password, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Reload_priv, Shutdown_priv, Process_priv, File_priv, Grant_priv, References_priv, Index_priv, Alter_priv) VALUES ('localhost', 'mail', PASSWORD('secret'), 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
   
INSERT INTO db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Grant_priv, References_priv, Index_priv, Alter_priv) VALUES ('localhost', 'mail', 'mail', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y ');
flush privileges;

create database mail;


use mail;

INSERT INTO adminuser (username, password) VALUES ('admin', 'test');
INSERT INTO domainadmin (domain_name,adminuser) VALUES ('*','admin');
INSERT INTO accountuser (username, password) VALUES ('cyrus', ENCRYPT('secret'));
