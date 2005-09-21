--**************************************************************************************************
--************************************** Trabajo SQL ***********************************************
--**************************************************************************************************

CREATE TABLE apl_objeto_trabsql 
(
	objeto_trabsql				int4				NOT NULL,
	secuencia					smallint		 	NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	listo							smallint			NULL,
	CONSTRAINT	"apl_obj_trabsql_pk" PRIMARY KEY ("objeto_trabsql","secuencia"),
	CONSTRAINT	"apl_obj_trabsql_fk_objeto" FOREIGN KEY ("objeto_trabsql") REFERENCES "apl_objeto" ("objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
-----------------------------------------------------------------------------------------------------

CREATE TABLE apl_objeto_trabsql_item 
(
	objeto_trabsql				int4				NOT NULL,
	secuencia					smallint			NOT NULL,
	item							smallint			NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	accion						text				NULL,
	prueba						varchar(255)	NULL,
	listo							smallint			NULL,
	CONSTRAINT	"apl_obj_trabsql_item_pk" PRIMARY KEY ("objeto_trabsql","secuencia","item"),
	CONSTRAINT	"apl_obj_trabsql_item_fk_obj" FOREIGN KEY ("objeto_trabsql","secuencia") REFERENCES "apl_objeto_trabsql" ("objeto_trabsql","secuencia") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--**************************************************************************************************
--************************************  Exportador SQL  ********************************************
--**************************************************************************************************

CREATE TABLE apl_objeto_expsql 
(
	objeto_expsql				int4				NOT NULL,
	orden							smallint			NOT NULL,
	tabla			 				varchar(80)		NOT NULL,
	sql_post_from 				varchar(255)	NULL,
	CONSTRAINT	"apl_obj_expsql_pk"	PRIMARY KEY ("objeto_expsql","orden"),
	CONSTRAINT	"apl_obj_expsql_fk_objeto" FOREIGN KEY ("objeto_expsql") REFERENCES "apl_objeto" ("objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--**************************************************************************************************
--************************************  Importador DBF  ********************************************
--**************************************************************************************************

CREATE TABLE apl_objeto_impdbf 
(
	objeto_impdbf				int4				NOT NULL,
	orden							smallint			NOT NULL,
	tabla			 				varchar(80)		NOT NULL,
	CONSTRAINT	"apl_obj_impdbf_pk"	PRIMARY KEY ("objeto_impdbf"),
	CONSTRAINT	"apl_obj_impdbf_fk_objeto" FOREIGN KEY ("objeto_impdbf") REFERENCES "apl_objeto" ("objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
