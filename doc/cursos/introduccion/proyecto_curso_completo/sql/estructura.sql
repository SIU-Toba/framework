--
-- PostgreSQL database dump
--

-- Started on 2007-05-09 01:17:46


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
-- TOC entry 1282 (class 1259 OID 30014)
-- Dependencies: 1628 1629 5
-- Name: ona_pais; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ona_pais (
    idpais character varying(2) NOT NULL,
    nombre character varying(40) NOT NULL,
    ddi character varying(2),
    esuniversidad integer DEFAULT 0 NOT NULL,
    modiuniversidad integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 1283 (class 1259 OID 30018)
-- Dependencies: 5
-- Name: ona_provincia; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ona_provincia (
    idprovincia character varying(4) NOT NULL,
    idpais character varying(2) NOT NULL,
    nombre character varying(40) NOT NULL
);


--
-- TOC entry 1285 (class 1259 OID 30024)
-- Dependencies: 5
-- Name: soe_edificios; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE soe_edificios (
    edificio integer NOT NULL,
    institucion integer NOT NULL,
    sede integer NOT NULL,
    nombre character varying(255),
    calle character varying(50),
    numero character varying(5),
    piso character varying(3),
    depto character varying(30)
);


--
-- TOC entry 1284 (class 1259 OID 30022)
-- Dependencies: 5 1285
-- Name: soe_edificios_edificio_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE soe_edificios_edificio_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1681 (class 0 OID 0)
-- Dependencies: 1284
-- Name: soe_edificios_edificio_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE soe_edificios_edificio_seq OWNED BY soe_edificios.edificio;


--
-- TOC entry 1682 (class 0 OID 0)
-- Dependencies: 1284
-- Name: soe_edificios_edificio_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('soe_edificios_edificio_seq', 599, true);


--
-- TOC entry 1287 (class 1259 OID 30029)
-- Dependencies: 5
-- Name: soe_instituciones; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE soe_instituciones (
    institucion integer NOT NULL,
    nombre_completo character varying(255) NOT NULL,
    nombre_abreviado character varying(50),
    sigla character varying(15),
    jurisdiccion integer
);


--
-- TOC entry 1286 (class 1259 OID 30027)
-- Dependencies: 1287 5
-- Name: soe_instituciones_institucion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE soe_instituciones_institucion_seq
    START WITH 8889
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE soe_instituciones_institucion_seq OWNER TO postgres;

--
-- TOC entry 1683 (class 0 OID 0)
-- Dependencies: 1286
-- Name: soe_instituciones_institucion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE soe_instituciones_institucion_seq OWNED BY soe_instituciones.institucion;


--
-- TOC entry 1684 (class 0 OID 0)
-- Dependencies: 1286
-- Name: soe_instituciones_institucion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('soe_instituciones_institucion_seq', 8889, false);


--
-- TOC entry 1288 (class 1259 OID 30032)
-- Dependencies: 5
-- Name: soe_jurisdicciones; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE soe_jurisdicciones (
    jurisdiccion integer NOT NULL,
    descripcion character varying(100) NOT NULL,
    estado character varying(1) NOT NULL
);


--
-- TOC entry 1290 (class 1259 OID 30036)
-- Dependencies: 5
-- Name: soe_sedes; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE soe_sedes (
    institucion integer NOT NULL,
    sede integer NOT NULL,
    nombre character varying(255) NOT NULL,
    codigopostal character varying(10)
);


--
-- TOC entry 1289 (class 1259 OID 30034)
-- Dependencies: 1290 5
-- Name: soe_sedes_sede_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE soe_sedes_sede_seq
    START WITH 2229
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1685 (class 0 OID 0)
-- Dependencies: 1289
-- Name: soe_sedes_sede_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE soe_sedes_sede_seq OWNED BY soe_sedes.sede;


--
-- TOC entry 1686 (class 0 OID 0)
-- Dependencies: 1289
-- Name: soe_sedes_sede_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('soe_sedes_sede_seq', 2229, false);


--
-- TOC entry 1291 (class 1259 OID 30039)
-- Dependencies: 5
-- Name: soe_sedesua; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE soe_sedesua (
    institucion integer NOT NULL,
    sede integer NOT NULL,
    unidadacad integer NOT NULL
);


--
-- TOC entry 1293 (class 1259 OID 30043)
-- Dependencies: 5
-- Name: soe_tiposua; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE soe_tiposua (
    tipoua integer NOT NULL,
    descripcion character varying(50) NOT NULL,
    detalle character varying(255),
    estado character varying(1) NOT NULL
);


--
-- TOC entry 1292 (class 1259 OID 30041)
-- Dependencies: 5 1293
-- Name: soe_tiposua_tipoua_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE soe_tiposua_tipoua_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;



--
-- TOC entry 1687 (class 0 OID 0)
-- Dependencies: 1292
-- Name: soe_tiposua_tipoua_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE soe_tiposua_tipoua_seq OWNED BY soe_tiposua.tipoua;


--
-- TOC entry 1688 (class 0 OID 0)
-- Dependencies: 1292
-- Name: soe_tiposua_tipoua_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('soe_tiposua_tipoua_seq', 3, true);


--
-- TOC entry 1295 (class 1259 OID 30048)
-- Dependencies: 5
-- Name: soe_unidadesacad; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE soe_unidadesacad (
    unidadacad integer NOT NULL,
    institucion integer,
    nombre character varying(255) NOT NULL,
    tipoua integer
);


--
-- TOC entry 1294 (class 1259 OID 30046)
-- Dependencies: 5 1295
-- Name: soe_unidadesacad_unidadacad_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE soe_unidadesacad_unidadacad_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1689 (class 0 OID 0)
-- Dependencies: 1294
-- Name: soe_unidadesacad_unidadacad_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE soe_unidadesacad_unidadacad_seq OWNED BY soe_unidadesacad.unidadacad;


--
-- TOC entry 1690 (class 0 OID 0)
-- Dependencies: 1294
-- Name: soe_unidadesacad_unidadacad_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('soe_unidadesacad_unidadacad_seq', 927, true);


--
-- TOC entry 1630 (class 2604 OID 30026)
-- Dependencies: 1284 1285 1285
-- Name: edificio; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE soe_edificios ALTER COLUMN edificio SET DEFAULT nextval('soe_edificios_edificio_seq'::regclass);


--
-- TOC entry 1631 (class 2604 OID 30031)
-- Dependencies: 1286 1287 1287
-- Name: institucion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE soe_instituciones ALTER COLUMN institucion SET DEFAULT nextval('soe_instituciones_institucion_seq'::regclass);


--
-- TOC entry 1632 (class 2604 OID 30038)
-- Dependencies: 1290 1289 1290
-- Name: sede; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE soe_sedes ALTER COLUMN sede SET DEFAULT nextval('soe_sedes_sede_seq'::regclass);


--
-- TOC entry 1633 (class 2604 OID 30045)
-- Dependencies: 1293 1292 1293
-- Name: tipoua; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE soe_tiposua ALTER COLUMN tipoua SET DEFAULT nextval('soe_tiposua_tipoua_seq'::regclass);


--
-- TOC entry 1634 (class 2604 OID 30050)
-- Dependencies: 1295 1294 1295
-- Name: unidadacad; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE soe_unidadesacad ALTER COLUMN unidadacad SET DEFAULT nextval('soe_unidadesacad_unidadacad_seq'::regclass);

--
-- TOC entry 1636 (class 2606 OID 33985)
-- Dependencies: 1281 1281
-- Name: ona_localidad_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ona_localidad
    ADD CONSTRAINT ona_localidad_pkey PRIMARY KEY (codigopostal);


--
-- TOC entry 1638 (class 2606 OID 33987)
-- Dependencies: 1282 1282
-- Name: ona_pais_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ona_pais
    ADD CONSTRAINT ona_pais_pkey PRIMARY KEY (idpais);


--
-- TOC entry 1640 (class 2606 OID 33989)
-- Dependencies: 1283 1283
-- Name: ona_provincia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ona_provincia
    ADD CONSTRAINT ona_provincia_pkey PRIMARY KEY (idprovincia);


--
-- TOC entry 1642 (class 2606 OID 33991)
-- Dependencies: 1285 1285
-- Name: soe_edificios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY soe_edificios
    ADD CONSTRAINT soe_edificios_pkey PRIMARY KEY (edificio);


--
-- TOC entry 1644 (class 2606 OID 33993)
-- Dependencies: 1287 1287
-- Name: soe_instituciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY soe_instituciones
    ADD CONSTRAINT soe_instituciones_pkey PRIMARY KEY (institucion);


--
-- TOC entry 1646 (class 2606 OID 33995)
-- Dependencies: 1288 1288
-- Name: soe_jurisdicciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY soe_jurisdicciones
    ADD CONSTRAINT soe_jurisdicciones_pkey PRIMARY KEY (jurisdiccion);


--
-- TOC entry 1648 (class 2606 OID 33997)
-- Dependencies: 1290 1290 1290
-- Name: soe_sedes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY soe_sedes
    ADD CONSTRAINT soe_sedes_pkey PRIMARY KEY (institucion, sede);


--
-- TOC entry 1650 (class 2606 OID 33999)
-- Dependencies: 1291 1291 1291 1291
-- Name: soe_sedesua_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY soe_sedesua
    ADD CONSTRAINT soe_sedesua_pkey PRIMARY KEY (institucion, sede, unidadacad);


--
-- TOC entry 1652 (class 2606 OID 34001)
-- Dependencies: 1293 1293
-- Name: soe_tiposua_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY soe_tiposua
    ADD CONSTRAINT soe_tiposua_pkey PRIMARY KEY (tipoua);


--
-- TOC entry 1654 (class 2606 OID 34003)
-- Dependencies: 1295 1295
-- Name: soe_unidadesacad_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY soe_unidadesacad
    ADD CONSTRAINT soe_unidadesacad_pkey PRIMARY KEY (unidadacad);


--
-- TOC entry 1655 (class 2606 OID 34004)
-- Dependencies: 1281 1637 1282
-- Name: ona_localidad_idpais_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ona_localidad
    ADD CONSTRAINT ona_localidad_idpais_fkey FOREIGN KEY (idpais) REFERENCES ona_pais(idpais) DEFERRABLE;


--
-- TOC entry 1656 (class 2606 OID 34009)
-- Dependencies: 1639 1283 1281
-- Name: ona_localidad_idprovincia_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ona_localidad
    ADD CONSTRAINT ona_localidad_idprovincia_fkey FOREIGN KEY (idprovincia) REFERENCES ona_provincia(idprovincia) DEFERRABLE;


--
-- TOC entry 1657 (class 2606 OID 34014)
-- Dependencies: 1282 1637 1283
-- Name: ona_provincia_idpais_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ona_provincia
    ADD CONSTRAINT ona_provincia_idpais_fkey FOREIGN KEY (idpais) REFERENCES ona_pais(idpais) DEFERRABLE;


--
-- TOC entry 1658 (class 2606 OID 34019)
-- Dependencies: 1647 1290 1290 1285 1285
-- Name: soe_edificios_institucion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY soe_edificios
    ADD CONSTRAINT soe_edificios_institucion_fkey FOREIGN KEY (institucion, sede) REFERENCES soe_sedes(institucion, sede) DEFERRABLE;


--
-- TOC entry 1659 (class 2606 OID 34024)
-- Dependencies: 1288 1645 1287
-- Name: soe_instituciones_jurisdiccion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY soe_instituciones
    ADD CONSTRAINT soe_instituciones_jurisdiccion_fkey FOREIGN KEY (jurisdiccion) REFERENCES soe_jurisdicciones(jurisdiccion) DEFERRABLE;


--
-- TOC entry 1660 (class 2606 OID 34029)
-- Dependencies: 1281 1635 1290
-- Name: soe_sedes_codigopostal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY soe_sedes
    ADD CONSTRAINT soe_sedes_codigopostal_fkey FOREIGN KEY (codigopostal) REFERENCES ona_localidad(codigopostal) DEFERRABLE;


--
-- TOC entry 1661 (class 2606 OID 34034)
-- Dependencies: 1287 1290 1643
-- Name: soe_sedes_institucion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY soe_sedes
    ADD CONSTRAINT soe_sedes_institucion_fkey FOREIGN KEY (institucion) REFERENCES soe_instituciones(institucion) DEFERRABLE;


--
-- TOC entry 1662 (class 2606 OID 34039)
-- Dependencies: 1291 1291 1647 1290 1290
-- Name: soe_sedesua_institucion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY soe_sedesua
    ADD CONSTRAINT soe_sedesua_institucion_fkey FOREIGN KEY (institucion, sede) REFERENCES soe_sedes(institucion, sede) DEFERRABLE;


--
-- TOC entry 1663 (class 2606 OID 34044)
-- Dependencies: 1291 1653 1295
-- Name: soe_sedesua_unidadacad_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY soe_sedesua
    ADD CONSTRAINT soe_sedesua_unidadacad_fkey FOREIGN KEY (unidadacad) REFERENCES soe_unidadesacad(unidadacad) DEFERRABLE;


--
-- TOC entry 1664 (class 2606 OID 34049)
-- Dependencies: 1295 1643 1287
-- Name: soe_unidadesacad_institucion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY soe_unidadesacad
    ADD CONSTRAINT soe_unidadesacad_institucion_fkey FOREIGN KEY (institucion) REFERENCES soe_instituciones(institucion) DEFERRABLE;


--
-- TOC entry 1665 (class 2606 OID 34054)
-- Dependencies: 1295 1651 1293
-- Name: soe_unidadesacad_tipoua_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY soe_unidadesacad
    ADD CONSTRAINT soe_unidadesacad_tipoua_fkey FOREIGN KEY (tipoua) REFERENCES soe_tiposua(tipoua) DEFERRABLE;
