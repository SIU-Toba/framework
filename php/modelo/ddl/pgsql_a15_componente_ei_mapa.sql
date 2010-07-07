--**************************************************************************************************
--**************************************************************************************************
--******************************************   mapa   ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_mapa
-----------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_mapa_proyecto
--: dump_clave_componente: objeto_mapa
--: dump_order_by: objeto_mapa
--: dump_where: ( objeto_mapa_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
-----------------------------------------------------------------------------------------------------
(
   objeto_mapa_proyecto   	varchar(15)		NOT NULL,
   objeto_mapa            	int8			NOT NULL,
   mapfile_path				varchar(200)	NULL,
   CONSTRAINT  "apex_objeto_mapa_pk" PRIMARY KEY ("objeto_mapa_proyecto","objeto_mapa"),
   CONSTRAINT  "apex_objeto_mapa_fk_objeto"  FOREIGN KEY ("objeto_mapa_proyecto","objeto_mapa") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
  );

