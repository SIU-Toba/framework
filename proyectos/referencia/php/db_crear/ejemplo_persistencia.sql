CREATE TABLE paises (
  pais 					INT4			NOT NULL,
  nombre 				VARCHAR(40)		NOT NULL,
  codigoiso 			CHAR(2),
  CONSTRAINT paises_pk 		PRIMARY KEY(pais)
);

CREATE SEQUENCE seq_personas;
CREATE TABLE personas (
  persona 			INT4			DEFAULT nextval('"seq_personas"'::text)	NOT NULL,
  nombre 			VARCHAR(40)		NOT NULL,
  sexo 				CHAR(1),
  nacionalidad 		INTEGER,
  email 			VARCHAR(40),
  CONSTRAINT personas_pk 	PRIMARY KEY(persona),
  CONSTRAINT personas_pais 	FOREIGN KEY (nacionalidad) REFERENCES paises(pais)
);

CREATE SEQUENCE seq_domicilios;
CREATE TABLE domicilios (
  domicilio 			INT4			DEFAULT nextval('"seq_domicilios"'::text)	NOT NULL, 
  calle 				VARCHAR(20)		NOT NULL, 
  numero 				VARCHAR(20)		NOT NULL, 
  piso 					VARCHAR(20), 
  departamento 			VARCHAR(20), 
  unidad 				VARCHAR(20), 
  fax 					VARCHAR(20), 
  telefono 				VARCHAR(20),
  CONSTRAINT domicilio_pk PRIMARY KEY(domicilio)
);

CREATE TABLE domicilio_roles (
  rol 					CHAR(8) 		NOT NULL, 
  nombre 				VARCHAR(20), 
  CONSTRAINT domicilio_roles_pk PRIMARY KEY(rol)
);

CREATE TABLE personas_domicilios (
  domicilio 		INTEGER 		NOT NULL, 
  persona 			INTEGER 		NOT NULL, 
  rol 				CHAR(8) 		NOT NULL, 
  CONSTRAINT personas_domicilios_pk 		PRIMARY KEY(domicilio, persona, rol), 
  CONSTRAINT personas_domicilios_domic 		FOREIGN KEY (domicilio) REFERENCES domicilios(domicilio),
  CONSTRAINT personas_domicilios_persona 	FOREIGN KEY (persona) REFERENCES personas(persona),
  CONSTRAINT personas_domicilios_rol FOREIGN KEY (rol) REFERENCES domicilio_roles(rol)
);

CREATE TABLE 	tipos_documento (
  tipo_documento	CHAR(4) 		NOT NULL, 
  CONSTRAINT tipos_documento_pk		PRIMARY KEY(tipo_documento)
);

CREATE TABLE personas_documentos (
  persona 				INTEGER NOT NULL, 
  tipo_documento		CHAR(4) NOT NULL, 
  numero 				VARCHAR(20), 
  CONSTRAINT personas_documentos_pk 		PRIMARY KEY(persona, tipo_documento), 
  CONSTRAINT personas_documentos_persona 	FOREIGN KEY (persona) REFERENCES personas(persona),
  CONSTRAINT personas_documentos_tipo 		FOREIGN KEY (tipo_documento) REFERENCES tipos_documento(tipo_documento)
);