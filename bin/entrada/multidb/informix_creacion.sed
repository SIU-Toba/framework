###################################################################################
###########   Migracion scripts de CREACION de POSTGRESQL a INFORMIX   ############
###################################################################################

s/"//g

s/^--.*CONSTRAINT.*$//

s/NOT * *NULL/NOT NULL/
s/NOT +NULL/NOT NULL/
s/  NULL//
s/	NULL//

s/.*UNIQUE/   UNIQUE/
s/.*PRIMARY KEY/   PRIMARY KEY/
s/.*FOREIGN KEY/   FOREIGN KEY/
s/ON DELETE NO ACTION//
s/ON DELETE CASCADE//
s/ON UPDATE NO ACTION//
s/ON UPDATE CASCADE//
s/NOT DEFERRABLE INITIALLY IMMEDIATE//

s/int4.*DEFAULT nextval.*$/serial,/
s/CREATE.*SEQUENCE.*$//

s/varchar/char/
s/int4/integer/

s/timestamp(0).*DEFAULT.*current_timestamp.*$/datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,/
s/timestamp(0).*$/datetime YEAR to SECOND,/

s/time(0) *without *time *zone/datetime HOUR to MINUTE/
s/HOUR to MINUTE.*NULL/HOUR to MINUTE DEFAULT NULL/

