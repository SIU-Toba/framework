--
-- Name: ona_localidad; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
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
-- Name: ona_pais; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE ona_pais (
    idpais character varying(2) NOT NULL,
    nombre character varying(40) NOT NULL,
    ddi character varying(2),
    esuniversidad integer DEFAULT 0 NOT NULL,
    modiuniversidad integer DEFAULT 0 NOT NULL
);


--
-- Name: ona_provincia; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE ona_provincia (
    idprovincia character varying(4) NOT NULL,
    idpais character varying(2) NOT NULL,
    nombre character varying(40) NOT NULL
);


--
-- Name: soe_edificios; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
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
-- Name: soe_instituciones; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE soe_instituciones (
    institucion integer NOT NULL,
    nombre_completo character varying(255) NOT NULL,
    nombre_abreviado character varying(50),
    sigla character varying(15),
    jurisdiccion integer
);


--
-- Name: soe_jurisdicciones; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE soe_jurisdicciones (
    jurisdiccion integer NOT NULL,
    descripcion character varying(100) NOT NULL,
    estado character varying(1) NOT NULL
);


--
-- Name: soe_sedes; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE soe_sedes (
    institucion integer NOT NULL,
    sede integer NOT NULL,
    nombre character varying(255) NOT NULL,
    codigopostal character varying(10)
);


--
-- Name: soe_sedesua; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE soe_sedesua (
    institucion integer NOT NULL,
    sede integer NOT NULL,
    unidadacad integer NOT NULL
);


--
-- Name: soe_tiposua; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE soe_tiposua (
    tipoua integer NOT NULL,
    descripcion character varying(50) NOT NULL,
    detalle character varying(255),
    estado character varying(1) NOT NULL
);


--
-- Name: soe_unidadesacad; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE soe_unidadesacad (
    unidadacad integer NOT NULL,
    institucion integer,
    nombre character varying(255) NOT NULL,
    tipoua integer
);


--
-- Name: soe_edificios_edificio_seq; Type: SEQUENCE; Schema: curso; Owner: -
--

CREATE SEQUENCE soe_edificios_edificio_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: soe_edificios_edificio_seq; Type: SEQUENCE OWNED BY; Schema: curso; Owner: -
--

ALTER SEQUENCE soe_edificios_edificio_seq OWNED BY soe_edificios.edificio;


--
-- Name: soe_instituciones_institucion_seq; Type: SEQUENCE; Schema: curso; Owner: -
--

CREATE SEQUENCE soe_instituciones_institucion_seq
    START WITH 8889
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: soe_instituciones_institucion_seq; Type: SEQUENCE OWNED BY; Schema: curso; Owner: -
--

ALTER SEQUENCE soe_instituciones_institucion_seq OWNED BY soe_instituciones.institucion;


--
-- Name: soe_sedes_sede_seq; Type: SEQUENCE; Schema: curso; Owner: -
--

CREATE SEQUENCE soe_sedes_sede_seq
    START WITH 2229
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: soe_sedes_sede_seq; Type: SEQUENCE OWNED BY; Schema: curso; Owner: -
--

ALTER SEQUENCE soe_sedes_sede_seq OWNED BY soe_sedes.sede;


--
-- Name: soe_tiposua_tipoua_seq; Type: SEQUENCE; Schema: curso; Owner: -
--

CREATE SEQUENCE soe_tiposua_tipoua_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: soe_tiposua_tipoua_seq; Type: SEQUENCE OWNED BY; Schema: curso; Owner: -
--

ALTER SEQUENCE soe_tiposua_tipoua_seq OWNED BY soe_tiposua.tipoua;


--
-- Name: soe_unidadesacad_unidadacad_seq; Type: SEQUENCE; Schema: curso; Owner: -
--

CREATE SEQUENCE soe_unidadesacad_unidadacad_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: soe_unidadesacad_unidadacad_seq; Type: SEQUENCE OWNED BY; Schema: curso; Owner: -
--

ALTER SEQUENCE soe_unidadesacad_unidadacad_seq OWNED BY soe_unidadesacad.unidadacad;


--
-- Name: edificio; Type: DEFAULT; Schema: curso; Owner: -
--

ALTER TABLE soe_edificios ALTER COLUMN edificio SET DEFAULT nextval('soe_edificios_edificio_seq'::regclass);


--
-- Name: institucion; Type: DEFAULT; Schema: curso; Owner: -
--

ALTER TABLE soe_instituciones ALTER COLUMN institucion SET DEFAULT nextval('soe_instituciones_institucion_seq'::regclass);


--
-- Name: sede; Type: DEFAULT; Schema: curso; Owner: -
--

ALTER TABLE soe_sedes ALTER COLUMN sede SET DEFAULT nextval('soe_sedes_sede_seq'::regclass);


--
-- Name: tipoua; Type: DEFAULT; Schema: curso; Owner: -
--

