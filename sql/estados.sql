
CREATE TABLE apex_cp_cambios_estado (
       fecha                DATE NOT NULL,
       descripcion          VARCHAR(255) NOT NULL,
       id_entregable        INTEGER NOT NULL,
       id_estado            INTEGER NOT NULL
);


ALTER TABLE apex_cp_cambios_estado
       ADD  ( PRIMARY KEY (id_entregable, id_estado, fecha) ) ;


CREATE TABLE apex_cp_cambios_responsables (
       fecha                DATE NOT NULL,
       descripcion          VARCHAR(255) NOT NULL,
       id_entregable        INTEGER NOT NULL,
       id_responsable       INTEGER NOT NULL
);


ALTER TABLE apex_cp_cambios_responsables
       ADD  ( PRIMARY KEY (id_entregable, id_responsable, fecha) ) ;


CREATE TABLE apex_cp_dificultades (
       id_dificultad        INTEGER NOT NULL,
       descripcion          VARCHAR(32) NOT NULL
);


ALTER TABLE apex_cp_dificultades
       ADD  ( PRIMARY KEY (id_dificultad) ) ;


CREATE TABLE apex_cp_entregables (
       id_entregable        INTEGER NOT NULL,
       numero               VARCHAR(8) NOT NULL,
       descripcion          VARCHAR(255) NOT NULL,
       dificultad           INTEGER NOT NULL,
       item                 INTEGER NULL
);


ALTER TABLE apex_cp_entregables
       ADD  ( PRIMARY KEY (id_entregable) ) ;


CREATE TABLE apex_cp_estados (
       id_estado            INTEGER NOT NULL,
       descripcion          VARCHAR(32) NOT NULL
);


ALTER TABLE apex_cp_estados
       ADD  ( PRIMARY KEY (id_estado) ) ;


CREATE TABLE apex_cp_iteraciones (
       id_iteracion         INTEGER NOT NULL,
       fecha_inicio         DATE NOT NULL,
       fecha_fin            DATE NOT NULL,
       Activa               INTEGER NOT NULL
);


ALTER TABLE apex_cp_iteraciones
       ADD  ( PRIMARY KEY (id_iteracion) ) ;


CREATE TABLE apex_cp_responsables (
       id_reponsable        INTEGER NOT NULL,
       Nombre               VARCHAR(32) NOT NULL
);


ALTER TABLE apex_cp_responsables
       ADD  ( PRIMARY KEY (id_reponsable) ) ;


CREATE TABLE apex_cp_tareas (
       descripcion          VARCHAR(255) NOT NULL,
       id_iteracion         INTEGER NOT NULL,
       id_entregable        INTEGER NOT NULL
);


ALTER TABLE apex_cp_tareas
       ADD  ( PRIMARY KEY (id_iteracion, id_entregable) ) ;


ALTER TABLE apex_cp_cambios_estado
       ADD  ( FOREIGN KEY (id_estado)
                             REFERENCES apex_cp_estados ) ;


ALTER TABLE apex_cp_cambios_estado
       ADD  ( FOREIGN KEY (id_entregable)
                             REFERENCES apex_cp_entregables ) ;


ALTER TABLE apex_cp_cambios_responsables
       ADD  ( FOREIGN KEY (id_responsable)
                             REFERENCES apex_cp_responsables ) ;


ALTER TABLE apex_cp_cambios_responsables
       ADD  ( FOREIGN KEY (id_entregable)
                             REFERENCES apex_cp_entregables ) ;


ALTER TABLE apex_cp_entregables
       ADD  ( FOREIGN KEY (dificultad)
                             REFERENCES apex_cp_dificultades
                             ON DELETE SET NULL ) ;


ALTER TABLE apex_cp_tareas
       ADD  ( FOREIGN KEY (id_entregable)
                             REFERENCES apex_cp_entregables ) ;


ALTER TABLE apex_cp_tareas
       ADD  ( FOREIGN KEY (id_iteracion)
                             REFERENCES apex_cp_iteraciones ) ;

