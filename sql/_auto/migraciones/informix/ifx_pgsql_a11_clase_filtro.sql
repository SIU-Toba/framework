--**************************************************************************************************
--**************************************************************************************************
--*****************************************  Filtro  ***********************************************
--**************************************************************************************************
--**************************************************************************************************


CREATE TABLE apex_objeto_filtro 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_filtro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_filtro_proyecto  char(15)    NOT NULL,
   objeto_filtro           integer           NOT NULL,
   dimension_proyecto      char(15)    NOT NULL,
   dimension               char(30)    NOT NULL,
   tabla                   char(40)  ,
   columna                 char(80)  ,
   orden                   float          NOT NULL,
   requerido               smallint     ,
   no_interactivo          smallint     ,
   PRIMARY KEY (objeto_filtro_proyecto,objeto_filtro,dimension_proyecto,dimension),
   FOREIGN KEY (objeto_filtro_proyecto,objeto_filtro) REFERENCES apex_objeto (proyecto,objeto)   ,
   FOREIGN KEY (dimension_proyecto,dimension) REFERENCES apex_dimension (proyecto,dimension)   
);
--###################################################################################################
