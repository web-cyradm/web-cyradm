-- From PostgreSQL documentation: "In the current implementation of ADD COLUMN, default and NOT NULL clauses for the new column are not supported. The new column always comes into being with all values null."
-- How to achive "AFTER quota"?
ALTER TABLE domain ADD domainquota int;
ALTER TABLE domain SET DEFAULT '0';
UPDATE domain SET domainquota = DEFAULT;
ALTER TABLE domain ALTER COLUMN domainquota SET NOT NULL;
