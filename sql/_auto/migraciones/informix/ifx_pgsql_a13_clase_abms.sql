--**************************************************************************************************
--**************************************************************************************************
--******************************************  ABM simple  ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_abms
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_abms_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_abms_proyecto       char(15)		NOT NULL,
   objeto_abms                integer    			NOT NULL,
   tabla                      char(40)    NOT NULL,
   titulo                     char(80)  ,       -- Titulo de la interface
   ev_mod_eliminar            smallint     ,       -- Pantalla de modificacion: Se permite eliminar registros ?
   ev_mod_estado_i            smallint     ,       -- Pantalla de modificacion: Hay un boton para volver al estado inicial?
   auto_reset                 smallint     ,       -- Pasar al estado de INSERT despues de un EVENTO
   PRIMARY KEY (objeto_abms_proyecto,objeto_abms),
   FOREIGN KEY (objeto_abms_proyecto,objeto_abms) REFERENCES apex_objeto (proyecto,objeto)   
);
--###################################################################################################

CREATE TABLE apex_objeto_abms_ef
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_abms_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_abms_proyecto    char(15)		NOT NULL,
   objeto_abms             integer			   NOT NULL,
   identificador          	char(30)    NOT NULL,
   columnas                char(255)   NOT NULL,
   clave_primaria          smallint     ,			-- El contenido de este EF es parte de una clave primaria?
   obligatorio             smallint     ,			-- El contenido de este EF es obligatorio?
   elemento_formulario     char(30)    NOT NULL,
   inicializacion          char      ,
   orden                   float       	NOT NULL,
   etiqueta                char(40)  ,
   descripcion             char      ,
   desactivado             smallint     ,
   PRIMARY KEY (objeto_abms_proyecto,objeto_abms,identificador),
   FOREIGN KEY (objeto_abms_proyecto,objeto_abms) REFERENCES apex_objeto_abms (objeto_abms_proyecto,objeto_abms)   ,
   FOREIGN KEY (elemento_formulario) REFERENCES apex_elemento_formulario (elemento_formulario)   
);
--###################################################################################################
