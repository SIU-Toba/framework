
CREATE TABLE apex_permiso
(
	permiso					integer auto_increment  NOT NULL, 
	proyecto							varchar(15)		NOT NULL,
	nombre								varchar(100)	NOT NULL,
	descripcion							varchar(255)	NULL,
	mensaje_particular					text			NULL,
	CONSTRAINT	 apex_per_pk  			PRIMARY	KEY ( permiso ,  proyecto ),
	CONSTRAINT	 apex_per_uq_nombre  	UNIQUE	( proyecto , nombre )
) ENGINE=InnoDB;
