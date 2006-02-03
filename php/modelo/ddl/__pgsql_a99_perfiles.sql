CREATE TABLE apex_dim_restric_soltipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: solicitud_tipo, usuario_perfil_datos
--: zona: perfiles
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   solicitud_tipo 					varchar(20)    NOT NULL, 
   usuario_perfil_datos_proyecto varchar(15)    NOT NULL,
   usuario_perfil_datos          varchar(20)    NOT NULL,
   PRIMARY KEY (solicitud_tipo,usuario_perfil_datos_proyecto,usuario_perfil_datos),
   FOREIGN KEY (usuario_perfil_datos_proyecto,usuario_perfil_datos) REFERENCES apex_usuario_perfil_datos (proyecto,usuario_perfil_datos)  DEFERRABLE INITIALLY IMMEDIATE,
   FOREIGN KEY (solicitud_tipo) REFERENCES apex_solicitud_tipo (solicitud_tipo)  DEFERRABLE INITIALLY IMMEDIATE 
);
--#################################################################################################
