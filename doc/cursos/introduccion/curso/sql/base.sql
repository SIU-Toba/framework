--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 1548 (class 0 OID 0)
-- Name: DUMP TIMESTAMP; Type: DUMP TIMESTAMP; Schema: -; Owner: 
--

-- Started on 2007-05-08 16:32:17 Hora est. de Sudamérica E.


--
-- TOC entry 1551 (class 0 OID 0)
-- Dependencies: 5
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = true;

--
-- TOC entry 1168 (class 1259 OID 673500768)
-- Dependencies: 1496 1497 5
-- Name: ona_localidad; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE ona_localidad (
    codigopostal character varying(10) NOT NULL,
    idpais character varying(2) NOT NULL,
    idprovincia character varying(4) NOT NULL,
    nombre character varying(40) NOT NULL,
    ddn character varying(6),
    esuniversidad integer DEFAULT 0 NOT NULL,
    modiuniversidad integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 1169 (class 1259 OID 673500772)
-- Dependencies: 1498 1499 5
-- Name: ona_pais; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE ona_pais (
    idpais character varying(2) NOT NULL,
    nombre character varying(40) NOT NULL,
    ddi character varying(2),
    esuniversidad integer DEFAULT 0 NOT NULL,
    modiuniversidad integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 1170 (class 1259 OID 673500776)
-- Dependencies: 1500 1501 5
-- Name: ona_provincia; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE ona_provincia (
    idprovincia character varying(4) NOT NULL,
    idpais character varying(2) NOT NULL,
    nombre character varying(40) NOT NULL,
    esuniversidad integer DEFAULT 0 NOT NULL,
    modiuniversidad integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 1172 (class 1259 OID 673500782)
-- Dependencies: 1502 5
-- Name: soe_edificios; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE soe_edificios (
    edificio serial NOT NULL,
    institucion integer NOT NULL,
    sede integer NOT NULL,
    nombre character varying(255),
    calle character varying(50),
    numero character varying(5),
    piso character varying(3),
    depto character varying(30)
);


--
-- TOC entry 1553 (class 0 OID 0)
-- Dependencies: 1171
-- Name: soe_edificios_edificio_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_edificios', 'edificio'), 599, true);


--
-- TOC entry 1174 (class 1259 OID 673500787)
-- Dependencies: 1503 5
-- Name: soe_instituciones; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE soe_instituciones (
    institucion serial NOT NULL,
    nombre_completo character varying(255) NOT NULL,
    nombre_abreviado character varying(50),
    sigla character varying(15),
    jurisdiccion integer
);


--
-- TOC entry 1554 (class 0 OID 0)
-- Dependencies: 1173
-- Name: soe_instituciones_institucion_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_instituciones', 'institucion'), 8889, false);


--
-- TOC entry 1175 (class 1259 OID 673500790)
-- Dependencies: 5
-- Name: soe_jurisdicciones; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE soe_jurisdicciones (
    jurisdiccion integer NOT NULL,
    descripcion character varying(100) NOT NULL,
    estado character varying(1) NOT NULL
);


--
-- TOC entry 1177 (class 1259 OID 673500794)
-- Dependencies: 1504 5
-- Name: soe_sedes; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE soe_sedes (
    institucion integer NOT NULL,
    sede serial NOT NULL,
    nombre character varying(255) NOT NULL,
    codigopostal character varying(10)
);


--
-- TOC entry 1555 (class 0 OID 0)
-- Dependencies: 1176
-- Name: soe_sedes_sede_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_sedes', 'sede'), 2229, false);


--
-- TOC entry 1178 (class 1259 OID 673500797)
-- Dependencies: 5
-- Name: soe_sedesua; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE soe_sedesua (
    institucion integer NOT NULL,
    sede integer NOT NULL,
    unidadacad integer NOT NULL
);


--
-- TOC entry 1180 (class 1259 OID 673500801)
-- Dependencies: 1505 5
-- Name: soe_tiposua; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE soe_tiposua (
    tipoua serial NOT NULL,
    descripcion character varying(50) NOT NULL,
    detalle character varying(255),
    estado character varying(1) NOT NULL
);


--
-- TOC entry 1556 (class 0 OID 0)
-- Dependencies: 1179
-- Name: soe_tiposua_tipoua_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_tiposua', 'tipoua'), 3, true);


--
-- TOC entry 1182 (class 1259 OID 673500806)
-- Dependencies: 1506 5
-- Name: soe_unidadesacad; Type: TABLE; Schema: public; Owner: dba; Tablespace: 
--

CREATE TABLE soe_unidadesacad (
    unidadacad serial NOT NULL,
    institucion integer,
    nombre character varying(255) NOT NULL,
    tipoua integer
);


--
-- TOC entry 1557 (class 0 OID 0)
-- Dependencies: 1181
-- Name: soe_unidadesacad_unidadacad_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_unidadesacad', 'unidadacad'), 927, true);


--
-- TOC entry 1538 (class 0 OID 673500768)
-- Dependencies: 1168
-- Data for Name: ona_localidad; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4126', 'AR', 'A', 'Candelaria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4141', 'AR', 'A', 'Tolombon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4190', 'AR', 'A', 'Rosario De La Frontera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4191', 'AR', 'A', 'El Naranjo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4193', 'AR', 'A', 'Alte. Brown', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4198', 'AR', 'A', 'Arenal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4400', 'AR', 'A', 'Salta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4401', 'AR', 'A', 'Caldera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4403', 'AR', 'A', 'Cerrillos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4405', 'AR', 'A', 'El Manzano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4407', 'AR', 'A', 'Campo Quijano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4409', 'AR', 'A', 'Cachiñal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4413', 'AR', 'A', 'Caipe', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4415', 'AR', 'A', 'El Potrero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4417', 'AR', 'A', 'Cachi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4419', 'AR', 'A', 'Luracatao', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4421', 'AR', 'A', 'Ampascachi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4423', 'AR', 'A', 'Chicoana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4425', 'AR', 'A', 'Alemania', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4427', 'AR', 'A', 'Angastaco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4430', 'AR', 'A', 'Gral. Guemes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4432', 'AR', 'A', 'Campo Santo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4434', 'AR', 'A', 'Cabeza De Buey', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4440', 'AR', 'A', 'Metan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4441', 'AR', 'A', 'Metan Viejo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4444', 'AR', 'A', 'El Galpon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4446', 'AR', 'A', 'Ceibalito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4448', 'AR', 'A', 'Cnel. Vidt', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4449', 'AR', 'A', 'Apolinario Saravia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4452', 'AR', 'A', 'El Quebrachal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4530', 'AR', 'A', 'San Ramon De La Nueva Oran', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4531', 'AR', 'A', 'Colonia Santa Rosa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4533', 'AR', 'A', 'El Tabacal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4534', 'AR', 'A', 'Pichanal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4535', 'AR', 'A', 'Algarrobal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4537', 'AR', 'A', 'Chaguaral', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4538', 'AR', 'A', 'Saucelito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4550', 'AR', 'A', 'Embarcacion', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4552', 'AR', 'A', 'Campichuelo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4554', 'AR', 'A', 'Cap. Juan Page', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4560', 'AR', 'A', 'Tartagal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4561', 'AR', 'A', 'Santa Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4562', 'AR', 'A', 'Gral. Enrique Mosconi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4563', 'AR', 'A', 'Campamento Vespucio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4564', 'AR', 'A', 'Piquirenda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4566', 'AR', 'A', 'Aguaray', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4568', 'AR', 'A', 'Pocitos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4633', 'AR', 'A', 'Colanzuli', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('0000', 'AR', 'B', 'San Miguel', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1', 'AR', 'B', 'Moreno', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1054', 'AR', 'B', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1601', 'AR', 'B', 'Isla Martin Garcia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1605', 'AR', 'B', 'Carapachay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1611', 'AR', 'B', 'Vicealmirante E. Montes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1612', 'AR', 'B', 'Adolfo Sourdeaux', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1613', 'AR', 'B', 'Yapeyu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1614', 'AR', 'B', 'Polvorines', '054', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1615', 'AR', 'B', 'Km. 38', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1617', 'AR', 'B', 'Doctor Ricardo Rojas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1618', 'AR', 'B', 'Gral. Pacheco', NULL, -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1619', 'AR', 'B', 'Garin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1621', 'AR', 'B', 'Benavidez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1623', 'AR', 'B', 'Dique Lujan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1625', 'AR', 'B', 'Escobar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1627', 'AR', 'B', 'Matheu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1629', 'AR', 'B', 'Pilar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1631', 'AR', 'B', 'Villa Rosa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1633', 'AR', 'B', 'Fatima', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1635', 'AR', 'B', 'Presidente Derqui', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1636', 'AR', 'B', 'Olivos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1640', 'AR', 'B', 'Martinez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1642', 'AR', 'B', 'San Isidro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1643', 'AR', 'B', 'Beccar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1644', 'AR', 'B', 'Victoria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1646', 'AR', 'B', 'San Fernando', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1647', 'AR', 'B', 'Varadero Del Mini', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1649', 'AR', 'B', 'Arroyo Borches', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1650', 'AR', 'B', 'Barrio Gral. San Martin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1653', 'AR', 'B', 'Villa Ballester', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1657', 'AR', 'B', 'Pablo Podesta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1659', 'AR', 'B', 'Bo. Sgto. Cabral-campo De Mayo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1661', 'AR', 'B', 'Villa Maside', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1663', 'AR', 'B', 'Barrio Martin Fierro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1664', 'AR', 'B', 'Trujui', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1665', 'AR', 'B', 'El Cruce', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1667', 'AR', 'B', 'El Palenque', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1669', 'AR', 'B', 'Del Viso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1678', 'AR', 'B', '3 De Febrero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1682', 'AR', 'B', 'Villa Martin Coronado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1684', 'AR', 'B', 'El Palomar', '01', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1702', 'AR', 'B', 'Villa Jose Ingenieros', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1704', 'AR', 'B', 'Ramos Mejia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1706', 'AR', 'B', 'Haedo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1708', 'AR', 'B', 'Moron', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1712', 'AR', 'B', 'Castelar.', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1713', 'AR', 'B', 'Villa Gdor. Udaondo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1714', 'AR', 'B', 'Itusaingo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1722', 'AR', 'B', 'Barrio Parque Gral. San Martin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1723', 'AR', 'B', 'Agustin Ferrari', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1727', 'AR', 'B', 'Elias Romero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1731', 'AR', 'B', 'Villars', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1733', 'AR', 'B', 'Plomer', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1737', 'AR', 'B', 'Gral. Las Heras', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1739', 'AR', 'B', 'Hornos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1741', 'AR', 'B', 'Enrique Fynn', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1742', 'AR', 'B', 'Villa Gral. Zapiola', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1744', 'AR', 'B', 'Barrio Jose A. Cortejarena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1746', 'AR', 'B', 'Agua De Oro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1748', 'AR', 'B', 'La Fraternidad', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1752', 'AR', 'B', 'Villa Rebasa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1754', 'AR', 'B', 'Villa Luzuriaga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1755', 'AR', 'B', 'Rafael Castillo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1759', 'AR', 'B', 'Ruta 3 - Km. 29', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1761', 'AR', 'B', 'Veinte De Junio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1763', 'AR', 'B', 'Puente Ezcurra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1765', 'AR', 'B', 'Casanova', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1770', 'AR', 'B', 'Aldo Bonzi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1773', 'AR', 'B', 'Villa Urbana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1774', 'AR', 'B', 'La Salada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1776', 'AR', 'B', 'Villa Transradio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1808', 'AR', 'B', 'Vicente Casares', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1812', 'AR', 'B', 'Maximo Paz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1814', 'AR', 'B', 'La Noria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1815', 'AR', 'B', 'Uribelarrea', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1816', 'AR', 'B', 'Colonia Santa Rosa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1822', 'AR', 'B', 'Puente Alsina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1824', 'AR', 'B', 'Lanus', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1825', 'AR', 'B', 'Villa Besada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1826', 'AR', 'B', 'Remedios de Escalada', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1828', 'AR', 'B', 'Villa Centenario', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1832', 'AR', 'B', 'Lomas De Zamora', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1834', 'AR', 'B', 'Villa La Perla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1836', 'AR', 'B', 'Santa Catalina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1842', 'AR', 'B', 'El Jaguel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1846', 'AR', 'B', 'Barrio Lindo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1852', 'AR', 'B', 'Ministro Rivadavia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1858', 'AR', 'B', 'Villa Numancia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1862', 'AR', 'B', 'Guernica', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1864', 'AR', 'B', 'Alejandro Korn', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1865', 'AR', 'B', 'San Vicente', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1870', 'AR', 'B', 'Avellaneda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1875', 'AR', 'B', 'Wilde', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1876', 'AR', 'B', 'Bernal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1878', 'AR', 'B', 'Quilmes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1884', 'AR', 'B', 'Villa España', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1885', 'AR', 'B', 'Platanos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1886', 'AR', 'B', 'Villa Giambruno', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1888', 'AR', 'B', 'Villa Gral. Manuel Caraballo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1889', 'AR', 'B', 'El Rocio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1890', 'AR', 'B', 'Juan Maria Gutierrez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1891', 'AR', 'B', 'Ing. Juan Allan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1893', 'AR', 'B', 'Ruta 2 - Km. 44,500', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1894', 'AR', 'B', 'Pereyra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1895', 'AR', 'B', 'Arturo Segui', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1896', 'AR', 'B', 'City Bell', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1897', 'AR', 'B', 'Manuel B. Gonnet', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1900', 'AR', 'B', 'La Plata', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1901', 'AR', 'B', 'La Josefa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1903', 'AR', 'B', 'Melchor Romero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1905', 'AR', 'B', 'Poblet', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1907', 'AR', 'B', 'El Pino', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1909', 'AR', 'B', 'Ignacio Correas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1911', 'AR', 'B', 'Gral. Mansilla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1913', 'AR', 'B', 'Atalaya', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1915', 'AR', 'B', 'Vieytes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1917', 'AR', 'B', 'Veronica', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1919', 'AR', 'B', 'Base Aerea De Punta Indio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1921', 'AR', 'B', 'Pipinas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1923', 'AR', 'B', 'Berisso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1925', 'AR', 'B', 'Ensenada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1927', 'AR', 'B', 'Esc. Nav. Militar Rio Santiago', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1929', 'AR', 'B', 'Base Naval De Rio Santiago', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1931', 'AR', 'B', 'Piria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1980', 'AR', 'B', 'Cnel. Brandsen Estaf. Nro 1', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1981', 'AR', 'B', 'Oliden', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1983', 'AR', 'B', 'Gomez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1984', 'AR', 'B', 'Domselaar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1986', 'AR', 'B', 'Jeppener', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1987', 'AR', 'B', 'Ranchos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2700', 'AR', 'B', 'Fontezuela', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2701', 'AR', 'B', 'Pergamino', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2703', 'AR', 'B', 'Roberto Cano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2705', 'AR', 'B', 'Rojas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2707', 'AR', 'B', 'Hunter', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2709', 'AR', 'B', 'Los Indios', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2711', 'AR', 'B', 'Paraje Santa Rosa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2713', 'AR', 'B', 'Manuel Ocampo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2715', 'AR', 'B', 'La Vanguardia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2717', 'AR', 'B', 'Juan A. De La Peña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2718', 'AR', 'B', 'Urquiza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2720', 'AR', 'B', 'Colon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2721', 'AR', 'B', 'Sarasa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2740', 'AR', 'B', 'Villa Sanguinetti', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2741', 'AR', 'B', 'Salto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2743', 'AR', 'B', 'Tacuari', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2745', 'AR', 'B', 'Gahan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2747', 'AR', 'B', 'Ines Indart', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2751', 'AR', 'B', 'La Violeta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2752', 'AR', 'B', 'La Luisa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2754', 'AR', 'B', 'Todd', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2760', 'AR', 'B', 'San Antonio De Areco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2761', 'AR', 'B', 'Villa Lia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2764', 'AR', 'B', 'Solis', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2800', 'AR', 'B', 'Zarate', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2801', 'AR', 'B', 'Escalada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2802', 'AR', 'B', 'Otamendi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2804', 'AR', 'B', 'Campana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2805', 'AR', 'B', 'La Horqueta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2806', 'AR', 'B', 'Lima', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2808', 'AR', 'B', 'Atucha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2812', 'AR', 'B', 'Capilla Del Señor', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2813', 'AR', 'B', 'Arroyo De La Cruz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2814', 'AR', 'B', 'Los Cardales', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2900', 'AR', 'B', 'San Nicolas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2901', 'AR', 'B', 'La Emilia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2903', 'AR', 'B', 'Lopez Arias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2905', 'AR', 'B', 'Gral. Rojo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2907', 'AR', 'B', 'Ing. Urcelay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2912', 'AR', 'B', 'Sanchez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2914', 'AR', 'B', 'Villa Ramallo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2915', 'AR', 'B', 'Ramallo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2916', 'AR', 'B', 'El Paraiso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2930', 'AR', 'B', 'San Pedrito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2931', 'AR', 'B', 'Isla Los Laureles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2933', 'AR', 'B', 'Colonia Velaz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2935', 'AR', 'B', 'El Descanso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2938', 'AR', 'B', 'Alsina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2942', 'AR', 'B', 'Baradero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2943', 'AR', 'B', 'Ireneo Portela', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2944', 'AR', 'B', 'Rio Tala', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2946', 'AR', 'B', 'Vuelta De Obligado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3314', 'AR', 'B', 'San Miguel', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3432', 'AR', 'B', 'Macedo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6000', 'AR', 'B', 'Junin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6001', 'AR', 'B', 'Rafael Obligado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6003', 'AR', 'B', 'La Trinidad', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6005', 'AR', 'B', 'Ham', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6007', 'AR', 'B', 'Arribeños', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6013', 'AR', 'B', 'Laplacette', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6015', 'AR', 'B', 'Gral. Viamonte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6017', 'AR', 'B', 'Chancay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6018', 'AR', 'B', 'Zavalia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6022', 'AR', 'B', 'Las Parvas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6030', 'AR', 'B', 'Vedia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6031', 'AR', 'B', 'El Dorado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6032', 'AR', 'B', 'Leandro N. Alem', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6034', 'AR', 'B', 'Juan Bautista Alberdi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6042', 'AR', 'B', 'Dos Hermanos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6050', 'AR', 'B', 'Gral. Pinto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6051', 'AR', 'B', 'Pichincha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6053', 'AR', 'B', 'Germania', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6058', 'AR', 'B', 'Pazos Kanki', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6062', 'AR', 'B', 'Cnel. Granada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6063', 'AR', 'B', 'Porvenir', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6064', 'AR', 'B', 'Volta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6065', 'AR', 'B', 'Blaquier', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6070', 'AR', 'B', 'Lincoln', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6071', 'AR', 'B', 'Triunvirato', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6073', 'AR', 'B', 'El Triunfo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6075', 'AR', 'B', 'Roberts', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6077', 'AR', 'B', 'Encina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6078', 'AR', 'B', 'Bayuca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6105', 'AR', 'B', 'Santa Regina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6223', 'AR', 'B', 'Cnel. Charlone', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6230', 'AR', 'B', 'Moores', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6231', 'AR', 'B', 'Pradere', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6233', 'AR', 'B', 'Hereford', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6235', 'AR', 'B', 'Villa Sauze', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6237', 'AR', 'B', 'America', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6239', 'AR', 'B', 'Meridiano V', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6241', 'AR', 'B', 'Piedritas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6242', 'AR', 'B', 'Elordi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6244', 'AR', 'B', 'Banderalo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6335', 'AR', 'B', 'Quenuma', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6337', 'AR', 'B', 'Ing. Thompson', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6338', 'AR', 'B', 'Leubuco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6339', 'AR', 'B', 'Salliquelo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6341', 'AR', 'B', 'Francisco Murature', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6343', 'AR', 'B', 'Thames', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6346', 'AR', 'B', 'Pellegrini', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6348', 'AR', 'B', 'Bocayuva', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6400', 'AR', 'B', 'Trenque Lauquen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6401', 'AR', 'B', 'Valentin Gomez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6403', 'AR', 'B', 'Villa Sena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6405', 'AR', 'B', 'Albariño', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6407', 'AR', 'B', 'Tronge', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6409', 'AR', 'B', 'Jose Maria Blanco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6411', 'AR', 'B', 'Garre', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6417', 'AR', 'B', 'Casbas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6422', 'AR', 'B', 'Primera Junta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6424', 'AR', 'B', 'Berutti', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6430', 'AR', 'B', 'Adolfo Alsina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6431', 'AR', 'B', 'Lago Epecuen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6435', 'AR', 'B', 'Guamini', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6437', 'AR', 'B', 'Arroyo Venado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6438', 'AR', 'B', 'Masurel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6439', 'AR', 'B', 'Bonifacio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6441', 'AR', 'B', 'Rivera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6443', 'AR', 'B', 'Arano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6450', 'AR', 'B', 'Pehuajo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6451', 'AR', 'B', 'Magdala', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6453', 'AR', 'B', 'Carlos Salas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6455', 'AR', 'B', 'Carlos Tejedor', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6457', 'AR', 'B', 'Timote', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6459', 'AR', 'B', 'Colonia Sere', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6461', 'AR', 'B', 'Cap. Castro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6463', 'AR', 'B', 'Alagon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6465', 'AR', 'B', 'Henderson', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6467', 'AR', 'B', 'Maria Lucila', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6469', 'AR', 'B', 'Asturias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6471', 'AR', 'B', 'La Carreta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6472', 'AR', 'B', 'Francisco Madero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6474', 'AR', 'B', 'Juan Jose Paso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6475', 'AR', 'B', 'Francisco Magnano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6476', 'AR', 'B', 'Guanaco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6500', 'AR', 'B', 'Nueve De Julio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6501', 'AR', 'B', 'Doce De Octubre', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6503', 'AR', 'B', 'Patricios', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6505', 'AR', 'B', 'Dudignac', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6507', 'AR', 'B', 'Morea', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6509', 'AR', 'B', 'Del Valle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6511', 'AR', 'B', 'Villa Sanz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6513', 'AR', 'B', 'La Niña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6515', 'AR', 'B', 'El Tejar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6516', 'AR', 'B', 'Dennehy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6530', 'AR', 'B', 'Carlos Casares', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6531', 'AR', 'B', 'Mauricio Hirsch', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6533', 'AR', 'B', 'Ramon J. Neild', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6535', 'AR', 'B', 'Bellocq', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6537', 'AR', 'B', 'Ordoqui', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6538', 'AR', 'B', 'La Dorita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6550', 'AR', 'B', 'Bolivar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6551', 'AR', 'B', 'Pirovano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6553', 'AR', 'B', 'Urdampilleta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6555', 'AR', 'B', 'Daireaux', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6557', 'AR', 'B', 'Arboleda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6559', 'AR', 'B', 'Recalde', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6561', 'AR', 'B', 'San Bernardo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6600', 'AR', 'B', 'Mercedes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6601', 'AR', 'B', 'Tuyuti', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6603', 'AR', 'B', 'Juan Jose Almeyra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6605', 'AR', 'B', 'Gonzalez Risos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6607', 'AR', 'B', 'Anasagasti', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6608', 'AR', 'B', 'Gowland', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6612', 'AR', 'B', 'Suipacha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6614', 'AR', 'B', 'Goldney', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6616', 'AR', 'B', 'Castilla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6620', 'AR', 'B', 'Chivilcoy - Agencia Aca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6621', 'AR', 'B', 'Henry Bell', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6623', 'AR', 'B', 'Indacochea', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6625', 'AR', 'B', 'Villa Moquehua', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6627', 'AR', 'B', 'Achupallas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6628', 'AR', 'B', 'Palemon Huergo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6632', 'AR', 'B', 'Gorostiaga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6634', 'AR', 'B', 'Alberti', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6640', 'AR', 'B', 'Bragado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6641', 'AR', 'B', 'Comodoro Py', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6643', 'AR', 'B', 'Araujo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6645', 'AR', 'B', 'Maximo Fernandez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6646', 'AR', 'B', 'Warnes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6648', 'AR', 'B', 'Mechita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6652', 'AR', 'B', 'Olascoaga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6660', 'AR', 'B', '25 De Mayo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6661', 'AR', 'B', 'San Enrique', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6663', 'AR', 'B', 'Norberto De La Riestra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6665', 'AR', 'B', 'Ernestina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6667', 'AR', 'B', 'Agustin Mosconi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6700', 'AR', 'B', 'Lujan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6701', 'AR', 'B', 'Carlos Keen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6703', 'AR', 'B', 'Etchegoyen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6705', 'AR', 'B', 'Villa Ruiz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6706', 'AR', 'B', 'Jauregui', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6708', 'AR', 'B', 'Open Door', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6712', 'AR', 'B', 'Villa Espil', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6720', 'AR', 'B', 'San Andres De Giles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6721', 'AR', 'B', 'Azcuenaga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6723', 'AR', 'B', 'Heavy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6725', 'AR', 'B', 'Carmen De Areco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6727', 'AR', 'B', 'Gouin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6734', 'AR', 'B', 'Rawson', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6740', 'AR', 'B', 'Chacabuco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6743', 'AR', 'B', 'Coliqueo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6746', 'AR', 'B', 'Cucha-cucha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6748', 'AR', 'B', 'Membrillar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7000', 'AR', 'B', 'Tandil', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7001', 'AR', 'B', 'La Pastora', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7003', 'AR', 'B', 'Gardey', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7005', 'AR', 'B', 'Claraz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7007', 'AR', 'B', 'San Manuel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7009', 'AR', 'B', 'Iraola', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7011', 'AR', 'B', 'Juan N. Fernandez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7013', 'AR', 'B', 'De La Canal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7020', 'AR', 'B', 'Benito Juarez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7021', 'AR', 'B', 'Alzaga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7100', 'AR', 'B', 'Dolores', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7101', 'AR', 'B', 'Canal 15', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7103', 'AR', 'B', 'Gral. Lavalle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7105', 'AR', 'B', 'San Clemente Del Tuyu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7106', 'AR', 'B', 'Las Toninas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7107', 'AR', 'B', 'Santa Teresita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7108', 'AR', 'B', 'Mar Del Tuyu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7109', 'AR', 'B', 'Mar De Ajo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7111', 'AR', 'B', 'Playa San Bernardo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7112', 'AR', 'B', 'Costa Azul', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7113', 'AR', 'B', 'La Lucila Del Mar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7114', 'AR', 'B', 'Castelli', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7116', 'AR', 'B', 'Lezama', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7118', 'AR', 'B', 'Gral. Guido', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7119', 'AR', 'B', 'Monsalvo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7130', 'AR', 'B', 'Chascomus - Estaf. Nro 4', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7135', 'AR', 'B', 'Don Cipriano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7136', 'AR', 'B', 'Gandara', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7150', 'AR', 'B', 'Ayacucho', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7151', 'AR', 'B', 'Solanet', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7153', 'AR', 'B', 'Fair', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7160', 'AR', 'B', 'Maipu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7161', 'AR', 'B', 'Labarden', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7163', 'AR', 'B', 'Gral. Madariaga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7165', 'AR', 'B', 'Villa Gesell', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7167', 'AR', 'B', 'Pinamar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7169', 'AR', 'B', 'Juancho', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7172', 'AR', 'B', 'Gral. Piran', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7174', 'AR', 'B', 'Cnel. Vidal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7200', 'AR', 'B', 'Las Flores', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7201', 'AR', 'B', 'Miranda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7203', 'AR', 'B', 'Chapaleofu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7205', 'AR', 'B', 'Rosas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7207', 'AR', 'B', 'El Trigo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7208', 'AR', 'B', 'Cnel. Boerr', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7212', 'AR', 'B', 'Doctor Domingo Harosteguy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7214', 'AR', 'B', 'Cachari', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7220', 'AR', 'B', 'Monte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7221', 'AR', 'B', 'Gdor. Udaondo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7223', 'AR', 'B', 'Chas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7225', 'AR', 'B', 'Real Audiencia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7226', 'AR', 'B', 'Zenon Videla Dorna', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7228', 'AR', 'B', 'Abbott', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7240', 'AR', 'B', 'Lobos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7241', 'AR', 'B', 'Salvador Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7243', 'AR', 'B', 'La Blanqueada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7245', 'AR', 'B', 'Roque Perez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7247', 'AR', 'B', 'Carlos Beguerie', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7249', 'AR', 'B', 'Empalme Lobos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7260', 'AR', 'B', 'Saladillo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7261', 'AR', 'B', 'San Benito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7263', 'AR', 'B', 'Gral. Alvear', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7265', 'AR', 'B', 'Del Carril', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7267', 'AR', 'B', 'Juan Blaquier', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7300', 'AR', 'B', 'Azul', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7301', 'AR', 'B', 'Arroyo De Los Huesos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7303', 'AR', 'B', 'Tapalque', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7305', 'AR', 'B', 'Velloso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7307', 'AR', 'B', 'Crotto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7311', 'AR', 'B', 'Chillar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7313', 'AR', 'B', 'Dieciseis De Julio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7316', 'AR', 'B', 'Parish', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7318', 'AR', 'B', 'Hinojo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7400', 'AR', 'B', 'Olavarria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7401', 'AR', 'B', 'Santa Luisa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7403', 'AR', 'B', 'Sierras Bayas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7404', 'AR', 'B', 'San Jorge', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7406', 'AR', 'B', 'Gral. La Madrid', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7407', 'AR', 'B', 'Libano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7408', 'AR', 'B', 'La Colina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7412', 'AR', 'B', 'Voluntad', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7414', 'AR', 'B', 'Laprida', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7500', 'AR', 'B', 'Tres Arroyos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7501', 'AR', 'B', 'Indio Rico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7503', 'AR', 'B', 'Cristiano Muerto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7505', 'AR', 'B', 'Balneario Claromeco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7507', 'AR', 'B', 'Micaela Cascallares', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7509', 'AR', 'B', 'Oriente', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7511', 'AR', 'B', 'Pueblo Balneario Reta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7513', 'AR', 'B', 'Adolfo Gonzalez Chaves', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7515', 'AR', 'B', 'De La Garma', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7517', 'AR', 'B', 'La Sortija', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7519', 'AR', 'B', 'Vasquez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7521', 'AR', 'B', 'San Cayetano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7530', 'AR', 'B', 'Krabbe', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7531', 'AR', 'B', 'El Divisorio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7533', 'AR', 'B', 'Quiñihual', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7535', 'AR', 'B', 'Pontaut', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7536', 'AR', 'B', 'Reserva', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7540', 'AR', 'B', 'Cnel. Suarez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7541', 'AR', 'B', 'Col. Nro 3 Cnel. Suarez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7543', 'AR', 'B', 'La Primavera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7545', 'AR', 'B', 'Zentena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7547', 'AR', 'B', 'Cascada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7548', 'AR', 'B', 'Curumalan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7600', 'AR', 'B', 'Mar Del Plata', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7601', 'AR', 'B', 'Barrio Batan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7603', 'AR', 'B', 'Cdte. Nicanor Otamendi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7605', 'AR', 'B', 'Chapadmalal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7607', 'AR', 'B', 'Mar Del Sud', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7609', 'AR', 'B', 'Balneario Mar Chiquita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7612', 'AR', 'B', 'Vivorata', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7613', 'AR', 'B', 'Ricardo Gaviña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7620', 'AR', 'B', 'Balcarce', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7621', 'AR', 'B', 'Ramos Otero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7623', 'AR', 'B', 'Las Nutrias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7630', 'AR', 'B', 'Necochea', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7631', 'AR', 'B', 'Quequen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7633', 'AR', 'B', 'Pieres', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7635', 'AR', 'B', 'Loberia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7637', 'AR', 'B', 'Nicanor Olivera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7639', 'AR', 'B', 'Lumb', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('7641', 'AR', 'B', 'Energia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8000', 'AR', 'B', 'Bahia Blanca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8101', 'AR', 'B', 'Galvan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8103', 'AR', 'B', 'Ing. White', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8105', 'AR', 'B', 'Gral. Cerri', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8107', 'AR', 'B', 'Base Aerea Cdte. Espora', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8109', 'AR', 'B', 'Punta Alta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8111', 'AR', 'B', 'Arroyo Pareja', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8113', 'AR', 'B', 'Baterias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8115', 'AR', 'B', 'Bajo Hondo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8117', 'AR', 'B', 'Pelicura', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8118', 'AR', 'B', 'Cabildo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8122', 'AR', 'B', 'Naposta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8124', 'AR', 'B', 'Berraondo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8126', 'AR', 'B', 'Villa Iris', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8127', 'AR', 'B', 'Estela', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8129', 'AR', 'B', 'Felipe Sola', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8132', 'AR', 'B', 'Medanos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8134', 'AR', 'B', 'Mascota', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8136', 'AR', 'B', 'Algarrobo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8142', 'AR', 'B', 'Hilario Ascasubi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8144', 'AR', 'B', 'Tte. Origone', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8146', 'AR', 'B', 'Mayor Buratovich', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8148', 'AR', 'B', 'Pedro Luro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8150', 'AR', 'B', 'Cnel. Dorrego', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8151', 'AR', 'B', 'Zubiaurre', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8153', 'AR', 'B', 'Balneario Monte Hermoso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8154', 'AR', 'B', 'Calvo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8156', 'AR', 'B', 'Jose A. Guisasola', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8158', 'AR', 'B', 'Aparicio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8160', 'AR', 'B', 'Tornquist', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8162', 'AR', 'B', 'Garcia Del Rio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8164', 'AR', 'B', 'Dufaur', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8166', 'AR', 'B', 'Saldungaray', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8168', 'AR', 'B', 'Sierra De La Ventana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8170', 'AR', 'B', 'Pigue', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8171', 'AR', 'B', 'Espartillar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8172', 'AR', 'B', 'Arroyo Corto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8174', 'AR', 'B', 'Saavedra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8175', 'AR', 'B', 'Goyena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8180', 'AR', 'B', 'Puan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8181', 'AR', 'B', 'Altavista', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8183', 'AR', 'B', 'Darragueira', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8185', 'AR', 'B', 'Colonia Lapin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8187', 'AR', 'B', 'Bordenave', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8504', 'AR', 'B', 'Faro Segunda Barranca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8506', 'AR', 'B', 'Bahia San Blas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8508', 'AR', 'B', 'Emilio Lamarca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8512', 'AR', 'B', 'Igarzabal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9999', 'AR', 'B', 'San Miguel', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1000', 'AR', 'C', 'Correo Central', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1020', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1030', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1033', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1034', 'AR', 'C', 'Capital Federal', NULL, -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1039', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1041', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1042', 'AR', 'C', 'Capital Federal Congreso', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1053', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1057', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1060', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1061', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1063', 'AR', 'C', 'Av.Juan de Garay 125', '01', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1069', 'AR', 'C', 'Capital federal', '01', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1073', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1078', 'AR', 'C', 'Capital Federal', '01', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1084', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1093', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1095', 'AR', 'C', 'Capital Federal', '011', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1104', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1107', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1115', 'AR', 'C', 'Capital Federal', '01', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1120', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1127', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1147', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1175', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1179', 'AR', 'C', 'Capital Federal', NULL, -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1182', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1186', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1198', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1200', 'AR', 'C', 'Capital Federal', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1401', 'AR', 'C', 'Domingo F. Sarmiento', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1402', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1403', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1404', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1405', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1406', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1407', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1408', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1409', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1410', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1411', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1412', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1413', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1414', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1415', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1416', 'AR', 'C', 'Esteban Echeverria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1417', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1418', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1419', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1420', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1421', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1422', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1423', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1424', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1425', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1426', 'AR', 'C', 'Roberto Arlt', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1427', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1428', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1429', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1430', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1431', 'AR', 'C', 'Alberto Gerchunoff', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1432', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1433', 'AR', 'C', 'Fray Mocho', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1434', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1435', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1436', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1437', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1438', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1439', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1440', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1441', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1442', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1443', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1444', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1445', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1446', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1447', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1448', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1449', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1450', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1451', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1452', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1453', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1454', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1455', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1456', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1457', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1458', 'AR', 'C', 'Eduardo Gutierrez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1459', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1460', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1461', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1462', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1463', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1464', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('1465', 'AR', 'C', 'Capital Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5421', 'AR', 'D', 'La Tranca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5598', 'AR', 'D', 'Desaguadero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5700', 'AR', 'D', 'San Luis', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5701', 'AR', 'D', 'Balde De La Isla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5703', 'AR', 'D', 'Arbol Solo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5705', 'AR', 'D', 'Balde De Quines', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5707', 'AR', 'D', 'Balde De Puertas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5709', 'AR', 'D', 'Lujan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5711', 'AR', 'D', 'Los Molles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5713', 'AR', 'D', 'Puesto Roberto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5715', 'AR', 'D', 'Balde De Escudero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5719', 'AR', 'D', 'Balzora', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5721', 'AR', 'D', 'Alto Pelado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5722', 'AR', 'D', 'Eleodoro Lobos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5724', 'AR', 'D', 'Alto Pencoso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5730', 'AR', 'D', 'Cnel. Alzogaray', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5731', 'AR', 'D', 'El Morro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5733', 'AR', 'D', 'Cramer', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5735', 'AR', 'D', 'Juan Llerena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5736', 'AR', 'D', 'Cdte. Granville', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5743', 'AR', 'D', 'Nueva Escocia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5750', 'AR', 'D', 'La Toma', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5751', 'AR', 'D', 'La Totora', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5753', 'AR', 'D', 'Casa De Piedra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5755', 'AR', 'D', 'San Martin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5759', 'AR', 'D', 'La Esquina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5770', 'AR', 'D', 'Concaran', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5771', 'AR', 'D', 'Guanaco Pampa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5773', 'AR', 'D', 'Guzman', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5775', 'AR', 'D', 'Renca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5777', 'AR', 'D', 'Ojo Del Rio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5779', 'AR', 'D', 'La Chilca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5835', 'AR', 'D', 'El Tala', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5881', 'AR', 'D', 'Merlo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5883', 'AR', 'D', 'Alto Lindo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6216', 'AR', 'D', 'Bagual', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6277', 'AR', 'D', 'Buena Esperanza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6389', 'AR', 'D', 'Anchorena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2100', 'AR', 'E', 'Charigue', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2820', 'AR', 'E', 'Gualeguaychu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2821', 'AR', 'E', 'Arroyo Del Cura', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2823', 'AR', 'E', 'Ceibas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2824', 'AR', 'E', 'Colonia Gdor. Basavilbaso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2826', 'AR', 'E', 'Aldea San Antonio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2828', 'AR', 'E', 'Escriña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2840', 'AR', 'E', 'Gualeguay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2841', 'AR', 'E', 'Aldea Asuncion', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2843', 'AR', 'E', 'Gral. Galarza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2845', 'AR', 'E', 'Gdor. Echague', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2846', 'AR', 'E', 'Holt', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2848', 'AR', 'E', 'Medanos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2852', 'AR', 'E', 'Alarcon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2854', 'AR', 'E', 'Las Mercedes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3100', 'AR', 'E', 'Bajada Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3101', 'AR', 'E', 'Aldea Brasilera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3102', 'AR', 'E', 'Paraná', NULL, -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3103', 'AR', 'E', 'Puiggari', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3105', 'AR', 'E', 'Diamante', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3107', 'AR', 'E', 'Distrito Espinillo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3109', 'AR', 'E', 'Crucesitas 7ma. Seccion', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3111', 'AR', 'E', 'Las Tunas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3113', 'AR', 'E', 'Colonia Celina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3114', 'AR', 'E', 'Aldea Maria Luisa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3116', 'AR', 'E', 'Aldea Eigenfeld', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3117', 'AR', 'E', 'El Taller', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3118', 'AR', 'E', 'Colonia Nueva', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3122', 'AR', 'E', 'Cerrito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3123', 'AR', 'E', 'Aldea Santa Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3125', 'AR', 'E', 'Antonio Tomas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3127', 'AR', 'E', 'Hernandarias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3129', 'AR', 'E', 'Colonia Hernandarias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3132', 'AR', 'E', 'El Pingo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3133', 'AR', 'E', 'Arroyo Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3134', 'AR', 'E', 'Antonio Tomas Sud', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3136', 'AR', 'E', 'Alcaraz Norte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3137', 'AR', 'E', 'Alcaraz Sud', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3138', 'AR', 'E', 'Alcaraz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3142', 'AR', 'E', 'Colonia Avigdor', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3144', 'AR', 'E', 'Arroyo Del Medio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3150', 'AR', 'E', 'Laurencena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3151', 'AR', 'E', 'Antelo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3153', 'AR', 'E', 'Victoria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3155', 'AR', 'E', 'Laguna Del Pescado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3156', 'AR', 'E', 'Betbeder', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3158', 'AR', 'E', 'Colonia La Llave', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3162', 'AR', 'E', 'Chilcas Sud', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3164', 'AR', 'E', 'Camps', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3170', 'AR', 'E', 'Basavilbaso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3172', 'AR', 'E', 'Rocamora', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3174', 'AR', 'E', 'Altamirano Sud', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3176', 'AR', 'E', 'Sola', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3177', 'AR', 'E', 'Guardamonte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3180', 'AR', 'E', 'Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3181', 'AR', 'E', 'Colonia Federal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3183', 'AR', 'E', 'La Calandria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3187', 'AR', 'E', 'La Esmeralda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3188', 'AR', 'E', 'Conscripto Bernardi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3190', 'AR', 'E', 'Arroyo Hondo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3191', 'AR', 'E', 'Colonia Oficial Nro 14', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3192', 'AR', 'E', 'El Quebracho', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3200', 'AR', 'E', 'Concordia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3201', 'AR', 'E', 'Camba Paso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3203', 'AR', 'E', 'Arroyo Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3204', 'AR', 'E', 'Colonia La Gloria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3206', 'AR', 'E', 'Colonia La Argentina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3208', 'AR', 'E', 'Santa Ana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3212', 'AR', 'E', 'El Redomon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3214', 'AR', 'E', 'Estacion Yerua', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3216', 'AR', 'E', 'Gral. Campos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3218', 'AR', 'E', 'Colonia Nueva Alemania', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3228', 'AR', 'E', 'Colonia Ensanche Sauce', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3229', 'AR', 'E', 'Colonia Freitas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3240', 'AR', 'E', 'Villaguay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3241', 'AR', 'E', 'Laguna Larga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3244', 'AR', 'E', 'Libaros', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3246', 'AR', 'E', 'Dominguez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3248', 'AR', 'E', 'Estacion Urquiza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3252', 'AR', 'E', 'Clara', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3254', 'AR', 'E', 'Colonia La Pampa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3260', 'AR', 'E', 'Concepcion Del Uruguay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3261', 'AR', 'E', 'Colonia Elia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3262', 'AR', 'E', 'Palacio San Jose', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3263', 'AR', 'E', 'Primero De Mayo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3265', 'AR', 'E', 'Colonia Hoker', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3267', 'AR', 'E', 'Cañada De Las Ovejas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3269', 'AR', 'E', 'Colonia Bailina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3272', 'AR', 'E', 'Herrera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3280', 'AR', 'E', 'Colon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3281', 'AR', 'E', 'Colonia Hughes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3283', 'AR', 'E', 'Colonia Mabragaña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3285', 'AR', 'E', 'Berduc', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3287', 'AR', 'E', 'Arroyo Concepcion', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5263', 'AR', 'F', 'El Medano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5274', 'AR', 'F', 'Milagro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5275', 'AR', 'F', 'Olpas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5276', 'AR', 'F', 'Castro Barros', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5300', 'AR', 'F', 'La Rioja', '0822', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5301', 'AR', 'F', 'Agua Blanca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5303', 'AR', 'F', 'Anjullon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5304', 'AR', 'F', 'Talamuyuna', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5310', 'AR', 'F', 'Aimogasta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5311', 'AR', 'F', 'Arauco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5313', 'AR', 'F', 'Estacion Mazan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5321', 'AR', 'F', 'Schaqui', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5325', 'AR', 'F', 'Alpasinche', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5327', 'AR', 'F', 'Chaupihuasi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5329', 'AR', 'F', 'Los Robles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5350', 'AR', 'F', 'Villa Union', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5351', 'AR', 'F', 'Banda Florida', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5353', 'AR', 'F', 'El Zapallar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5355', 'AR', 'F', 'El Condado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5357', 'AR', 'F', 'Vinchina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5359', 'AR', 'F', 'Bajo Jague', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5360', 'AR', 'F', 'Chilecito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5361', 'AR', 'F', 'Aicuña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5363', 'AR', 'F', 'Anguinan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5365', 'AR', 'F', 'Famatina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5367', 'AR', 'F', 'Miranda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5369', 'AR', 'F', 'Pagancillo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5372', 'AR', 'F', 'Monogasta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5374', 'AR', 'F', 'Vichigasta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5380', 'AR', 'F', 'Chamical', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5381', 'AR', 'F', 'Bella Vista', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5383', 'AR', 'F', 'Olta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5384', 'AR', 'F', 'Punta De Los Llanos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5385', 'AR', 'F', 'Alcazar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5386', 'AR', 'F', 'Amana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5470', 'AR', 'F', 'Chepes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5471', 'AR', 'F', 'Corral De Isaac', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5473', 'AR', 'F', 'Aguayo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5474', 'AR', 'F', 'Desiderio Tello', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5475', 'AR', 'F', 'Ambil', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5717', 'AR', 'F', 'El Calden', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2132', 'AR', 'G', 'Funes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2185', 'AR', 'G', 'San Jose De La Esquina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2354', 'AR', 'G', 'Argentina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2356', 'AR', 'G', 'Pinto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2357', 'AR', 'G', 'Colonia Santa Rosa Aguirre', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2374', 'AR', 'G', 'Palo Negro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3062', 'AR', 'G', 'Desvio Pozo Dulce', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3064', 'AR', 'G', 'Bandera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3141', 'AR', 'G', 'Agustina Libarona', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3712', 'AR', 'G', 'Cnel. Manuel Leoncio Rico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3714', 'AR', 'G', 'Atahualpa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3731', 'AR', 'G', 'Sachayoj', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3736', 'AR', 'G', 'Campo Del Cielo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3740', 'AR', 'G', 'Quimili', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3741', 'AR', 'G', 'Aerolito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3743', 'AR', 'G', 'Tintina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3745', 'AR', 'G', 'El Hoyo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3747', 'AR', 'G', 'Campo Gallo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3749', 'AR', 'G', 'Campo Alegre', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3752', 'AR', 'G', 'Nasalo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3760', 'AR', 'G', 'Añatuya', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3761', 'AR', 'G', 'El Malacara', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3763', 'AR', 'G', 'Los Juries', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3766', 'AR', 'G', 'Averias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4184', 'AR', 'G', 'El Rincon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4186', 'AR', 'G', 'El Palomar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4187', 'AR', 'G', 'Bobadal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4189', 'AR', 'G', 'Campo Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4195', 'AR', 'G', 'El Remate', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4197', 'AR', 'G', 'Ahi Veremos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4200', 'AR', 'G', 'Santiago Del Estero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4201', 'AR', 'G', 'Aragones', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4203', 'AR', 'G', 'Guampacha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4205', 'AR', 'G', 'Beltran - Loreto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4206', 'AR', 'G', 'Arraga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4208', 'AR', 'G', 'Loreto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4212', 'AR', 'G', 'Guanaco Sombriana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4220', 'AR', 'G', 'Chañar Pozo De Abajo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4221', 'AR', 'G', 'La Donosa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4223', 'AR', 'G', 'Vinara', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4225', 'AR', 'G', 'Amicha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4230', 'AR', 'G', 'Frias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4233', 'AR', 'G', 'Ancajan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4234', 'AR', 'G', 'Lavalle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4237', 'AR', 'G', 'Las Peñas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4238', 'AR', 'G', 'Guasayan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4300', 'AR', 'G', 'La Banda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4301', 'AR', 'G', 'Aguas Coloradas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4302', 'AR', 'G', 'Ardiles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4304', 'AR', 'G', 'Chañar Pozo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4306', 'AR', 'G', 'Cashico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4308', 'AR', 'G', 'Beltran', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4312', 'AR', 'G', 'Forres', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4313', 'AR', 'G', 'Atojpozo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4315', 'AR', 'G', 'Atamisqui', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4317', 'AR', 'G', 'Soconcho', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4319', 'AR', 'G', 'Barrancas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4321', 'AR', 'G', 'Anga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4322', 'AR', 'G', 'Fernandez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4324', 'AR', 'G', 'Garza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4326', 'AR', 'G', 'Caloj', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4328', 'AR', 'G', 'Guañagasta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4332', 'AR', 'G', 'Blanca Pozo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4334', 'AR', 'G', 'Icaño', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4336', 'AR', 'G', 'Abra Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4338', 'AR', 'G', 'Clodomira', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4339', 'AR', 'G', 'Simbolar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4350', 'AR', 'G', 'Suncho Corral', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4351', 'AR', 'G', 'El Pertigo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4353', 'AR', 'G', 'Amama', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4354', 'AR', 'G', 'Colonia El Simbolar Robles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4356', 'AR', 'G', 'Colonia Siegel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4823', 'AR', 'G', 'Villa Guasayan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5250', 'AR', 'G', 'Los Pozos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5251', 'AR', 'G', 'Amiman', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5253', 'AR', 'G', 'Arbol Solo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5255', 'AR', 'G', 'Baez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5257', 'AR', 'G', 'Oratorio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5258', 'AR', 'G', 'Km. 49', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3500', 'AR', 'H', 'Resistencia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3503', 'AR', 'H', 'Barranqueras', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3505', 'AR', 'H', 'Colonia Baranda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3507', 'AR', 'H', 'La Eduviges', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3509', 'AR', 'H', 'Campo El Bermejo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3511', 'AR', 'H', 'Presidencia Roca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3513', 'AR', 'H', 'Cote-lai', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3514', 'AR', 'H', 'Fontana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3515', 'AR', 'H', 'Cap. Solari', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3518', 'AR', 'H', 'Las Palmas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3522', 'AR', 'H', 'Gral. Vedia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3524', 'AR', 'H', 'Puerto Bermejo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3530', 'AR', 'H', 'Quitilipi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3531', 'AR', 'H', 'Colonia Aborigen Chaco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3532', 'AR', 'H', 'Fortin Aguilar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3534', 'AR', 'H', 'Km. 22', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3540', 'AR', 'H', 'Villa Angela', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3543', 'AR', 'H', 'Enrique Urien', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3545', 'AR', 'H', 'Villa Berthet', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3700', 'AR', 'H', 'Presidencia Roque Saenz Peña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3701', 'AR', 'H', 'Colonia Jose Marmol', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3703', 'AR', 'H', 'Fortin Lavalle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3705', 'AR', 'H', 'Colonia Juan Jose Castelli', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3706', 'AR', 'H', 'Avia Terai', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3708', 'AR', 'H', 'Concepcion Del Bermejo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3716', 'AR', 'H', 'Campo Largo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3718', 'AR', 'H', 'Corzuela', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3722', 'AR', 'H', 'Las Breñas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3730', 'AR', 'H', 'Charata', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3732', 'AR', 'H', 'Gral. Pinedo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3733', 'AR', 'H', 'Hermoso Campo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3734', 'AR', 'H', 'Gancedo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9007', 'AR', 'H', 'Garayalde', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5400', 'AR', 'J', 'San Juan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5400RIV', 'AR', 'J', 'Rivadavia', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5401', 'AR', 'J', 'La Isla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5403', 'AR', 'J', 'Calingasta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5405', 'AR', 'J', 'Barreal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5407', 'AR', 'J', 'Bebida', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5409', 'AR', 'J', 'Mogna', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5411', 'AR', 'J', 'La Legua', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5413', 'AR', 'J', 'Chimbas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5415', 'AR', 'J', 'Domingo De Oro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5417', 'AR', 'J', 'Angaco Sud', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5419', 'AR', 'J', 'Albardon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5423', 'AR', 'J', 'Cap. Lazo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5425', 'AR', 'J', 'Villa Gral. Acha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5427', 'AR', 'J', 'Quinto Cuartel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5429', 'AR', 'J', 'Pocito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5431', 'AR', 'J', 'Cañana Honda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5435', 'AR', 'J', 'Campo De Batalla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5436', 'AR', 'J', 'Colonia Zapata', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5438', 'AR', 'J', 'Alto De Sierra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5439', 'AR', 'J', 'Dos Acequias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5442', 'AR', 'J', 'Caucete', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5443', 'AR', 'J', 'Cuyo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5444', 'AR', 'J', 'Bermejo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5446', 'AR', 'J', 'Marayes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5447', 'AR', 'J', 'Astica', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5449', 'AR', 'J', 'Balde Del Rosario', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5460', 'AR', 'J', 'Jachal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5461', 'AR', 'J', 'Boca De La Quebrada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5463', 'AR', 'J', 'Huaco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5465', 'AR', 'J', 'Rodeo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5467', 'AR', 'J', 'Angualasto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5577', 'AR', 'J', 'Rivadavia', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4139', 'AR', 'K', 'Agua Amarilla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4231', 'AR', 'K', 'Albigasta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4235', 'AR', 'K', 'Bella Vista', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4700', 'AR', 'K', 'San Fernando del Valle de Catamarca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4701', 'AR', 'K', 'Amana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4705', 'AR', 'K', 'Antofagasta De La Sierra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4707', 'AR', 'K', 'San Antonio De Fray M. Esquiu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4709', 'AR', 'K', 'Piedra Blanca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4711', 'AR', 'K', 'Casas Viejas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4713', 'AR', 'K', 'Las Pirquitas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4715', 'AR', 'K', 'El Rodeo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4716', 'AR', 'K', 'Amadores', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4718', 'AR', 'K', 'La Merced', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4719', 'AR', 'K', 'Balcosna', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4722', 'AR', 'K', 'La Viña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4723', 'AR', 'K', 'Alijilan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4724', 'AR', 'K', 'Los Angeles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4726', 'AR', 'K', 'Capayan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4728', 'AR', 'K', 'Chumbicha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4740', 'AR', 'K', 'Andalgala', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4741', 'AR', 'K', 'Agua De Las Palomas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4743', 'AR', 'K', 'Alto De La Junta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4750', 'AR', 'K', 'Belen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4751', 'AR', 'K', 'Condor Huasi De Belen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4753', 'AR', 'K', 'Londres', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5260', 'AR', 'K', 'Km. 969', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5261', 'AR', 'K', 'Divisadero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5264', 'AR', 'K', 'Km. 1008', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5265', 'AR', 'K', 'Baviano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5266', 'AR', 'K', 'Quiros', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5315', 'AR', 'K', 'El Pajonal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5317', 'AR', 'K', 'Mutquin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5319', 'AR', 'K', 'Colpes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5331', 'AR', 'K', 'Cerro Negro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5333', 'AR', 'K', 'Copacabana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5340', 'AR', 'K', 'Tinogasta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5341', 'AR', 'K', 'Antinaco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5343', 'AR', 'K', 'Santa Rosa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5345', 'AR', 'K', 'Fiambala', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5625', 'AR', 'K', 'Icaño', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6200', 'AR', 'L', 'Realico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6203', 'AR', 'L', 'Embajador Martini', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6205', 'AR', 'L', 'Ing. Luiggi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6207', 'AR', 'L', 'Alta Italia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6212', 'AR', 'L', 'Adolfo Van Praet', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6213', 'AR', 'L', 'Parera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6214', 'AR', 'L', 'Chamaico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6220', 'AR', 'L', 'Bernardo Larroude', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6221', 'AR', 'L', 'Ceballos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6228', 'AR', 'L', 'Cnel. Hilario Lagos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6300', 'AR', 'L', 'Santa Rosa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6301', 'AR', 'L', 'Ataliva Roca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6303', 'AR', 'L', 'Cachirulo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6305', 'AR', 'L', 'Atreuco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6307', 'AR', 'L', 'Macachin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6309', 'AR', 'L', 'Alpachiri', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6311', 'AR', 'L', 'Colonia Santa Teresa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6313', 'AR', 'L', 'Winifreda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6315', 'AR', 'L', 'Colonia Baron', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6317', 'AR', 'L', 'Luan Toro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6319', 'AR', 'L', 'Carro Quemado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6321', 'AR', 'L', 'Telen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6323', 'AR', 'L', 'Algarrobo Del Aguila', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6325', 'AR', 'L', 'Naico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6326', 'AR', 'L', 'Anguil', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6330', 'AR', 'L', 'Catrilo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6331', 'AR', 'L', 'Miguel Cane', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6333', 'AR', 'L', 'Alfredo Peña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6352', 'AR', 'L', 'Lonquimay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6354', 'AR', 'L', 'Uriburu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6360', 'AR', 'L', 'Gral. Pico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6361', 'AR', 'L', 'Agustoni', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6365', 'AR', 'L', 'Dorila', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6367', 'AR', 'L', 'Metileo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6369', 'AR', 'L', 'Trenel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6380', 'AR', 'L', 'Boeuf', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6381', 'AR', 'L', 'Conhelo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6383', 'AR', 'L', 'Monte Nievas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6385', 'AR', 'L', 'Arata', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6387', 'AR', 'L', 'Caleufu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8138', 'AR', 'L', 'Anzoategui', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8200', 'AR', 'L', 'Gral. Acha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8201', 'AR', 'L', 'Colonia 25 De Mayo (aca)', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8203', 'AR', 'L', 'Utracan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8204', 'AR', 'L', 'Bernasconi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8206', 'AR', 'L', 'Gral. San Martin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8208', 'AR', 'L', 'Jacinto Arauz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8212', 'AR', 'L', 'Abramo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8214', 'AR', 'L', 'Colonia Santa Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8307', 'AR', 'L', 'Puelen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5500', 'AR', 'M', 'Mendoza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5501', 'AR', 'M', 'Godoy Cruz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5503', 'AR', 'M', 'Paso De Los Andes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5505', 'AR', 'M', 'Carrodilla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5507', 'AR', 'M', 'Lujan De Cuyo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5509', 'AR', 'M', 'Agrelo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5511', 'AR', 'M', 'Gral. Gutierrez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5513', 'AR', 'M', 'Barrio Jardin Luzuriaga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5515', 'AR', 'M', 'Maipu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5517', 'AR', 'M', 'Barrancas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5519', 'AR', 'M', 'Cnel. Dorrego', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5521', 'AR', 'M', 'Villa Nueva De Guaymallen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5523', 'AR', 'M', 'Buena Nueva', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5525', 'AR', 'M', 'Colonia Segovia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5527', 'AR', 'M', 'Colonia Santa Teresa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5529', 'AR', 'M', 'Pedregal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5531', 'AR', 'M', 'Blanco Encalada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5532', 'AR', 'M', 'Los Eucaliptos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5533', 'AR', 'M', 'Bermejo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5535', 'AR', 'M', 'Costa De Araujo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5537', 'AR', 'M', 'Arroyito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5539', 'AR', 'M', 'Espejo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5541', 'AR', 'M', 'El Algarrobal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5543', 'AR', 'M', 'Capdeville', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5544', 'AR', 'M', 'Gdor. Benegas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5545', 'AR', 'M', 'Termas Villavicencio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5547', 'AR', 'M', 'Villa Hipodromo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5549', 'AR', 'M', 'Cacheuta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5551', 'AR', 'M', 'Estacion Uspallata', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5553', 'AR', 'M', 'Punta De Vacas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5555', 'AR', 'M', 'Puente Del Inca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5557', 'AR', 'M', 'Las Cuevas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5560', 'AR', 'M', 'Tunuyan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5561', 'AR', 'M', 'Cordon Del Plata', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5563', 'AR', 'M', 'Los Arboles De Villegas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5565', 'AR', 'M', 'Campo Los Andes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5567', 'AR', 'M', 'La Consulta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5569', 'AR', 'M', 'Chilecito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5570', 'AR', 'M', 'Buen Orden', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5571', 'AR', 'M', 'Chivilcoy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5573', 'AR', 'M', 'Villa De Junin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5575', 'AR', 'M', 'Andrade', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5579', 'AR', 'M', 'Campamentos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5582', 'AR', 'M', 'Alto Verde', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5584', 'AR', 'M', 'Palmira', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5585', 'AR', 'M', 'Los Barriales', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5587', 'AR', 'M', 'Barcala', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5589', 'AR', 'M', 'Chapanay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5590', 'AR', 'M', 'Cadetes De Chile', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5592', 'AR', 'M', 'La Dormida', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5594', 'AR', 'M', 'Cdte. Salas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5595', 'AR', 'M', 'Ñacuñan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5596', 'AR', 'M', 'La Costa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5600', 'AR', 'M', 'San Rafael', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5601', 'AR', 'M', 'Cap. Montoya', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5603', 'AR', 'M', 'Colonia Elena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5605', 'AR', 'M', 'Balloffet', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5607', 'AR', 'M', 'Cuadro Nacional', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5609', 'AR', 'M', 'Aristides Villanueva', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5611', 'AR', 'M', 'Bardas Blancas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5613', 'AR', 'M', 'Malargue', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5615', 'AR', 'M', 'Colonia Pascual Lacarini', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5620', 'AR', 'M', 'El Juncalito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5621', 'AR', 'M', 'Agua Escondida', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5622', 'AR', 'M', 'Villa Atuel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5623', 'AR', 'M', 'Colonia Lopez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5624', 'AR', 'M', 'Palermo Chico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5632', 'AR', 'M', 'Colonia Alvear Oeste', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5634', 'AR', 'M', 'Bowen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5636', 'AR', 'M', 'Canalejas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5637', 'AR', 'M', 'Corral De Lorca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('M5620DFC', 'AR', 'M', 'General Alvear', '02625', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3300', 'AR', 'N', 'Posadas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3304', 'AR', 'N', 'Fachinal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3306', 'AR', 'N', 'Parada Leis', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3308', 'AR', 'N', 'Candelaria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3309', 'AR', 'N', 'Bella Vista', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3311', 'AR', 'N', 'Picada Galitziana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3313', 'AR', 'N', 'Arroyo Del Medio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3315', 'AR', 'N', 'Dos Arroyos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3316', 'AR', 'N', 'Loreto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3317', 'AR', 'N', 'Bonpland', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3318', 'AR', 'N', 'Colonia Martires', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3322', 'AR', 'N', 'Colonia Domingo Savio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3324', 'AR', 'N', 'Gdor. Roca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3326', 'AR', 'N', 'Colonia Polana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3327', 'AR', 'N', 'Corpus', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3328', 'AR', 'N', 'Hipolito Yrigoyen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3332', 'AR', 'N', 'Cainguas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3334', 'AR', 'N', 'Puerto Rico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3350', 'AR', 'N', 'Apostoles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3353', 'AR', 'N', 'Arroyo Santa Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3355', 'AR', 'N', 'Concepcion De La Sierra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3357', 'AR', 'N', 'San Javier', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3358', 'AR', 'N', 'Estacion Apostoles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3360', 'AR', 'N', 'Obera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3361', 'AR', 'N', 'Campo Ramon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3362', 'AR', 'N', 'Campo Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3363', 'AR', 'N', 'Alba Posse', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3364', 'AR', 'N', 'Aristobulo Del Valle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3366', 'AR', 'N', 'Bernardo De Irigoyen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3370', 'AR', 'N', 'Iguazu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3371', 'AR', 'N', 'Cabure I', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3374', 'AR', 'N', 'Libertad', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3376', 'AR', 'N', 'Colonia Wanda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3378', 'AR', 'N', 'Puerto Esperanza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3380', 'AR', 'N', 'Eldorado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3381', 'AR', 'N', 'Colonia Maria Magdalena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3382', 'AR', 'N', 'Colonia Victoria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3384', 'AR', 'N', 'Montecarlo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3386', 'AR', 'N', 'Colonia Caraguatay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3526', 'AR', 'P', 'Cabo Adriano Ayala', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3600', 'AR', 'P', 'Formosa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3601', 'AR', 'P', 'Banco Payagua', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3603', 'AR', 'P', 'El Colorado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3604', 'AR', 'P', 'Gran Guardia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3606', 'AR', 'P', 'Loma Senes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3608', 'AR', 'P', 'Desvio Los Matacos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3610', 'AR', 'P', 'Clorinda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3611', 'AR', 'P', 'Florentino Ameghino', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3613', 'AR', 'P', 'Laguna Blanca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3615', 'AR', 'P', 'Buena Vista', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3620', 'AR', 'P', 'Cdte. Fontana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3621', 'AR', 'P', 'Fortin Lugones', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3622', 'AR', 'P', 'Bartolome De Las Casas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3624', 'AR', 'P', 'Ibarreta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3626', 'AR', 'P', 'Colonia Union Escuela', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3628', 'AR', 'P', 'Paso De Naite', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3630', 'AR', 'P', 'Las Lomitas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3632', 'AR', 'P', 'Chiriguanos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3634', 'AR', 'P', 'Laguna Yema', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3636', 'AR', 'P', 'Gral. Enrique Mosconi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8300', 'AR', 'Q', 'Neuquen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8301', 'AR', 'Q', 'Colonia Valentina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8305', 'AR', 'Q', 'Añelo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8309', 'AR', 'Q', 'Centenario', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8311', 'AR', 'Q', 'Villa El Chocon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8313', 'AR', 'Q', 'Picun Leufu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8315', 'AR', 'Q', 'Piedra Del Aguila', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8316', 'AR', 'Q', 'Plotier', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8318', 'AR', 'Q', 'Plaza Huincul', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8319', 'AR', 'Q', 'Cpto.nro 1 Y.p.f.plaza Huincul', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8322', 'AR', 'Q', 'Cutral-co', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8340', 'AR', 'Q', 'Zapala', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8341', 'AR', 'Q', 'Espinazo Del Zorro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8345', 'AR', 'Q', 'Alumine', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8347', 'AR', 'Q', 'Las Lajas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8349', 'AR', 'Q', 'Copahue', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8351', 'AR', 'Q', 'Bajada Del Agrio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8353', 'AR', 'Q', 'Barrancas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8370', 'AR', 'Q', 'San Martin De Los Andes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8371', 'AR', 'Q', 'Junin De Los Andes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8373', 'AR', 'Q', 'Pampa Del Malleo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8375', 'AR', 'Q', 'Huechahue', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8401', 'AR', 'Q', 'El Cruce', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8403', 'AR', 'Q', 'Traful', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8407', 'AR', 'Q', 'Villa La Angostura', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8303', 'AR', 'R', 'Cinco Saltos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8324', 'AR', 'R', 'Cipolletti', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8326', 'AR', 'R', 'Cervantes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8328', 'AR', 'R', 'Allen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8332', 'AR', 'R', 'Gral. Roca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8333', 'AR', 'R', 'Aguada Guzman', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8334', 'AR', 'R', 'Ing. Huergo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8336', 'AR', 'R', 'Villa Regina', NULL, -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8360', 'AR', 'R', 'Choele Choel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8361', 'AR', 'R', 'Luis Beltran', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8363', 'AR', 'R', 'Colonia Josefa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8364', 'AR', 'R', 'Cnel. Belisle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8366', 'AR', 'R', 'Chelforo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8400', 'AR', 'R', 'San Carlos De Bariloche', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8409', 'AR', 'R', 'Llao Llao', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8411', 'AR', 'R', 'Puerto Blest', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8412', 'AR', 'R', 'Las Bayas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8415', 'AR', 'R', 'Cerro Mesa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8416', 'AR', 'R', 'Clemente Onelli', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8417', 'AR', 'R', 'Cañadon Chileno', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8418', 'AR', 'R', 'Ing. Jacobacci', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8422', 'AR', 'R', 'Maquinchao', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8424', 'AR', 'R', 'Aguada De Guerra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8430', 'AR', 'R', 'El Bolson', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8500', 'AR', 'R', 'Viedma', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8501', 'AR', 'R', 'Balneario El Condor', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8503', 'AR', 'R', 'Gral. Conesa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8505', 'AR', 'R', 'Boca De La Travesia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8514', 'AR', 'R', 'Gral. Lorenzo Vintter', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8520', 'AR', 'R', 'San Antonio Oeste', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8521', 'AR', 'R', 'Arroyo De La Ventana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8532', 'AR', 'R', 'Sierra Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8534', 'AR', 'R', 'Sierra Colorada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8536', 'AR', 'R', 'Musters', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2000', 'AR', 'S', 'Rosario', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2101', 'AR', 'S', 'Albarellos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2103', 'AR', 'S', 'Cnel. Bogado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2105', 'AR', 'S', 'Cañada Rica', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2107', 'AR', 'S', 'Alvarez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2109', 'AR', 'S', 'Acebal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2111', 'AR', 'S', 'Santa Teresa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2113', 'AR', 'S', 'Peyrano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2115', 'AR', 'S', 'Maximo Paz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2117', 'AR', 'S', 'Alcorta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2119', 'AR', 'S', 'Arminda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2121', 'AR', 'S', 'Perez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2123', 'AR', 'S', 'Cnel. Arnold', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2123ZAV', 'AR', 'S', 'Zavalla', '', -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2124', 'AR', 'S', 'Villa Gdor. Galvez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2126', 'AR', 'S', 'Alvear', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2128', 'AR', 'S', 'Arroyo Seco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2134', 'AR', 'S', 'Roldan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2136', 'AR', 'S', 'San Jeronimo Sud', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2138', 'AR', 'S', 'Carcaraña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2142', 'AR', 'S', 'Ibarlucea', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2144', 'AR', 'S', 'Larguia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2146', 'AR', 'S', 'Clason', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2147', 'AR', 'S', 'San Genaro Norte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2148', 'AR', 'S', 'Casas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2152', 'AR', 'S', 'Estacion Aeronautica Paganini', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2154', 'AR', 'S', 'Cap. Bermudez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2156', 'AR', 'S', 'Fray Luis Beltran', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2170', 'AR', 'S', 'Casilda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2173', 'AR', 'S', 'Chabas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2175', 'AR', 'S', 'Villa Mugueta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2177', 'AR', 'S', 'Bigand', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2179', 'AR', 'S', 'Bombal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2181', 'AR', 'S', 'Los Molinos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2183', 'AR', 'S', 'Arequito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2187', 'AR', 'S', 'Arteaga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2200', 'AR', 'S', 'San Lorenzo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2201', 'AR', 'S', 'Ricardone', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2202', 'AR', 'S', 'Puerto Gral. San Martin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2204', 'AR', 'S', 'Timbues', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2206', 'AR', 'S', 'Oliveros', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2208', 'AR', 'S', 'Gaboto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2212', 'AR', 'S', 'Monje', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2214', 'AR', 'S', 'Aldao', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2216', 'AR', 'S', 'Serodino', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2218', 'AR', 'S', 'Carrizales', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2222', 'AR', 'S', 'Diaz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2240', 'AR', 'S', 'Coronda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2241', 'AR', 'S', 'Larrechea', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2242', 'AR', 'S', 'Arijon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2246', 'AR', 'S', 'Barrancas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2248', 'AR', 'S', 'Bernardo De Irigoyen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2252', 'AR', 'S', 'Galvez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2253', 'AR', 'S', 'Gessler', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2255', 'AR', 'S', 'Lopez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2257', 'AR', 'S', 'Colonia Belgrano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2258', 'AR', 'S', 'Santa Clara De Buena Vista', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2300', 'AR', 'S', 'Barrio Puzzi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2301', 'AR', 'S', 'Colonia Castellanos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2302', 'AR', 'S', 'Rafaela', NULL, -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2303', 'AR', 'S', 'Angelica', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2305', 'AR', 'S', 'Lehmann', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2307', 'AR', 'S', 'Ataliva', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2309', 'AR', 'S', 'Humberto 1ro.', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2311', 'AR', 'S', 'Capivara', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2313', 'AR', 'S', 'Moises Ville', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2315', 'AR', 'S', 'Estacion Saguier', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2317', 'AR', 'S', 'Casablanca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2318', 'AR', 'S', 'Aurelia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2322', 'AR', 'S', 'Cabaña El Cisne', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2324', 'AR', 'S', 'Colonia Taculares', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2326', 'AR', 'S', 'Colonia Bossi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2340', 'AR', 'S', 'Ceres', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2341', 'AR', 'S', 'Colonia Montefiore', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2342', 'AR', 'S', 'Curupaity', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2344', 'AR', 'S', 'Arrufo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2345', 'AR', 'S', 'Villa Trinidad', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2347', 'AR', 'S', 'Colonia Rosa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2349', 'AR', 'S', 'Monte Obscuridad', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2352', 'AR', 'S', 'Ambrosetti', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2401', 'AR', 'S', 'Colonia Castelar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2403', 'AR', 'S', 'Bauer Y Sigel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2405', 'AR', 'S', 'Colonia Cello', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2407', 'AR', 'S', 'Clucellas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2409', 'AR', 'S', 'Estrada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2438', 'AR', 'S', 'Frontera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2440', 'AR', 'S', 'Sastre', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2441', 'AR', 'S', 'Crispi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2443', 'AR', 'S', 'Colonia Margarita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2445', 'AR', 'S', 'Maria Juana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2447', 'AR', 'S', 'Los Sembrados', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2449', 'AR', 'S', 'San Martin De Las Escobas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2451', 'AR', 'S', 'Las Petacas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2453', 'AR', 'S', 'Carlos Pellegrini', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2454', 'AR', 'S', 'Cañada Rosquin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2456', 'AR', 'S', 'Esmeralda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2500', 'AR', 'S', 'Cañada De Gomez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2501', 'AR', 'S', 'Maria Luisa Correa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2503', 'AR', 'S', 'Villa Eloisa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2505', 'AR', 'S', 'Las Parejas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2506', 'AR', 'S', 'Correa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2508', 'AR', 'S', 'Armstrong', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2512', 'AR', 'S', 'Tortugas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2520', 'AR', 'S', 'La California', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2521', 'AR', 'S', 'Iturraspe', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2523', 'AR', 'S', 'Bouquet', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2527', 'AR', 'S', 'Maria Susana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2529', 'AR', 'S', 'Piamonte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2531', 'AR', 'S', 'Landeta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2533', 'AR', 'S', 'Los Cardos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2535', 'AR', 'S', 'El Trebol', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2600', 'AR', 'S', 'Venado Tuerto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2601', 'AR', 'S', 'La Chispa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2603', 'AR', 'S', 'Chapuy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2605', 'AR', 'S', 'Rastreador Fournier', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2607', 'AR', 'S', 'Villa Cañas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2609', 'AR', 'S', 'Colonia Morgan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2611', 'AR', 'S', 'Runciman', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2613', 'AR', 'S', 'San Gregorio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2615', 'AR', 'S', 'San Eduardo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2617', 'AR', 'S', 'Sancti Spiritu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2618', 'AR', 'S', 'Carmen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2622', 'AR', 'S', 'Maggiolo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2630', 'AR', 'S', 'Firmat', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2631', 'AR', 'S', 'Pueblo Miguel Torres', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2633', 'AR', 'S', 'Chovet', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2635', 'AR', 'S', 'Cañada Del Ucle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2637', 'AR', 'S', 'Colonia Hansen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2639', 'AR', 'S', 'Berabevu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2643', 'AR', 'S', 'Cafferata', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2722', 'AR', 'S', 'Wheelwright', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2723', 'AR', 'S', 'Juncal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2725', 'AR', 'S', 'Hughes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2726', 'AR', 'S', 'Labordeboy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2728', 'AR', 'S', 'Melincue', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2729', 'AR', 'S', 'Carreras', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2732', 'AR', 'S', 'El Jardin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2918', 'AR', 'S', 'Empalme Villa Constitucion', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2919', 'AR', 'S', 'Villa Constitucion', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2921', 'AR', 'S', 'Godoy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3000', 'AR', 'S', 'Santa Fe', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3001', 'AR', 'S', 'Alto Verde', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3003', 'AR', 'S', 'Helvecia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3005', 'AR', 'S', 'Colonia Francesa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3007', 'AR', 'S', 'Empalme San Carlos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3009', 'AR', 'S', 'Franck', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3011', 'AR', 'S', 'San Mariano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3013', 'AR', 'S', 'Colonia Matilde', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3014', 'AR', 'S', 'Angel Gallardo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3016', 'AR', 'S', 'San Jose', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3017', 'AR', 'S', 'Sauce Viejo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3018', 'AR', 'S', 'Candioti', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3020', 'AR', 'S', 'Reynaldo Cullen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3021', 'AR', 'S', 'Campo Andino', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3023', 'AR', 'S', 'Cululu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3025', 'AR', 'S', 'Maria Luisa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3027', 'AR', 'S', 'La Pelada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3029', 'AR', 'S', 'Desvio Arauz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3032', 'AR', 'S', 'Nelson', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3036', 'AR', 'S', 'Aromos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3038', 'AR', 'S', 'Cayastacito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3040', 'AR', 'S', 'San Justo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3041', 'AR', 'S', 'Cacique Ariacaiquin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3042', 'AR', 'S', 'Abipones', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3044', 'AR', 'S', 'Gdor. Crespo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3045', 'AR', 'S', 'Colonia Dolores', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3046', 'AR', 'S', 'Las Cañas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3048', 'AR', 'S', 'Luciano Leiva', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3050', 'AR', 'S', 'Calchaqui', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3051', 'AR', 'S', 'Alejandra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3052', 'AR', 'S', 'Colonia La Blanca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3054', 'AR', 'S', 'Colonia La Negra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3056', 'AR', 'S', 'Colonia La Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3057', 'AR', 'S', 'La Gallareta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3060', 'AR', 'S', 'Independencia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3061', 'AR', 'S', 'Antonio Pini', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3066', 'AR', 'S', 'Campo Garay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3070', 'AR', 'S', 'San Cristobal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3071', 'AR', 'S', 'Aguara Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3072', 'AR', 'S', 'La Lucila', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3074', 'AR', 'S', 'La Cabral', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3076', 'AR', 'S', 'Huanqueros', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3080', 'AR', 'S', 'Esperanza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3081', 'AR', 'S', 'Cavour', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3083', 'AR', 'S', 'Grutly', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3085', 'AR', 'S', 'Pilar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3087', 'AR', 'S', 'Felicia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3089', 'AR', 'S', 'Ing. Boasi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3516', 'AR', 'S', 'Florencia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3536', 'AR', 'S', 'Fortin Charrua', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3541', 'AR', 'S', 'Gato Colorado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3550', 'AR', 'S', 'Vera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3551', 'AR', 'S', 'Cañada Ombu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3553', 'AR', 'S', 'Campo Duran', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3555', 'AR', 'S', 'Las Palmas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3557', 'AR', 'S', 'Caraguatay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3560', 'AR', 'S', 'Reconquista', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3561', 'AR', 'S', 'Avellaneda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3563', 'AR', 'S', 'Colonia San Manuel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3565', 'AR', 'S', 'El Tajamar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3567', 'AR', 'S', 'Destaca. Aer. Mil. Reconquista', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3569', 'AR', 'S', 'Berna', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3572', 'AR', 'S', 'Campo Ramseyer', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3574', 'AR', 'S', 'Guadalupe Norte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3575', 'AR', 'S', 'Arroyo Ceibal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3580', 'AR', 'S', 'Villa Ocampo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3581', 'AR', 'S', 'Campo Redondo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3583', 'AR', 'S', 'Villa Ana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3585', 'AR', 'S', 'El Sombrerito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3586', 'AR', 'S', 'Las Toscas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3587', 'AR', 'S', 'San Antonio De Obligado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3589', 'AR', 'S', 'Villa Guillermina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3592', 'AR', 'S', 'Campo Hardy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3765', 'AR', 'S', 'Tomas Young', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6009', 'AR', 'S', 'San Marcelo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6036', 'AR', 'S', 'Diego De Alvear', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6039', 'AR', 'S', 'Christophersen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6100', 'AR', 'S', 'Rufino', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6103', 'AR', 'S', 'Amenabar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6106', 'AR', 'S', 'Castellanos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4000', 'AR', 'T', 'San Miguel De Tucuman', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4101', 'AR', 'T', 'Alta Gracia - Burruyacu', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4103', 'AR', 'T', 'Tafi Viejo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4105', 'AR', 'T', 'Barrio Miguel Lillo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4107', 'AR', 'T', 'Yerba Buena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4109', 'AR', 'T', 'Banda Del Rio Sali', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4111', 'AR', 'T', 'Colombres', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4113', 'AR', 'T', 'El Guardamonte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4115', 'AR', 'T', 'Agua Dulce', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4117', 'AR', 'T', 'Delfin Gallo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4119', 'AR', 'T', 'Benjamin Araoz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4122', 'AR', 'T', 'Benjamin Paz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4124', 'AR', 'T', 'Leocadio Paz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4128', 'AR', 'T', 'Lules', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4129', 'AR', 'T', 'Ingenio Lules', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4132', 'AR', 'T', 'El Cruce', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4133', 'AR', 'T', 'Padilla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4134', 'AR', 'T', 'Acheral', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4135', 'AR', 'T', 'Caspinchango', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4137', 'AR', 'T', 'Amaicha Del Valle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4142', 'AR', 'T', 'Cap. Caceres', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4143', 'AR', 'T', 'Independencia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4144', 'AR', 'T', 'Amberes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4145', 'AR', 'T', 'Rio Seco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4146', 'AR', 'T', 'Concepcion', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4147', 'AR', 'T', 'Arcadia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4149', 'AR', 'T', 'Alpachiri', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4151', 'AR', 'T', 'Los Gucheas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4152', 'AR', 'T', 'Aguilares', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4153', 'AR', 'T', 'Alto Verde', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4155', 'AR', 'T', 'Ingenio Santa Ana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4157', 'AR', 'T', 'Ingenio Santa Barbara', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4158', 'AR', 'T', 'El Batiruano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4159', 'AR', 'T', 'Campo Bello', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4161', 'AR', 'T', 'Domingo Millan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4162', 'AR', 'T', 'La Cocha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4163', 'AR', 'T', 'Huasa Pampa Norte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4164', 'AR', 'T', 'Huasa Pampa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4166', 'AR', 'T', 'Manchala', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4168', 'AR', 'T', 'Amaicha Del Llano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4171', 'AR', 'T', 'Buena Vista', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4172', 'AR', 'T', 'Macio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4174', 'AR', 'T', 'Arroyo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4176', 'AR', 'T', 'Arboles Grandes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4178', 'AR', 'T', 'Alderetes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4182', 'AR', 'T', 'Finca Mayo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4242', 'AR', 'T', 'Taco Ralo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('8431', 'AR', 'U', 'Lago Puelo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9000', 'AR', 'U', 'Comodoro Rivadavia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9001', 'AR', 'U', 'Astra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9003', 'AR', 'U', 'Comodoro Rivadavia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9009', 'AR', 'U', 'Cañadon Lagarto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9020', 'AR', 'U', 'Valle Hermoso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9021', 'AR', 'U', 'Colhue Huapi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9023', 'AR', 'U', 'Buen Pasto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9030', 'AR', 'U', 'Sgto. Rugestein', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9031', 'AR', 'U', 'Facundo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9033', 'AR', 'U', 'Alto Rio Senguer', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9035', 'AR', 'U', 'Doctor Ricardo Rojas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9037', 'AR', 'U', 'Alto Rio Mayo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9100', 'AR', 'U', 'Trelew', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9101', 'AR', 'U', 'Bajada Del Diablo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9103', 'AR', 'U', 'Rawson', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9105', 'AR', 'U', 'Gaiman', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9107', 'AR', 'U', 'Dolavon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9111', 'AR', 'U', 'Cabo Raso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9113', 'AR', 'U', 'Florentino Ameghino', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9120', 'AR', 'U', 'Puerto Madryn', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9121', 'AR', 'U', 'El Escorial', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9200', 'AR', 'U', 'Esquel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9201', 'AR', 'U', 'Cajo De Ginebre Chico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9203', 'AR', 'U', 'Trevelin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9207', 'AR', 'U', 'Cerro Condor', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9210', 'AR', 'U', 'El Maiten', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9211', 'AR', 'U', 'Cushamen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9213', 'AR', 'U', 'Lepa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9217', 'AR', 'U', 'Cholila', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9220', 'AR', 'U', 'Jose De San Martin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9221', 'AR', 'U', 'Valle Hondo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9223', 'AR', 'U', 'Alto Rio Pico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9225', 'AR', 'U', 'Frontera De Rio Pico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9227', 'AR', 'U', 'La Laurita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9297', 'AR', 'U', 'Paso Moreno', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9339', 'AR', 'U', 'Lago Blanco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9410', 'AR', 'V', 'Petrel - Agencia Aca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9411', 'AR', 'V', 'Base Aerea Tte. Matienzo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9420', 'AR', 'V', 'Rio Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9421', 'AR', 'V', 'Frigorifico C.a.p.', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3185', 'AR', 'W', 'Rincon De Tunas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3194', 'AR', 'W', 'Guayquiraro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3196', 'AR', 'W', 'Esquina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3197', 'AR', 'W', 'Cnel. Abraham Schweizer', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3199', 'AR', 'W', 'Los Laureles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3220', 'AR', 'W', 'El Ceibo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3222', 'AR', 'W', 'Juan Pujol', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3224', 'AR', 'W', 'Colonia Libertad', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3226', 'AR', 'W', 'Mocoreta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3230', 'AR', 'W', 'Paso De Los Libres', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3231', 'AR', 'W', 'Mirunga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3232', 'AR', 'W', 'Cabred', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3234', 'AR', 'W', 'Paso Ledesma', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3302', 'AR', 'W', 'Apipe Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3340', 'AR', 'W', 'Santo Tome', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3342', 'AR', 'W', 'Caza Pava', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3344', 'AR', 'W', 'Alvear', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3346', 'AR', 'W', 'La Cruz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3351', 'AR', 'W', 'Garruchos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3400', 'AR', 'W', 'Corrientes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3401', 'AR', 'W', 'Arroyo Ponton', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3403', 'AR', 'W', 'Cavia Cue', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3405', 'AR', 'W', 'Cerrito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3407', 'AR', 'W', 'Capillita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3409', 'AR', 'W', 'Paso De La Patria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3412', 'AR', 'W', 'Ensenada Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3414', 'AR', 'W', 'Itati', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3416', 'AR', 'W', 'El Sombrero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3418', 'AR', 'W', 'Empedrado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3420', 'AR', 'W', 'Saladas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3421', 'AR', 'W', 'Batel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3423', 'AR', 'W', 'Concepcion', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3425', 'AR', 'W', 'Loma Alta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3427', 'AR', 'W', 'El Pago', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3428', 'AR', 'W', 'Estacion Saladas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3433', 'AR', 'W', 'Carrizal Norte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3440', 'AR', 'W', 'Colonia Cecilio Echevarria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3441', 'AR', 'W', 'Cruz De Los Milagros', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3443', 'AR', 'W', 'Lavalle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3445', 'AR', 'W', 'Costa Batel', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3446', 'AR', 'W', 'Manuel Florencio Mantilla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3448', 'AR', 'W', 'Arroyito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3449', 'AR', 'W', 'Boliche Lata', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3450', 'AR', 'W', 'Colonia Mercedes Cossio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3451', 'AR', 'W', 'Colonia Carolina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3453', 'AR', 'W', 'Ifran', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3454', 'AR', 'W', 'Buena Esperanza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3460', 'AR', 'W', 'Curuzu-cuatia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3461', 'AR', 'W', 'Perugorria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3463', 'AR', 'W', 'Sauce', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3465', 'AR', 'W', 'Cap. Joaquin Madariaga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3466', 'AR', 'W', 'Acuña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3470', 'AR', 'W', 'Mercedes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3471', 'AR', 'W', 'Alen Cue', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3472', 'AR', 'W', 'Felipe Yofre', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3474', 'AR', 'W', 'Chavarria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3476', 'AR', 'W', 'Solari', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3480', 'AR', 'W', 'Ita-ibate', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3481', 'AR', 'W', 'Arerungua', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3483', 'AR', 'W', 'Loreto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3485', 'AR', 'W', 'Colonia El Caiman', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('3486', 'AR', 'W', 'Villa Olivari', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2189', 'AR', 'X', 'Cruz Alta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2400', 'AR', 'X', 'San Francisco', NULL, -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2411', 'AR', 'X', 'Luxardo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2413', 'AR', 'X', 'Colonia Anita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2415', 'AR', 'X', 'Porteña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2417', 'AR', 'X', 'Altos De Chipion', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2419', 'AR', 'X', 'Brinkmann', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2421', 'AR', 'X', 'Morteros', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2423', 'AR', 'X', 'Colonia Prosperidad', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2424', 'AR', 'X', 'Colonia Marina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2426', 'AR', 'X', 'Colonia San Bartolome', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2428', 'AR', 'X', 'El Fuertecito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2432', 'AR', 'X', 'El Tio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2433', 'AR', 'X', 'Las Delicias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2434', 'AR', 'X', 'Arroyito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2435', 'AR', 'X', 'Colonia Coyunda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2436', 'AR', 'X', 'Plaza Bruno', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2525', 'AR', 'X', 'Saira', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2550', 'AR', 'X', 'Bell Ville', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2551', 'AR', 'X', 'Cuatro Caminos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2553', 'AR', 'X', 'Justiniano Posse', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2555', 'AR', 'X', 'Ordoñez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2557', 'AR', 'X', 'Idiazabal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2559', 'AR', 'X', 'Cintra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2561', 'AR', 'X', 'Chilibroste', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2563', 'AR', 'X', 'Noetinger', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2564', 'AR', 'X', 'Monte Leña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2566', 'AR', 'X', 'San Marcos Sud', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2568', 'AR', 'X', 'Las Lagunitas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2572', 'AR', 'X', 'Ballesteros Sud', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2580', 'AR', 'X', 'Marcos Juarez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2581', 'AR', 'X', 'Los Surgentes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2583', 'AR', 'X', 'Gral. Baldissera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2585', 'AR', 'X', 'Camilo Aldao', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2587', 'AR', 'X', 'Inriville', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2589', 'AR', 'X', 'Monte Buey', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2592', 'AR', 'X', 'Gral. Roca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2594', 'AR', 'X', 'Leones', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2619', 'AR', 'X', 'Km. 57', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2624', 'AR', 'X', 'Arias', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2625', 'AR', 'X', 'Cavanagh', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2627', 'AR', 'X', 'Guatimozin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2645', 'AR', 'X', 'Cap. Bernardo O''higgins', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2650', 'AR', 'X', 'Canals', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2651', 'AR', 'X', 'Aldea Santa Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2655', 'AR', 'X', 'Wenceslao Escalante', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2657', 'AR', 'X', 'Laborde', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2659', 'AR', 'X', 'Colonia Barge', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2661', 'AR', 'X', 'Isla Verde', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2662', 'AR', 'X', 'Alejo Ledesma', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2664', 'AR', 'X', 'Benjamin Gould', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2670', 'AR', 'X', 'La Carlota', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2671', 'AR', 'X', 'Assunta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2675', 'AR', 'X', 'Chazon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2677', 'AR', 'X', 'Ucacha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2679', 'AR', 'X', 'Pascanas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2681', 'AR', 'X', 'Etruria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2684', 'AR', 'X', 'Los Cisnes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('2686', 'AR', 'X', 'Alejandro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4275', 'AR', 'X', 'Villa Huidobro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5000', 'AR', 'X', 'Córdoba', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5009', 'AR', 'X', 'Rodriguez Del Busto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5016', 'AR', 'X', 'Córdoba', NULL, -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5101', 'AR', 'X', 'Bajo Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5103', 'AR', 'X', 'Guarnicion Aerea Cordoba', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5105', 'AR', 'X', 'Villa Allende', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5107', 'AR', 'X', 'Agua De Oro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5109', 'AR', 'X', 'Unquillo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5111', 'AR', 'X', 'La Estancita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5113', 'AR', 'X', 'Salsipuedes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5115', 'AR', 'X', 'La Granja', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5117', 'AR', 'X', 'Ascochinga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5119', 'AR', 'X', 'Bouwer', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5121', 'AR', 'X', 'Despeñaderos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5123', 'AR', 'X', 'Ferreyra', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5125', 'AR', 'X', 'Blas De Rosales', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5127', 'AR', 'X', 'Los Guindos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5129', 'AR', 'X', 'Comechingones', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5131', 'AR', 'X', 'El Alcalde', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5133', 'AR', 'X', 'Santa Rosa De Rio Primero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5135', 'AR', 'X', 'Buey Muerto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5136', 'AR', 'X', 'La Quinta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5137', 'AR', 'X', 'La Para', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5139', 'AR', 'X', 'Marull', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5141', 'AR', 'X', 'Balnearia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5143', 'AR', 'X', 'Miramar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5145', 'AR', 'X', 'Juarez Celman', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5147', 'AR', 'X', 'Arguello', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5149', 'AR', 'X', 'Cassaffousth', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5151', 'AR', 'X', 'Casa Bamba', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5152', 'AR', 'X', 'Villa Carlos Paz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5153', 'AR', 'X', 'Copina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5155', 'AR', 'X', 'Cavalango', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5158', 'AR', 'X', 'Bialetmasse', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5162', 'AR', 'X', 'Casa Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5164', 'AR', 'X', 'Domingo Funes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5165', 'AR', 'X', 'Sanatorio Santa Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5166', 'AR', 'X', 'Cosquin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5168', 'AR', 'X', 'Valle Hermoso', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5172', 'AR', 'X', 'El Vallecito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5174', 'AR', 'X', 'Huerta Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5176', 'AR', 'X', 'Villa Giardino', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5178', 'AR', 'X', 'Cruz Chica', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5182', 'AR', 'X', 'Los Cocos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5184', 'AR', 'X', 'Capilla Del Monte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5186', 'AR', 'X', 'Alta Gracia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5187', 'AR', 'X', 'La Falda Del Carmen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5189', 'AR', 'X', 'Bajo Chico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5191', 'AR', 'X', 'Calmayo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5192', 'AR', 'X', 'Dique Los Molinos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5194', 'AR', 'X', 'Atos Pampa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5196', 'AR', 'X', 'Santa Rosa De Calamuchita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5197', 'AR', 'X', 'El Parador De La Montaña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5199', 'AR', 'X', 'Amboy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5200', 'AR', 'X', 'Canteras Km. 428', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5201', 'AR', 'X', 'Copacabana', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5203', 'AR', 'X', 'Alto De Flores', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5205', 'AR', 'X', 'El Cerrito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5209', 'AR', 'X', 'Cachi Yaco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5211', 'AR', 'X', 'Macha', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5212', 'AR', 'X', 'Avellaneda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5214', 'AR', 'X', 'Km. 881', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5216', 'AR', 'X', 'Km. 907', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5218', 'AR', 'X', 'Chuña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5220', 'AR', 'X', 'Jesus Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5221', 'AR', 'X', 'Agua De Las Piedras', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5223', 'AR', 'X', 'Caroya', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5225', 'AR', 'X', 'Atahona', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5227', 'AR', 'X', 'La Posta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5229', 'AR', 'X', 'Cañada De Luque', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5231', 'AR', 'X', 'Campo Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5233', 'AR', 'X', 'El Zapallar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5236', 'AR', 'X', 'Villa Del Totoral', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5238', 'AR', 'X', 'Las Peñas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5242', 'AR', 'X', 'Simbolar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5244', 'AR', 'X', 'Caminiaga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5246', 'AR', 'X', 'Chañar Viejo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5248', 'AR', 'X', 'Eufrasio Loza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5249', 'AR', 'X', 'Candelaria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5270', 'AR', 'X', 'Iglesia Vieja', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5271', 'AR', 'X', 'Piedrita Blanca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5272', 'AR', 'X', 'El Chacho', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5280', 'AR', 'X', 'Cruz Del Eje', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5281', 'AR', 'X', 'Canteras De Quilpo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5282', 'AR', 'X', 'Charbonier', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5284', 'AR', 'X', 'Aguas De Ramon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5285', 'AR', 'X', 'Bañado De Soto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5287', 'AR', 'X', 'Capilla La Candelaria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5289', 'AR', 'X', 'Cienaga Del Coro', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5291', 'AR', 'X', 'Estancia De Guadalupe', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5293', 'AR', 'X', 'El Durazno Minas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5295', 'AR', 'X', 'Salsacate', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5297', 'AR', 'X', 'Cañada Del Puerto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5299', 'AR', 'X', 'Ambul', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5738', 'AR', 'X', 'Paunero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5800', 'AR', 'X', 'Rio Cuarto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5801', 'AR', 'X', 'Alpa Corral', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5803', 'AR', 'X', 'Paso Del Durazno', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5805', 'AR', 'X', 'Las Higueras', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5807', 'AR', 'X', 'Bengolea', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5809', 'AR', 'X', 'Gral. Cabrera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5811', 'AR', 'X', 'Cnel. Baigorria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5813', 'AR', 'X', 'Gigena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5815', 'AR', 'X', 'Elena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5817', 'AR', 'X', 'Berrotaran', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5819', 'AR', 'X', 'Cañada De Alvarez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5821', 'AR', 'X', 'Cano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5823', 'AR', 'X', 'Los Condores', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5825', 'AR', 'X', 'Holmberg', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5827', 'AR', 'X', 'Las Vertientes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5829', 'AR', 'X', 'Sampacho', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5831', 'AR', 'X', 'Estacion Achiras', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5833', 'AR', 'X', 'Achiras', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5837', 'AR', 'X', 'Chajan', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5839', 'AR', 'X', 'Estacion Punta Del Agua', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5841', 'AR', 'X', 'San Basilio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5843', 'AR', 'X', 'Adelia Maria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5845', 'AR', 'X', 'Bulnes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5847', 'AR', 'X', 'Cnel. Moldes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5848', 'AR', 'X', 'La Gilda', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5850', 'AR', 'X', 'Fabrica Militar - Rio Tercero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5851', 'AR', 'X', 'Colonia Santa Catalina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5853', 'AR', 'X', 'Corralito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5854', 'AR', 'X', 'Almafuerte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5856', 'AR', 'X', 'Embalse', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5857', 'AR', 'X', 'Embalse (sucursal Nro 1)', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5859', 'AR', 'X', 'Arroyo San Antonio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5862', 'AR', 'X', 'Villa Del Dique', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5864', 'AR', 'X', 'Villa Rumipal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5870', 'AR', 'X', 'La Cañada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5871', 'AR', 'X', 'Altautina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5873', 'AR', 'X', 'Capilla De Romero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5875', 'AR', 'X', 'Cruz De Caña', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5877', 'AR', 'X', 'Yacanto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5879', 'AR', 'X', 'La Paz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5885', 'AR', 'X', 'Hornillos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5887', 'AR', 'X', 'Nono', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5889', 'AR', 'X', 'Mina Clavero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5891', 'AR', 'X', 'Cienaga De Allende', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5893', 'AR', 'X', 'Alto Grande', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5900', 'AR', 'X', 'Las Playas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5901', 'AR', 'X', 'Ausonia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5902', 'AR', 'X', 'Villa María', NULL, -1, -1);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5903', 'AR', 'X', 'Villa Nueva', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5905', 'AR', 'X', 'Ana Zumaran', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5907', 'AR', 'X', 'Alto Alegre', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5909', 'AR', 'X', 'Arroyo Algodon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5911', 'AR', 'X', 'La Playosa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5913', 'AR', 'X', 'Pozo Del Molle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5915', 'AR', 'X', 'Carrilobo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5917', 'AR', 'X', 'Arroyo Cabral', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5919', 'AR', 'X', 'Dalmacio Velez', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5921', 'AR', 'X', 'Las Perdices', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5923', 'AR', 'X', 'Gral. Deheza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5925', 'AR', 'X', 'La Palestina', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5929', 'AR', 'X', 'Hernando', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5931', 'AR', 'X', 'Las Isletillas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5933', 'AR', 'X', 'Gral. Fotheringham', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5935', 'AR', 'X', 'Villa Ascasubi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5936', 'AR', 'X', 'San Antonio De Yucat', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5940', 'AR', 'X', 'Las Varillas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5941', 'AR', 'X', 'Las Varas', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5943', 'AR', 'X', 'Saturnino Laspur', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5945', 'AR', 'X', 'Sacanta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5947', 'AR', 'X', 'El Arañado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5949', 'AR', 'X', 'Alhuampa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5951', 'AR', 'X', 'El Fortin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5960', 'AR', 'X', 'Rio Segundo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5961', 'AR', 'X', 'Cañada De Machado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5963', 'AR', 'X', 'Cañada De Machado Sud', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5965', 'AR', 'X', 'Calchin Oeste', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5967', 'AR', 'X', 'Luque', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5969', 'AR', 'X', 'Estacion Calchin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5972', 'AR', 'X', 'Pilar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5974', 'AR', 'X', 'Laguna Larga', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5980', 'AR', 'X', 'Oliva', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5981', 'AR', 'X', 'Colonia Vidal Abal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5984', 'AR', 'X', 'James Craik', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5986', 'AR', 'X', 'Oncativo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5987', 'AR', 'X', 'Colonia Almada', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('5988', 'AR', 'X', 'Manfredi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6101', 'AR', 'X', 'La Cesira', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6120', 'AR', 'X', 'Guardia Vieja', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6121', 'AR', 'X', 'El Rastreador', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6123', 'AR', 'X', 'Melo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6125', 'AR', 'X', 'Serrano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6127', 'AR', 'X', 'Jovita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6128', 'AR', 'X', 'Leguizamon', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6130', 'AR', 'X', 'Curapaligue', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6132', 'AR', 'X', 'Gral. Levalle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6134', 'AR', 'X', 'Rio Bamba', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6140', 'AR', 'X', 'Pretot Freyre', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6141', 'AR', 'X', 'Tosquita', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6142', 'AR', 'X', 'Gral. Soler', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6144', 'AR', 'X', 'Laguna Oscura', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6225', 'AR', 'X', 'Hipolito Bouchard', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6227', 'AR', 'X', 'Onagoity', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6270', 'AR', 'X', 'Huinca Renanco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6271', 'AR', 'X', 'De La Serna', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6273', 'AR', 'X', 'Lecueder', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6275', 'AR', 'X', 'La Nacional', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('6279', 'AR', 'X', 'La Penca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4411', 'AR', 'Y', 'Sey', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4431', 'AR', 'Y', 'Aguas Calientes', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4500', 'AR', 'Y', 'El Arenal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4501', 'AR', 'Y', 'El Fuerte', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4503', 'AR', 'Y', 'La Esperanza', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4504', 'AR', 'Y', 'Chalican', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4506', 'AR', 'Y', 'Fraile Pintado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4512', 'AR', 'Y', 'Ldor. Gral. San Martin', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4513', 'AR', 'Y', 'Pampichuela', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4514', 'AR', 'Y', 'Calilegua', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4516', 'AR', 'Y', 'Caimancito', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4518', 'AR', 'Y', 'Yuto', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4522', 'AR', 'Y', 'La Mendieta', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4542', 'AR', 'Y', 'El Talar', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4600', 'AR', 'Y', 'San Salvador De Jujuy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4601', 'AR', 'Y', 'Huaico Chico', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4603', 'AR', 'Y', 'Perico Del Carmen', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4605', 'AR', 'Y', 'Perico De San Antonio', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4606', 'AR', 'Y', 'Los Lapachos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4608', 'AR', 'Y', 'Bordo La Isla', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4612', 'AR', 'Y', 'Centro Forestal', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4616', 'AR', 'Y', 'Barcena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4618', 'AR', 'Y', 'Colorados', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4622', 'AR', 'Y', 'Maimara', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4624', 'AR', 'Y', 'Abramayo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4626', 'AR', 'Y', 'Huacalera', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4630', 'AR', 'Y', 'Humahuaca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4631', 'AR', 'Y', 'Caspala', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4632', 'AR', 'Y', 'Chaupi Rodero', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4634', 'AR', 'Y', 'Abralaite', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4638', 'AR', 'Y', 'Tres Cruces', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4640', 'AR', 'Y', 'Abra Pampa', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4641', 'AR', 'Y', 'Abdon Castro Tolay', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4643', 'AR', 'Y', 'Arbolito Nuevo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4644', 'AR', 'Y', 'Cangrejillos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4650', 'AR', 'Y', 'La Quiaca', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4651', 'AR', 'Y', 'Yavi', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4653', 'AR', 'Y', 'Casira', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('4655', 'AR', 'Y', 'Cabreria', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9011', 'AR', 'Z', 'Caleta Olivia', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9013', 'AR', 'Z', 'Cañadon Seco', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9015', 'AR', 'Z', 'Pico Truncado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9017', 'AR', 'Z', 'El Pluma', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9019', 'AR', 'Z', 'Fitz Roy', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9040', 'AR', 'Z', 'El Portezuelo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9041', 'AR', 'Z', 'Los Antiguos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9050', 'AR', 'Z', 'Gdor. Moyano', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9053', 'AR', 'Z', 'Jaramillo', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9300', 'AR', 'Z', 'Puerto Santa Cruz', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9301', 'AR', 'Z', 'La Florida', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9303', 'AR', 'Z', 'Cdte. Luis Piedra Buena', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9305', 'AR', 'Z', 'Puerto Coyle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9310', 'AR', 'Z', 'Puerto San Julian', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9311', 'AR', 'Z', 'Gdor. Gregores', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9313', 'AR', 'Z', 'El Salado', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9315', 'AR', 'Z', 'Bajo Caracoles', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9316', 'AR', 'Z', 'Laura', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9400', 'AR', 'Z', 'Rio Gallegos', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9401', 'AR', 'Z', 'Fuentes Del Coyle', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9405', 'AR', 'Z', 'Bahia Tranquila', '', 0, 0);
INSERT INTO ona_localidad (codigopostal, idpais, idprovincia, nombre, ddn, esuniversidad, modiuniversidad) VALUES ('9407', 'AR', 'Z', 'El Turbio', '', 0, 0);


--
-- TOC entry 1539 (class 0 OID 673500772)
-- Dependencies: 1169
-- Data for Name: ona_pais; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO ona_pais (idpais, nombre, ddi, esuniversidad, modiuniversidad) VALUES ('AR', 'Argentina', '54', -1, -1);
INSERT INTO ona_pais (idpais, nombre, ddi, esuniversidad, modiuniversidad) VALUES ('P', 'Paraguay', '59', 0, 0);
INSERT INTO ona_pais (idpais, nombre, ddi, esuniversidad, modiuniversidad) VALUES ('U', 'Uruguay', '59', 0, 0);


--
-- TOC entry 1540 (class 0 OID 673500776)
-- Dependencies: 1170
-- Data for Name: ona_provincia; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('A', 'AR', 'Salta', 0, -1);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('B', 'AR', 'Buenos Aires', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('C', 'AR', 'Capital Federal', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('D', 'AR', 'San Luis', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('E', 'AR', 'Entre Ríos', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('F', 'AR', 'La Rioja', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('G', 'AR', 'Santiago Del Estero', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('H', 'AR', 'Chaco', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('J', 'AR', 'San Juan', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('K', 'AR', 'Catamarca', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('L', 'AR', 'La Pampa', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('M', 'AR', 'Mendoza', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('N', 'AR', 'Misiones', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('P', 'AR', 'Formosa', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('Q', 'AR', 'Neuquén', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('R', 'AR', 'Río Negro', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('S', 'AR', 'Santa Fé', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('T', 'AR', 'Tucumán', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('U', 'AR', 'Chubut', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('V', 'AR', 'Tierra Del Fuego', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('W', 'AR', 'Corrientes', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('X', 'AR', 'Córdoba', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('Y', 'AR', 'Jujuy', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('Z', 'AR', 'Santa Cruz', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('PP', 'P', 'Una Provincia de Paraguay', 0, 0);
INSERT INTO ona_provincia (idprovincia, idpais, nombre, esuniversidad, modiuniversidad) VALUES ('PU', 'U', 'Una Provincia de Uruguay', 0, 0);


--
-- TOC entry 1541 (class 0 OID 673500782)
-- Dependencies: 1172
-- Data for Name: soe_edificios; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (1, 1, 1, 'Edificio de SedePrincipal - UBA', 'Viamonte', '444', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (2, 2, 1, 'Edificio de SedePrincipal - CATAMARCA', 'Esquiú', '612', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (3, 3, 1, 'Edificio de SedePrincipal - CENTRO', 'Gral. Pinto', '99', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (4, 4, 1, 'Edificio de SedePrincipal - COMAHUE', 'Buenos Aires', '1400', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (5, 5, 1, 'Edificio de SedePrincipal - CORDOBA', 'Obispo Trejo y Sanabria', '242', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (6, 6, 1, 'Edificio de SedePrincipal - CUYO', 'Parque Gral. San Martín', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (7, 7, 1, 'Edificio de SedePrincipal - ENTRE RIOS', 'Eva Perón', '24', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (8, 8, 1, 'Edificio de SedePrincipal - FORMOSA', 'Avda. Gobernador Gutnisky', '3200', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (9, 9, 1, 'Edificio de SedePrincipal - SAN MARTIN', 'Calle 91', '3391', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (10, 10, 1, 'Edificio de SedePrincipal - SARMIENTO', 'Julio A. Roca', '850', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (11, 11, 1, 'Edificio de SedePrincipal - JUJUY', 'Bolivia', '235', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (12, 12, 1, 'Edificio de SedePrincipal - MATANZA', 'Florencio Varela', '1903', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (13, 13, 1, 'Edificio de SedePrincipal - LANUS', '29 de Septiembre', '3901', NULL, 'Recto');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (14, 14, 1, 'Edificio de SedePrincipal - LA PAMPA', 'Coronel Gil', '353', '3°', 'Estad');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (15, 15, 1, 'Edificio de SedePrincipal - LA PATAGONIA AUSTRAL', 'Lisandro de la Torre', '860', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (16, 16, 1, 'Edificio de SedePrincipal - LA PATAG. SAN JUAN BOSCO', 'Ruta Provincial Nº 1 - km 4', NULL, '4', 'Dir.');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (17, 18, 1, 'Edificio de SedePrincipal - LA RIOJA', 'Avda. Laprida y Vicente Bustos', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (18, 19, 1, 'Edificio de SedePrincipal - LITORAL', 'Bv. Pellegrini', '2750', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (19, 20, 1, 'Edificio de SedePrincipal - LOMAS DE ZAMORA', 'Camino de Cintura - km. 2', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (20, 21, 1, 'Edificio de SedePrincipal - LUJAN', 'Ruta 5 - km. 70', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (21, 22, 1, 'Edificio de SedePrincipal - MAR DEL PLATA', 'Bv. Juan Bautista Alberdi', '2695', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (22, 23, 1, 'Edificio de SedePrincipal - MISIONES', 'Ruta 12 - km. 7,5', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (23, 24, 1, 'Edificio de SedePrincipal - NORDESTE', '25 de Mayo', '868', NULL, 'Decan');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (24, 25, 1, 'Edificio de SedePrincipal - QUILMES', 'Roque Saenz Peña', '180', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (25, 26, 1, 'Edificio de SedePrincipal - RIO CUARTO', 'Ruta 6 y 36 - km. 603', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (26, 27, 1, 'Edificio de SedePrincipal - ROSARIO', 'Córdoba', '1814', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (27, 28, 1, 'Edificio de SedePrincipal - SALTA', 'Buenos Aires', '177', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (28, 29, 1, 'Edificio de SedePrincipal - SAN JUAN', 'Avda. José Ignacio de la Roza', '391', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (29, 30, 1, 'Edificio de SedePrincipal - SAN LUIS', 'Ejercito de los Andes', '950', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (30, 31, 1, 'Edificio de SedePrincipal - SANTIAGO DEL ESTERO', 'Avda. Balgrano Sur', '1912', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (31, 32, 1, 'Edificio de SedePrincipal - SUR', 'Avda. Colón', '80', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (32, 33, 1, 'Edificio de SedePrincipal - TUCUMAN', 'Ayacucho', '491', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (33, 34, 1, 'Edificio de SedePrincipal - VILLA MARIA', 'Lisandro de la Torre', '252', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (34, 35, 1, 'Edificio de SedePrincipal - UTN', 'Sarmiento', '440', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (35, 36, 1, 'Edificio de SedePrincipal - TRES DE FEBRERO', 'Av.San Martin', '2921', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (36, 37, 1, 'Edificio de SedePrincipal - IESE', 'Avda. Luis María Campos', '230', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (37, 38, 1, 'Edificio de SedePrincipal - IAERONAUTICO', 'Avda. Fuerza Aerea Argentina', '6500', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (38, 39, 1, 'Edificio de SedePrincipal - INAVAL', 'Av. del Libertador', '8209', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (39, 40, 1, 'Edificio de SedePrincipal - IPOLICIA', 'Rosario', '532', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (40, 41, 1, 'Edificio de SedePrincipal - NOTARIAL', 'Calle 51', '435', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (41, 42, 1, 'Edificio de SedePrincipal - CEMA', 'Avda. Córdoba', '637', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (42, 43, 1, 'Edificio de SedePrincipal - ESCUELA DE TEOLOGIA', 'Pasaje Catedral', '1750', '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (43, 44, 1, 'Edificio de SedePrincipal - IUNA', 'Paraguay', '786', '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (44, 45, 1, 'Edificio de SedePrincipal - ITBA', 'Avda. Eduardo Madero', '351', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (45, 46, 1, 'Edificio de SedePrincipal - FAVALORO', 'Solís', '453', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (46, 47, 1, 'Edificio de SedePrincipal - BARCELO', 'Larrea', '770', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (47, 48, 1, 'Edificio de SedePrincipal - UCA', 'Av. Alicia Moreau de Justo', '1300', '2', 'Recto');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (48, 49, 1, 'Edificio de SedePrincipal - ABIERTA INTERAMERICANA', 'Chacabuco', '90', '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (49, 50, 1, 'Edificio de SedePrincipal - ADVENTISTA', '25 de Mayo', '99', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (50, 51, 1, 'Edificio de SedePrincipal - UADE', 'Lima', '761', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (51, 52, 1, 'Edificio de SedePrincipal - KENNEDY', 'Bartolomé Mitre', '1407', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (52, 53, 1, 'Edificio de SedePrincipal - ATLANTIDA', 'Diag. Rivadavia', '515', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (53, 54, 1, 'Edificio de SedePrincipal - AUSTRAL', 'Avda. Juan de Garay', '125', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (54, 56, 1, 'Edificio de SedePrincipal - CAECE', 'Tte. Gral. J.D.Perón', '2933', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (55, 57, 1, 'Edificio de SedePrincipal - CATOLICA CORDOBA', 'Obispo Trejo y Sanabria', '323', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (56, 58, 1, 'Edificio de SedePrincipal - CATOLICA DE CUYO', 'Avda. José Ignacio de la Roza', '1516', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (57, 59, 1, 'Edificio de SedePrincipal - CATOLICA DE LA PLATA', 'Calle 13', '1227', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (58, 60, 1, 'Edificio de SedePrincipal - CATOLICA DE SALTA', 'Campo Casdtañares', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (59, 61, 1, 'Edificio de SedePrincipal - CATOLICA DE SANTA FE', 'Echagüe', '7151', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (60, 62, 1, 'Edificio de SedePrincipal - CATOLICA DE SANTIAGO DEL ESTERO', 'Avda. Alsina y Dalmacio Velez Sarsfield', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (61, 63, 1, 'Edificio de SedePrincipal - CHAMPAGNAT', 'San Martín', '866', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (62, 64, 1, 'Edificio de SedePrincipal - BELGRANO', 'Zabala', '1851', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (63, 65, 1, 'Edificio de SedePrincipal - CIENCIAS EMPRESARIALES', 'Paraguay', '1345', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (64, 66, 1, 'Edificio de SedePrincipal - CONCEPCIO DEL URUGUAY', '8 de Junio', '522', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (65, 67, 1, 'Edificio de SedePrincipal - CONGRESO', 'Avda. Mitre', '617', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (66, 68, 1, 'Edificio de SedePrincipal - FLORES', 'Camacuá', '282', '-', '-');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (67, 69, 1, 'Edificio de SedePrincipal - MENDOZA', 'Avda. Bulogne Sur Mer', '665', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (68, 70, 1, 'Edificio de SedePrincipal - MORON', 'Cabildo', '134', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (69, 71, 1, 'Edificio de SedePrincipal - PALERMO', 'Mario Bravo', '1302', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (70, 72, 1, 'Edificio de SedePrincipal - SAN ANDRES', 'Vito Dumas', '284', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (71, 73, 1, 'Edificio de SedePrincipal - ACONCAGUA', 'Catamarca', '147', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (72, 74, 1, 'Edificio de SedePrincipal - CENTRO EDUCATIVO LATINOAMERICANO', 'Avda. Pellegrini', '1352', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (73, 75, 1, 'Edificio de SedePrincipal - DEL CINE', 'Pje. J. M. Giuffa', '330', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (74, 76, 1, 'Edificio de SedePrincipal - MUSEO SOCIAL', 'Avda. Corrientes', '1723', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (75, 77, 1, 'Edificio de SedePrincipal - SANTO TOMAS', '9 de Julio', '165', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (76, 78, 1, 'Edificio de SedePrincipal - SALVADOR', 'Viamonte', '1856', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (77, 79, 1, 'Edificio de SedePrincipal - CUENCA DEL PLATA', 'Plácido Martínez', '964', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (78, 80, 1, 'Edificio de Sede Principal - F.A.S.T.A.', 'Gascón', '3145', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (79, 81, 1, 'Edificio de Sede Principal - MARINA MERCANTE', 'Rivadavia', '2258', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (80, 82, 1, 'Edificio de SedePrincipal - SIGLO XXI', 'Rondeau', '165', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (81, 83, 1, 'Edificio de SedePrincipal - BAR ILAN', 'Teniente General Juan Domingo Perón', '3460', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (82, 84, 1, 'Edificio de SedePrincipal - MAZA', 'Avda. Acceso Este Lateral Sur', '2245', NULL, 'Secre');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (83, 85, 1, 'Edificio de SedePrincipal - MAIMONIDES', 'Talcahuano', '456', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (84, 23, 4, 'DELEGACION BUENOS AIRES', 'SARMIENTO', '1462', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (85, 88, 1, 'Edificio de SedePrincipal - CEMIC', 'Sánchez de Bustamante', '2560', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (86, 89, 1, 'Edificio de SedePrincipal - GASTON DACHARY', 'Salta', '1968', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (87, 90, 1, 'Edificio de SedePrincipal - ISALUD', 'Venezuela', '925/3', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (88, 91, 1, 'Edificio de SedePrincipal - ESEADE', 'Uriarte', '2472', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (89, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (90, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (91, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (92, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (93, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (94, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (95, 1, 2, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (96, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (97, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (98, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (99, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (100, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (101, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (102, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (103, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (104, 1, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (105, 2, 1, 'Edificio de Sede 00001 - Avenida Belgrano y Maestro Qiroga', 'Avenida Belgrano y Maestro Qiroga', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (106, 2, 1, 'Edificio de Sede 00001 - Maximo Victoria', 'Maximo Victoria', '55', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (107, 2, 1, 'Edificio de Sede 00001 - Maestro Quiroga', 'Maestro Quiroga', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (108, 2, 1, 'Edificio de Sede 00001 - Avenida Belgrano', 'Avenida Belgrano', '300', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (109, 2, 1, 'Edificio de Sede 00001 - Avenidad Belgrano', 'Avenidad Belgrano', '300', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (110, 2, 1, 'Edificio de Sede 00001 - Maximo Victoria 1era Cuadra', 'Maximo Victoria 1era Cuadra', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (111, 2, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (112, 3, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (113, 3, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (114, 3, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (115, 3, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (116, 3, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (117, 3, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (118, 3, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (119, 3, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (120, 3, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (121, 4, 2, 'Edificio de Sede 00002 - Ruta 151  Km 12,5', 'Ruta 151  Km 12,5', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (122, 4, 3, 'Edificio de Sede 00003 - Pasaje de la Paz', 'Pasaje de la Paz', '235', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (123, 4, 4, 'Edificio de Sede 00004 - 25 de Mayo y Reconquista', '25 de Mayo y Reconquista', 'S/N', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (124, 4, 5, 'Edificio de Sede 00005 - Av.12 de Julio y Rahue', 'Av.12 de Julio y Rahue', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (125, 4, 6, 'Edificio de Sede 00006 - Mendoza', 'Mendoza', '1050', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (126, 4, 1, 'Edificio de Sede 00001 - Buenos Airres', 'Buenos Airres', '1400', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (127, 4, 6, 'Edificio de Sede 00006 - Mendoza', 'Mendoza', '2150', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (128, 4, 7, 'Edificio de Sede 00007 - Irigoyen', 'Irigoyen', '2000', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (129, 4, 8, 'Edificio de Sede 00008 - Mons.Esandi y Ayacucho', 'Mons.Esandi y Ayacucho', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (130, 4, 9, 'Edificio de Sede 00009 - B° Jardín Botánico- calle Quintral', 'B° Jardín Botánico- calle Quintral', 's/nº', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (131, 4, 10, 'Edificio de Sede 00010 - Belgrano', 'Belgrano', '325', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (132, 4, 11, 'Edificio de Sede 00011 - Guemes', 'Guemes', '1030', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (133, 4, 7, 'Edificio de Sede 00007 - Toschi y Arrayanes', 'Toschi y Arrayanes', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (134, 4, 12, 'Edificio de Sede 00012 - bb', 'bb', '12', '1', 'w');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (135, 4, 13, 'Edificio de Sede 00013 - Guemes y Eisntein', 'Guemes y Eisntein', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (136, 4, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (137, 5, 1, 'Edificio de Sede 00001 - Av. Valparaíso y Rogelio Martínez - C.U.', 'Av. Valparaíso y Rogelio Martínez - C.U.', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (138, 5, 1, 'Edificio de Sede 00001 - Av. Velez Sarfiel', 'Av. Velez Sarfiel', '264', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (139, 5, 1, 'Edificio de Sede 00001 - Av.Velez Sarfield', 'Av.Velez Sarfield', '299', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (140, 5, 1, 'Edificio de Sede 00001 - Pabellón Argentina, ala 1', 'Pabellón Argentina, ala 1', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (141, 5, 1, 'Edificio de Sede 00001 - Av. Valparaíso - Ciudad Universitaria', 'Av. Valparaíso - Ciudad Universitaria', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (142, 5, 1, 'Edificio de Sede 00001 - Obispo Trejo', 'Obispo Trejo', '242', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (143, 5, 1, 'Edificio de Sede 00001 - Enrique Barros s/n C. Universitaria', 'Enrique Barros s/n C. Universitaria', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (144, 5, 1, 'Edificio de Sede 00001 - Pabellón Residencial - C. Universitaria', 'Pabellón Residencial - C. Universitaria', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (145, 5, 1, 'Edificio de Sede 00001 - Av. Velez Sarfield', 'Av. Velez Sarfield', '187', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (146, 5, 1, 'Edificio de Sede 00001 - Pabellón Perú - Ciudad Universitaria', 'Pabellón Perú - Ciudad Universitaria', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (147, 5, 1, 'Edificio de Sede 00001 - Enrique Barros - Ciudad Universitaria', 'Enrique Barros - Ciudad Universitaria', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (148, 5, 1, 'Edificio de Sede 00001 - Pabellón Argentina - Ciudad Universitari', 'Pabellón Argentina - Ciudad Universitari', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (149, 5, 1, 'Edificio de Sede 00001 - Av. Haya de la Torre - Ciud. Universitar', 'Av. Haya de la Torre - Ciud. Universitar', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (150, 5, 1, 'Edificio de Sede 00001 - Pabellón Argentina (ala dcha, P.B) C.U.', 'Pabellón Argentina (ala dcha, P.B) C.U.', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (151, 5, 1, 'Edificio de Sede 00001 - Pabellón Argentina (ala Dcha. P. B)', 'Pabellón Argentina (ala Dcha. P. B)', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (152, 5, 1, 'Edificio de Sede 00001 - Pabellón Argentina - Ciud. Universitaria', 'Pabellón Argentina - Ciud. Universitaria', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (153, 5, 1, 'Edificio de Sede 00001 - Av. Valparaíso y E. Barros', 'Av. Valparaíso y E. Barros', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (154, 5, 1, 'Edificio de Sede 00001 - Ciudad Universitaria', 'Ciudad Universitaria', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (155, 5, 1, 'Edificio de Sede 00001 - Av. Valparaíso y R. Martínez - C. Univer', 'Av. Valparaíso y R. Martínez - C. Univer', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (156, 5, 1, 'Edificio de Sede 00001 - Velez Sarfield', 'Velez Sarfield', '153', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (157, 5, 1, 'Edificio de Sede 00001 - Avenida Valparaíso', 'Avenida Valparaíso', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (158, 5, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (159, 6, 2, 'Edificio de Sede 00002 - Almirante Brown', 'Almirante Brown', '500', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (160, 6, 1, 'Edificio de Sede 00001 - Centro Univ. Parque Gral. San Martín', 'Centro Univ. Parque Gral. San Martín', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (161, 6, 3, 'Edificio de Sede 00003 - Ctro. Atómico Bariloche-Av. E. Bustillo', 'Ctro. Atómico Bariloche-Av. E. Bustillo', '9500', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (162, 6, 1, 'Edificio de Sede 00001 - Centro Univ. - Parque Gral. San Martín', 'Centro Univ. - Parque Gral. San Martín', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (163, 6, 4, 'Edificio de Sede 00004 - Bernardo de Yrigoyen', 'Bernardo de Yrigoyen', '343', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (164, 6, 1, 'Edificio de Sede 00001 - Centro Univ.- Parque Gral. San Martín', 'Centro Univ.- Parque Gral. San Martín', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (165, 6, 1, 'Edificio de Sede 00001 - Centro Univ. - Parque Gral San Martín', 'Centro Univ. - Parque Gral San Martín', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (166, 6, 1, 'Edificio de Sede 00001 - Sobremonte', 'Sobremonte', '81', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (167, 6, 1, 'Edificio de Sede 00001 - Centro Univ. - Parque Gral. San  Martín', 'Centro Univ. - Parque Gral. San  Martín', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (168, 6, 1, 'Edificio de Sede 00001 - Centro Univ. - Parque Gral. San Martin', 'Centro Univ. - Parque Gral. San Martin', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (169, 6, 1, 'Edificio de Sede 00001 - Facultad de Ciencias Médicas', 'Facultad de Ciencias Médicas', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (170, 6, 4, 'Edificio de Sede 00004 - San Martín', 'San Martín', '352', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (171, 6, 1, 'Edificio de Sede 00001 - Centro Univ.- Parque Gral. San Martín', 'Centro Univ.- Parque Gral. San Martín', '242', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (172, 6, 1, 'Edificio de Sede 00001 - Facultad de Medicina', 'Facultad de Medicina', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (173, 6, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (174, 6, 5, 'Edificio de Sede 00005 - Chapeaurouge', 'Chapeaurouge', '163', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (175, 6, 4, 'Edificio de Sede 00004 - Bernardo de Irigoyen', 'Bernardo de Irigoyen', '343', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (176, 6, 6, 'Edificio de Sede 00006 - Alem y Sarmiento', 'Alem y Sarmiento', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (178, 7, 2, 'Edificio de Sede 00002 - Ruta Pcial.11, Km. 10.cc 24. Suc. 3', 'Ruta Pcial.11, Km. 10.cc 24. Suc. 3', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (179, 7, 2, 'Edificio de Sede 00002 - Ruta Pcial. 11, Km. 10. cc 57 Suc. 3', 'Ruta Pcial. 11, Km. 10. cc 57 Suc. 3', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (180, 7, 3, 'Edificio de Sede 00003 - Mons. Tavella', 'Mons. Tavella', '1450', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (181, 7, 4, 'Edificio de Sede 00004 - 25 de Mayo', '25 de Mayo', '709', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (182, 7, 3, 'Edificio de Sede 00003 - Mons. Tavella', 'Mons. Tavella', '1424', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (183, 7, 2, 'Edificio de Sede 00002 - Urquiza', 'Urquiza', '552', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (184, 7, 2, 'Edificio de Sede 00002 - Rioja', 'Rioja', '6', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (185, 7, 2, 'Edificio de Sede 00002 - Rivadavia', 'Rivadavia', '106', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (186, 7, 1, 'Edificio de Sede 00001 - 8 de Junio', '8 de Junio', '600', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (187, 7, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (188, 8, 1, 'Edificio de Sede 00001 - Av. Gobernador Gutnisky', 'Av. Gobernador Gutnisky', '3200', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (189, 8, 1, 'Edificio de Sede 00001 - GUTNISKY', 'GUTNISKY', '3200', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (190, 8, 1, 'Edificio de Sede 00001 - Gutnisky', 'Gutnisky', '3200', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (191, 8, 1, 'Edificio de Sede 00001 - Av. 9 de Julio', 'Av. 9 de Julio', '1125', 'pb', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (192, 8, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (193, 8, 1, 'Edificio de Sede 00001 - Av.Gutnizky', 'Av.Gutnizky', '3200', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (194, 8, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (195, 9, 2, 'Edificio de Sede 00002 - Caseros', 'Caseros', '2241', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (196, 9, 2, 'Edificio de Sede 00002 - Calle 78', 'Calle 78', '3901', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (197, 9, 2, 'Edificio de Sede 00002 - Belgrano', 'Belgrano', '3563', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (198, 9, 2, 'Edificio de Sede 00002 - Avda. Gral Paz', 'Avda. Gral Paz', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (199, 9, 2, 'Edificio de Sede 00002 - Avda. Gral Paz entre albarellos y Consti', 'Avda. Gral Paz entre albarellos y Consti', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (200, 9, 2, 'Edificio de Sede 00002 - Yapeyu', 'Yapeyu', '2068', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (201, 9, 3, 'Edificio de Sede 00003 - Ramsay', 'Ramsay', '2250', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (202, 9, 2, 'Edificio de Sede 00002 - Avda. Gral paz/Albarellos y constituyent', 'Avda. Gral paz/Albarellos y constituyent', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (203, 9, 4, 'Edificio de Sede 00004 - Paraná', 'Paraná', '145', '5', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (204, 11, 1, 'Edificio de Sede 00001 - Alberdi', 'Alberdi', '47', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (205, 11, 1, 'Edificio de Sede 00001 - Gorriti', 'Gorriti', '237', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (206, 11, 1, 'Edificio de Sede 00001 - Alvear', 'Alvear', '843', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (207, 11, 1, 'Edificio de Sede 00001 - Otero', 'Otero', '262', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (208, 11, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (209, 12, 1, 'Edificio de Sede 00001 - F.Varela', 'F.Varela', '1903', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (210, 12, 1, 'Edificio de Sede 00001 - F.varela', 'F.varela', '1903', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (211, 12, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (212, 14, 1, 'Edificio de Sede 00001 - Ruta 35 Km. 334', 'Ruta 35 Km. 334', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (213, 14, 1, 'Edificio de Sede 00001 - Avda. Uruguay  y Perú', 'Avda. Uruguay  y Perú', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (214, 14, 1, 'Edificio de Sede 00001 - C. Gil', 'C. Gil', '353', '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (215, 14, 1, 'Edificio de Sede 00001 - C.Gil', 'C.Gil', '353', '2', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (216, 15, 1, 'Edificio de Sede 00001 - LISANDRO DE LA TORRE', 'LISANDRO DE LA TORRE', '1070', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (217, 15, 2, 'Edificio de Sede 00002 - RUTA NACIONAL 3 - ACC. NORTE', 'RUTA NACIONAL 3 - ACC. NORTE', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (218, 15, 3, 'Edificio de Sede 00003 - COLON esq. SARMIENTO - Bº 200 VIV.', 'COLON esq. SARMIENTO - Bº 200 VIV.', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (219, 15, 4, 'Edificio de Sede 00004 - AV. DE LOS MINEROS', 'AV. DE LOS MINEROS', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (220, 15, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (221, 16, 1, 'Edificio de Sede 00001 - Ruta Provincial Nº 1 - Km4', 'Ruta Provincial Nº 1 - Km4', NULL, '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (222, 16, 1, 'Edificio de Sede 00001 - Ruta Provincial Nº 1- Km 4', 'Ruta Provincial Nº 1- Km 4', NULL, '2', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (223, 16, 2, 'Edificio de Sede 00002 - San Martín esquina Pellegrini', 'San Martín esquina Pellegrini', '407', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (224, 16, 1, 'Edificio de Sede 00001 - Ruta Provincial Nº 1- Km4', 'Ruta Provincial Nº 1- Km4', NULL, '4', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (225, 16, 3, 'Edificio de Sede 00003 - Ruta 258 - km 4', 'Ruta 258 - km 4', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (226, 16, 4, 'Edificio de Sede 00004 - Boulevard Almirante Brown', 'Boulevard Almirante Brown', '3700', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (227, 16, 2, 'Edificio de Sede 00002 - Belgrano', 'Belgrano', '504', '2', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (228, 16, 5, 'Edificio de Sede 00005 - Darwin y Canga', 'Darwin y Canga', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (229, 16, 3, 'Edificio de Sede 00003 - Ruta Nacional Nº 259 - Km4', 'Ruta Nacional Nº 259 - Km4', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (230, 16, 3, 'Edificio de Sede 00003 - Ruta Nacional 259 - km 4', 'Ruta Nacional 259 - km 4', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (231, 16, 2, 'Edificio de Sede 00002 - Rawson entre Gales y Belgrano', 'Rawson entre Gales y Belgrano', NULL, '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (232, 16, 3, 'Edificio de Sede 00003 - Sarmiento', 'Sarmiento', '849', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (233, 16, 2, 'Edificio de Sede 00002 - 9 de Julio y Belgrano', '9 de Julio y Belgrano', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (234, 16, 5, 'Edificio de Sede 00005 - Intevu VI casa', 'Intevu VI casa', '70', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (235, 17, 1, 'Edificio de Sede 00001 - 60 y 117', '60 y 117', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (236, 17, 1, 'Edificio de Sede 00001 - 60 y 116', '60 y 116', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (237, 17, 1, 'Edificio de Sede 00001 - 116 e/ 47 y 48', '116 e/ 47 y 48', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (238, 17, 1, 'Edificio de Sede 00001 - 1 y 47', '1 y 47', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (239, 17, 1, 'Edificio de Sede 00001 - 115 y 47', '115 y 47', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (240, 17, 1, 'Edificio de Sede 00001 - Bosque de La Plata', 'Bosque de La Plata', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (241, 17, 1, 'Edificio de Sede 00001 - 6 e/ 47 y 48', '6 e/ 47 y 48', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (242, 17, 1, 'Edificio de Sede 00001 - 48 e/ 6 y 7', '48 e/ 6 y 7', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (243, 17, 1, 'Edificio de Sede 00001 - 44 e/ 8 y 9', '44 e/ 8 y 9', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (244, 17, 1, 'Edificio de Sede 00001 - 7 y 60', '7 y 60', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (245, 17, 1, 'Edificio de Sede 00001 - 60 y 118', '60 y 118', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (246, 17, 1, 'Edificio de Sede 00001 - 9 y 63', '9 y 63', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (247, 17, 1, 'Edificio de Sede 00001 - 50 y 115', '50 y 115', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (248, 17, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (249, 18, 1, 'Edificio de Sede 00001 - Av. Ortiz de Ocampo', 'Av. Ortiz de Ocampo', '1700', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (250, 18, 2, 'Edificio de Sede 00002 - Castro Barros', 'Castro Barros', 'S/Nº', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (251, 18, 4, 'Edificio de Sede 00004 - Av. Lavalle', 'Av. Lavalle', 'S/Nº', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (252, 18, 5, 'Edificio de Sede 00005 - Av. San Martin', 'Av. San Martin', '462', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (253, 18, 6, 'Edificio de Sede 00006 - Hipolito Yrigoyen S/N', 'Hipolito Yrigoyen S/N', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (254, 18, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (255, 19, 1, 'Edificio de Sede 00001 - Bv. Pellegrini', 'Bv. Pellegrini', '2947', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (256, 19, 1, 'Edificio de Sede 00001 - Santiago del Estero', 'Santiago del Estero', '2829', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (257, 19, 1, 'Edificio de Sede 00001 - Ciudad Universitaria - Paraje El Pozo', 'Ciudad Universitaria - Paraje El Pozo', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (258, 19, 1, 'Edificio de Sede 00001 - 25 de Mayo', '25 de Mayo', '1783', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (259, 19, 1, 'Edificio de Sede 00001 - Candido Pujato', 'Candido Pujato', '2751', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (260, 19, 1, 'Edificio de Sede 00001 - tftftftftf', 'tftftftftf', '12122', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (261, 19, 1, 'Edificio de Sede 00001 - fgggfgff', 'fgggfgff', '56565', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (262, 19, 1, 'Edificio de Sede 00001 - gfgfgfgf', 'gfgfgfgf', '45454', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (263, 19, 1, 'Edificio de Sede 00001 - asasasaa', 'asasasaa', '78787', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (264, 19, 1, 'Edificio de Sede 00001 - jhjhjhjh', 'jhjhjhjh', '32332', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (265, 19, 1, 'Edificio de Sede 00001 - 9 de Julio', '9 de Julio', '2655', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (266, 19, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (267, 20, 1, 'Edificio de Sede 00001 - Ruta Provincial 4 Km. 2', 'Ruta Provincial 4 Km. 2', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (268, 20, 1, 'Edificio de Sede 00001 - Juan XXIII y Camino de Cintura', 'Juan XXIII y Camino de Cintura', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (269, 20, 1, 'Edificio de Sede 00001 - Juan  XXIII y Camino de Cintura', 'Juan  XXIII y Camino de Cintura', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (270, 20, 1, 'Edificio de Sede 00001 - Camino de Cintura  Km. 2', 'Camino de Cintura  Km. 2', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (271, 20, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (272, 21, 2, 'Edificio de Sede 00002 - Avda. Sarmiento', 'Avda. Sarmiento', '479', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (273, 21, 3, 'Edificio de Sede 00003 - Balcarce', 'Balcarce', '120', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (274, 21, 4, 'Edificio de Sede 00004 - Farias', 'Farias', '1590', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (275, 21, 5, 'Edificio de Sede 00005 - Rivadavia', 'Rivadavia', '886', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (276, 21, 6, 'Edificio de Sede 00006 - Av. del Libertador', 'Av. del Libertador', '1800', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (277, 21, 7, 'Edificio de Sede 00007 - Intituto Carlos Pellegrini ruta 5 km 3', 'Intituto Carlos Pellegrini ruta 5 km 3', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (278, 21, 8, 'Edificio de Sede 00008 - Florida', 'Florida', '629', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (279, 21, 9, 'Edificio de Sede 00009 - Robbio', 'Robbio', '322', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (280, 21, 8, 'Edificio de Sede 00008 - Esc. Forencio Molina Campos', 'Esc. Forencio Molina Campos', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (281, 21, 10, 'Edificio de Sede 00010 - Inst. Saturnino Unzue calle 26 y 47', 'Inst. Saturnino Unzue calle 26 y 47', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (282, 21, 11, 'Edificio de Sede 00011 - Ecuador', 'Ecuador', '873', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (283, 22, 2, 'Edificio de Sede 00002 - Ruta 226 Km 72,3', 'Ruta 226 Km 72,3', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (284, 22, 1, 'Edificio de Sede 00001 - Funes  (Complejo Universitario)', 'Funes  (Complejo Universitario)', '3250', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (285, 22, 1, 'Edificio de Sede 00001 - Av. Juan B. Justo', 'Av. Juan B. Justo', '4302', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (286, 22, 1, 'Edificio de Sede 00001 - Funes   (Complejo Universitario)', 'Funes   (Complejo Universitario)', '3250', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (287, 22, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (288, 23, 2, 'Edificio de Sede 00002 - AVDA. SAN MARTIN', 'AVDA. SAN MARTIN', 'KM. 3', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (289, 23, 3, 'Edificio de Sede 00003 - JUAN M. DE ROSAS', 'JUAN M. DE ROSAS', '325', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (290, 23, 1, 'Edificio de Sede 00001 - FELIX DE AZARA', 'FELIX DE AZARA', '1552', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (291, 23, 1, 'Edificio de Sede 00001 - Avda. Lopez Torres y José María Moreno', 'Avda. Lopez Torres y José María Moreno', '3415', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (292, 23, 1, 'Edificio de Sede 00001 - CAMPUS UNIVERSITARIO - RUTA 12 KM. 7 ½', 'CAMPUS UNIVERSITARIO - RUTA 12 KM. 7 ½', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (293, 23, 1, 'Edificio de Sede 00001 - TUCUMAN', 'TUCUMAN', '1946', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (294, 23, 1, 'Edificio de Sede 00001 - CARHUÉ', 'CARHUÉ', '832', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (295, 23, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (296, 23, 2, 'Edificio de Sede 00002 - Bertoni', 'Bertoni', '152', NULL, 'Eldor');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (297, 24, 1, 'Edificio de Sede 00001 - Sargento Cabral', 'Sargento Cabral', '2131', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (298, 24, 1, 'Edificio de Sede 00001 - Sargento Cabral', 'Sargento Cabral', '2139', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (299, 24, 2, 'Edificio de Sede 00002 - Av. Las Heras', 'Av. Las Heras', '727', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (300, 24, 2, 'Edificio de Sede 00002 - Av. Las Heras', 'Av. Las Heras', ' 727', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (301, 24, 3, 'Edificio de Sede 00003 - Comandante Fernández', 'Comandante Fernández', '755', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (302, 24, 1, 'Edificio de Sede 00001 - 9 de Julio', '9 de Julio', '1449', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (303, 24, 2, 'Edificio de Sede 00002 - Avenida Las Heras', 'Avenida Las Heras', '727', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (304, 24, 1, 'Edificio de Sede 00001 - Av. Libertad', 'Av. Libertad', '5470', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (305, 24, 1, 'Edificio de Sede 00001 - Moreno', 'Moreno', '1240', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (306, 24, 1, 'Edificio de Sede 00001 - Av. Libertad', 'Av. Libertad', '5450', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (307, 24, 2, 'Edificio de Sede 00002 - Las Heras', 'Las Heras', '727', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (308, 24, 4, 'Edificio de Sede 00004 - Madariaga', 'Madariaga', '1300', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (309, 24, 5, 'Edificio de Sede 00005 - Rivadavia', 'Rivadavia', ' 886', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (310, 24, 1, 'Edificio de Sede 00001 - Catamarca', 'Catamarca', '375', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (311, 24, 1, 'Edificio de Sede 00001 - San Juan', 'San Juan', '434', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (312, 24, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (313, 24, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (314, 24, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (315, 25, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (316, 25, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (317, 25, 1, 'Edificio de Sede 00001 - Roque Sáenz Peña', 'Roque Sáenz Peña', '180', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (318, 26, 1, 'Edificio de Sede 00001 - Ruta 36', 'Ruta 36', 'Km. 6', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (319, 26, 1, 'Edificio de Sede 00001 - Ruta 36', 'Ruta 36', 'Km. 6', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (320, 26, 1, 'Edificio de Sede 00001 - Ruta 36', 'Ruta 36', 'Km 60', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (321, 26, 1, 'Edificio de Sede 00001 - Ruta 36', 'Ruta 36', 'Km 60', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (322, 26, 1, 'Edificio de Sede 00001 - Ruta 36', 'Ruta 36', 'Km 60', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (323, 26, 1, 'Edificio de Sede 00001 - Ruta 36', 'Ruta 36', 'Km. 6', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (324, 26, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (325, 27, 2, 'Edificio de Sede 00002 - Campo Exp. J.Villarino', 'Campo Exp. J.Villarino', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (326, 27, 3, 'Edificio de Sede 00003 - Ruta 33 y Ov. Lagos', 'Ruta 33 y Ov. Lagos', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (327, 27, 1, 'Edificio de Sede 00001 - Riobamba y Berutti C.U.R.', 'Riobamba y Berutti C.U.R.', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (328, 27, 1, 'Edificio de Sede 00001 - Pellegrini', 'Pellegrini', '250', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (329, 27, 1, 'Edificio de Sede 00001 - Suipacha', 'Suipacha', '531', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (330, 27, 1, 'Edificio de Sede 00001 - Bv. Oroño', 'Bv. Oroño', '1261', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (331, 27, 1, 'Edificio de Sede 00001 - Córdoba', 'Córdoba', '2020', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (332, 27, 1, 'Edificio de Sede 00001 - Riobamba y Berutti . C.U.R', 'Riobamba y Berutti . C.U.R', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (333, 27, 1, 'Edificio de Sede 00001 - Entre Rios', 'Entre Rios', '758', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (334, 27, 1, 'Edificio de Sede 00001 - Riobamba', 'Riobamba', '250 b', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (335, 27, 1, 'Edificio de Sede 00001 - Santa Fe', 'Santa Fe', '3100', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (336, 27, 1, 'Edificio de Sede 00001 - Santa Fe', 'Santa Fe', '3160', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (337, 27, 1, 'Edificio de Sede 00001 - Av Pellegrini', 'Av Pellegrini', '250', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (338, 27, 1, 'Edificio de Sede 00001 - Balcarce', 'Balcarce', '1240', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (339, 27, 1, 'Edificio de Sede 00001 - Balcarce', 'Balcarce', '1514', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (340, 27, 3, 'Edificio de Sede 00003 - O.Lagos y Ruta 33', 'O.Lagos y Ruta 33', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (341, 27, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (342, 28, 1, 'Edificio de Sede 00001 - BUENOS AIRES', 'BUENOS AIRES', '177', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (343, 28, 2, 'Edificio de Sede 00002 - ALVARADO', 'ALVARADO', '751', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (344, 28, 3, 'Edificio de Sede 00003 - WARNES', 'WARNES', '890', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (345, 28, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (346, 29, 1, 'Edificio de Sede 00001 - Av. Lib. Gral San Martín (Oeste)', 'Av. Lib. Gral San Martín (Oeste)', '1109', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (347, 29, 1, 'Edificio de Sede 00001 - Av. José I. de la Roza y Meglioli', 'Av. José I. de la Roza y Meglioli', 'S/N', NULL, 'Rivad');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (348, 29, 2, 'Edificio de Sede 00002 - Av. José I. de la Roza', 'Av. José I. de la Roza', '590', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (349, 29, 1, 'Edificio de Sede 00001 - Av. José I. de la Roza (Oeste)', 'Av. José I. de la Roza (Oeste)', '230', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (350, 29, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (351, 30, 1, 'Edificio de Sede 00001 - Avda. 25 de Mayo', 'Avda. 25 de Mayo', '  384', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (352, 30, 1, 'Edificio de Sede 00001 - Avda. Ejercito de los Andes', 'Avda. Ejercito de los Andes', '    9', ' 2º', 'Edifi');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (353, 30, 1, 'Edificio de Sede 00001 - Avda. Ejercito de los Andes', 'Avda. Ejercito de los Andes', '   95', '2º', 'Edifi');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (354, 30, 1, 'Edificio de Sede 00001 - Avda. Ejercito de los Andes', 'Avda. Ejercito de los Andes', '   95', '  1', 'Edifi');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (355, 30, 1, 'Edificio de Sede 00001 - Avda. Ejercito de los Andes', 'Avda. Ejercito de los Andes', '  950', '  2', 'Edifi');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (356, 30, 1, 'Edificio de Sede 00001 - Av. Ejercito de los Andes', 'Av. Ejercito de los Andes', '950', '2º', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (357, 30, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (358, 31, 1, 'Edificio de Sede 00001 - Av. Belgrano (S)', 'Av. Belgrano (S)', '1912', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (359, 31, 1, 'Edificio de Sede 00001 - Av. Belgrano (s)', 'Av. Belgrano (s)', '1912', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (360, 31, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (361, 32, 1, 'Edificio de Sede 00001 - Alem', 'Alem', '1253', '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (362, 32, 1, 'Edificio de Sede 00001 - Alem 1253', 'Alem 1253', NULL, '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (363, 32, 1, 'Edificio de Sede 00001 - Avenida Alem', 'Avenida Alem', '1253', '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (364, 32, 1, 'Edificio de Sede 00001 - San Juan', 'San Juan', '670', '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (365, 32, 1, 'Edificio de Sede 00001 - 12 de Octubre y San Juan', '12 de Octubre y San Juan', NULL, '8', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (366, 32, 1, 'Edificio de Sede 00001 - 12 de octubre y San juan', '12 de octubre y San juan', NULL, '7', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (367, 32, 1, 'Edificio de Sede 00001 - 12 de octubre y San Juan', '12 de octubre y San Juan', NULL, '4', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (368, 32, 2, 'Edificio de Sede 00002 - Paraje El Pozo', 'Paraje El Pozo', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (369, 32, 1, 'Edificio de Sede 00001 - 11 de abril 475', '11 de abril 475', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (370, 32, 1, 'Edificio de Sede 00001 - 11 de abril', '11 de abril', '475', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (371, 32, 1, 'Edificio de Sede 00001 - Sarmiento al 2000', 'Sarmiento al 2000', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (372, 32, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (373, 33, 1, 'Edificio de Sede 00001 - Av. Roca', 'Av. Roca', '1900', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (374, 33, 1, 'Edificio de Sede 00001 - Av. Roca', 'Av. Roca', '1800', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (375, 33, 1, 'Edificio de Sede 00001 - Av. Independencia', 'Av. Independencia', '1800', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (376, 33, 1, 'Edificio de Sede 00001 - Miguel Lillo', 'Miguel Lillo', '205', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (377, 33, 1, 'Edificio de Sede 00001 - Av Independencia', 'Av Independencia', '1900', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (378, 33, 1, 'Edificio de Sede 00001 - 25 de Mayo', '25 de Mayo', '471', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (379, 33, 1, 'Edificio de Sede 00001 - Av. Benjamin Araoz', 'Av. Benjamin Araoz', '800', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (380, 33, 1, 'Edificio de Sede 00001 - Av. Benjamin Araoz', 'Av. Benjamin Araoz', '751', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (381, 33, 1, 'Edificio de Sede 00001 - Bolivar', 'Bolivar', '700', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (382, 33, 1, 'Edificio de Sede 00001 - Chacabuco', 'Chacabuco', '242', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (383, 33, 1, 'Edificio de Sede 00001 - Lamadrid', 'Lamadrid', '875', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (384, 33, 1, 'Edificio de Sede 00001 - General Paz', 'General Paz', '875', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (385, 33, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (386, 33, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (387, 34, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (388, 35, 2, 'Edificio de Sede 00002 - Avda. Mitre', 'Avda. Mitre', '750', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (389, 35, 3, 'Edificio de Sede 00003 - 11 de abril', '11 de abril', '461', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (390, 35, 4, 'Edificio de Sede 00004 - Medrano', 'Medrano', '951', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (391, 35, 5, 'Edificio de Sede 00005 - Ing. Pereyra', 'Ing. Pereyra', '676', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (392, 35, 6, 'Edificio de Sede 00006 - Maestro M. López esq.Cruz Roja Argentina', 'Maestro M. López esq.Cruz Roja Argentina', NULL, NULL, 'Ciuda');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (393, 35, 7, 'Edificio de Sede 00007 - San Martín', 'San Martín', '1171', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (394, 35, 8, 'Edificio de Sede 00008 - Hipólito Yrigoyen', 'Hipólito Yrigoyen', '288', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (395, 35, 9, 'Edificio de Sede 00009 - París', 'París', '532', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (396, 35, 10, 'Edificio de Sede 00010 - Calle 60 esq. 124', 'Calle 60 esq. 124', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (397, 35, 11, 'Edificio de Sede 00011 - Rodríguez', 'Rodríguez', '273', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (398, 35, 12, 'Edificio de Sede 00012 - Almafuerte', 'Almafuerte', '1033', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (399, 35, 13, 'Edificio de Sede 00013 - French', 'French', '414', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (400, 35, 14, 'Edificio de Sede 00014 - Estanislao Zeballos', 'Estanislao Zeballos', '1341', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (401, 35, 15, 'Edificio de Sede 00015 - Avda. de la Universidad', 'Avda. de la Universidad', '501', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (402, 35, 16, 'Edificio de Sede 00016 - Colón', 'Colón', '332', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (403, 35, 17, 'Edificio de Sede 00017 - Comandante Salas', 'Comandante Salas', '370', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (404, 35, 18, 'Edificio de Sede 00018 - Lavaise', 'Lavaise', '610', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (405, 35, 19, 'Edificio de Sede 00019 - Rivadavia', 'Rivadavia', '1050', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (406, 35, 20, 'Edificio de Sede 00020 - Av. Universidad - Barrio Bello Horizonte', 'Av. Universidad - Barrio Bello Horizonte', '450', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (407, 35, 21, 'Edificio de Sede 00021 - Salta', 'Salta', '277', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (408, 35, 22, 'Edificio de Sede 00022 - Pedro Rotter - Barrio Uno', 'Pedro Rotter - Barrio Uno', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (409, 35, 23, 'Edificio de Sede 00023 - Roberts', 'Roberts', '61', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (410, 35, 24, 'Edificio de Sede 00024 - San Nicolás de Bari (E)', 'San Nicolás de Bari (E)', '1100', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (411, 35, 25, 'Edificio de Sede 00025 - Bv. Roca y Artigas', 'Bv. Roca y Artigas', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (412, 35, 26, 'Edificio de Sede 00026 - Presidente Roca', 'Presidente Roca', '1250', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (413, 35, 27, 'Edificio de Sede 00027 - Solís y Béccar', 'Solís y Béccar', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (414, 35, 28, 'Edificio de Sede 00028 - Islas Malvinas', 'Islas Malvinas', '1650', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (415, 35, 29, 'Edificio de Sede 00029 - Villegas', 'Villegas', '980', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (416, 35, 30, 'Edificio de Sede 00030 - Castelli', 'Castelli', '501', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (417, 35, 25, 'Edificio de Sede 00025 - Bvard. Roca y Artigas', 'Bvard. Roca y Artigas', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (418, 35, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (419, 35, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (420, 37, 2, 'Edificio de Sede 00002 - Av Matienzo y Ruta 201', 'Av Matienzo y Ruta 201', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (421, 37, 1, 'Edificio de Sede 00001 - Av Cabildo', 'Av Cabildo', '15', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (422, 37, 1, 'Edificio de Sede 00001 - Av Luis María Campos', 'Av Luis María Campos', '480', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (423, 37, 3, 'Edificio de Sede 00003 - Maipú', 'Maipú', '262', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (424, 38, 1, 'Edificio de Sede 00001 - Av. Fuerza Aérea Argentina', 'Av. Fuerza Aérea Argentina', '6500', '-.-', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (425, 38, 1, 'Edificio de Sede 00001 - Av. Fuerza Aérea Km 6,5', 'Av. Fuerza Aérea Km 6,5', '-.-', '-.-', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (426, 38, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (427, 39, 2, 'Edificio de Sede 00002 - Av. Antartida Argentina', 'Av. Antartida Argentina', '1535', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (428, 39, 3, 'Edificio de Sede 00003 - Rio Santiago', 'Rio Santiago', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (429, 39, 4, 'Edificio de Sede 00004 - Puerto Belgrano', 'Puerto Belgrano', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (430, 39, 1, 'Edificio de Sede 00001 - Avda. del Libertador', 'Avda. del Libertador', '8071', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (431, 39, 5, 'Edificio de Sede 00005 - Avda. Montes de Oca', 'Avda. Montes de Oca', '2124', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (432, 39, 6, 'Edificio de Sede 00006 - Av. Montes de Oca', 'Av. Montes de Oca', '2124', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (433, 39, 1, 'Edificio de Sede 00001 - Av. del Libertador', 'Av. del Libertador', '8071', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (434, 40, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (435, 41, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (436, 42, 2, 'Edificio de Sede 00002 - Córdoba', 'Córdoba', '374', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (437, 44, 2, 'Edificio de Sede 00002 - Las Heras', 'Las Heras', '1749', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (438, 44, 2, 'Edificio de Sede 00002 - Avda Cordoba', 'Avda Cordoba', '2445', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (439, 44, 3, 'Edificio de Sede 00003 - Sanchez de  Loria', 'Sanchez de  Loria', '443', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (440, 44, 3, 'Edificio de Sede 00003 - French', 'French', '3614', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (441, 44, 3, 'Edificio de Sede 00003 - Sanchez de Loria', 'Sanchez de Loria', '443', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (442, 44, 3, 'Edificio de Sede 00003 - Piedras', 'Piedras', '1655', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (443, 45, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (444, 45, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (445, 48, 1, 'Edificio de Sede 00001 - Av. Alicia Moreau de Justo', 'Av. Alicia Moreau de Justo', '1500', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (446, 48, 1, 'Edificio de Sede 00001 - Av. Alicia Moreau de Justo', 'Av. Alicia Moreau de Justo', '1400', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (447, 48, 3, 'Edificio de Sede 00003 - 11 de septiembre', '11 de septiembre', '646', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (448, 48, 4, 'Edificio de Sede 00004 - E. Zeballos', 'E. Zeballos', '668', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (449, 48, 4, 'Edificio de Sede 00004 - Av. Salta', 'Av. Salta', '2763', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (450, 48, 6, 'Edificio de Sede 00006 - Buenos Aires', 'Buenos Aires', '249', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (451, 48, 7, 'Edificio de Sede 00007 - Perú', 'Perú', '1160', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (452, 48, 4, 'Edificio de Sede 00004 - Mendoza', 'Mendoza', '4197', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (453, 48, 7, 'Edificio de Sede 00007 - Patricias Mendocinas', 'Patricias Mendocinas', '1475', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (454, 49, 2, 'Edificio de Sede 00002 - Av. San Juan', 'Av. San Juan', '983', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (455, 49, 3, 'Edificio de Sede 00003 - Montañeses', 'Montañeses', '2759', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (456, 49, 4, 'Edificio de Sede 00004 - Arias', 'Arias', '3550', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (457, 49, 5, 'Edificio de Sede 00005 - Palestina', 'Palestina', '748', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (458, 49, 2, 'Edificio de Sede 00002 - Av.San Juan', 'Av.San Juan', '983', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (459, 51, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (460, 51, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (461, 51, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (462, 51, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (463, 51, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (464, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (465, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (466, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (467, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (468, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (469, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (470, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (471, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (472, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (473, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (474, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (475, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (476, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (477, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (478, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (479, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (480, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (481, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (482, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (483, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (484, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (485, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (486, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (487, 52, 2, 'Edificio de Sede 00002 - Estados Unidos', 'Estados Unidos', '929', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (488, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (489, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (490, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (491, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (492, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (493, 52, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (494, 53, 2, 'Edificio de Sede 00002 - calle Buenos Aires', 'calle Buenos Aires', '1280', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (495, 53, 1, 'Edificio de Sede 00001 - RIVADAVIA', 'RIVADAVIA', '515', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (496, 53, 4, 'Edificio de Sede 00004 - AV BUENOS AIRES Y URRUTIA', 'AV BUENOS AIRES Y URRUTIA', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (497, 53, 2, 'Edificio de Sede 00002 - LAMADRID', 'LAMADRID', '341', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (498, 53, 5, 'Edificio de Sede 00005 - Intermedanos S/N', 'Intermedanos S/N', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (499, 53, 6, 'Edificio de Sede 00006 - ALMIRANTE BROWN', 'ALMIRANTE BROWN', '1074', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (500, 54, 2, 'Edificio de Sede 00002 - Juan Domingo Perón', 'Juan Domingo Perón', '1500', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (501, 54, 3, 'Edificio de Sede 00003 - Paraguay', 'Paraguay', '1950', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (502, 54, 1, 'Edificio de Sede 00001 - Avanida Juan de Garay', 'Avanida Juan de Garay', '125', '5º', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (503, 54, 1, 'Edificio de Sede 00001 - Avenida Juan de Garay', 'Avenida Juan de Garay', '125', '2º', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (504, 54, 2, 'Edificio de Sede 00002 - Mariano Acosta. Derqui', 'Mariano Acosta. Derqui', 'S/N', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (505, 54, 1, 'Edificio de Sede 00001 -  Avenida Juan de Garay', ' Avenida Juan de Garay', '125', '3º', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (506, 54, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (507, 56, 2, 'Edificio de Sede 00002 - Av. de Mayo', 'Av. de Mayo', '866', '9', 'Dto.');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (508, 56, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (509, 57, 1, 'Edificio de Sede 00001 - Camino a Alta Gracia Km. 10', 'Camino a Alta Gracia Km. 10', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (510, 57, 1, 'Edificio de Sede 00001 - Jacinto Ríos', 'Jacinto Ríos', '571', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (511, 58, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (512, 58, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (513, 59, 1, 'Edificio de Sede 00001 - Diagonal 73', 'Diagonal 73', '2137', '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (514, 59, 1, 'Edificio de Sede 00001 - Avenida 51', 'Avenida 51', '807', 'PB', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (515, 59, 1, 'Edificio de Sede 00001 - Calle 57', 'Calle 57', '936', '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (516, 59, 2, 'Edificio de Sede 00002 - Calle 25 de Mayo', 'Calle 25 de Mayo', '51', 'PB', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (517, 59, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (518, 60, 1, 'Edificio de Sede 00001 - Campo Castañares', 'Campo Castañares', 's/n', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (519, 60, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (520, 60, 1, 'Edificio de Sede 00001 - Pellegrini', 'Pellegrini', '790', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (521, 60, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (522, 60, 2, 'Edificio de Sede 00002 - Avda. Paseo Colon', 'Avda. Paseo Colon', '533', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (523, 60, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (524, 60, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (525, 60, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (526, 61, 1, 'Edificio de Sede 00001 - Echacgüe', 'Echacgüe', '7151', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (527, 61, 1, 'Edificio de Sede 00001 - Eschagüe', 'Eschagüe', '7151', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (528, 61, 1, 'Edificio de Sede 00001 - Pascual Echague', 'Pascual Echague', '7151', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (529, 61, 2, 'Edificio de Sede 00002 - Rademacher 3943', 'Rademacher 3943', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (530, 61, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (531, 62, 1, 'Edificio de Sede 00001 - Av. Alsina Y Dalmacio Vélez Sarsfield', 'Av. Alsina Y Dalmacio Vélez Sarsfield', '---', '---', 'Matem');
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (532, 62, 2, 'Edificio de Sede 00002 - Lavalle', 'Lavalle', '333', '---', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (533, 62, 3, 'Edificio de Sede 00003 - Corrientes', 'Corrientes', '180', '--', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (534, 62, 4, 'Edificio de Sede 00004 - Boulevard Hipólito Irigoyen', 'Boulevard Hipólito Irigoyen', '1502', '---', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (535, 62, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (536, 63, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (537, 63, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (538, 63, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (539, 63, 2, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (540, 64, 1, 'Edificio de Sede 00001 - Federico Lacroze', 'Federico Lacroze', '1955', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (541, 64, 1, 'Edificio de Sede 00001 - Zabala', 'Zabala', '1837', '16', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (542, 64, 1, 'Edificio de Sede 00001 - Villanueva', 'Villanueva', '1324', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (543, 64, 1, 'Edificio de Sede 00001 - Federico Lacroze', 'Federico Lacroze', '1947', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (544, 64, 1, 'Edificio de Sede 00001 - Jose Hernandez', 'Jose Hernandez', '1820', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (545, 64, 2, 'Edificio de Sede 00002 - M. T. de Alvear', 'M. T. de Alvear', '1560', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (546, 64, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (547, 66, 1, 'Edificio de Sede 00001 - Eurasquin', 'Eurasquin', '158', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (548, 66, 1, 'Edificio de Sede 00001 - 8 de junio', '8 de junio', '552', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (549, 66, 1, 'Edificio de Sede 00001 - 8 de Junio', '8 de Junio', '552', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (550, 66, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (551, 66, 1, 'Edificio de Sede 00001 - Las Violetas', 'Las Violetas', '853', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (552, 66, 2, 'Edificio de Sede 00002 - 25 de Mayo', '25 de Mayo', '737', '1', NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (553, 66, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (554, 67, 1, 'Edificio de Sede 00001 - Av. Colón', 'Av. Colón', '90', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (555, 69, 1, 'Edificio de Sede 00001 - Dag Hammarskjold', 'Dag Hammarskjold', '750', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (556, 69, 1, 'Edificio de Sede 00001 - Av. Boulogme Sur Mer', 'Av. Boulogme Sur Mer', '665', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (557, 69, 1, 'Edificio de Sede 00001 - Arístides Villanueva', 'Arístides Villanueva', '773', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (558, 69, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (559, 70, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (560, 70, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (561, 71, 2, 'Edificio de Sede 00002 - Soler', 'Soler', '3666', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (562, 71, 2, 'Edificio de Sede 00002 - Anchorena', 'Anchorena', '1314', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (563, 71, 3, 'Edificio de Sede 00003 - Mario Bravo', 'Mario Bravo', '1259', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (565, 71, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (566, 73, 2, 'Edificio de Sede 00002 - José Antonio Cabrera', 'José Antonio Cabrera', '3507', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (567, 73, 1, 'Edificio de Sede 00001 - Lavalle', 'Lavalle', '393', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (568, 73, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (569, 74, 1, 'Edificio de Sede 00001 - Pellegrini', 'Pellegrini', '1332', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (570, 75, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (571, 75, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (572, 75, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (573, 76, 3, 'Edificio de Sede 00003 - Av. Corrientes', 'Av. Corrientes', '1723', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (574, 76, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (575, 77, 1, 'Edificio de Sede 00001 - 9 de julio', '9 de julio', '165', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (576, 77, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (577, 77, 1, 'Edificio de Sede 00001 - 9 de Julio 165', '9 de Julio 165', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (578, 77, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (579, 77, 2, 'Edificio de Sede 00002 - Roca', 'Roca', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (580, 77, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (581, 77, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (582, 77, 3, 'Edificio de Sede 00003 - .', '.', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (583, 78, 6, 'Edificio de Sede 00006 - Calle 29', 'Calle 29', '317', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (584, 78, 7, 'Edificio de Sede 00007 - Champagnat', 'Champagnat', '1599', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (585, 78, 8, 'Edificio de Sede 00008 - Gob. Virasoro', 'Gob. Virasoro', NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (586, 78, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (587, 79, 1, 'Edificio de Sede 00001 - Plácido Martínez', 'Plácido Martínez', '886', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (588, 79, 1, 'Edificio de Sede 00001 - La Rioja', 'La Rioja', '455', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (589, 82, 1, 'Edificio de Sede 00001 - Rondeau', 'Rondeau', ' 165', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (590, 83, 2, 'Edificio de Sede 00002 - Teniente General Juan Domingo Perón', 'Teniente General Juan Domingo Perón', '2933', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (591, 84, 1, 'Edificio de Sede 00001 - Avenida de Acceso Este', 'Avenida de Acceso Este', '2245', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (592, 84, 1, 'Edificio de Sede 00001 - Avenida de Acceso  Este', 'Avenida de Acceso  Este', '2245', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (593, 84, 2, 'Edificio de Sede 00002 - Espejo', 'Espejo', '256', NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (594, 84, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (595, 85, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (596, 85, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (597, 85, 3, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (598, 89, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) VALUES (599, 89, 1, NULL, NULL, NULL, NULL, NULL);


--
-- TOC entry 1542 (class 0 OID 673500787)
-- Dependencies: 1174
-- Data for Name: soe_instituciones; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (1, 'UNIVERSIDAD DE BUENOS AIRES', 'UBA', 'UBA', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (2, 'UNIVERSIDAD NACIONAL DE CATAMARCA', 'CATAMARCA', 'UNCAT', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (3, 'UNIVERSIDAD NACIONAL DEL CENTRO DE LA PROVINCIA DE BUENOS AIRES', 'CENTRO', 'UNCPBA', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (4, 'UNIVERSIDAD NACIONAL DEL COMAHUE', 'COMAHUE', 'UNCOM', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (5, 'UNIVERSIDAD NACIONAL DE CORDOBA', 'CORDOBA', 'UNC', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (6, 'UNIVERSIDAD NACIONAL DE CUYO', 'CUYO', 'UNCUY', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (7, 'UNIVERSIDAD NACIONAL DE ENTRE RIOS', 'ENTRE RIOS', 'UNER', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (8, 'UNIVERSIDAD NACIONAL DE FORMOSA', 'FORMOSA', 'UNFO', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (9, 'UNIVERSIDAD NACIONAL DE GENERAL SAN MARTIN', 'SAN MARTIN', 'UNGSM', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (10, 'UNIVERSIDAD NACIONAL DE GENERAL SARMIENTO', 'SARMIENTO', 'UNGSAR', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (11, 'UNIVERSIDAD NACIONAL DE JUJUY', 'JUJUY', 'UNJU', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (12, 'UNIVERSIDAD NACIONAL DE LA MATANZA', 'MATANZA', 'UNLM', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (13, 'UNIVERSIDAD NACIONAL DE LANUS', 'LANUS', 'UNLA', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (14, 'UNIVERSIDAD NACIONAL DE LA PAMPA', 'LA PAMPA', 'UNLAPAMPA', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (15, 'UNIVERSIDAD NACIONAL DE LA PATAGONIA AUSTRAL', 'LA PATAGONIA AUSTRAL', 'UNLPAU', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (16, 'UNIVERSIDAD NACIONAL DE LA PATAGONIA SAN JUAN BOSCO', 'LA PATAG. SAN JUAN BOSCO', 'UNLPSJB', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (17, 'UNIVERSIDAD NACIONAL DE LA PLATA', 'LA PLATA', 'UNLP', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (18, 'UNIVERSIDAD NACIONAL DE LA RIOJA', 'LA RIOJA', 'UNLR', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (19, 'UNIVERSIDAD NACIONAL DEL LITORAL', 'LITORAL', 'UNL', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (20, 'UNIVERSIDAD NACIONAL DE LOMAS DE ZAMORA', 'LOMAS DE ZAMORA', 'UNLZ', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (21, 'UNIVERSIDAD NACIONAL DE LUJAN', 'LUJAN', 'UNLU', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (22, 'UNIVERSIDAD NACIONAL DE MAR DEL PLATA', 'MAR DEL PLATA', 'UNMP', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (23, 'UNIVERSIDAD NACIONAL DE MISIONES', 'MISIONES', 'UNAM', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (24, 'UNIVERSIDAD NACIONAL DEL NORDESTE', 'NORDESTE', 'UNNE', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (25, 'UNIVERSIDAD NACIONAL DE QUILMES', 'QUILMES', 'UNQUI', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (26, 'UNIVERSIDAD NACIONAL DE RIO CUARTO', 'RIO CUARTO', 'UNRC', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (27, 'UNIVERSIDAD NACIONAL DE ROSARIO', 'ROSARIO', 'UNR', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (28, 'UNIVERSIDAD NACIONAL DE SALTA', 'SALTA', 'UNAS', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (29, 'UNIVERSIDAD NACIONAL DE SAN JUAN', 'SAN JUAN', 'UNSJ', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (30, 'UNIVERSIDAD NACIONAL DE SAN LUIS', 'SAN LUIS', 'UNSL', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (31, 'UNIVERSIDAD NACIONAL DE SANTIAGO DEL ESTERO', 'SANTIAGO DEL ESTERO', 'UNSE', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (32, 'UNIVERSIDAD NACIONAL DEL SUR', 'SUR', 'UNS', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (33, 'UNIVERSIDAD NACIONAL DE TUCUMAN', 'TUCUMAN', 'UNT', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (34, 'UNIVERSIDAD NACIONAL DE VILLA MARIA', 'VILLA MARIA', 'UNVM', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (35, 'UNIVERSIDAD TECNOLOGICA NACIONAL', 'UTN', 'UTN', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (36, 'UNIVERSIDAD NACIONAL DE TRES DE FEBRERO', 'TRES DE FEBRERO', 'UNTFE', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (37, 'INSTITUTO DE ENSEÑANZA SUPERIOR DEL EJERCITO', 'IESE', 'IESE', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (38, 'INSTITUTO UNIVERSITARIO AERONAUTICO', 'IAERONAUTICO', 'IUA', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (39, 'INSTITUTO UNIVERSITARIO NAVAL', 'INAVAL', 'IUN', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (40, 'INSTITUTO UNIVERSITARIO DE LA POLICIA FEDERAL ARGENTINA', 'IPOLICIA', 'IUPF', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (41, 'UNIVERSIDAD NOTARIAL ARGENTINA', 'NOTARIAL', 'UNOTA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (42, 'UNIVERSIDAD CEMA', 'CEMA', 'UCEM', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (43, 'ESCUELA UNIVERSITARIA DE TEOLOGIA', 'ESCUELA DE TEOLOGIA', 'EUTEO', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (44, 'INSTITUTO UNIVERSITARIO NACIONAL DEL ARTE', 'IUNA', 'IUNA', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (45, 'INSTITUTO TECNOLOGICO DE BUENOS AIRES', 'ITBA', 'ITBA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (46, 'UNIVERSIDAD FAVALORO', 'FAVALORO', 'UFAV', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (47, 'INSTITUTO UNIVERSITARIO DE CS. DE LA SALUD - FUNDACION UNIVERSITARIA HECTOR A. BARCELO', 'BARCELO', 'IUCSAL', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (48, 'PONTIFICIA UNIVERSIDAD CATOLICA ARGENTINA SANTA MARIA DE LOS BUENOS AIRES', 'UCA', 'UCA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (49, 'UNIVERSIDAD ABIERTA INTERAMERICANA', 'ABIERTA INTERAMERICANA', 'UAI', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (50, 'UNIVERSIDAD ADVENTISTA DEL PLATA', 'ADVENTISTA', 'UAPL', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (51, 'UNIVERSIDAD ARGENTINA DE LA EMPRESA', 'UADE', 'UADE', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (52, 'UNIVERSIDAD ARGENTINA JOHN F. KENNEDY', 'KENNEDY', 'UAJFK', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (53, 'UNIVERSIDAD ATLANTIDA ARGENTINA', 'ATLANTIDA', 'UAA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (54, 'UNIVERSIDAD AUSTRAL', 'AUSTRAL', 'UA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (55, 'UNIVERSIDAD BLAS PASCAL', 'BLAS PASCAL', 'UBP', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (56, 'UNIVERSIDAD CENTRO DE ALTOS ESTUDIOS EN CIENCIAS EXACTAS', 'CAECE', 'CAECE', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (57, 'UNIVERSIDAD CATOLICA DE CORDOBA', 'CATOLICA CORDOBA', 'UCCOR', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (58, 'UNIVERSIDAD CATOLICA DE CUYO', 'CATOLICA DE CUYO', 'UCCUY', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (59, 'UNIVERSIDAD CATOLICA DE LA PLATA', 'CATOLICA DE LA PLATA', 'UCLP', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (60, 'UNIVERSIDAD CATOLICA DE SALTA', 'CATOLICA DE SALTA', 'UCS', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (61, 'UNIVERSIDAD CATOLICA DE SANTA FE', 'CATOLICA DE SANTA FE', 'UCSFE', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (62, 'UNIVERSIDAD CATOLICA DE SANTIAGO DEL ESTERO', 'CATOLICA DE SANTIAGO DEL ESTERO', 'UCSE', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (63, 'UNIVERSIDAD CHAMPAGNAT', 'CHAMPAGNAT', 'UCHAM', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (64, 'UNIVERSIDAD DE BELGRANO', 'BELGRANO', 'UB', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (65, 'UNIVERSIDAD DE CIENCIAS EMPRESARIALES Y SOCIALES', 'CIENCIAS EMPRESARIALES', 'UCES', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (66, 'UNIVERSIDAD DE CONCEPCION DEL URUGUAY', 'CONCEPCIO DEL URUGUAY', 'UCU', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (67, 'UNIVERSIDAD DE CONGRESO', 'CONGRESO', 'UCON', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (68, 'UNIVERSIDAD DE FLORES', 'FLORES', 'UFLO', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (69, 'UNIVERSIDAD DE MENDOZA', 'MENDOZA', 'UMEN', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (70, 'UNIVERSIDAD DE MORON', 'MORON', 'UM', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (71, 'UNIVERSIDAD DE PALERMO', 'PALERMO', 'UP', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (72, 'UNIVERSIDAD DE SAN ANDRES', 'SAN ANDRES', 'USA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (73, 'UNIVERSIDAD DEL ACONCAGUA', 'ACONCAGUA', 'UAC', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (74, 'UNIVERSIDAD DEL CENTRO EDUCATIVO LATINOAMERICANO', 'CENTRO EDUCATIVO LATINOAMERICANO', 'UCEL', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (75, 'UNIVERSIDAD DEL CINE', 'DEL CINE', 'UC', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (76, 'UNIVERSIDAD DEL MUSEO SOCIAL ARGENTINO', 'MUSEO SOCIAL', 'UMSA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (77, 'UNIVERSIDAD DEL NORTE SANTO TOMAS DE AQUINO', 'SANTO TOMAS', 'UNSTA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (78, 'UNIVERSIDAD DEL SALVADOR', 'SALVADOR', 'USAL', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (79, 'UNIVERSIDAD DE LA CUENCA DEL PLATA', 'CUENCA DEL PLATA', 'UCUPLA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (80, 'UNIVERSIDAD DE LA FRATERNIDAD Y AGRUPACIONES SANTO TOMAS DE AQUINO (FASTA)', 'F.A.S.T.A.', 'FASTA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (81, 'UNIVERSIDAD DE LA MARINA MERCANTE', 'MARINA MERCANTE', 'UMM', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (82, 'UNIVERSIDAD EMPRESARIAL SIGLO XXI', 'SIGLO XXI', 'UESXXI', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (83, 'UNIVERSIDAD HEBREA BAR ILAN - (INSTITUCION CERRADA - R.M.083/00)', 'BAR ILAN', 'UHBI', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (84, 'UNIVERSIDAD JUAN AGUSTIN MAZA', 'MAZA', 'UJAM', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (85, 'UNIVERSIDAD MAIMONIDES', 'MAIMONIDES', 'UMA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (87, 'UNIVERSIDAD TORCUATO DI TELLA', 'DI TELLA', 'UTDT', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (88, 'INSTITUTO UNIVERSITARIO CEMIC', 'CEMIC', 'CEMIC', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (89, 'INSTITUTO UNIVERSITARIO GASTON DACHARY', 'GASTON DACHARY', 'IUGD', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (90, 'INSTITUTO UNIVERSITARIO DE LA FUNDACION ISALUD', 'ISALUD', 'ISALUD', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (91, 'INSTITUTO UNIVERSITARIO ESCUELA SUPERIOR DE ECONOMIA Y ADMINISTRACION DE EMPRESAS (ESEADE)', 'ESEADE', 'ESEADE', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (92, 'INSTITUTO UNIVERSITARIO ESCUELA DE MEDICINA DEL HOSPITAL ITALIANO', 'ITALIANO DE BUENOS AIRES', 'EMHI', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (93, 'INSTITUTO UNIVERSITARIO ITALIANO DE ROSARIO', 'ITALIANO DE ROSARIO', 'UNIR', 0);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (94, 'REPRESENTACION EN LA REPUBLICA ARGENTINA DE LA UNIVERSIDAD DE BOLOGNA', 'BOLOGNA', 'UNIBO', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (95, 'UNIVERSIDAD AUTONOMA DE ENTRE RIOS', 'AUTONOMA DE ENTRE RIOS', 'UADER', 3);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (96, 'INSTITUTO UNIVERSITARIO IDEA', 'IDEA', 'IDEA', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (97, 'INSTITUTO UNIVERSITARIO DE SEGURIDAD MARITIMA', 'SEGURIDAD MARITIMA', 'IUSM', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (99, 'UNIVERSIDAD NACIONAL DEL NOROESTE DE LA PROVINCIA DE BUENOS AIRES', 'JUNIN', 'UNNO', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (100, 'INSTITUTO UNIVERSITARIO ISEDET', 'ISEDET', 'ISEDET', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (101, 'FACULTAD LATINOAMERICANA DE CIENCIAS SOCIALES', 'FLACSO', 'FLACSO', 4);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (102, 'INSTITUTO UNIVERSITARIO ESCUELA ARGENTINA DE NEGOCIOS', 'ESCUELA ARGENTINA DE NEGOCIOS', 'IUEAN', 2);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (103, 'UNIVERSIDAD NACIONAL DE CHILECITO', NULL, 'UNCHI', 1);
INSERT INTO soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion) VALUES (8889, 'Institución 01', 'Institución 01', 'Institución 01', 6);


--
-- TOC entry 1543 (class 0 OID 673500790)
-- Dependencies: 1175
-- Data for Name: soe_jurisdicciones; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO soe_jurisdicciones (jurisdiccion, descripcion, estado) VALUES (1, 'Nacional', 'A');
INSERT INTO soe_jurisdicciones (jurisdiccion, descripcion, estado) VALUES (2, 'Privada', 'A');
INSERT INTO soe_jurisdicciones (jurisdiccion, descripcion, estado) VALUES (3, 'Provincial', 'A');
INSERT INTO soe_jurisdicciones (jurisdiccion, descripcion, estado) VALUES (4, 'Internacional', 'A');
INSERT INTO soe_jurisdicciones (jurisdiccion, descripcion, estado) VALUES (0, 'Indefinida', 'A');
INSERT INTO soe_jurisdicciones (jurisdiccion, descripcion, estado) VALUES (5, 'Instituto Universitario Nacional-Ley 24.521 art. 77', 'A');
INSERT INTO soe_jurisdicciones (jurisdiccion, descripcion, estado) VALUES (6, 'Privada con Autorización Definitiva', 'A');
INSERT INTO soe_jurisdicciones (jurisdiccion, descripcion, estado) VALUES (7, 'Privada con Autorización Provisoria', 'A');
INSERT INTO soe_jurisdicciones (jurisdiccion, descripcion, estado) VALUES (8, 'Extranjera', 'A');


--
-- TOC entry 1544 (class 0 OID 673500794)
-- Dependencies: 1177
-- Data for Name: soe_sedes; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (1, 1, 'SedePrincipal - UBA', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (1, 2, 'Sede - 00002 de Facultad de Ciencias Económicas', '1120');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (2, 1, 'SedePrincipal - CATAMARCA', '4700');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (3, 1, 'Sede Principal - UNICEN TANDIL', '7000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 1, 'SedePrincipal - COMAHUE', '8300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 2, 'Sede - 00002 de Facultad de Ciencias Agrarias', '8303');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 3, 'Sede - 00003 de Asentamiento Universitario San Martín de los Andes', '8370');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 4, 'Sede - 00004 de Asentamiento Universitario Villa Regina', '8336');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 5, 'Sede - 00005 de Asentamiento Universitario Zapala', '8340');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 6, 'Sede - 00006 de Facultad de Derecho y Ciencias Sociales, General Roca', '8332');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 7, 'Sede - 00007 de Facultad de Ciencias de la Educación, Sede Cipolletti', '8324');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 8, 'Sede - 00008 de Centro Universitario Regional Zona Atlántica', '8500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 9, 'Sede - 00009 de Centro Regional Universitario Bariloche', '8400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 10, 'Sede - 00010 de Módulo Chos Malal - Fac. de Economía y Administración', '8353');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 11, 'Sede - 00011 de Sede San Antonio Oeste . C.U.R.Z.A.', '8520');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 12, 'Sede - 00012 de Módulo El Hoyo - Facultad de Turismo', '9000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (4, 13, 'Sede - 00013 de Módulo Allen  de Enfernería - I.U.C.S.', '8328');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (5, 1, 'SedePrincipal - CORDOBA', '5000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (6, 1, 'Sede Principal - CUYO', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (6, 2, 'Sede - 00002 de Facultad de Ciencias Agrarias', '5507');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (6, 3, 'Sede - 00003 de Instituto Balseiro', '8400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (6, 4, 'Sede - 00004 de Facultad de Ciencias Económicas - Delegación San Rafael', '5600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (6, 5, 'Sede - 00005 de Inst.Tecnológico Univ. (Sede Gral.Alvear)', 'M5620DFC');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (6, 6, 'Sede - 00006 de Inst.Tecnológico Univ. (Sede Tunuyán)', '5560');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (7, 1, 'SedePrincipal - ENTRE RIOS', '3260');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (7, 2, 'Sede - 00002 de Facultad de Ciencias Agropecuarias', '3100');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (7, 3, 'Sede - 00003 de Facultad de Ciencias de la Alimentación', '3200');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (7, 4, 'Sede - 00004 de Facultad de Bromatología', '2820');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (8, 1, 'SedePrincipal - FORMOSA', '3600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (9, 1, 'SedePrincipal - SAN MARTIN', '1653');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (9, 2, 'Sede - 00002 de Escuela de Economía y Negocios', '1650');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (9, 3, 'Sede - 00003 de Instituto de Ciencias de la Rehabilitación', '1428');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (9, 4, 'Sede - 00004 de Escuela de Política y Gobierno', '1030');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (10, 1, 'SedePrincipal - SARMIENTO', '9999');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (11, 1, 'SedePrincipal - JUJUY', '4600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (12, 1, 'Sede Principal - SAN JUSTO', '1754');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (13, 1, 'SedePrincipal - LANUS', '1826');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (14, 1, 'Sede Principal - SANTA ROSA - LA PAMPA', '6300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (14, 2, 'Sede GENERAL PICO', '6360');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (58, 4, 'Instituto Cervantes - CORDOBA', '5800');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (15, 1, 'SedePrincipal - Patagonia Austral', '9400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (15, 2, 'Sede - 00002 Unidad Academica Caleta Olivia', '9011');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (15, 3, 'Sede - 00003 Unidad Academica Puerto San Julian', '9310');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (15, 4, 'Sede - 00004 Unidad Academica Rio Turbio', '9407');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (16, 1, 'Sede Principal - Comodoro Rivadavia', '9000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (16, 2, 'Subsede Trelew', '9100');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (16, 3, 'Subsede Esquel', '9200');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (16, 4, 'Subsede Puerto Madryn', '9120');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (16, 5, 'Subsede Ushuaia', '9410');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (17, 1, 'Sede Principal - LA PLATA', '1900');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (18, 1, 'Sede Principal - LA RIOJA', '5300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (18, 2, 'Sede Chamical', '5380');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (101, 2, 'ROSARIO', '2000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (18, 4, 'Sede Villa Unión', '5350');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (18, 5, 'Sede Chepes', '5470');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (18, 6, 'Sede Aimogasta', '5310');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (19, 1, 'SedePrincipal - LITORAL', '3000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (67, 2, 'LOCALIZACION CORDOBA', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (80, 3, 'Sede BARILOCHE', '8400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 18, 'Subsede Neuquén', '8300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (20, 1, 'Sede Principal - LOMAS DE ZAMORA', '1832');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 1, 'SedePrincipal - LUJAN', '6700');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 2, 'Sede - 00002 de Centro Regional Campana', '2804');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 3, 'Sede - 00003 de Centro Regional Chivilcoy', '6620');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 4, 'Sede - 00004 de Centro Regional General Sarmiento', '3314');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 5, 'Sede - 00005 de Delegación Académica Escobar', '1625');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 6, 'Sede - 00006 de Delegación Académica San Fernando', '1646');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 7, 'Sede - 00007 de Delegación Académica Pilar', '1629');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 8, 'Sede - 00008 de Delegación Académica Pergamino', '6660');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 9, 'Sede - 00009 de Delegación Académica 9 de Julio', '6500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 10, 'Sede - 00010 de Delegacion Academica Mercedes', '6600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (21, 11, 'Sede - 00011 de Delegación Académica Capital Federal', '1412');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (22, 1, 'SedePrincipal - MAR DEL PLATA', '7600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (22, 2, 'Sede - 00002 de Facultad de Ciencias Agrarias', '7620');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (23, 1, 'Sede Principal POSADAS', '3300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (23, 2, 'Subsede ELDORADO', '3380');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (23, 3, 'Subsede OBERA', '3360');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (24, 1, 'SedePrincipal - NORDESTE', '3400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (24, 2, 'Sede 00002 -Resistencia -Chaco', '3500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (24, 3, 'Sede 00003 -Presidencia Roque Sáenz Peña -Chaco', '3700');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (24, 4, 'Sede  00004 -Paso de los Libres- de Carreras a Término en Comercio Exterior', '3230');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (24, 5, 'Sede  00005 -Curuzú Cuatiá-de Instituto de Administración de Empresas Agropecuarias', '3460');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (25, 1, 'SedePrincipal - QUILMES', '1876');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (26, 1, 'SedePrincipal - RIO CUARTO', '5800');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (27, 1, 'SedePrincipal - ROSARIO', '2000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (27, 2, 'Sede ZAVALLA - 00002 Facultad de Ciencias Agrarias', '2123ZAV');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (27, 3, 'Sede CASILDA - 00003 Facultad de Ciencias Veterinarias', '2170');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (28, 1, 'SedePrincipal - SALTA', '4400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (28, 2, 'Sede - 00002 de Sede Regional Orán', '4530');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (28, 3, 'Sede - 00003 de Sede Regional Tartagal', '4560');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (29, 1, 'SedePrincipal - SAN JUAN', '5400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (29, 2, 'Sede - 00002 de Facultad de Ciencias Sociales', '5577');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (30, 1, 'SedePrincipal - SAN LUIS', '5700');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (31, 1, 'SedePrincipal - SANTIAGO DEL ESTERO', '4200');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (32, 1, 'SedePrincipal - SUR', '8000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (32, 2, 'Sede - 00002 de Departamento de Derecho', '3000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (33, 1, 'Sede Principal - TUCUMAN', '4000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (34, 2, 'Extensión Pilar', '5972');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (34, 1, 'SedePrincipal - VILLA MARIA', '5903');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (34, 3, 'Extensión Laboulaye', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 1, 'SedePrincipal - UTN', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 2, 'Sede - 00002 de Facultad Regional Avellaneda', '1870');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 3, 'Sede - 00003 de Facultad Regional Bahía Blanca', '8000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 4, 'Sede - 00004 de Facultad Regional Buenos Aires', '1179');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 5, 'Sede - 00005 de Facultad Regional Concepción del Uruguay', '3260');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 6, 'Sede - 00006 de Facultad Regional Córdoba', '5016');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 7, 'Sede - 00007 de Facultad Regional Delta', '2804');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 8, 'Sede - 00008 de Facultad Regional General Pacheco', '1618');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 9, 'Sede - 00009 de Facultad Regional Haedo', '1706');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 10, 'Sede - 00010 de Facultad Regional La Plata', '1900');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 11, 'Sede - 00011 de Facultad Regional Mendoza', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 12, 'Sede - 00012 de Facultad Regional Paraná', '3102');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 13, 'Sede - 00013 de Facultad Regional Resistencia', '3500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 14, 'Sede - 00014 de Facultad Regional Rosario', '2000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 15, 'Sede - 00015 de Facultad Regional San Francisco', '2400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 16, 'Sede - 00016 de Facultad Regional San Nicolás', '2900');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 17, 'Sede - 00017 de Facultad Regional San Rafael', '5600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 18, 'Sede - 00018 de Facultad Regional Santa Fé', '3000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 19, 'Sede - 00019 de Facultad Regional Tucumán', '4000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 20, 'Sede - 00020 de Facultad Regional Villa María', '5902');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 21, 'Sede - 00021 de Unidad Académica Concordia', '3200');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 22, 'Sede - 00022 de Unidad Académica Confluencia', '8318');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 23, 'Sede - 00023 de Unidad Académica Chubut', '9120');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 24, 'Sede - 00024 de Unidad Académica La Rioja', '5300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 25, 'Sede - 00025 de Unidad Académica Rafaela', '2302');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 26, 'Sede - 00026 de Unidad Académica Reconquista', '3560');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 27, 'Sede - 00027 de Unidad Académica Rio Gallegos', '9400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 28, 'Sede - 00028 de Unidad Académica Río Grande', '9420');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 29, 'Sede - 00029 de Unidad Académica Trenque Lauquen', '6400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (35, 30, 'Sede - 00030 de Unidad Académica Venado Tuerto', '2600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (36, 1, 'SedeRectorado - TRES DE FEBRERO', '1678');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (37, 1, 'SedePrincipal - IESE', '1425');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (37, 2, 'Sede - 00002 de Unidad Académica Colegio Militar de la Nación', '1684');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (37, 3, 'Sede - 00003 de Unidad Académica Escuela de Defensa Nacional (Asociada)', '1084');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (38, 1, 'SedePrincipal - IAERONAUTICO', '5000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (38, 2, 'Sede - 00002 de Escuela de Ingeniería Aeronáutica', '1084');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (39, 1, 'SedePrincipal - INAVAL', '1429');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (39, 2, 'Sede - 00002 de Unidad Academica Escuela Nacional de Nautica', '1104');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (39, 3, 'Sede - 00003 de Unidad Académica Escuela Naval Militar', '1929');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (39, 4, 'Sede - 00004 de Unidad Académica Escuela de Oficiales de la Armada', '8109');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (39, 5, 'Sede - 00005 de Unidad Académica Escuela de Ciencias del Mar', '1408');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (39, 6, 'Sede - 00006 de Unidad Academica Escuela Ciencias del Mar', '1404');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (40, 1, 'SedePrincipal - IPOLICIA', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (41, 1, 'SedePrincipal - La Plata', '1900');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (42, 1, 'SedePrincipal - CEMA', '1000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (42, 2, 'Sede - 00002 de Departamento de Economía', '1054');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (43, 1, 'SedePrincipal - ESCUELA DE TEOLOGIA', '7600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (44, 1, 'SedePrincipal - IUNA', '1115');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (44, 2, 'Departamento de Artes Visuales "Prilidiano Pueyrredón"', '1054');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (44, 3, 'Departamento de Artes del Movimiento "Maria Ruanova"', '1020');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (45, 1, 'SedePrincipal - ITBA', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (46, 1, 'Sede Principal - CAPITAL FEDERAL', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (47, 1, 'Sede Principal - CAPITAL FEDERAL', '1000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (48, 1, 'Sede Principal - UCA CIUDAD BUENOS AIRES', '1107');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (101, 3, 'SAN JUAN', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (48, 3, 'CENTRO REGIONAL PERGAMINO - BS. AS.', '2701');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (48, 4, 'SEDE ROSARIO - SANTA FE', '2000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (101, 4, 'CORDOBA', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (48, 6, 'SEDE PARANÁ - ENTRE RÍOS', '3102');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (48, 7, 'SEDE MENDOZA', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (102, 1, 'Sede Principal CAPITAL FEDERAL', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (49, 1, 'SedePrincipal - ABIERTA INTERAMERICANA', '1069');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (49, 2, 'Sede - 00002 de Facultad de Tecnología Informática', '1147');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (49, 3, 'Sede - 00003 de Facultad de Arquitectura.', '1428');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (49, 4, 'Sede - 00004 de Facultad de Desarrollo e Investigación Educativos.', '1712');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (49, 5, 'Sede - 00005 de Facultad de Motricidad Humana y Deportes', '1182');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (50, 1, 'Sede Principal - Va. LIB. SAN MARTIN (ENTRE RIOS)', '3103');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (51, 1, 'SedePrincipal - UADE', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (51, 2, 'Sede - 00002 de Facultad de Ciencias Agrarias', '1107');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (52, 1, 'SedePrincipal - KENNEDY', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (52, 2, 'Sede - 00002 de Escuela de Graduados', '1041');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (53, 1, 'SEDE CENTRAL - Mar de Ajo', '7109');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (53, 2, 'Anexo -  00002 Dolores', '7100');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (53, 4, 'Anexo -  00004 General Madariaga', '7163');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (53, 5, 'Anexo -  00005 Pinamar', '7167');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (53, 6, 'Anexo -  00006 Mar del Plata', '7600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (54, 1, 'Sede Principal - CAPITAL FEDERAL', '1053');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (54, 2, 'Sede PILAR', '1629');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (54, 3, 'Sede ROSARIO', '2000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (56, 1, 'SedePrincipal - CAECE', '1198');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (56, 2, 'Sede - 00002 de Departamento de Sistemas', '1084');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (57, 1, 'Sede Principal - CORDOBA', '5000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (58, 1, 'Sede Principal - SAN JUAN', '5400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (59, 1, 'SedePrincipal - CATOLICA DE LA PLATA', '1900');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (59, 2, 'Sede - 00002 de Unidad Académica Bernal. Facultad de Arquitectura', '1876');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (60, 1, 'SedePrincipal - CATOLICA DE SALTA', '4400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (60, 2, 'Sede - 00002 de Subsede Académica Buenos Aires', '1041');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (61, 1, 'Sede Principal - SANTA FE', '3000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (61, 2, 'SUBSEDE POSADAS', '3300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (62, 1, 'SedePrincipal - CATOLICA DE SANTIAGO DEL ESTERO', '4200');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (62, 2, 'Sede - 00002 de Departamento Académico San Salvador', '4600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (62, 3, 'Sede - 00003 de Departamento Académico Buenos Aires', '1636');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (62, 4, 'Sede - 00004 de Departamento Académico Rafaela', '2300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (63, 1, 'SedePrincipal - CHAMPAGNAT', '5501');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (63, 2, 'Sede - 00002 de Facultad de Derecho', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (64, 1, 'SedePrincipal - BELGRANO', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (64, 2, 'Sede - 00002 de Escuela de Economía', '1060');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (65, 1, 'Sede Principal - UCES BUENOS AIRES', '1000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (79, 2, 'Delegación Gobernador Virasoro', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (79, 3, 'Delegación Monte Caseros', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (79, 4, 'Delegación Santa Isabel', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (79, 5, 'Delegación Presidencia Roque Saenz Peña', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (14, 3, 'Localización CASTEX', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (66, 1, 'Sede Principal - CONCEPCION DEL URUGUAY', '3260');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (66, 2, 'Centro Regional GUALEGUAYCHU', '2820');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (67, 1, 'SEDE PRINCIPAL  - MENDOZA', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (68, 1, 'Sede Principal - CAPITAL FEDERAL', '1406');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (69, 1, 'SedePrincipal - MENDOZA', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (70, 1, 'SedePrincipal - MORON', '1708');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (71, 1, 'SedePrincipal - PALERMO', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (71, 2, 'Sede - 00002 de Facultad de Arquitectura', '1425');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (71, 3, 'Sede - 00003 de Facultad de Ciencias Económicas y Empresariales', '1175');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (72, 1, 'Sede Principal - SAN ANDRES', '1644');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (14, 4, 'Localización 9 de Julio', '6500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (73, 1, 'SedePrincipal - ACONCAGUA', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (73, 2, 'Sede - Tunuyan', '5560');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (73, 3, 'Sede - Facultad de Ciencias Sociales y Administrativas', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (74, 1, 'Sede Principal - UCEL', '2000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (75, 1, 'SedePrincipal - DEL CINE', '1000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (76, 1, 'SedePrincipal - MUSEO SOCIAL', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (76, 2, 'Sede - 00002 Facultad de Ciencias Economicas, de la Administracion y de los Negocios', '1042');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (76, 3, 'Sede - 00003 Facultad de Ciencias Juridicas y Politicas', '1041');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (77, 1, 'SedePrincipal - SANTO TOMAS', '4000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (77, 2, 'Sede - 00002 de Centro Universitario de Concepción', '4146');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (77, 3, 'Sede - 00003 de Centro de Estudios Institucionales (Bs.As)', '1020');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 1, 'Sede Principal - Ciudad de Buenos Aires', '1427');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (44, 4, 'Departamento de Artes Audiovisuales', '1054');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (23, 4, 'Delegación CIUDAD DE BUENOS AIRES', '1033');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 2, 'Sede  Basavilbaso', '3170');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 3, 'Sede Chajarí', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 6, 'Subsede Mercedes - Campus N. S. de Luján', '6600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 7, 'Subsede Pilar - Campus N. S. del Pilar', '1629');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 8, 'Delegación Corrientes - Campus San Roque Gonzalez de Santa Cruz', '3340');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (79, 1, 'SedePrincipal - CUENCA DEL PLATA', '3400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (80, 1, 'Sede Principal - MAR DEL PLATA', '7600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (81, 1, 'Sede Principal - CAPITAL FEDERAL', '1034');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (82, 1, 'SedePrincipal - SIGLO XXI', '5000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (83, 1, 'SedePrincipal - BAR ILAN', '1000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (83, 2, 'Sede - 00002 de Facultad de Ciencias Biologicas', '1198');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (84, 1, 'SedePrincipal - MAZA', '5519');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (84, 2, 'Sede - 00002 de Facultad de Ciencias Empresariales', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (85, 1, 'SedePrincipal - MAIMONIDES', '1000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (85, 2, 'Sede - 00002 de Facultad de Medicina', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (85, 3, 'Sede - 00003 de Escuela de Comunicación Multimedial y Gráfica', '1411');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (87, 1, 'SedePrincipal - DI TELLA', '1428');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (88, 1, 'Sede Principal - CAPITAL FEDERAL', '1425');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (89, 1, 'Sede Principal - POSADAS', '3300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (90, 1, 'Sede Principal - CAPITAL FEDERAL', '1095');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (91, 1, 'SedePrincipal - CAPITAL FEDERAL', '1425');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (92, 1, 'Sede Principal - CAPITAL FEDERAL', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (93, 1, 'Sede Principal - ROSARIO', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (94, 1, 'Sede Principal - BOLOGNA', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 1, 'SedePrincipal - AUTONOMA DE ENTRE RIOS', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (96, 1, 'Sede Principal - IDEA ROSARIO', '2000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (97, 1, 'SedePrincipal - SEGURIDAD MARITIMA', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (99, 1, 'Sede Principal - JUNIN', '6000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (100, 1, 'Sede Principal - CAPITAL FEDERAL', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (101, 1, 'Sede Principal - CAPITAL FEDERAL', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (56, 3, 'SEDE MAR DEL PLATA', '7600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 9, 'Delegación Posadas', '3300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 10, 'Subsede Córdoba', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 11, 'Subsede Venado Tuerto', '2600');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 12, 'Subsede Bahía Blanca', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 13, 'Subsede Salta', '4400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 14, 'Subsede Gualeguaychú', '2820');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 15, 'Subsede Rosario', '2000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 16, 'Subsede Santa Rosa', '6300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (78, 17, 'Subsede Río Grande - Ushuaia', '9420');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (56, 4, 'SAN ISIDRO', '1642');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (68, 2, 'Sede COMAHUE -CIPOLLETTI', '8324');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (3, 2, 'Subsede AZUL', '7300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (3, 3, 'Subsede OLAVARRIA', '7400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (96, 2, 'Sede IDEA BUENOS AIRES', '1033');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (65, 7, 'Subsede RAFAELA', '2302');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (65, 8, 'Subsede UTN - SAN FRANCISCO', '2400');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (65, 9, 'Subsede SAN ISIDRO - BUENOS AIRES', '1642');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (74, 2, 'Sede del IUCS BARCELO - BUENOS AIRES', '1033');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (27, 4, 'BAHIA BLANCA - BUENOS AIRES', '8000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 4, 'Sede Concepción del Uruguay', '3260');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 5, 'Sede Crespo', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 6, 'Sede Diamante', '3105');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 7, 'Sede Federación', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 8, 'Sede Gualeguay', '2840');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 9, 'Sede Gualeguaychú', '2820');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 10, 'Sede La Picada', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 11, 'Sede Oro Verde', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 12, 'Sede Ramirez', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (95, 13, 'Sede Villaguay', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (51, 3, 'Sede - 00003 de Escuela de Direccion de Empresas', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (36, 2, 'Sede Caseros', '1678');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (36, 3, 'Sede Aromos', '1678');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (36, 4, 'Sede Centro Cultural Borges', '1678');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (36, 5, 'Sede Saenz Peña', '1678');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (24, 6, 'Sede 00006 -Paso de los Libres -Instituto de Comercio Exterior', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (71, 4, 'Sede - 0004 Escuela de Educacion Superior', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (97, 2, 'Sede - Olivos', '1636');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (41, 2, 'Capital Federal', '1054');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (58, 2, 'Sede MENDOZA', '5500');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (58, 3, 'Sede SAN LUIS', '5700');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (102, 2, 'Localización MARTINEZ (PCIA. BUENOS AIRES)', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (103, 1, 'Sede Principal - CHILECITO', '5360');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (47, 2, 'Sede LA RIOJA', '5300');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (47, 3, 'Sede SANTO TOME - CORRIENTES', '3340');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (55, 3, 'Sede Campus', '5147');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (55, 4, 'Sede Centro', '5000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (88, 2, 'Localización SAN ISIDRO - BS. AS.', '1642');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (89, 2, 'Localización OBERA', '3360');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (99, 2, 'Sede Pergamino', '2701');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (18, 3, 'Centro Científico Regional Catuna', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (16, 6, 'Localizacion Petrel - Agencia ACA', '9410');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (17, 2, 'Localización CNEL. PRINGLES', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (17, 3, 'Localización JUNIN', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (20, 2, 'Subsede CORRIENTES', NULL);
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (20, 3, 'Subsede SANTA FE', '3560');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (33, 2, 'Localización AGUILARES', '4152');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (66, 3, 'Centro Regional FEDERACION', '3102');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (66, 4, 'Centro Regional PARANA', '3102');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (66, 5, 'Localización ROSARIO', '2000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (47, 4, 'Localización ROSARIO (UCEL)', '2000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (37, 4, 'Rectorado', '1054');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (80, 2, 'Localización TANDIL', '7000');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (8889, 1119, 'Sede 01', '4566');
INSERT INTO soe_sedes (institucion, sede, nombre, codigopostal) VALUES (8889, 2229, 'Sede 02', '4566');


--
-- TOC entry 1545 (class 0 OID 673500797)
-- Dependencies: 1178
-- Data for Name: soe_sedesua; Type: TABLE DATA; Schema: public; Owner: dba
--



--
-- TOC entry 1546 (class 0 OID 673500801)
-- Dependencies: 1180
-- Data for Name: soe_tiposua; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO soe_tiposua (tipoua, descripcion, detalle, estado) VALUES (2, 'Tipo A', 'Tipo A', '1');
INSERT INTO soe_tiposua (tipoua, descripcion, detalle, estado) VALUES (3, 'Tipo B', 'Tipo B', '0');


--
-- TOC entry 1547 (class 0 OID 673500806)
-- Dependencies: 1182
-- Data for Name: soe_unidadesacad; Type: TABLE DATA; Schema: public; Owner: dba
--

INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (76, 5, 'Facultad', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (216, 67, 'Córdoba', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (300, 24, 'Taller de Artes Visuales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (1, 1, 'Facultad de Agronomía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (2, 1, 'Facultad de Ciencias Veterinarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (3, 1, 'Facultad de Arquitectura Diseño y Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (4, 1, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (5, 1, 'Facultad de Ciencias Exactas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (6, 1, 'Facultad de Farmacia y Bioquímica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (7, 1, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (8, 1, 'Facultad de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (9, 1, 'Facultad de Filosofía y Letras', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (10, 1, 'Facultad de Psicología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (11, 1, 'Facultad de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (12, 1, 'Facultad de Odontología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (13, 1, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (14, 1, 'Ciclo Básico Común', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (15, 1, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (16, 2, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (17, 2, 'Facultad de Tecnología y Ciencias Aplicadas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (18, 2, 'Facultad de Ciencias Económicas y de Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (19, 2, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (20, 2, 'Escuela de Arqueología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (21, 2, 'Facultad de Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (22, 2, 'Escuela de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (23, 2, 'Facultad de Ciencias Exactas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (24, 2, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (25, 37, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (26, 3, 'Facultad de Ciencias Veterinarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (27, 80, 'Sede BARILOCHE', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (28, 3, 'Facultad de Ciencias Exactas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (29, 3, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (30, 3, 'Facultad de Ciencias Humanas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (31, 80, 'Localización TANDIL', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (32, 87, 'Escuela de Gobierno', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (33, 78, 'Subsede Neuquén', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (34, 4, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (35, 4, 'Asentamiento Universitario San Martín de los Andes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (36, 4, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (37, 4, 'Asentamiento Universitario Villa Regina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (38, 4, 'Asentamiento Universitario Zapala', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (39, 4, 'Facultad de Economía y Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (40, 4, 'Facultad de Derecho y Ciencias Sociales, General Roca', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (41, 4, 'Facultad de Turismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (42, 4, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (43, 4, 'Escuela Superior de Idiomas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (44, 4, 'Facultad de Ciencias de la Educación, Sede Cipolletti', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (45, 4, 'Módulo Neuquén - Fac. de Derecho y Cs. Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (46, 4, 'Instituto Universitario de Ciencias para la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (47, 4, 'Centro Universitario Regional Zona Atlántica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (48, 4, 'Centro Regional Universitario Bariloche', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (49, 4, 'Módulo Chos Malal - Fac. de Economía y Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (50, 4, 'Sede San Antonio Oeste . C.U.R.Z.A.', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (51, 4, 'Carrera de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (52, 4, 'Módulo El Hoyo - Facultad de Turismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (53, 4, 'Módulo Chos Malal  - Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (54, 4, 'Módulo Allen  de Enfernería - I.U.C.S.', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (55, 5, 'Facultad de Ciencias Agropecuarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (56, 5, 'Facultad de Arquitectura, Urbanismo y Diseño Industrial', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (57, 5, 'Facultad de Ciencias Exactas, Físicas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (58, 5, 'Facultad de Ciencias Químicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (59, 5, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (60, 5, 'Facultad de Derecho y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (61, 5, 'Escuela de Trabajo Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (62, 5, 'Facultad de Filosofía y Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (63, 5, 'Escuela Superior de Lenguas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (64, 5, 'Facultad de Ciencias Médicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (65, 5, 'Escuela de Nutrición', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (66, 5, 'Escuela deTecnología Médica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (67, 5, 'Escuela de Enfermería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (68, 5, 'Escuela de Fonoaudiología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (69, 5, 'Escuela de Kinesiología y Fisioterapia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (70, 5, 'Facultad de Odontología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (71, 5, 'Escuela de Ciencias de Información', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (72, 5, 'Facultad de Psicología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (73, 5, 'Facultad de Matemáticas, Astronomía y Física', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (74, 5, 'Centro de Estudios Avanzados', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (75, 5, 'Instituto de Investigación y Formación en la Administración Pública', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (77, 5, 'Facultad de Lenguas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (78, 6, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (79, 6, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (80, 6, 'Instituto Balseiro', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (81, 6, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (82, 6, 'Facultad de Ciencias Económicas - Delegación San Rafael', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (83, 6, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (84, 6, 'Facultad de Ciencias Políticas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (85, 6, 'Facultad de Filosofía y Letras', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (86, 6, 'Facultad de Educación Elemental y Especial', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (87, 6, 'Facultad de Artes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (88, 6, 'Escuela de Artes Plásticas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (89, 6, 'Escuela de Cerámica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (90, 6, 'Escuela de Diseño', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (91, 6, 'Escuela de Música', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (92, 6, 'Escuela de Teatro', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (93, 6, 'Facultad de Ciencias Médicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (94, 6, 'Escuela de Enfermería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (95, 6, 'Facultad de Odontología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (96, 6, 'Facultad de Ciencias Aplicadas a la Industria', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (97, 6, 'Inst.Tecnológico Univ. (Sede Luján de Cuyo)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (98, 6, 'Instituto Tecnológico Universitario', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (99, 6, 'Convenio entre: Facultad de Ciencias Médicas y Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (100, 6, 'Convenio entre: Fac.Cs.Económicas-Fac.de Ingen.-Min.Econ.y Hac.Mza-Ecole Nationale Des Ponts Chausse', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (101, 6, 'Convenio entre: Facultad de Ciencias Agrarias - Instituto Nacional de Tecnología Agropecuaria', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (102, 6, 'Convenio entre: Facultad de Ciencias Políticas y Sociales y Fac. de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (103, 6, 'Secretaría Académica - Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (104, 6, 'Escuela de Técnicos Asistenciales de Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (105, 6, 'Facultad de Artes y Diseño', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (106, 6, 'Convenio Fac.Ciencias Agrarias - Fac.Cs. Aplicadas a la Industria', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (107, 6, 'Inst.Tecnológico Univ. (Sede Gral.Alvear)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (108, 6, 'Inst.Tecnológico Univ. (Sede San Rafael)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (109, 6, 'Inst.Tecnológico Univ. (Sede Tunuyán)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (110, 6, 'Inst.Tecnológico Univ. (Sede Rivadavia)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (111, 7, 'Facultad de Ciencias Agropecuarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (112, 7, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (113, 7, 'Facultad de Ciencias de la Alimentación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (114, 7, 'Facultad de Bromatología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (115, 7, 'Facultad de Ciencias de la Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (116, 7, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (117, 7, 'Facultad de Trabajo Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (118, 7, 'Facultad de Ciencias de la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (119, 7, 'Facultad de Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (120, 8, 'Facultad de Recursos Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (121, 8, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (122, 8, 'Facultad de Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (123, 8, 'Facultad de Administración, Economía y Negocios', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (124, 8, 'PROGRAMA NUEVAS OFERTAS ACADEMICAS', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (125, 8, 'Facultad de Administración Economía y Negocios', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (126, 8, 'Instituto Universitario', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (127, 9, 'Escuela de Economía y Negocios', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (128, 9, 'Escuela de Ciencia y Tecnología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (129, 9, 'Secretaría General Académica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (130, 9, 'Escuela de Posgrado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (131, 9, 'Instituto de Tecnología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (132, 9, 'Instituto de Investigaciones Biotecnológicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (133, 9, 'Escuela de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (134, 9, 'Instituto de Ciencias de la Rehabilitación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (135, 9, 'Instituto de Tecnología "Prof. Jorge Sábato""', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (136, 9, 'Instituto de Ciencias de la Rehabilitación y el Movimiento', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (137, 9, 'Escuela de Política y Gobierno', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (138, 10, 'Instituto de Ciencias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (139, 10, 'Instituto del Conurbano', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (140, 10, 'Instituto del Desarrollo Humano', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (141, 10, 'Instituto de Industria', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (142, 10, 'Instituto de Industrias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (143, 11, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (144, 11, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (145, 11, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (146, 11, 'Facultad de Humanidades y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (147, 12, 'Departamento de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (148, 12, 'Departamento de Ingeniería e Investigaciones Tecnológicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (149, 12, 'Departamento de Humanidades y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (151, 12, 'Instituto de Postgrado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (152, 41, 'Capital Federal', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (153, 13, 'Departamento de Desarrollo Productivo y Trabajo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (154, 13, 'Departamento de Planificación y Políticas Públicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (155, 13, 'Departamento de Humanidades y Artes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (156, 13, 'Departamento de Salud Comunitaria', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (157, 13, 'Secretaría Académica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (158, 14, 'Facultad de Agronomía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (159, 14, 'Facultad de Ciencias Veterinarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (160, 14, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (161, 14, 'Facultad de Ciencias Exactas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (162, 69, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (163, 14, 'Facultad de Ciencias Humanas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (164, 69, 'Subsede San Rafael', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (165, 24, 'Instituto de Relaciones Laborales, Comunicación Social y Turismo(Rectorado)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (166, 14, 'Facultad de Ciencias Económicas y Jurídicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (167, 51, 'Escuela de Direccion de Empresas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (168, 15, 'Unidad Academica Rio Gallegos', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (169, 15, 'Unidad Academica Caleta Olivia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (170, 15, 'Unidad Academica Puerto San Julian', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (171, 15, 'Unidad Academica Rio Turbio', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (172, 15, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (173, 16, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (174, 16, 'Facultad de Ciencias Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (175, 16, 'Facultad de Ciencias Económicas - Sede Trelew', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (176, 16, 'Facultad de Humanidades y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (177, 36, 'Caseros', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (178, 16, 'Facultad  de Ciencias Naturales - Sede Esquel', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (179, 16, 'Facultad de Ingenieria - Sede Puerto Madryn', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (180, 16, 'Facultad de Ingeniería - Sede Trelew', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (181, 16, 'Facultad de Ingeniería - Sede Ushuaia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (182, 16, 'Facultad de Ingenieria  - Sede Esquel', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (183, 16, 'Facultad de Ciencia Naturales - Sede Puerto Madryn', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (184, 16, 'Facultad de Ciencias Naturales - Sede Trelew', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (185, 16, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (186, 16, 'Facultad de Ciencias Económicas - Sede Esquel', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (187, 16, 'Facultad de Humanidades y Ciencias Sociales - Sede Trelew', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (188, 16, 'Facultad Humanidades y Ciencias Sociales - Sede Ushuaia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (189, 16, 'Escuela Superior de Derecho - Sede Esquel', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (190, 36, 'Aromos', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (191, 36, 'Centro Cultural Borges', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (192, 16, 'Escuela Superior de Derecho - Sede Puerto Madryn', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (193, 16, 'Escuela Superior de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (194, 36, 'Saenz Peña', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (195, 18, 'Departamento Académico de Ciencias Exactas, Físicas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (196, 18, 'Departamento Académico de Ciencias Sociales, Jurídicas y Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (197, 18, 'Departamento Académico de Ciencias y Tecnologías Aplicadas a la Producción, al Ambiente y al Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (198, 18, 'Departamento Académico de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (199, 17, 'Facultad de Ciencias Agrarias y Forestales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (200, 17, 'Facultad de Ciencias Veterinarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (201, 17, 'Facultad de Arquitectura y Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (202, 17, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (203, 17, 'Facultad de Ciencias Exactas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (204, 17, 'Facultad de Ciencias Naturales y Museo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (205, 17, 'Facultad de Ciencias Astronómicas y Geofísicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (206, 17, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (207, 17, 'Facultad de Ciencias Jurídicas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (208, 17, 'Facultad de Periodismo y Comunicación Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (209, 17, 'Facultad de Humanidades y Ciencias de la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (210, 17, 'Facultad de Bellas Artes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (211, 17, 'Facultad de Ciencias Médicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (212, 17, 'Escuela Universitaria de Recursos Humanos y Técnicos del Equipo de Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (213, 17, 'Escuela Superior de Trabajo Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (214, 17, 'Facultad de Odontología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (215, 17, 'Facultad de Informática', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (217, 18, 'Departamento Académico de Ciencias de la Salud y la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (218, 18, 'Sede Chamical', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (219, 24, 'Instituto de Comercio Exterior -Sede Paso de los Libres', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (220, 18, 'Sede Villa Unión', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (221, 18, 'Sede Chepes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (222, 18, 'Sede Aimogasta', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (223, 76, 'Facultad de Ciencias Economicas, de la Administracion y de los Negocios', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (224, 62, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (225, 19, 'Facultad de Arquitectura, Diseño y Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (226, 19, 'Facultad de Ingeniería Química', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (227, 19, 'Facultad de Ingeniería y Ciencias Hídricas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (228, 77, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (229, 9, 'Instituto de Altos Estudios Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (230, 19, 'Facultad de Bioquímica y Ciencias Biológicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (231, 19, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (232, 19, 'Facultad de Ciencias Jurídicas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (233, 9, 'Instituto de Calidad Industrial', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (234, 45, 'Centro de Actualización Permanente en Ingeniería de Software e Ingeniería del Conocimiento', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (235, 87, 'Escuela de Negocios', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (236, 34, 'Instituto Académico Pedagógico de Ciencias Humanas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (150, 48, 'Facultad de Filosofía y Letras', 3);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (237, 19, 'Facultad de Ciencias Veterinarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (238, 34, 'Instituto Académico Pedagógico de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (239, 34, 'Pilar', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (240, 34, 'Laboulaye', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (241, 19, 'Facultad de Humanidades y Ciencias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (242, 32, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (243, 20, 'Facultad de Ingeniería y Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (244, 20, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (245, 20, 'Facultad de Ciencias Econòmicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (246, 20, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (247, 20, 'Facultad de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (248, 20, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (249, 20, 'Facultad de  Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (250, 21, 'Departamento de Ciencias Básicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (251, 21, 'Centro Regional Campana', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (252, 21, 'Centro Regional Chivilcoy', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (253, 21, 'Centro Regional General Sarmiento', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (254, 21, 'Delegación Académica Escobar', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (255, 21, 'Delegación Académica San Fernando', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (256, 21, 'Delegación Académica Pilar', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (257, 21, 'Delegación Académica Pergamino', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (258, 21, 'Delegación Académica 9 de Julio', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (259, 21, 'Delegacion Academica Moreno', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (260, 21, 'Delegacion Academica Mercedes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (261, 21, 'Delegación Académica Capital Federal', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (262, 21, 'Departamento de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (263, 22, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (264, 22, 'Facultad de Arquitectura y Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (265, 22, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (266, 22, 'Facultad de Ciencias Exactas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (267, 22, 'Facultad de Ciencias Económicas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (268, 22, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (269, 22, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (270, 22, 'Facultad de Psicología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (271, 22, 'Facultad de Ciencias de la Salud y del Servicio Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (272, 23, 'Facultad de Ciencias Forestales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (273, 23, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (274, 76, 'Facultad de Lenguas Modernas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (275, 23, 'Escuela de Enfermería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (276, 23, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (277, 23, 'Facultad de Humanidades y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (278, 12, 'Departamento de Derecho y Ciencias Políticas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (279, 76, 'Facultad de Ciencias de la Interaccion Social - Escuela de Bibliotecologia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (280, 76, 'Facultad de Ciencias de la Interaccion Social - Escuela de Periodismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (281, 24, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (282, 24, 'Facultad de Ciencias Veterinarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (283, 24, 'Facultad de Arquitectura y Urbanismo -Sede Resistencia -Chaco', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (284, 24, 'Facultad de Ingeniería -Sede Resistencia -Chaco', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (285, 24, 'Facultad de Agroindustrias -Sede Presidencia Roque Saenz Peña -Chaco', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (286, 24, 'Facultad de Ciencias Exactas y Naturales y Agrimensura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (287, 24, 'Facultad de Ciencias Económicas -Sede Resistencia -Chaco', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (288, 24, 'Facultad de Humanidades -Sede Resistencia -Chaco', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (289, 24, 'Facultad de Derecho, Ciencias Sociales y Políticas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (290, 24, 'Facultad de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (291, 24, 'Facultad de Odontología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (292, 24, 'Dirección de Bibliotecas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (293, 24, 'Carreras a Término en Comercio Exterior -Sede Paso de los Libres', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (294, 24, 'Instituto de Administración de Empresas Agropecuarias -Sede Curuzú Cuatiá', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (295, 24, 'Instituto de Economía Agropecuaria -Sede Curuzú Cuatiá', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (296, 24, 'Instituto de Ciencias Criminalísticas y Criminología(Rectorado)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (297, 24, 'Carrera a Término de Relaciones Industriales, Comunicación Social y Turismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (298, 24, 'Carrera a Término de Relaciones Laborales, Comunicación Social y Turismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (299, 24, 'Instituto Universitario Formosa', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (301, 25, 'Departamento de Ciencia y Tecnología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (302, 25, 'Departamento de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (303, 25, 'Departamento Centro de Estudios e Investigaciones', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (304, 25, 'Instituto de Estudios Sociales de la Ciencia y la Tecnología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (305, 25, 'Universidad Virtual de Quilmes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (306, 25, 'Programa de educación no presencial Universidad Virtual de Quilmes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (307, 26, 'Facultad de Agronomía y Veterinaria', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (308, 26, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (309, 26, 'Facultad de Ciencias Exactas, Físico-Químicas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (310, 26, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (311, 26, 'Facultad de Ciencias Humanas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (312, 26, 'Secretaria Académica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (313, 27, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (314, 27, 'Facultad de Ciencias Veterinarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (315, 27, 'Facultad de Arquitectura, Planeamiento y Diseño', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (316, 27, 'Facultad de Ciencias Exactas, Ingeniería y Agrimensura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (317, 27, 'Facultad de Ciencias Bioquímicas y Farmacéuticas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (318, 27, 'Facultad de Ciencias Económicas y Estadística', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (319, 27, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (320, 27, 'Facultad de Ciencia Política y Relaciones Internacionales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (321, 27, 'Facultad de Humanidades y Artes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (322, 27, 'Facultad de Psicología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (323, 27, 'Facultad de Ciencias Médicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (324, 27, 'Facultad de Odontología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (325, 27, 'Instituto Politécnico Superior General San Martín', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (326, 27, 'Escuela Superior de Comercio Libertador General San Martín', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (327, 27, 'Centro de Estudios Interdisciplinarios', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (328, 21, 'Departamento de Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (329, 21, 'Departamento de Tecnología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (330, 79, 'Licenciatura en Ciencias de la Educación- Ciclo de Licenciatura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (331, 28, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (332, 28, 'Facultad de Ciencias Exactas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (333, 28, 'Facultad de Ciencias Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (334, 28, 'Facultad de Ciencias Económicas, Jurídicas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (335, 28, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (336, 28, 'Facultad de Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (337, 28, 'Sede Regional Orán', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (338, 28, 'Sede Regional Tartagal', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (339, 29, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (340, 29, 'Facultad de Arquitectura, Urbanismo y Diseño', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (341, 29, 'Facultad de Ciencias Exactas, Físicas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (342, 29, 'Facultad de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (343, 29, 'Facultad de Filosofía, Humanidades y Artes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (344, 30, 'Facultad de Ingeniería y Ciencias Económico-Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (345, 30, 'Facultad de Cs. Físico-Matemáticas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (346, 30, 'Facultad de Química, Bioquímica y Farmacia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (347, 30, 'Facultad de Ciencias Humanas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (348, 30, 'Departamento de Ens. Téc. Instrumental', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (349, 30, 'Departamento de Educación a Distancia y Abierta', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (350, 31, 'Facultad de Agronomía y Agroindustrias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (351, 31, 'Facultad de Ciencias Forestales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (352, 31, 'Facultad de Ciencias  Exactas y Tecnológicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (353, 31, 'Facultad de Humanidades, Cs. Sociales y de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (354, 31, 'Escuela para la Innovación Educativa', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (355, 31, 'Secretaría Académica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (356, 32, 'Departamento de Agronomía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (357, 32, 'Departamento de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (358, 32, 'Departamento de Ingeniería Eléctrica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (359, 32, 'Departamento de Química e Ingeniería Química', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (360, 32, 'Departamento de Física', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (361, 32, 'Departamento de Matemática', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (362, 32, 'Departamento de Biología, Bioquímica y Farmacia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (363, 32, 'Departamento de Geología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (364, 32, 'Departamento de Ciencias de la Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (365, 32, 'Departamento de Economía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (366, 32, 'Departamento de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (367, 32, 'Departamento de Geografía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (368, 32, 'Departamento de Ciencias de la Computación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (369, 32, 'Departamento de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (370, 32, 'CEMS', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (371, 32, 'Escuela Normal Superior', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (372, 32, 'Escuela de Agricultura y Ganadería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (373, 32, 'Escuela Ciclo Básico', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (374, 32, 'Escuela Superior de Comercio', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (375, 32, 'Departamento de Química', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (376, 32, 'Departamento de Ingeniería Química', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (377, 33, 'Facultad de Agronomía y Zootecnia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (378, 33, 'Facultad de Arquitectura y Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (379, 33, 'Facultad de Ciencias Exactas y Tecnología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (380, 33, 'Facultad de Ciencias Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (381, 33, 'Facultad de Bioquímica, Química y Farmacia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (382, 33, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (383, 33, 'Facultad de Derecho y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (384, 33, 'Facultad de Filosofía y Letras', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (385, 33, 'Escuela Universitaria de Educación Física', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (386, 33, 'Facultad de Artes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (387, 79, 'Licenciatura en Ciencias de la Educación- Ciclo de Licenciatura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (388, 33, 'Facultad de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (389, 33, 'Escuela Universitaria de Enfermería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (390, 33, 'Facultad de Odontología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (391, 33, 'Facultad de Psicología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (392, 79, 'Licenciatura en Ciencias de la Educación- Ciclo de Licenciatura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (393, 79, 'Licenciatura en Ciencias de la Educación- Ciclo de Licenciatura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (394, 40, 'Facultad de Ciencias Juridicas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (395, 40, 'Centro de Educación a Distancia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (396, 40, 'Facultad de Ciencias Biomédicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (397, 40, 'Facultad de Ciencias de la Criminalística', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (398, 34, 'Instituto Académico Pedagógico de Ciencias Básicas y Aplicadas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (399, 35, 'Facultad Regional Avellaneda', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (400, 35, 'Facultad Regional Bahía Blanca', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (401, 35, 'Facultad Regional Buenos Aires', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (402, 35, 'Facultad Regional Concepción del Uruguay', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (403, 35, 'Facultad Regional Córdoba', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (404, 35, 'Facultad Regional Delta', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (405, 35, 'Facultad Regional General Pacheco', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (406, 35, 'Facultad Regional Haedo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (407, 35, 'Facultad Regional La Plata', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (408, 35, 'Facultad Regional Mendoza', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (409, 35, 'Facultad Regional Paraná', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (410, 35, 'Facultad Regional Resistencia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (411, 35, 'Facultad Regional Rosario', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (412, 35, 'Facultad Regional San Francisco', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (413, 35, 'Facultad Regional San Nicolás', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (414, 35, 'Facultad Regional San Rafael', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (415, 35, 'Facultad Regional Santa Fé', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (416, 35, 'Facultad Regional Tucumán', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (417, 35, 'Facultad Regional Villa María', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (418, 35, 'Unidad Académica Concordia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (419, 35, 'Unidad Académica Confluencia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (420, 35, 'Unidad Académica Chubut', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (421, 35, 'Unidad Académica La Rioja', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (422, 35, 'Unidad Académica Rafaela', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (423, 35, 'Unidad Académica Reconquista', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (424, 35, 'Unidad Académica Rio Gallegos', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (425, 35, 'Unidad Académica Río Grande', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (426, 35, 'Unidad Académica Trenque Lauquen', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (427, 35, 'Unidad Académica Venado Tuerto', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (428, 35, 'Facultad Regional Rafaela', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (429, 35, 'Facultad Regional Rio Grande', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (430, 35, 'Facultad Regional Venado Tuerto', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (431, 35, 'Facultad Regional Rawson', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (432, 36, 'Secretaría Académica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (433, 37, 'Unidad Académica Colegio Militar de la Nación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (434, 37, 'Unidad Académica Escuela Superior Técnica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (435, 37, 'Unidad Académica Escuela Superior de Guerra', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (436, 37, 'Unidad Académica Escuela de Defensa Nacional (Asociada)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (437, 38, 'Escuela de Ingeniería Aeronáutica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (438, 38, 'Facultad de Educación a Distancia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (439, 38, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (440, 38, 'Facultad de Ciencias de la Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (441, 39, 'Unidad Academica Escuela Nacional de Nautica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (442, 39, 'Unidad Académica Escuela Naval Militar', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (443, 39, 'Unidad Académica Escuela de Oficiales de la Armada', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (444, 39, 'Unidad Académica Escuela de Guerra Naval', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (445, 39, 'Unidad Académica Escuela de Ciencias del Mar', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (446, 39, 'Unidad Academica Escuela Ciencias del Mar', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (447, 39, 'Unidad Academica Escuela de Guerra Naval', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (448, 40, 'Sede', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (449, 43, 'Sede Central', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (450, 44, 'Departamento de Artes Visuales "Prilidiano Pueyrredón"', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (451, 44, 'Departamento de Artes Musicales y Sonoras "Carlos Lopez Buchardo"', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (452, 44, 'Departamento de Artes del Movimiento "Maria Ruanova"', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (453, 44, 'Departamento de Artes Dramáticas "Antonio Cunill Cabanellas"', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (454, 44, 'Carreras de Folklore', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (455, 44, 'Carreras de Formación Docente', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (456, 45, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (457, 48, 'Facultad de Ciencias Físico Matemáticas e Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (458, 71, 'Escuela de Educacion Superior', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (459, 48, 'Facultad de Ciencias Sociales y Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (460, 48, 'CENTRO REGIONAL PERGAMINO', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (461, 48, 'Facultad de Ciencias Económicas del Rosario', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (462, 48, 'Facultad de Derecho y Ciencias Sociales del Rosario', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (463, 48, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (464, 75, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (466, 48, 'Facultad de Humanidades y Ciencias de la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (467, 71, 'Escuela Politica y Gestion Publica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (468, 48, 'Facultad de Artes y Ciencias Musicales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (469, 48, 'Facultad de Química e Ingeniería Fray Rogelio Bacon', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (470, 48, 'Facultad de Ciencias Económicas San Francisco', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (471, 48, 'Facultad de Derecho Canónico', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (472, 48, 'Facultad de Teología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (473, 48, 'Facultad de Posgrado en Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (474, 48, 'Instituto de Comunicación Social, Periodismo y Publicidad', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (475, 76, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (476, 48, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (477, 97, 'Escuela de Prefectura "General Matias de Irigoyen"', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (478, 97, 'Instituto de Formacion, Perfeccionamiento y Actualizacion Docente', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (479, 97, 'Departamento Academico Buenos Aires "Prefectura Naval Argentina" de la Universidad de Santiago del Estero', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (480, 48, 'Subsede Paraná de la Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (481, 49, 'Facultad de Tecnología Informática', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (482, 49, 'Facultad de Ciencias Jurídicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (483, 49, 'Facultad de Ciencias Empresariales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (484, 49, 'Facultad de Arquitectura.', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (485, 49, 'Facultad de Desarrollo e Investigación Educativos.', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (486, 49, 'Facultad de Motricidad Humana y Deportes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (487, 49, 'Facultad de Medicina.', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (488, 49, 'Facultad de Psicología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (489, 49, 'Facultad de Ciencias de la Comunicación.', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (490, 49, 'Facultad de Turismo y Hospitalidad', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (491, 51, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (492, 51, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (493, 51, 'Facultad de Ciencias de la Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (494, 51, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (495, 51, 'Facultad de Ciencias Sociales y Jurídicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (496, 51, 'Facultad de Artes y Ciencias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (497, 52, 'Escuela de Arquitectura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (498, 52, 'Profesorado en Informática', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (499, 52, 'Escuela de Sistemas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (500, 52, 'Escuela de Bioquímica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (501, 52, 'Escuela de Farmacia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (502, 52, 'Escuela de Química', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (503, 52, 'Escuela de Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (504, 52, 'Escuela de Contador Público', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (505, 52, 'Escuela de Comercialización', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (506, 52, 'Escuela de Abogacía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (507, 52, 'Escuela de Ciencia Política', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (508, 52, 'Escuela de Sociología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (509, 52, 'Escuela de Servicio Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (510, 52, 'Escuela de Periodismo y Comunicaciones', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (511, 52, 'Escuela de Publicidad', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (512, 52, 'Escuela de Relaciones Laborales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (513, 52, 'Escuela de Relaciones Públicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (514, 52, 'Escuela de Demografía y Turismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (515, 52, 'Escuela de Ciencias de la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (516, 52, 'Escuela de Antropología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (517, 52, 'Escuela de Psicología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (518, 52, 'Escuela de Psicopedagogía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (519, 52, 'Escuela de Artes y Ciencias del Teatro', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (520, 52, 'Escuela de Graduados', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (521, 52, 'Escuela de Administración Hotelera', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (522, 52, 'Escuela de Comercio Internacional', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (523, 52, 'Escuela de Diseño Gráfico', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (524, 52, 'Escuela de Relaciones Internacionales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (525, 52, 'Escuela de Odontología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (526, 53, 'Universidad Atlántida Argentina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (527, 53, 'Unidad Academica Dolores', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (528, 53, 'Unidad Academica General Madariaga', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (529, 53, 'Unidad Academica Pinamar', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (530, 53, 'Unidad Academica Mar del Plata', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (531, 54, 'Facultad de Ciencias Biomédicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (532, 54, 'Facultad de Ciencias Empresariales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (533, 54, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (534, 54, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (535, 54, 'Instituto de Altos Estudios Empresariales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (536, 54, 'Facultad de Ciencias de la Información', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (537, 56, 'Departamento de Ciencias Pedagogicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (538, 56, 'Departamento de Sistemas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (539, 56, 'Departamento de Matemáticas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (540, 56, 'Departamento de Ciencias Biológicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (541, 56, 'Departamento de Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (542, 56, 'Departamento de Ciencias Interdisciplinarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (543, 56, 'Departamento de Filosofía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (544, 56, 'Departamento de Psicopedagogía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (545, 56, 'Departamento de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (546, 56, 'Departamento de Escuela de Posgrado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (547, 57, 'Facultad de Ciencias Agropecuarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (548, 57, 'Facultad de Arquitectura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (549, 57, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (550, 57, 'Facultad de Ciencias Químicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (551, 57, 'Facultad de Ciencias Económicas y de Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (552, 57, 'Facultad de Derecho y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (553, 57, 'Facultad de Ciencia Política y Relaciones Internacionales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (554, 57, 'Facultad de Filosofía y Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (555, 57, 'Facultad de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (556, 57, 'Instituto de Ciencias de la Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (557, 101, 'Convenio Universidad Nacional de San Juan', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (558, 58, 'Facultad de Ciencias de la Alimentación, Bioquímicas y Farmacéuticas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (559, 58, 'Facultad de Ciencias Económicas y Empresariales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (560, 58, 'Facultad de Derecho y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (561, 101, 'Convenio Universidad Católica de Cuyo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (562, 58, 'Facultad de Filosofía y Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (563, 58, 'Facultad de Ciencias Médicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (564, 101, 'Convenio Universidad Nacional de Rosario', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (565, 59, 'Facultad de Arquitectura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (566, 59, 'Facultad de Matemática Aplicada', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (567, 59, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (568, 59, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (569, 59, 'Facultad de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (570, 59, 'Facultad de Ciencias de la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (571, 59, 'Unidad Académica Bernal. Facultad de Arquitectura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (572, 59, 'Unidad Académica Bernal. Facultad de Matemática Aplicada', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (573, 59, 'Unidad Académica Bernal. Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (574, 59, 'Unidad Académica Bernal. Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (575, 59, 'Unidad Académica Bernal. Facultad de Ciencias de la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (576, 60, 'Facultad de Arquitectura y Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (577, 60, 'Facultad de Ingeniería e Informática', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (578, 60, 'Facultad de Economía y Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (579, 60, 'Facultad de Ciencias Jurídicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (580, 60, 'Escuela de Servicio Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (581, 60, 'Facultad de Artes y Ciencias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (582, 60, 'Escuela Universitaria de Educación Física', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (583, 60, 'Anexo Metan - Escuela Universitaria de Profesorados', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (584, 60, 'Escuela de Turismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (585, 60, 'Facultad de Economia y Administración - Inst.de Educ.Abierta y a Distancia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (586, 60, 'Facultad de Ciencias Informáticas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (587, 60, 'Subsede Académica Buenos Aires', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (588, 60, 'Subsede - Junta Provincial del Oporto', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (589, 60, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (590, 60, 'Facultad de Ciencias Jurídicas-Inst.de Educ.Abierta y a Distancia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (591, 60, 'Subsede Académica Buenos Aires-Inst.de Educ. Abierta y a Distancia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (592, 61, 'Facultad de Arquitectura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (593, 61, 'Facultad de Ingeniería, Geoecología y Medio Ambiente', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (594, 61, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (595, 61, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (596, 61, 'Facultad de Filosofía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (597, 61, 'Facultad de Letras', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (598, 61, 'Facultad de Ciencias de la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (599, 61, 'Departamento de Posgrado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (600, 61, 'Facultad de Historia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (601, 61, 'POSADAS', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (602, 61, 'Facultad de Ciencias de la Comunicación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (603, 61, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (604, 62, 'Facultad de Matemática Aplicada', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (605, 62, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (606, 62, 'Facultad de Ciencias Políticas, Sociales y Jurídicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (607, 62, 'Facultad de Ciencias de la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (608, 62, 'Departamento Académico San Salvador', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (609, 62, 'Departamento Académico Buenos Aires', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (610, 62, 'Departamento Académico Rafaela', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (611, 63, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (612, 63, 'Facultad de Informática', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (613, 63, 'Facultad de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (614, 63, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (615, 64, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (616, 64, 'Facultad de Arquitectura y Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (617, 64, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (618, 64, 'Facultad de Tecnología Informática', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (619, 64, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (620, 64, 'Facultad de Derecho y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (621, 64, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (622, 64, 'Facultad de Lenguas y Estudios Extranjeros', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (623, 64, 'Facultad de Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (624, 64, 'Facultad de Estudios a Distancia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (625, 64, 'Facultad de Ciencias Exactas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (626, 64, 'Escuela de Economía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (627, 64, 'Facultad de Estudios para Graduados', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (628, 40, 'Facultad de Ciencias de la Seguridad', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (629, 54, 'Escuela de Direccion de Negocios', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (630, 14, 'Facultad de Ciencias Humanas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (631, 14, 'CASTEX', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (632, 14, '9 DE JULIO', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (633, 66, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (634, 66, 'Facultad de Arquitectura y Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (635, 66, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (636, 66, 'Centro Regional Gualeguaychú', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (637, 66, 'Facultad de Ciencias Jurídicas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (638, 67, 'Mendoza', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (639, 68, 'Facultad de Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (640, 68, 'Facultad de Actividad Física y Deporte', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (641, 68, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (642, 68, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (643, 68, 'Facultad de Psicología y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (644, 68, 'Facultad de Planeamiento Socio - Ambiental', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (645, 69, 'Facultad de Arquitectura y Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (646, 69, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (647, 69, 'Facultad de Ciencias Jurídicas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (648, 69, 'Facultad de Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (649, 70, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (650, 70, 'Facultad de Arquitectura, Diseño, Arte y Urbanismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (651, 70, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (652, 70, 'Facultad de Ciencias Exactas, Químicas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (653, 70, 'Facultad de Ciencias Económicas y Empresariales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (654, 70, 'Facultad de Informática, Ciencias de la Comunicación y Técnicas Especiales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (655, 70, 'Facultad de Filosofía, Ciencias de la Educación y Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (656, 70, 'Escuela Diocesana de Servicio Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (657, 70, 'Facultad de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (658, 70, 'Facultad de Agronomía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (659, 70, 'Facultad de Derecho y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (660, 70, 'Facultad de Estudios Turísticos', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (661, 70, 'Facultad de Agronomía y Ciencias Agroalimentarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (662, 70, 'Facultad de Derecho, Ciencias Políticas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (663, 70, 'Facultad de Ciencias  Aplicadas al Turismo y la Población', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (664, 71, 'Facultad de Arquitectura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (665, 71, 'Facultad de Ciencias Económicas y Empresariales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (666, 71, 'Facultad de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (667, 71, 'Facultad de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (668, 71, 'Facultad de Diseño y Comunicación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (669, 71, 'Facultad de Ingenieria', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (670, 44, 'Departamento de Artes Audiovisuales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (671, 72, 'Departamento de Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (672, 72, 'Departamento de Economia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (673, 72, 'Departamento de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (674, 73, 'Facultad de Ciencias Sociales y Administrativas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (675, 73, 'Facultad de Ciencias Sociales y Administrativas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (676, 73, 'Facultad de Psicología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (677, 73, 'Facultad de Ciencias Médicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (678, 73, 'Escuela Superior de Lenguas Extranjeras', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (679, 73, 'Facultad de Ciencias Económicas y Jurídicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (680, 74, 'Facultad de Ciencias Económicas y Empresariales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (681, 74, 'Facultad de Química', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (682, 75, 'Facultad de Cinematografía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (683, 75, 'Facultad de Comunicación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (684, 76, 'Facultad de Ciencias Políticas. Jurídicas y Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (685, 76, 'Facultad de Servicio Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (686, 76, 'Facultad de Ciencias de la Información y Opinión', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (687, 76, 'Escuela Universitaria de Lenguas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (688, 76, 'Facultad de Ciencias de la Recuperación Humana', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (689, 76, 'Facultad de Ciencias Jurídicas y Políticas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (690, 76, 'Facultad de Ciencias Económicas, de la Administración y de los Negocios', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (691, 76, 'Facultad de Ciencias Psicológicas y Pedagógicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (692, 24, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (693, 76, 'Facultad de Ciencias de la Interacción Social-Escuela de Servicio Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (694, 76, 'Facultad de Artes y Ciencias de la Conservación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (695, 76, 'Departamento de Posgrado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (696, 77, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (697, 77, 'Facultad de Economía y Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (698, 77, 'Facultad de Ciencias Jurídicas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (699, 77, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (700, 77, 'Centro de Estudios Institucionales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (701, 77, 'Centro Universitario de Concepción', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (702, 77, 'Instituto Superior de Trabajo Social Juan XXIII', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (703, 77, 'Facultad de Antropologia y Psicologia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (704, 77, 'Facultad de Derecho y Ciencias Políticas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (705, 77, 'Centro de Estudios Institucionales (Tuc)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (706, 77, 'Facultad de Psicologia y Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (707, 77, 'Facultad de Filosofía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (708, 77, 'Centro de Estudios Institucionales (Bs.As)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (709, 77, 'Escuela de Cs. de la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (710, 44, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (711, 44, 'Carreras de Multimedia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (712, 16, 'Escuela Superior de Derecho - Sede Trelew', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (713, 23, 'Facultad de Ciencias Exactas, Químicas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (714, 23, 'Facultad de Artes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (715, 23, 'Delegación BUENOS AIRES', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (716, 95, 'Facultad de Humanidades, Artes y Ciencias Sociales -Sede Principal', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (717, 95, 'Facultad de Ciencia y Tecnología -Sede Principal', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (718, 95, 'Facultad de Ciencia de la Gestión -Sede Principal', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (719, 95, 'Facultad de Ciencias de la Vida y Salud -Sede Principal', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (720, 95, 'Facultad de Ciencia y Tecnología - Sede Basavilbaso', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (721, 95, 'Facultad de Ciencia y Tecnología - Sede Chajari', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (722, 95, 'Facultad de Ciencias de la Gestión - Sede Chajari', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (723, 95, 'Facultad de Humanidades, Artes y Ciencias Sociales - Sede Concepción del Uruguay', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (724, 95, 'Facultad de Ciencia y Tecnología - Sede Concepción del Uruguay', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (725, 95, 'Facultad de Ciencias de la Gestión - Sede concepción del Uruguay', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (726, 78, 'Pilar - Carreras de Agronomía y Tecnología de los Alimentos', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (727, 95, 'Facultad de Ciencia y Tecnología -Sede Crespo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (728, 95, 'Facultad de Ciencias de la Gestión - Sede Crespo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (729, 78, 'Delegación Provincia de Corrientes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (730, 78, 'Pilar - Carrera de Veterinaria', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (731, 95, 'Facultad de Ciencia y Tecnología Sede Diamante', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (732, 48, 'Instituto de Ciencias Políticas y Relaciones Internacionales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (733, 79, 'Facultad de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (734, 79, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (735, 79, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (736, 80, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (737, 80, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (738, 80, 'Facultad de Ciencias Jurídicas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (739, 80, 'Facultad de Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (740, 80, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (741, 82, 'Facultad Derecho y Letras', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (742, 82, 'Facultad de Economia y Administracion', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (743, 83, 'Facultad de Ciencias Biologicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (744, 83, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (745, 84, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (746, 84, 'Facultad de Ciencias Físicas, Químicas y  Matemáticas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (747, 84, 'Facultad de Farmacia y Bioquímica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (748, 84, 'Facultad de Periodismo', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (749, 84, 'Facultad de Ciencias de la Nutrición', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (750, 84, 'Facultad Tecnológica de Enología y de Industria Frutihortícola', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (751, 84, 'Facultad de Ciencias Veterinarias y Ambientales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (752, 84, 'Facultad de Kinesiología y Fisioterapia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (753, 84, 'Facultad de Ciencias Empresariales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (754, 84, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (755, 84, 'Facultad de Educación Física', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (756, 85, 'Facultad de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (757, 85, 'Facultad de Odontología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (758, 85, 'Facultad de Humanidades,Ciencias Sociales y Empresariales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (759, 85, 'Escuela de Comunicación Multimedial y Gráfica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (760, 87, 'Escuela de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (761, 95, 'Facultad de Ciencia y Tecnología - Sede Federación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (762, 87, 'Departamento de Economía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (763, 87, 'Departamento de Ciencia Política y Estudios Internacionales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (764, 87, 'Departamento de Matemática y Estadística', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (765, 87, 'Centro de Arquitectura Contemporánea', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (766, 87, 'Departamento de Historia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (767, 95, 'Facultad de Ciencias de la Vida y Salud - Sede Gualeguay', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (768, 89, 'Departamento de Administración y Comercialización', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (769, 89, 'Departamento de Informática', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (770, 60, 'Escuela de Negocios', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (771, 60, 'Escuela de Educación Permanente y Posgrados en Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (772, 38, 'Instituto Nacional de Derecho Aeronáutico y Espacial', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (773, 56, 'SEDE MAR DEL PLATA', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (774, 59, 'Facultad de Humanidades', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (775, 19, 'Facultad de Ciencias Agrarias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (776, 78, 'Facultad de Ciencias de la Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (777, 78, 'Facultad de Ciencias de la Educación y de la Comunicación Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (778, 78, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (779, 78, 'Facultad de Ciencias Jurídicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (780, 78, 'Facultad de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (781, 78, 'Facultad de Ciencia y Tecnología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (782, 78, 'Facultad de Filosofía. Historia y Letras', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (783, 78, 'Facultad de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (784, 78, 'Facultad de Psicología y Psicopedagogía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (785, 78, 'Escuela de Arte y Arquitectura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (786, 78, 'Escuela de Estudios Orientales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (787, 78, 'Subsede Mercedes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (788, 78, 'Pilar - Facultad de Ciencias Jurídicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (789, 78, 'Vicerrectorado de Investigación y Desarrollo - Instituto de Prevención de la Drogadependencia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (790, 78, 'Instituto de Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (791, 78, 'Pilar - Facultad de Filosofía, Historia y Letras', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (792, 78, 'Delegación Posadas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (793, 78, 'Subsede Córdoba', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (794, 78, 'Subsede Venado Tuerto', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (795, 78, 'Subsede Bahía Blanca', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (796, 78, 'Subsede Salta', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (797, 78, 'Subsede Gualeguaychú', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (798, 78, 'Subsede Rosario', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (799, 78, 'Subsede Santa Rosa', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (800, 78, 'Subsede Río Grande - Ushuaia', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (801, 78, 'Pilar - Facultad de Ciencias de la Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (802, 78, 'Pilar - Facultad de Ciencias de la Educación y de la Comunicación Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (803, 78, 'Pilar - Facultad de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (804, 78, 'Pilar - Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (805, 78, 'Pilar - Facultad de Psicología y Psicopedagogía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (806, 78, 'Pilar - Escuela de Arte y Arquitectura', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (807, 78, 'Vicerrectorado Académico', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (808, 72, 'Departamento de Matemática y Ciencias', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (809, 72, 'Escuela de Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (810, 56, 'SAN ISIDRO', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (811, 68, 'Facultad de Planeamiento Socio - Ambiental - COMAHUE', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (812, 68, 'Facultad de Psicología y Ciencias Sociales - COMAHUE', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (813, 68, 'Facultad de Actividad Física y Deporte - COMAHUE', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (814, 68, 'Facultad de Administracion - COMAHUE', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (815, 5, 'Escuela de Graduados de la Fac.de Ccias.Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (816, 19, 'Facultad de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (817, 19, 'Escuela Universitaria del Alimento', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (818, 19, 'Escuela Universitaria de Analisis de Alimentos', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (819, 3, 'Facultad de Agronomía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (820, 3, 'Escuela Superior de Derecho', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (821, 96, 'Rosario', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (822, 3, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (823, 3, 'Facultad de Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (824, 3, 'Facultad de Arte', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (825, 3, 'Escuela Superior de Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (826, 64, 'Departamento de Estudios de Postgrados', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (827, 64, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (828, 96, 'Buenos Aires', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (829, 65, 'Facultad de Ciencias Empresariales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (830, 65, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (831, 65, 'Facultad de Comunicación Social', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (832, 65, 'Facultad de Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (833, 65, 'Facultad de Ciencias Jurídicas y Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (834, 65, 'Escuela de Negocios, Masters y Posgrados', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (835, 85, 'Escuela der Farmacia y Bioquímica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (836, 65, 'Subsede RAFAELA', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (837, 65, 'Subsede SAN FRANCISCO', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (838, 65, 'Subsede SAN ISIDRO - BUENOS AIRES', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (839, 74, 'IUCS BARCELO - BUENOS AIRES', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (840, 74, 'Escuela de Lenguas y Perfeccionamiento Docente', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (841, 27, 'Subsede BAHIA BLANCA', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (842, 27, 'Escuela Agrotécnica', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (843, 95, 'Facultad Ciencias de la Gestión - Sede Gualeguaychú', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (844, 95, 'Facultad de Humanidades, Artes y Ciencias Sociales - Sede La Picada', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (845, 95, 'Facultad de Humanidades,Artes y Ciencias Sociales - Sede Oro Verde.', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (846, 95, 'Facultad de Ciencia y Tecnología - Sede Oro Verde', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (847, 95, 'Facultad de Ciencias de la Vida y Salud - Sede Ramirez', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (848, 95, 'Facultad de Ciencias de la Gestión - Sede Villaguay', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (849, 35, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (850, 41, 'Sede - La Plata', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (851, 41, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (852, 71, 'Escuela de Turismo y Hoteleria', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (853, 57, 'Facultad de Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (854, 58, 'Facultad DON BOSCO de Enología y Ciencias de la Alimentación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (855, 58, 'Sede SAN LUIS', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (856, 58, 'Fundación ALTA DIRECCION', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (857, 101, 'FLACSO', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (858, 71, 'Graduate School of Business', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (859, 47, 'ROSARIO', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (860, 58, 'Instituto de Formación Docente', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (861, 58, 'Instituto Cervantes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (862, 101, 'Unión de Educadores de la Pcia. de Córdoba', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (863, 102, 'CAPITAL FEDERAL', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (864, 102, 'MARTINEZ', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (865, 103, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (866, 91, 'CAPITAL FEDERAL', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (867, 90, 'CAPITAL FEDERAL', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (868, 103, 'Facultad de Ciencias Económicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (869, 103, 'Facultad de Ciencias Jurídicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (870, 103, 'Facultad de Agonomía y Ciencias Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (871, 81, 'Facultad de Ingeniería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (872, 93, 'Escuela de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (873, 93, 'Escuela de Enfermería', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (874, 92, 'CAPITAL FEDERAL', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (875, 42, 'Departamento de Ingenieria', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (876, 42, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (877, 42, 'Departamento de Finanzas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (878, 47, 'Facultad de Medicina', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (879, 47, 'LA RIOJA', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (880, 47, 'SANTO TOME', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (881, 55, 'Sede Campus', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (882, 88, 'CAPITAL FEDERAL', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (883, 55, 'Sede Centro (Distancia)', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (884, 88, 'SAN ISIDRO', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (885, 100, 'CAPITAL FEDERAL', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (886, 89, 'Departamento de Informática', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (887, 89, 'Departamento de Administración y Comercialización', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (888, 73, 'Colegio de la Universidad del Aconcagua', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (889, 73, 'Escuela Internacional de Turismo, Hoteleria y Gastronomia de Mendoza', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (890, 50, 'Facultad de Ciencias de la Salud', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (891, 50, 'Facultad de Ciencias Económicas y de la Administración', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (892, 50, 'Facultad de Humanidades, Educación y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (893, 50, 'Facultad de Teología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (894, 16, 'Localización Petrel - Agencia ACA', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (895, 17, 'CNEL. PRINGLES', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (896, 17, 'JUNIN', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (897, 20, 'Goya', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (898, 20, 'Mercedes', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (899, 20, 'Reconquista', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (900, 33, 'AGUILARES', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (901, 66, 'Centro Regional Federación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (902, 66, 'Centro Regional Paraná', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (903, 66, 'Facultad de Ciencias de la Comunicación y de la Educación', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (904, 66, 'ROSARIO', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (905, 46, 'Facultad de Ingeniería y Ciencias Exactas y Naturales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (906, 46, 'Facultad de Ciencias Médicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (907, 46, 'Facultad de Posgrado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (908, 73, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (909, 55, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (910, 99, 'Escuela de Ciencias Agrarias, Naturales y Ambientales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (911, 99, 'Escuela de Ciencias Sociales y Humanas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (912, 99, 'Escuela de Ciencias Económicas y Jurídicas', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (913, 99, 'Escuela de Tecnología', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (914, 99, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (915, 99, 'Unidad Académica Pergamino', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (916, 18, 'Catuna', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (917, 73, 'Instituto Superior del Profesorado "San Pedro Nolasco"', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (918, 81, 'Facultad de Administración y Economía', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (919, 81, 'Facultad de Humanidades y Ciencias Sociales', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (920, 94, 'Sede Principal - BOLOGNA', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (921, 81, 'Rectorado', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (922, 93, 'NUEVA UNIDAD ACADEMICA', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (923, 93, 'OTRA UNIDAD ACADEMICA', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (924, 8889, 'Unidad Académica 01', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (925, 8889, 'Unidad Académica 02', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (926, 8889, 'Unidad Académica 01', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (927, 8889, 'Unidad Académica 02', 2);
INSERT INTO soe_unidadesacad (unidadacad, institucion, nombre, tipoua) VALUES (465, 48, 'Facultad de Humanidades Teresa de Avila', 3);


--
-- TOC entry 1508 (class 16386 OID 673504745)
-- Dependencies: 1168 1168
-- Name: ona_localidad_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY ona_localidad
    ADD CONSTRAINT ona_localidad_pkey PRIMARY KEY (codigopostal);


--
-- TOC entry 1510 (class 16386 OID 673504747)
-- Dependencies: 1169 1169
-- Name: ona_pais_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY ona_pais
    ADD CONSTRAINT ona_pais_pkey PRIMARY KEY (idpais);


--
-- TOC entry 1512 (class 16386 OID 673504749)
-- Dependencies: 1170 1170
-- Name: ona_provincia_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY ona_provincia
    ADD CONSTRAINT ona_provincia_pkey PRIMARY KEY (idprovincia);


--
-- TOC entry 1514 (class 16386 OID 673504751)
-- Dependencies: 1172 1172
-- Name: soe_edificios_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY soe_edificios
    ADD CONSTRAINT soe_edificios_pkey PRIMARY KEY (edificio);


--
-- TOC entry 1516 (class 16386 OID 673504753)
-- Dependencies: 1174 1174
-- Name: soe_instituciones_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY soe_instituciones
    ADD CONSTRAINT soe_instituciones_pkey PRIMARY KEY (institucion);


--
-- TOC entry 1518 (class 16386 OID 673504755)
-- Dependencies: 1175 1175
-- Name: soe_jurisdicciones_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY soe_jurisdicciones
    ADD CONSTRAINT soe_jurisdicciones_pkey PRIMARY KEY (jurisdiccion);


--
-- TOC entry 1520 (class 16386 OID 673504757)
-- Dependencies: 1177 1177 1177
-- Name: soe_sedes_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY soe_sedes
    ADD CONSTRAINT soe_sedes_pkey PRIMARY KEY (institucion, sede);


--
-- TOC entry 1522 (class 16386 OID 673504759)
-- Dependencies: 1178 1178 1178 1178
-- Name: soe_sedesua_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY soe_sedesua
    ADD CONSTRAINT soe_sedesua_pkey PRIMARY KEY (institucion, sede, unidadacad);


--
-- TOC entry 1524 (class 16386 OID 673504761)
-- Dependencies: 1180 1180
-- Name: soe_tiposua_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY soe_tiposua
    ADD CONSTRAINT soe_tiposua_pkey PRIMARY KEY (tipoua);


--
-- TOC entry 1526 (class 16386 OID 673504763)
-- Dependencies: 1182 1182
-- Name: soe_unidadesacad_pkey; Type: CONSTRAINT; Schema: public; Owner: dba; Tablespace: 
--

ALTER TABLE ONLY soe_unidadesacad
    ADD CONSTRAINT soe_unidadesacad_pkey PRIMARY KEY (unidadacad);


--
-- TOC entry 1527 (class 16386 OID 673504764)
-- Dependencies: 1168 1169 1509
-- Name: ona_localidad_idpais_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ona_localidad
    ADD CONSTRAINT ona_localidad_idpais_fkey FOREIGN KEY (idpais) REFERENCES ona_pais(idpais);


--
-- TOC entry 1528 (class 16386 OID 673504768)
-- Dependencies: 1168 1170 1511
-- Name: ona_localidad_idprovincia_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ona_localidad
    ADD CONSTRAINT ona_localidad_idprovincia_fkey FOREIGN KEY (idprovincia) REFERENCES ona_provincia(idprovincia);


--
-- TOC entry 1529 (class 16386 OID 673504772)
-- Dependencies: 1170 1169 1509
-- Name: ona_provincia_idpais_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ona_provincia
    ADD CONSTRAINT ona_provincia_idpais_fkey FOREIGN KEY (idpais) REFERENCES ona_pais(idpais);


--
-- TOC entry 1530 (class 16386 OID 673504776)
-- Dependencies: 1172 1172 1177 1177 1519
-- Name: soe_edificios_institucion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY soe_edificios
    ADD CONSTRAINT soe_edificios_institucion_fkey FOREIGN KEY (institucion, sede) REFERENCES soe_sedes(institucion, sede);


--
-- TOC entry 1531 (class 16386 OID 673504780)
-- Dependencies: 1174 1175 1517
-- Name: soe_instituciones_jurisdiccion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY soe_instituciones
    ADD CONSTRAINT soe_instituciones_jurisdiccion_fkey FOREIGN KEY (jurisdiccion) REFERENCES soe_jurisdicciones(jurisdiccion);


--
-- TOC entry 1532 (class 16386 OID 673504784)
-- Dependencies: 1177 1168 1507
-- Name: soe_sedes_codigopostal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY soe_sedes
    ADD CONSTRAINT soe_sedes_codigopostal_fkey FOREIGN KEY (codigopostal) REFERENCES ona_localidad(codigopostal);


--
-- TOC entry 1533 (class 16386 OID 673504796)
-- Dependencies: 1177 1174 1515
-- Name: soe_sedes_institucion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY soe_sedes
    ADD CONSTRAINT soe_sedes_institucion_fkey FOREIGN KEY (institucion) REFERENCES soe_instituciones(institucion);


--
-- TOC entry 1534 (class 16386 OID 673504800)
-- Dependencies: 1178 1178 1177 1177 1519
-- Name: soe_sedesua_institucion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY soe_sedesua
    ADD CONSTRAINT soe_sedesua_institucion_fkey FOREIGN KEY (institucion, sede) REFERENCES soe_sedes(institucion, sede);


--
-- TOC entry 1535 (class 16386 OID 673504804)
-- Dependencies: 1178 1182 1525
-- Name: soe_sedesua_unidadacad_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY soe_sedesua
    ADD CONSTRAINT soe_sedesua_unidadacad_fkey FOREIGN KEY (unidadacad) REFERENCES soe_unidadesacad(unidadacad);


--
-- TOC entry 1536 (class 16386 OID 673504808)
-- Dependencies: 1182 1174 1515
-- Name: soe_unidadesacad_institucion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY soe_unidadesacad
    ADD CONSTRAINT soe_unidadesacad_institucion_fkey FOREIGN KEY (institucion) REFERENCES soe_instituciones(institucion);


--
-- TOC entry 1537 (class 16386 OID 673504812)
-- Dependencies: 1182 1180 1523
-- Name: soe_unidadesacad_tipoua_fkey; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY soe_unidadesacad
    ADD CONSTRAINT soe_unidadesacad_tipoua_fkey FOREIGN KEY (tipoua) REFERENCES soe_tiposua(tipoua);


--
-- TOC entry 1558 (class 0 OID 0)
-- Name: DUMP TIMESTAMP; Type: DUMP TIMESTAMP; Schema: -; Owner: 
--

-- Completed on 2007-05-08 16:32:17 Hora est. de Sudamérica E.


--
-- TOC entry 1552 (class 0 OID 0)
-- Dependencies: 5
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_edificios', 'edificio'), (SELECT max(edificio) FROM soe_edificios), true);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_instituciones', 'institucion'), (SELECT max(institucion) FROM soe_instituciones), false);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_sedes', 'sede'), (SELECT max(sede) FROM soe_sedes), false);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_tiposua', 'tipoua'),(SELECT max(tipoua) FROM soe_tiposua), true);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_unidadesacad', 'unidadacad'), (SELECT max(unidadacad) FROM soe_unidadesacad), true);
