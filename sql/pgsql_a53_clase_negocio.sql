--**************************************************************************************************
--**************************************************************************************************
--****************************************     NEGOCIO    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_negocio
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_negocio_proyecto
--: dump_clave_componente: objeto_negocio
--: dump_order_by: objeto_negocio
--: dump_where: ( objeto_negocio_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_negocio_proyecto  	varchar(15)		NOT NULL,
   objeto_negocio           	int4			NOT NULL,
   descripcion             		varchar(255)    NOT NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar
   CONSTRAINT  "apex_objeto_negocio_pk" PRIMARY KEY ("objeto_negocio_proyecto","objeto_negocio"),
   CONSTRAINT  "apex_objeto_negocio_fk_objeto"  FOREIGN KEY ("objeto_negocio_proyecto","objeto_negocio") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_objeto_negocio_regla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_negocio_proyecto
--: dump_clave_componente: objeto_negocio
--: dump_order_by: objeto_negocio, nombre
--: dump_where: ( objeto_negocio_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   	objeto_negocio_proyecto     varchar(15)   	NOT NULL,
   	objeto_negocio              int4          	NOT NULL,
   	nombre			       		varchar(80)    	NOT NULL,
   	descripcion             	varchar(255)    NOT NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar
	activada					smallint		NULL,
	mensaje_a					varchar(255)    NULL, 
	mensaje_b					varchar(255)    NULL, 
   	CONSTRAINT  "apex_obj_negocio_r_pk" PRIMARY KEY ("objeto_negocio_proyecto","objeto_negocio","nombre"),
   	CONSTRAINT  "apex_obj_negocio_r_fk_p" FOREIGN KEY ("objeto_negocio_proyecto","objeto_negocio") REFERENCES "apex_objeto_negocio" ("objeto_negocio_proyecto","objeto_negocio") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

