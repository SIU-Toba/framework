--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: nacionalidades; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE nacionalidades (
    nacionalidad serial NOT NULL,
    descripcion character varying(60) NOT NULL
);


--
-- Name: nacionalidades_nacionalidad_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('nacionalidades', 'nacionalidad'), 4, true);


--
-- Name: personas; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE personas (
    persona serial NOT NULL,
    tipo_documento integer,
    nacionalidad integer,
    nro_documento numeric(15,0),
    apellido character varying(30),
    nombre character varying(30),
    fecha_nacimiento date
);


--
-- Name: personas_persona_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('personas', 'persona'), 1, false);


--
-- Name: tipos_documentos; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE tipos_documentos (
    tipo_documento serial NOT NULL,
    descripcion character varying(35) NOT NULL,
    desc_abreviada character(3) NOT NULL
);


--
-- Name: tipos_documentos_tipo_documento_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('tipos_documentos', 'tipo_documento'), 4, true);


--
-- Data for Name: nacionalidades; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO nacionalidades (nacionalidad, descripcion) VALUES (2, 'Uruguay');
INSERT INTO nacionalidades (nacionalidad, descripcion) VALUES (3, 'Chile');
INSERT INTO nacionalidades (nacionalidad, descripcion) VALUES (4, 'Argentina');


--
-- Data for Name: personas; Type: TABLE DATA; Schema: public; Owner: dba
--



--
-- Data for Name: tipos_documentos; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO tipos_documentos (tipo_documento, descripcion, desc_abreviada) VALUES (1, 'D.N.I', 'DNI');
INSERT INTO tipos_documentos (tipo_documento, descripcion, desc_abreviada) VALUES (2, 'L.C.', 'LC ');
INSERT INTO tipos_documentos (tipo_documento, descripcion, desc_abreviada) VALUES (4, 'L.E.', 'LE ');


--
-- Name: nacionalidades_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY nacionalidades
    ADD CONSTRAINT nacionalidades_pkey PRIMARY KEY (nacionalidad);


--
-- Name: personas_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY personas
    ADD CONSTRAINT personas_pkey PRIMARY KEY (persona);


--
-- Name: tipos_documentos_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY tipos_documentos
    ADD CONSTRAINT tipos_documentos_pkey PRIMARY KEY (tipo_documento);


--
-- Name: personas_nacionalidad_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY personas
    ADD CONSTRAINT personas_nacionalidad_fkey FOREIGN KEY (nacionalidad) REFERENCES nacionalidades(nacionalidad) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: personas_tipo_documento_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY personas
    ADD CONSTRAINT personas_tipo_documento_fkey FOREIGN KEY (tipo_documento) REFERENCES tipos_documentos(tipo_documento) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- PostgreSQL database dump complete
--

