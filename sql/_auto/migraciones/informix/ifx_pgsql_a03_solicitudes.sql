--##################################################################################################
--##################################################################################################
--################################  Registro de Solicitudes  #######################################
--##################################################################################################
--##################################################################################################



CREATE TABLE apex_solicitud
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( item_proyecto = '%%' )
--: dump_order_by: apex_solicitud.solicitud
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud	 					serial,
	solicitud_tipo					char(20)		NOT NULL,
	item_proyecto					char(15)		NOT NULL,
	item 								char(60)		NOT NULL,
   item_id							integer        , 
	momento							datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
	tiempo_respuesta				float			,
   PRIMARY KEY (solicitud),
   FOREIGN KEY (item_proyecto,item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (solicitud_tipo) REFERENCES apex_solicitud_tipo (solicitud_tipo)   
);
--###################################################################################################


CREATE TABLE apex_sesion_browser
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: sesion_browser
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	sesion_browser					serial,
	usuario							char(20) 	NOT NULL,
	proyecto							char(15) 	NOT NULL,
	ingreso							datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
	egreso							datetime YEAR to SECOND,
	observaciones					char(255),
	php_id							char(100)	NOT NULL,
	ip									char(20)	,
	punto_acceso					char(80) ,
   PRIMARY KEY (sesion_browser), 
   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   ,

   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--###################################################################################################

CREATE TABLE apex_solicitud_browser
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: (apex_solicitud.solicitud = dd.solicitud_browser) AND (apex_solicitud.item_proyecto ='%%')
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_browser				integer				NOT NULL, 
	sesion_browser					integer				NOT NULL,
	ip									char(20)	,
   PRIMARY KEY (solicitud_browser),
   FOREIGN KEY (solicitud_browser) REFERENCES apex_solicitud (solicitud)    ,

   FOREIGN KEY (sesion_browser) REFERENCES apex_sesion_browser (sesion_browser)   

);
--###################################################################################################

CREATE TABLE apex_solicitud_wddx
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud_wddx) AND (apex_solicitud.item_proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_wddx				integer					NOT NULL, 
	usuario						char(20) 		NOT NULL,
	ip								char(20)		,
	instancia					char(80) 		NOT NULL,
	instancia_usuario			char(20) 		NOT NULL,
	paquete						text				,
   PRIMARY KEY (solicitud_wddx),
   FOREIGN KEY (solicitud_wddx) REFERENCES apex_solicitud (solicitud)    ,

   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   

);
--###################################################################################################

CREATE TABLE apex_solicitud_consola
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud_consola) AND (apex_solicitud.item_proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_consola			integer					NOT NULL, 
	usuario						char(20) 		NOT NULL,
	ip								char(20)		,
	llamada						char(255) 	,
	entorno						text				,
   PRIMARY KEY (solicitud_consola),
   FOREIGN KEY (solicitud_consola) REFERENCES apex_solicitud (solicitud)    ,

   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   

);
--###################################################################################################

CREATE TABLE apex_solicitud_cronometro
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud) AND (apex_solicitud.item_proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud	 				integer				NOT NULL,
	marca							smallint			NOT NULL,
	nivel_ejecucion			char(15)		NOT NULL,
	texto							char(120),
	tiempo						float			,
   PRIMARY KEY (solicitud,marca),
   FOREIGN KEY (nivel_ejecucion) REFERENCES apex_nivel_ejecucion (nivel_ejecucion)    ,
   FOREIGN KEY (solicitud) REFERENCES apex_solicitud (solicitud)    

);
--###################################################################################################


CREATE TABLE apex_solicitud_observacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud_observacion) AND (apex_solicitud.item_proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_observacion			serial,
	solicitud_obs_tipo_proyecto	char(15)	,
	solicitud_obs_tipo				char(20)	,
	solicitud	 						integer				NOT NULL,
	observacion							char		,
   PRIMARY KEY (solicitud_observacion),
   FOREIGN KEY (solicitud_obs_tipo_proyecto,solicitud_obs_tipo) REFERENCES apex_solicitud_obs_tipo (proyecto,solicitud_obs_tipo)   ,
   FOREIGN KEY (solicitud) REFERENCES apex_solicitud (solicitud)    

);

--###################################################################################################


CREATE TABLE apex_solicitud_obj_observacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud) AND (apex_solicitud.item_proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_obj_observacion			serial,
	solicitud_obj_obs_tipo				char(20)	,
	solicitud		 						integer				NOT NULL,
   objeto_proyecto          			char(15)    NOT NULL,
	objeto									integer				NOT NULL,
	observacion								char		,
   PRIMARY KEY (solicitud_obj_observacion),
   FOREIGN KEY (solicitud_obj_obs_tipo) REFERENCES apex_solicitud_obj_obs_tipo (solicitud_obj_obs_tipo)   ,
   FOREIGN KEY (objeto_proyecto,objeto) REFERENCES apex_objeto (proyecto,objeto)   ,
   FOREIGN KEY (solicitud) REFERENCES apex_solicitud (solicitud)    

);

--##################################################################################################
--##################################################################################################
--##################################  Monitoreo y control  #########################################
--##################################################################################################
--##################################################################################################

CREATE TABLE apex_log_sistema_tipo 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: solicitud
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_sistema_tipo			char(20)		NOT NULL,
	descripcion					char(255)	NOT NULL,
   PRIMARY KEY (log_sistema_tipo)
);
------------------------


CREATE TABLE apex_log_sistema
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_sistema		 			serial,
	momento						datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
	usuario						char(20) 	,
	log_sistema_tipo			char(20) 		NOT NULL,
	observaciones				text				,
   PRIMARY KEY (log_sistema),
   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   ,

   FOREIGN KEY (log_sistema_tipo) REFERENCES apex_log_sistema_tipo (log_sistema_tipo)   
);
--###################################################################################################-------------------


CREATE TABLE apex_log_error_login
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_error_login 			serial,
	momento						datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
	usuario						char(20) 						,
	clave							char(20) 						,
	ip								char(20)							,
	gravedad						smallint								,
	mensaje						text									,
	punto_acceso				char(80) 						,
   PRIMARY KEY (log_error_login)
);
--###################################################################################################-------------------

CREATE TABLE apex_log_ip_rechazada
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	ip								char(20)								NOT NULL,
	momento						datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (ip)
);

--###################################################################################################-------------------
