
CREATE TABLE apl_usuario
(
   usuario                    char(20)    NOT NULL,
   clave                      char(20)    NOT NULL,
   usuario_grupo_acc          char(20)    NOT NULL,
   usuario_perfil_datos        char(20)    NOT NULL,
   nombre                     char(80)  ,
   usuario_tipodoc            char(10)  ,
   pre                        char(2)   ,
   ciu                        char(18)  ,
   suf                        char(1)   ,
   email                      char(80)  ,
   telefono                   char(18)  ,
   vencimiento                date         ,
   dias                       smallint     ,
   hora_entrada               datetime HOUR to MINUTE DEFAULT NULL,
   hora_salida                datetime HOUR to MINUTE DEFAULT NULL,
   ip_permitida               char(20)  ,
   solicitud_registrar        smallint     ,
   solicitud_obs_tipo         char(20)  ,
   solicitud_observacion      char(255) ,
   PRIMARY KEY (usuario),
   UNIQUE (usuario_tipodoc,pre,ciu,suf),
   FOREIGN KEY (usuario_grupo_acc) REFERENCES apl_usuario_grupo_acc (usuario_grupo_acc)   ,
   FOREIGN KEY (usuario_perfil_datos) REFERENCES apl_usuario_perfil_datos (usuario_perfil_datos)   ,
   FOREIGN KEY (solicitud_obs_tipo) REFERENCES apl_solicitud_obs_tipo (solicitud_obs_tipo)   ,
   FOREIGN KEY (usuario_tipodoc) REFERENCES apl_usuario_tipodoc (usuario_tipodoc)   
);
