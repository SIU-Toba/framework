--**************************************************************************************************
--**************************************************************************************************
--*****************************************  Filtro  ***********************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_multicheq
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_multicheq
--: dump_where: ( objeto_multicheq_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_multicheq_proyecto  	varchar(15)    NOT NULL,
   objeto_multicheq           	int4           NOT NULL,
	sql							varchar			NOT NULL,
	claves						varchar(100)	NULL,
	descripcion					varchar(255)	NULL,
	chequeado					varchar(100)	NULL,
	forzar_chequeo				smallint		NULL,
   CONSTRAINT  "apex_obj_mul_pk" PRIMARY KEY ("objeto_multicheq_proyecto","objeto_multicheq"),
   CONSTRAINT  "apex_obj_mul_fk_objeto" FOREIGN KEY ("objeto_multicheq_proyecto","objeto_multicheq") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
