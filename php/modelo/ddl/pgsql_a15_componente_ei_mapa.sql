--**************************************************************************************************
--**************************************************************************************************
--******************************************   mapa   ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_mapa_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: mapa_tipo
--: zona: general
--: desc: Tipo	de	grafico
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	mapa_tipo					int8			NOT NULL,
	descripcion					varchar(40)			NULL,	--NOT
	CONSTRAINT	"apex_mapa_tipo_pk" PRIMARY KEY ("mapa_tipo") 
);
--###################################################################################################

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
   mapa_tipo           	   	int8  	NULL, -- NEATO, DOT, ETC
   CONSTRAINT  "apex_objeto_mapa_pk" PRIMARY KEY ("objeto_mapa_proyecto","objeto_mapa"),
   CONSTRAINT  "apex_objeto_mapa_fk_objeto"  FOREIGN KEY ("objeto_mapa_proyecto","objeto_mapa") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_objeto_mapa_fk_tipo"  FOREIGN KEY ("mapa_tipo") REFERENCES "apex_mapa_tipo" ("mapa_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
  );

--###################################################################################################

CREATE SEQUENCE apex_ei_mapa_layer_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_ei_mapa_layer
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_ei_mapa_proyecto
--: dump_clave_componente: objeto_ei_mapa
--: dump_order_by: objeto_ei_mapa, objeto_ei_mapa_layer
--: dump_where: ( objeto_ei_mapa_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_ei_mapa_layer				int8			DEFAULT nextval('"apex_ei_mapa_layer_seq"'::text) NOT NULL, 
	objeto_ei_mapa_proyecto 		   	varchar(15)		NOT NULL,
	objeto_ei_mapa            		 	int8			NOT NULL,
	identificador      					varchar(30)    	NOT NULL,
	CONSTRAINT  "apex_ei_mapa_layer_pk" PRIMARY KEY ("objeto_ei_mapa_layer"),
	CONSTRAINT  "apex_ei_mapa_layer_fk_padre" FOREIGN KEY ("objeto_ei_mapa", "objeto_ei_mapa_proyecto") REFERENCES "apex_objeto_mapa" ("objeto_mapa", "objeto_mapa_proyecto") ON DELETE CASCADE 
);
--###################################################################################################