ALTER TABLE soe_tiposua ALTER COLUMN tipoua SET DEFAULT nextval('soe_tiposua_tipoua_seq'::regclass);


--
-- Name: unidadacad; Type: DEFAULT; Schema: curso; Owner: -
--

ALTER TABLE soe_unidadesacad ALTER COLUMN unidadacad SET DEFAULT nextval('soe_unidadesacad_unidadacad_seq'::regclass);


--
-- Name: ona_localidad_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ona_localidad
    ADD CONSTRAINT ona_localidad_pkey PRIMARY KEY (codigopostal);


--
-- Name: ona_pais_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ona_pais
    ADD CONSTRAINT ona_pais_pkey PRIMARY KEY (idpais);


--
-- Name: ona_provincia_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ona_provincia
    ADD CONSTRAINT ona_provincia_pkey PRIMARY KEY (idprovincia);


--
-- Name: soe_edificios_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY soe_edificios
    ADD CONSTRAINT soe_edificios_pkey PRIMARY KEY (edificio);


--
-- Name: soe_instituciones_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY soe_instituciones
    ADD CONSTRAINT soe_instituciones_pkey PRIMARY KEY (institucion);


--
-- Name: soe_jurisdicciones_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY soe_jurisdicciones
    ADD CONSTRAINT soe_jurisdicciones_pkey PRIMARY KEY (jurisdiccion);


--
-- Name: soe_sedes_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY soe_sedes
    ADD CONSTRAINT soe_sedes_pkey PRIMARY KEY (institucion, sede);


--
-- Name: soe_sedesua_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY soe_sedesua
    ADD CONSTRAINT soe_sedesua_pkey PRIMARY KEY (institucion, sede, unidadacad);


--
-- Name: soe_tiposua_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY soe_tiposua
    ADD CONSTRAINT soe_tiposua_pkey PRIMARY KEY (tipoua);


--
-- Name: soe_unidadesacad_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY soe_unidadesacad
    ADD CONSTRAINT soe_unidadesacad_pkey PRIMARY KEY (unidadacad);


--
-- Name: ona_localidad_idpais_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY ona_localidad
    ADD CONSTRAINT ona_localidad_idpais_fkey FOREIGN KEY (idpais) REFERENCES ona_pais(idpais) DEFERRABLE;


--
-- Name: ona_localidad_idprovincia_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY ona_localidad
    ADD CONSTRAINT ona_localidad_idprovincia_fkey FOREIGN KEY (idprovincia) REFERENCES ona_provincia(idprovincia) DEFERRABLE;


--
-- Name: ona_provincia_idpais_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY ona_provincia
    ADD CONSTRAINT ona_provincia_idpais_fkey FOREIGN KEY (idpais) REFERENCES ona_pais(idpais) DEFERRABLE;


--
-- Name: soe_edificios_institucion_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY soe_edificios
    ADD CONSTRAINT soe_edificios_institucion_fkey FOREIGN KEY (institucion, sede) REFERENCES soe_sedes(institucion, sede) DEFERRABLE;


--
-- Name: soe_instituciones_jurisdiccion_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY soe_instituciones
    ADD CONSTRAINT soe_instituciones_jurisdiccion_fkey FOREIGN KEY (jurisdiccion) REFERENCES soe_jurisdicciones(jurisdiccion) DEFERRABLE;


--
-- Name: soe_sedes_codigopostal_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY soe_sedes
    ADD CONSTRAINT soe_sedes_codigopostal_fkey FOREIGN KEY (codigopostal) REFERENCES ona_localidad(codigopostal) DEFERRABLE;


--
-- Name: soe_sedes_institucion_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY soe_sedes
    ADD CONSTRAINT soe_sedes_institucion_fkey FOREIGN KEY (institucion) REFERENCES soe_instituciones(institucion) DEFERRABLE;


--
-- Name: soe_sedesua_institucion_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY soe_sedesua
    ADD CONSTRAINT soe_sedesua_institucion_fkey FOREIGN KEY (institucion, sede) REFERENCES soe_sedes(institucion, sede) DEFERRABLE;


--
-- Name: soe_sedesua_unidadacad_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY soe_sedesua
    ADD CONSTRAINT soe_sedesua_unidadacad_fkey FOREIGN KEY (unidadacad) REFERENCES soe_unidadesacad(unidadacad) DEFERRABLE;


--
-- Name: soe_unidadesacad_institucion_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY soe_unidadesacad
    ADD CONSTRAINT soe_unidadesacad_institucion_fkey FOREIGN KEY (institucion) REFERENCES soe_instituciones(institucion) DEFERRABLE;


--
-- Name: soe_unidadesacad_tipoua_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY soe_unidadesacad
    ADD CONSTRAINT soe_unidadesacad_tipoua_fkey FOREIGN KEY (tipoua) REFERENCES soe_tiposua(tipoua) DEFERRABLE;


--
-- PostgreSQL database dump complete
--

