
CREATE SEQUENCE apex_cp_dificultades_sq;

CREATE TABLE apex_cp_dificultades (
       id_dificultad        INTEGER NOT NULL DEFAULT nextval('"apex_cp_dificultades_sq"'::text),
       descripcion          VARCHAR(32) NOT NULL,
       CONSTRAINT XPKapex_cp_dificultades 
              PRIMARY KEY (id_dificultad)
);

CREATE SEQUENCE apex_cp_entregables_sq;

CREATE TABLE apex_cp_entregables (
       id_entregable        INTEGER NOT NULL DEFAULT nextval('"apex_cp_entregables_sq"'::text),
       numero               VARCHAR(8) NOT NULL,
       nombre				VARCHAR(32) NOT NULL,
       descripcion          VARCHAR(255) NOT NULL,
       dificultad           INTEGER NOT NULL,
		item_proyecto		varchar(15)		NOT NULL,
		item				varchar(60)		NOT NULL,
       CONSTRAINT XPKapex_cp_entregables 
              PRIMARY KEY (id_entregable), 
       CONSTRAINT R_5
              FOREIGN KEY (dificultad)
                             REFERENCES apex_cp_dificultades
                             ON DELETE SET NULL,
		CONSTRAINT	"R6"	FOREIGN KEY	("item_proyecto","item")	
			REFERENCES "apex_item" ("proyecto","item") 
			ON DELETE NO ACTION	ON	UPDATE NO ACTION NOT	
			DEFERRABLE INITIALLY	IMMEDIATE
);


CREATE TABLE apex_cp_responsables (
       id_responsable        INTEGER NOT NULL,
       Nombre               VARCHAR(32) NOT NULL,
       CONSTRAINT XPKapex_cp_responsables 
              PRIMARY KEY (id_responsable)
);

CREATE SEQUENCE apex_cp_estados_sq;

CREATE TABLE apex_cp_estados (
       id_estado            INTEGER NOT NULL DEFAULT nextval('"apex_cp_estados_sq"'::text),
       descripcion          VARCHAR(32) NOT NULL,
       CONSTRAINT XPKapex_cp_estados 
              PRIMARY KEY (id_estado)
);


CREATE SEQUENCE apex_cp_iteraciones_sq;

CREATE TABLE apex_cp_iteraciones (
       id_iteracion         INTEGER NOT NULL DEFAULT nextval('"apex_cp_iteraciones_sq"'::text),
       fecha_inicio         DATE NOT NULL,
       fecha_fin            DATE NOT NULL,
       Activa               INTEGER NOT NULL,
       CONSTRAINT XPKapex_cp_iteraciones 
              PRIMARY KEY (id_iteracion)
);


CREATE TABLE apex_cp_cambios_estado (
       fecha                DATE NOT NULL,
       descripcion          VARCHAR(255) NOT NULL,
       id_entregable        INTEGER NOT NULL,
       id_estado            INTEGER NOT NULL,
       CONSTRAINT XPKapex_cp_cambios_estado 
              PRIMARY KEY (id_entregable, id_estado, fecha), 
       CONSTRAINT R_12
              FOREIGN KEY (id_estado)
                             REFERENCES apex_cp_estados, 
       CONSTRAINT R_11
              FOREIGN KEY (id_entregable)
                             REFERENCES apex_cp_entregables
);


CREATE TABLE apex_cp_tareas (
       descripcion          VARCHAR(255) NOT NULL,
       id_iteracion         INTEGER NOT NULL,
       id_entregable        INTEGER NOT NULL,
       CONSTRAINT XPKapex_cp_tareas 
              PRIMARY KEY (id_iteracion, id_entregable), 
       CONSTRAINT R_14
              FOREIGN KEY (id_entregable)
                             REFERENCES apex_cp_entregables, 
       CONSTRAINT R_13
              FOREIGN KEY (id_iteracion)
                             REFERENCES apex_cp_iteraciones
);


CREATE TABLE apex_cp_cambios_responsables (
       fecha                DATE NOT NULL,
       descripcion          VARCHAR(255) NOT NULL,
       id_entregable        INTEGER NOT NULL,
       id_responsable       INTEGER NOT NULL,
       CONSTRAINT XPKapex_cp_cambios_responsable 
              PRIMARY KEY (id_entregable, id_responsable, fecha), 
       CONSTRAINT R_17
              FOREIGN KEY (id_responsable)
                             REFERENCES apex_cp_responsables, 
       CONSTRAINT R_16
              FOREIGN KEY (id_entregable)
                             REFERENCES apex_cp_entregables
);

