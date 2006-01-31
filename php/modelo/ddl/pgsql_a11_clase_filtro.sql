--**************************************************************************************************
--**************************************************************************************************
--*****************************************  Filtro  ***********************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_filtro 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_filtro_proyecto
--: dump_clave_componente: objeto_filtro
--: dump_order_by: objeto_filtro
--: dump_where: ( objeto_filtro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_filtro_proyecto  varchar(15)    NOT NULL,
   objeto_filtro           int4           NOT NULL,
   dimension_proyecto      varchar(15)    NOT NULL,
   dimension               varchar(30)    NOT NULL,
	etiqueta						varchar(40)		NULL,
   tabla                   varchar(300)   NULL,  -- Puede ser una subconsulta.
   columna                 varchar(255)   NULL,
   orden                   float          NOT NULL,
   requerido               smallint       NULL,
   no_interactivo          smallint       NULL,
	predeterminado				varchar(100)	NULL,
   CONSTRAINT  "apex_obj_fil_pk" PRIMARY KEY ("objeto_filtro_proyecto","objeto_filtro","dimension_proyecto","dimension"),
   CONSTRAINT  "apex_obj_fil_fk_objeto" FOREIGN KEY ("objeto_filtro_proyecto","objeto_filtro") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_obj_fil_fk_dimension" FOREIGN KEY ("dimension_proyecto","dimension") REFERENCES "apex_dimension" ("proyecto","dimension") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
