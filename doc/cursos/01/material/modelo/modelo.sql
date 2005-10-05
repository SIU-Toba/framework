BEGIN TRANSACTION;

CREATE TABLE ona_pais (
       idpais               char(2) NOT NULL,
       nombre               varchar(40) NOT NULL,
       ddi                  char(2),
       esuniversidad        integer DEFAULT 0 NOT NULL,
       modiuniversidad      integer DEFAULT 0 NOT NULL,
       PRIMARY KEY (idpais)
);

CREATE TABLE ona_provincia (
       idprovincia          char(4) NOT NULL,
       idpais               char(2) NOT NULL,
       nombre               varchar(40) NOT NULL,
       esuniversidad        integer DEFAULT 0 NOT NULL,
       modiuniversidad      integer DEFAULT 0 NOT NULL,
       PRIMARY KEY (idprovincia), 
       FOREIGN KEY (idpais)  REFERENCES ona_pais
);

CREATE TABLE ona_localidad (
       codigopostal         char(10) NOT NULL,
       idpais               char(2) NOT NULL,
       idprovincia          char(4) NOT NULL,
       nombre               varchar(40) NOT NULL,
       ddn                  varchar(6),
       esuniversidad        integer DEFAULT 0 NOT NULL,
       modiuniversidad      integer DEFAULT 0 NOT NULL,
       PRIMARY KEY (codigopostal), 
       FOREIGN KEY (idpais)  REFERENCES ona_pais, 
       FOREIGN KEY (idprovincia) REFERENCES ona_provincia
);

CREATE TABLE soe_jurisdicciones (
       jurisdiccion         integer NOT NULL,
       descripcion          varchar(100) NOT NULL,
       estado               char(1) NOT NULL,
       PRIMARY KEY (jurisdiccion)
);

CREATE TABLE soe_tiposinstit (
       tipoinstit           serial NOT NULL,
       descripcion          varchar(50) NOT NULL,
       detalle              varchar(255),
       estado               char(1) NOT NULL,
       PRIMARY KEY (tipoinstit)
);

CREATE TABLE soe_instituciones (
       institucion          serial NOT NULL,
       nombre_completo      varchar(255) NOT NULL,
       nombre_abreviado     varchar(50),
       sigla                varchar(15),
       jurisdiccion         integer,
       tipoinstit           integer,
       PRIMARY KEY (institucion),
       FOREIGN KEY (tipoinstit) REFERENCES soe_tiposinstit,
       FOREIGN KEY (jurisdiccion) REFERENCES soe_jurisdicciones
);

CREATE TABLE soe_tiposede (
       tiposede             serial NOT NULL,
       descripcion          varchar(50) NOT NULL,
       detalle              varchar(255),
       estado               char(1) NOT NULL,
       PRIMARY KEY (tiposede)
);

CREATE TABLE soe_sedes (
       institucion          integer NOT NULL,
       sede                 serial NOT NULL,
       nombre               varchar(255) NOT NULL,
       tiposede             integer,
       idpais               char(2),
       idprovincia          char(4),
       codigopostal         char(10),
       PRIMARY KEY (institucion, sede),
       FOREIGN KEY (institucion)
                             REFERENCES soe_instituciones,
       FOREIGN KEY (tiposede)
                             REFERENCES soe_tiposede,
       FOREIGN KEY (idpais)  REFERENCES ona_pais,
       FOREIGN KEY (idprovincia) REFERENCES ona_provincia,
	   FOREIGN KEY (codigopostal) REFERENCES ona_localidad                             
);

CREATE TABLE soe_edificios (
       edificio             serial NOT NULL,
       institucion          integer NOT NULL,
       sede                 integer NOT NULL,
       nombre               varchar(255),
       calle                varchar(50),
       numero               varchar(5),
       piso                 varchar(3),
       depto                varchar(30),
       PRIMARY KEY (edificio),
       FOREIGN KEY (institucion, sede)
                             REFERENCES soe_sedes
);

CREATE TABLE soe_tiposua (
       tipoua               serial NOT NULL,
       descripcion          varchar(50) NOT NULL,
       detalle              varchar(255),
       estado               char(1) NOT NULL,
       PRIMARY KEY (tipoua)
);


CREATE TABLE soe_unidadesacad (
       unidadacad           serial NOT NULL,
       institucion          integer,
       nombre               varchar(255) NOT NULL,
       tipoua               integer,
       PRIMARY KEY (unidadacad),
       FOREIGN KEY (institucion)
                             REFERENCES soe_instituciones,
       FOREIGN KEY (tipoua)
                             REFERENCES soe_tiposua
);


CREATE TABLE soe_sedesua (
       institucion          integer NOT NULL,
       sede                 integer NOT NULL,
       unidadacad           integer NOT NULL,
       PRIMARY KEY (institucion,sede, unidadacad),
       FOREIGN KEY (unidadacad)
                             REFERENCES soe_unidadesacad,
       FOREIGN KEY (institucion, sede)
                             REFERENCES soe_sedes
);

COMMIT TRANSACTION;