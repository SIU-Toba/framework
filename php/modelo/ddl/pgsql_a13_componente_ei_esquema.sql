--**************************************************************************************************
--**************************************************************************************************
--******************************************   esquema   ******************************************
--**************************************************************************************************
--**************************************************************************************************
--Generacion de esquemas via GRAPHVIZ

CREATE TABLE apex_objeto_esquema
-----------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_esquema_proyecto
--: dump_clave_componente: objeto_esquema
--: dump_order_by: objeto_esquema
--: dump_where: ( objeto_esquema_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
-----------------------------------------------------------------------------------------------------
(
   objeto_esquema_proyecto   	varchar(15)		NOT NULL,
   objeto_esquema            	int8			NOT NULL,
   parser	            	   	varchar(30)  	NULL, -- NEATO, DOT, ETC
   descripcion            	   	varchar(80)  	NULL,
   dot		               		TEXT			NULL, --Descripcion del grafico en sintaxis DOT
   debug						smallint		NULL,
   formato						varchar(15)		NULL,
   modelo_ejecucion				varchar(15)		NULL,
   modelo_ejecucion_cache		smallint		NULL, -- Usar el cache??
   tipo_incrustacion			varchar(15)		NULL, -- IMG o IFRAME
   ancho						varchar(10)		NULL,
   alto							varchar(10)		NULL,
   dirigido						smallint		DEFAULT 1 NULL,
   -- Para el esquema_db
   sql							TEXT			NULL,
   CONSTRAINT  "apex_objeto_esquema_pk" PRIMARY KEY ("objeto_esquema_proyecto","objeto_esquema"),
   CONSTRAINT  "apex_objeto_esquema_fk_objeto"  FOREIGN KEY ("objeto_esquema_proyecto","objeto_esquema") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################