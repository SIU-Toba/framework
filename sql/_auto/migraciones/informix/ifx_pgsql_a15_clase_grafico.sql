--**************************************************************************************************
--**************************************************************************************************
--******************************************  GRAFICO  ******************************************
--**************************************************************************************************
--**************************************************************************************************


CREATE TABLE apex_objeto_grafico
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_grafico_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_grafico_proyecto   		char(15)		NOT NULL,
   objeto_grafico                integer			   NOT NULL,
   grafico                       char(30)    NOT NULL,
	sql									char		,
	inicializacion						char		,
   PRIMARY KEY (objeto_grafico_proyecto,objeto_grafico),
   FOREIGN KEY (grafico) REFERENCES apex_grafico (grafico)   ,
   FOREIGN KEY (objeto_grafico_proyecto,objeto_grafico) REFERENCES apex_objeto (proyecto,objeto)   
);
--###################################################################################################