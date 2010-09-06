
---SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: -
--

SET default_with_oids = false;

--
-- Name: t_tipos_instituciones; Type: TYPE; Schema: public; Owner: -
--
DROP TYPE IF EXISTS t_tipos_instituciones CASCADE;

CREATE TYPE t_tipos_instituciones AS ENUM (
    '',
    'Universidad',
    'IESNU'
);

--
-- Name: area_disc_rama; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS area_disc_rama CASCADE;

CREATE TABLE area_disc_rama (
    area_id character varying(5) NOT NULL,
    area_desc character varying(100) NOT NULL,
    disc_id character(5) NOT NULL,
    disc_descripcion character varying(256) NOT NULL,
    rama_id character(5) NOT NULL,
    rama_desc character varying(256) NOT NULL
);


SET default_with_oids = true;

--
-- Name: depto_plano; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS depto_plano CASCADE;
CREATE TABLE depto_plano (
    idpais character(2) NOT NULL,
    idprovincia character(2) NOT NULL,
    iddepto character(3) NOT NULL,
    nombre character varying(40) NOT NULL,
    mug_depto integer
);



SET default_with_oids = false;

--
-- Name: instituciones; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS instituciones CASCADE;
CREATE TABLE instituciones (
    univ_id bigint NOT NULL,
    id_tipo_institucion integer NOT NULL,
    univ_desc character varying(256) NOT NULL,
    id_tipo_institucion_enum t_tipos_instituciones
);


SET default_with_oids = true;

--
-- Name: localidad_plano; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS localidad_plano CASCADE;
CREATE TABLE localidad_plano (
    longitud character varying(10) NOT NULL,
    latitud character varying(10) NOT NULL,
    idpais character(2) NOT NULL,
    idprovincia character(2) NOT NULL,
    iddepto character(3) NOT NULL,
    nombre character varying(40) NOT NULL,
    idlocalidad integer NOT NULL
);


--
-- Name: localidades_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--
DROP SEQUENCE IF EXISTS localidades_id_seq CASCADE;
CREATE SEQUENCE localidades_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


SET default_with_oids = false;

--
-- Name: modalidades; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS modalidades CASCADE;
CREATE TABLE modalidades (
    id_modalidad integer NOT NULL,
    descripcion character varying(20) NOT NULL
);


--
-- Name: niveles; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS niveles CASCADE;
CREATE TABLE niveles (
    id_nivel character(1) NOT NULL,
    desc_nivel character varying(30) NOT NULL
);


SET default_with_oids = true;

--
-- Name: provincia_plano; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS provincia_plano CASCADE;
CREATE TABLE provincia_plano (
    idpais character(2) NOT NULL,
    idprovincia character(2) NOT NULL,
    idona character(2) NOT NULL,
    nombre character varying(40) NOT NULL,
    mug_provincia integer
);


SET default_with_oids = false;

--
-- Name: regimenes; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS regimenes CASCADE;
CREATE TABLE regimenes (
    id_regimen character(2) NOT NULL,
    desc_regimen character varying(30) NOT NULL
);


--
-- Name: regiones; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS regiones CASCADE;
CREATE TABLE regiones (
    id_region character(5) NOT NULL,
    desc_region character varying(30) NOT NULL,
    r integer,
    g integer,
    b integer
);

--
-- Name: tipos_instituciones; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS tipos_instituciones CASCADE;
CREATE TABLE tipos_instituciones (
    id_tipo_institucion integer NOT NULL,
    descripcion character varying(20) NOT NULL
);


--
-- Name: tipos_normas; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS tipos_normas CASCADE;
CREATE TABLE tipos_normas (
    id_tipo_norma character(2) NOT NULL,
    desc_tipo_norma character varying(20) NOT NULL
);


--
-- Name: titulos; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS titulos CASCADE;
CREATE TABLE titulos (
    area_id character varying(5) NOT NULL,
    id_modalidad integer NOT NULL,
    id_regimen character(2) NOT NULL,
    id_nivel character(1) NOT NULL,
    titulo character varying(256) NOT NULL,
    anio_informado integer NOT NULL,
    duracion_anios numeric(5,2),
    id_tipo_norma character(2),
    tipo_titulo character varying(40),
    carrera character varying(256),
    alumnos integer,
    egresados integer,
    univ_id bigint NOT NULL,
    ua_id bigint NOT NULL,
    id integer NOT NULL,
    baja character(1),
    fecha_baja date,
    modificado character(1),
    CONSTRAINT titulos_baja_check CHECK ((baja = ANY (ARRAY['S'::bpchar, 'N'::bpchar]))),
    CONSTRAINT titulos_modificado_check CHECK ((modificado = ANY (ARRAY['S'::bpchar, 'N'::bpchar])))
);


--
-- Name: titulos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--
DROP SEQUENCE  IF EXISTS titulos_id_seq CASCADE;
CREATE SEQUENCE titulos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: titulos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE titulos_id_seq OWNED BY titulos.id;


--
-- Name: unidades_acad_geo; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS unidades_acad_geo CASCADE;
CREATE TABLE unidades_acad_geo
(
  iduniversidad integer NOT NULL,
  idfacultad integer NOT NULL,
  facultad character varying(150),
  provincia character varying(100),
  localidad character varying(100),
  domicilio character varying(150),
  numero character varying(10),
  dire_geo character varying(256),
  latitud numeric(15,7),
  longitud numeric(15,7),
  url text,
  sql character varying(256),
  provincia_plano character varying(2),
  marcado integer,
  dire_geo_nueva character varying(256),
  CONSTRAINT unidades_acad_geo_pkey PRIMARY KEY (iduniversidad, idfacultad)
)
WITH (OIDS=FALSE);
---ALTER TABLE unidades_acad_geo OWNER TO postgres;

SET default_with_oids = true;

--
-- Name: unidades_academicas; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--
DROP TABLE IF EXISTS unidades_academicas CASCADE;
CREATE TABLE unidades_academicas (
    univ_id bigint NOT NULL,
    ua_id bigint NOT NULL,
    iddepto character(3) NOT NULL,
    idprovincia character(2) NOT NULL,
    idpais character(2) NOT NULL,
    egresados bigint,
    alumnos bigint,
    id_region character(5) NOT NULL,
    descripcion character varying(256) NOT NULL,
    idlocalidad integer,
    longitud numeric(15,7),
    latitud numeric(15,7),
    direccion character varying(256),
    numero character varying(10)
);

SELECT AddGeometryColumn('localidad_plano','the_geom',4326,'POINT',2);
SELECT AddGeometryColumn('provincia_plano','the_geom',4326,'MULTIPOLYGON',2);
SELECT AddGeometryColumn('depto_plano','the_geom',4326,'MULTIPOLYGON',2);
SELECT AddGeometryColumn('unidades_academicas','the_geom',4326,'POINT',2);
