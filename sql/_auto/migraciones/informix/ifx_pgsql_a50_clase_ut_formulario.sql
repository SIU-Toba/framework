--**************************************************************************************************
--**************************************************************************************************
--**************************************  UT - Formulario  *****************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_ut_formulario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_ut_formulario_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_ut_formulario_proyecto       char(15)		NOT NULL,
   objeto_ut_formulario                integer    			NOT NULL,
   tabla                      			char(40)    NOT NULL,
   titulo                     			char(80)  ,       -- Titulo de la interface
   ev_mod_eliminar            			smallint     ,       -- Pantalla de modificacion: Se permite eliminar registros ?
   ev_mod_clave      	      			smallint     ,       -- Pantalla de modificacion: Hay un boton para volver al estado inicial?
   ev_mod_limpiar	            			smallint     ,       -- Pantalla de modificacion: Se permite limpiar el formulario?
	auto_reset									smallint     ,       -- Se resetea el formulario despues de transaccionar
   PRIMARY KEY (objeto_ut_formulario_proyecto,objeto_ut_formulario),
   FOREIGN KEY (objeto_ut_formulario_proyecto,objeto_ut_formulario) REFERENCES apex_objeto (proyecto,objeto)   
);
--###################################################################################################

CREATE TABLE apex_objeto_ut_formulario_ef
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_ut_formulario_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_ut_formulario_proyecto    char(15)		NOT NULL,
   objeto_ut_formulario             integer			   NOT NULL,
   identificador          				char(30)    NOT NULL,
   columnas                			char(255)   NOT NULL,
   clave_primaria          			smallint     ,			-- El contenido de este EF es parte de una clave primaria?
   obligatorio             			smallint     ,			-- El contenido de este EF es obligatorio?
   elemento_formulario     			char(30)    NOT NULL,
   inicializacion          			char      ,
   orden                   			float       	NOT NULL,
   etiqueta                			char(40)  ,
   descripcion             			char      ,
   desactivado             			smallint     ,
   PRIMARY KEY (objeto_ut_formulario_proyecto,objeto_ut_formulario,identificador),
   FOREIGN KEY (objeto_ut_formulario_proyecto,objeto_ut_formulario) REFERENCES apex_objeto_ut_formulario (objeto_ut_formulario_proyecto,objeto_ut_formulario)   ,
   FOREIGN KEY (elemento_formulario) REFERENCES apex_elemento_formulario (elemento_formulario)   
);
--###################################################################################################
