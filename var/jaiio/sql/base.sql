CREATE TABLE tipos_documentos
(
  tipo_documento serial NOT NULL,
  descripcion varchar(35) NOT NULL,
  desc_abreviada char(3) NOT NULL,
  CONSTRAINT tipos_documentos_pkey PRIMARY KEY (tipo_documento)
);

CREATE TABLE nacionalidades
(
  nacionalidad serial NOT NULL,
  descripcion varchar(60) NOT NULL,
  CONSTRAINT nacionalidades_pkey PRIMARY KEY (nacionalidad)
);

CREATE TABLE personas
(
  persona serial NOT NULL,
  tipo_documento int4,
  nacionalidad int4,
  nro_documento numeric(15),
  apellido varchar(30),
  nombre varchar(30),
  fecha_nacimiento date,
  CONSTRAINT personas_pkey PRIMARY KEY (persona),
  CONSTRAINT personas_nacionalidad_fkey FOREIGN KEY (nacionalidad) REFERENCES nacionalidades (nacionalidad) ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT personas_tipo_documento_fkey FOREIGN KEY (tipo_documento) REFERENCES tipos_documentos (tipo_documento) ON UPDATE RESTRICT ON DELETE RESTRICT
);

