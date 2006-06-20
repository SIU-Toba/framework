--**************************************************************************************************
--**************************************************************************************************
--******************************************     Lista    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_lista
-----------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_lista_proyecto
--: dump_clave_componente: objeto_lista 
--: dump_order_by: objeto_lista
--: dump_where: ( objeto_lista_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
-----------------------------------------------------------------------------------------------------
(
   objeto_lista_proyecto   varchar(15)		NOT NULL,
   objeto_lista            int4			   NOT NULL,
   titulo                  varchar(80)    NULL,
   subtitulo               varchar(80)    NULL,
   sql                     varchar        NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar
   col_ver                 varchar(255)   NULL,
   col_titulos             varchar(255)   NULL,
   col_formato             varchar(255)   NULL,
   ancho                   smallint       NULL,
   ordenar                 smallint       NULL,
   exportar                smallint       NULL,
   vinculo_clave           varchar(80)   NULL,       -- Columnas que poseen la clave, separadas por comas
   vinculo_indice				varchar(20)    NULL,       -- Titulo de la columna que tiene
   CONSTRAINT  "apex_objeto_lista_pk" PRIMARY KEY ("objeto_lista_proyecto","objeto_lista"),
   CONSTRAINT  "apex_objeto_lista_fk_objeto"  FOREIGN KEY ("objeto_lista_proyecto","objeto_lista") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################