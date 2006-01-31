--**************************************************************************************************
--**************************************************************************************************
--********************************   Entorno de Trabajo   ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_et_item
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: (item_proyecto ='%%')
--: dump_order_by: usuario, item
--: zona: entorno_trabajo
--: desc: Portafolios de items
--: version: 1.0
--: instancia:	1
---------------------------------------------------------------------------------------------------
(
	item_proyecto			varchar(15)		NOT NULL,
	item					varchar(60)   	NOT NULL,
	usuario					varchar(20)	NOT NULL,
	creacion				timestamp(0) without time zone DEFAULT current_timestamp NOT NULL,
  	CONSTRAINT  "apex_et_item_usu_pk"   PRIMARY KEY ("item","item_proyecto","usuario"),
  	CONSTRAINT  "apex_et_item_usu_item_fk" FOREIGN KEY ("item","item_proyecto") REFERENCES "apex_item" ("item","proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_et_item_usu_usu_fk" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_et_objeto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: (objeto_proyecto ='%%')
--: dump_order_by: usuario, objeto
--: zona: entorno_trabajo
--: desc: Portafolios de objetos
--: version: 1.0
--: instancia:	1
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto	varchar(15)	NOT NULL,
	objeto			int4	   	NOT NULL,
	usuario			varchar(20)	NOT NULL,
	creacion		timestamp(0) without time zone DEFAULT current_timestamp NOT NULL,
  	CONSTRAINT  "apex_et_objeto_usu_pk"   PRIMARY KEY ("objeto_proyecto","objeto","usuario"),
  	CONSTRAINT  "apex_et_objeto_usu_objeto_fk" FOREIGN KEY ("objeto","objeto_proyecto") REFERENCES "apex_objeto" ("objeto","proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_et_objeto_usu_usu_fk" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_et_preferencias
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: (usuario_proyecto ='%%')
--: dump_order_by: usuario
--: zona: entorno_trabajo
--: desc: Portafolios de Item
--: version: 1.0
--: instancia:	1
---------------------------------------------------------------------------------------------------
(
	usuario_proyecto		varchar(15)		NOT NULL,
	usuario					varchar(20)		NOT NULL,
	listado_obj_pref		varchar(20) 	NULL,
	listado_item_pref		varchar(20)		NULL,
	item_proyecto			varchar(15)		NOT NULL, -- Item inicial
	item					varchar(60)   	NOT NULL,
	CONSTRAINT  		"apex_et_item_prefs_pk"   PRIMARY KEY ("usuario_proyecto","usuario"),
  	CONSTRAINT  		"apex_et_item_usu_item_fk" FOREIGN KEY ("item","item_proyecto") REFERENCES "apex_item" ("item","proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  		"apex_et_item_prefs_usuario_fk" FOREIGN KEY ("usuario_proyecto","usuario") REFERENCES "apex_usuario_proyecto" ("proyecto","usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
