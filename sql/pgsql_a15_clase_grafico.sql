--**************************************************************************************************
--**************************************************************************************************
--******************************************  GRAFICO  ******************************************
--**************************************************************************************************
--**************************************************************************************************


CREATE TABLE apex_objeto_grafico
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_grafico_proyecto
--: dump_clave_componente: objeto_grafico
--: dump_order_by: objeto_grafico
--: dump_where: ( objeto_grafico_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_grafico_proyecto   		varchar(15)		NOT NULL,
   objeto_grafico                int4			   NOT NULL,
   grafico                       varchar(30)    NOT NULL,
	sql									varchar			NULL,
	inicializacion						varchar			NULL,
   CONSTRAINT  "apex_obj_grafico_pk" PRIMARY KEY ("objeto_grafico_proyecto","objeto_grafico"),
   CONSTRAINT  "apex_obj_grafico_grafico" FOREIGN KEY ("grafico") REFERENCES "apex_grafico" ("grafico") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_obj_grafico_fk_objeto" FOREIGN KEY ("objeto_grafico_proyecto","objeto_grafico") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################