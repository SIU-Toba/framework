--**************************************************************************************************
--**************************************************************************************************
--******************************************     Lista    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_lista
-----------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_lista_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
-----------------------------------------------------------------------------------------------------
(
   objeto_lista_proyecto   char(15)		NOT NULL,
   objeto_lista            integer			   NOT NULL,
   titulo                  char(80)  ,
   subtitulo               char(80)  ,
   sql                     char      ,       -- SQL que arma el cuadro que permite elegir un registro a modificar
   col_ver                 char(255) ,
   col_titulos             char(255) ,
   col_formato             char(255) ,
   ancho                   smallint     ,
   ordenar                 smallint     ,
   exportar                smallint     ,
   vinculo_clave           char(80) ,       -- Columnas que poseen la clave, separadas por comas
   vinculo_indice				char(20)  ,       -- Titulo de la columna que tiene
   PRIMARY KEY (objeto_lista_proyecto,objeto_lista),
   FOREIGN KEY (objeto_lista_proyecto,objeto_lista) REFERENCES   apex_objeto (proyecto,objeto)   
);
--###################################################################################################