
--
-- Name: institucion; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE institucion (
    id_institucion integer NOT NULL,
    nombre character varying(255) NOT NULL,
    sigla character varying(15) NOT NULL,
    id_jurisdiccion integer
);


--
-- Name: jurisdiccion; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE jurisdiccion (
    id_jurisdiccion integer NOT NULL,
    nombre character varying(100) NOT NULL,
    estado character varying(1) NOT NULL
);


--
-- Name: localidad; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE localidad (
    cp character varying(10) NOT NULL,
    id_pais character varying(2) NOT NULL,
    id_provincia character varying(4) NOT NULL,
    nombre character varying(40) NOT NULL,
    ddn character varying(6)
);


--
-- Name: pais; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE pais (
    id_pais character varying(2) NOT NULL,
    nombre character varying(40) NOT NULL,
    ddi character varying(2)
);


--
-- Name: provincia; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE provincia (
    id_provincia character varying(4) NOT NULL,
    id_pais character varying(2) NOT NULL,
    nombre character varying(40) NOT NULL
);


--
-- Name: sede; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE sede (
    id_institucion integer NOT NULL,
    id_sede integer NOT NULL,
    nombre character varying(255) NOT NULL,
    cp character varying(10)
);


--
-- Name: sede_edificio; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE sede_edificio (
    id_edificio integer NOT NULL,
    id_sede integer NOT NULL,
    nombre character varying(255),
    calle character varying(50),
    numero character varying(5),
    piso character varying(3),
    depto character varying(30)
);


--
-- Name: sede_ua; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE sede_ua (
    id_sede integer NOT NULL,
    id_ua integer NOT NULL
);


--
-- Name: soe_edificios_edificio_seq; Type: SEQUENCE; Schema: curso; Owner: -
--

CREATE SEQUENCE soe_edificios_edificio_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: soe_edificios_edificio_seq; Type: SEQUENCE OWNED BY; Schema: curso; Owner: -
--

ALTER SEQUENCE soe_edificios_edificio_seq OWNED BY sede_edificio.id_edificio;


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

ALTER SEQUENCE soe_instituciones_institucion_seq OWNED BY institucion.id_institucion;


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

ALTER SEQUENCE soe_sedes_sede_seq OWNED BY sede.id_sede;


--
-- Name: ua_tipo; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE ua_tipo (
    id_ua_tipo integer NOT NULL,
    nombre character varying(50) NOT NULL,
    detalle character varying(255),
    estado character varying(1) NOT NULL
);


--
-- Name: soe_tiposua_tipoua_seq; Type: SEQUENCE; Schema: curso; Owner: -
--

CREATE SEQUENCE soe_tiposua_tipoua_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: soe_tiposua_tipoua_seq; Type: SEQUENCE OWNED BY; Schema: curso; Owner: -
--

ALTER SEQUENCE soe_tiposua_tipoua_seq OWNED BY ua_tipo.id_ua_tipo;


--
-- Name: ua; Type: TABLE; Schema: curso; Owner: -; Tablespace: 
--

CREATE TABLE ua (
    id_ua integer NOT NULL,
    id_institucion integer,
    nombre character varying(255) NOT NULL,
    id_ua_tipo integer
);


--
-- Name: soe_unidadesacad_unidadacad_seq; Type: SEQUENCE; Schema: curso; Owner: -
--

CREATE SEQUENCE soe_unidadesacad_unidadacad_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: soe_unidadesacad_unidadacad_seq; Type: SEQUENCE OWNED BY; Schema: curso; Owner: -
--

ALTER SEQUENCE soe_unidadesacad_unidadacad_seq OWNED BY ua.id_ua;


--
-- Name: id_institucion; Type: DEFAULT; Schema: curso; Owner: -
--

ALTER TABLE institucion ALTER COLUMN id_institucion SET DEFAULT nextval('soe_instituciones_institucion_seq'::regclass);


--
-- Name: id_sede; Type: DEFAULT; Schema: curso; Owner: -
--

ALTER TABLE sede ALTER COLUMN id_sede SET DEFAULT nextval('soe_sedes_sede_seq'::regclass);


--
-- Name: id_edificio; Type: DEFAULT; Schema: curso; Owner: -
--

ALTER TABLE sede_edificio ALTER COLUMN id_edificio SET DEFAULT nextval('soe_edificios_edificio_seq'::regclass);


--
-- Name: id_ua; Type: DEFAULT; Schema: curso; Owner: -
--

ALTER TABLE ua ALTER COLUMN id_ua SET DEFAULT nextval('soe_unidadesacad_unidadacad_seq'::regclass);


--
-- Name: id_ua_tipo; Type: DEFAULT; Schema: curso; Owner: -
--

ALTER TABLE ua_tipo ALTER COLUMN id_ua_tipo SET DEFAULT nextval('soe_tiposua_tipoua_seq'::regclass);


--
-- Name: ona_localidad_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY localidad
    ADD CONSTRAINT ona_localidad_pkey PRIMARY KEY (cp);


--
-- Name: ona_pais_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pais
    ADD CONSTRAINT ona_pais_pkey PRIMARY KEY (id_pais);


--
-- Name: ona_provincia_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY provincia
    ADD CONSTRAINT ona_provincia_pkey PRIMARY KEY (id_provincia);


--
-- Name: soe_edificios_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sede_edificio
    ADD CONSTRAINT soe_edificios_pkey PRIMARY KEY (id_edificio);


--
-- Name: soe_instituciones_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY institucion
    ADD CONSTRAINT soe_instituciones_pkey PRIMARY KEY (id_institucion);


--
-- Name: soe_jurisdicciones_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY jurisdiccion
    ADD CONSTRAINT soe_jurisdicciones_pkey PRIMARY KEY (id_jurisdiccion);


--
-- Name: soe_sedes_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sede
    ADD CONSTRAINT soe_sedes_pkey PRIMARY KEY (id_sede);


--
-- Name: soe_sedes_ua_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sede_ua
    ADD CONSTRAINT soe_sedes_ua_pkey PRIMARY KEY (id_sede, id_ua);

ALTER TABLE ONLY sede_ua ADD CONSTRAINT soe_sedes_fk FOREIGN KEY (id_sede)
      REFERENCES sede (id_sede) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;

--
-- Name: soe_tiposua_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ua_tipo
    ADD CONSTRAINT soe_tiposua_pkey PRIMARY KEY (id_ua_tipo);


--
-- Name: soe_unidadesacad_pkey; Type: CONSTRAINT; Schema: curso; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ua
    ADD CONSTRAINT soe_unidadesacad_pkey PRIMARY KEY (id_ua);


--
-- Name: ona_localidad_idpais_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY localidad
    ADD CONSTRAINT ona_localidad_idpais_fkey FOREIGN KEY (id_pais) REFERENCES pais(id_pais) DEFERRABLE;


--
-- Name: ona_localidad_idprovincia_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY localidad
    ADD CONSTRAINT ona_localidad_idprovincia_fkey FOREIGN KEY (id_provincia) REFERENCES provincia(id_provincia) DEFERRABLE;


--
-- Name: ona_provincia_idpais_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY provincia
    ADD CONSTRAINT ona_provincia_idpais_fkey FOREIGN KEY (id_pais) REFERENCES pais(id_pais) DEFERRABLE;


--
-- Name: soe_edificios_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY sede_edificio
    ADD CONSTRAINT soe_edificios_fkey FOREIGN KEY (id_sede) REFERENCES sede(id_sede);


--
-- Name: soe_instituciones_jurisdiccion_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY institucion
    ADD CONSTRAINT soe_instituciones_jurisdiccion_fkey FOREIGN KEY (id_jurisdiccion) REFERENCES jurisdiccion(id_jurisdiccion) DEFERRABLE;


--
-- Name: soe_sedes_codigopostal_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY sede
    ADD CONSTRAINT soe_sedes_codigopostal_fkey FOREIGN KEY (cp) REFERENCES localidad(cp) DEFERRABLE;


--
-- Name: soe_sedes_institucion_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY sede
    ADD CONSTRAINT soe_sedes_institucion_fkey FOREIGN KEY (id_institucion) REFERENCES institucion(id_institucion) DEFERRABLE;


--
-- Name: soe_sedesua_unidadacad_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY sede_ua
    ADD CONSTRAINT soe_sedesua_unidadacad_fkey FOREIGN KEY (id_ua) REFERENCES ua(id_ua) DEFERRABLE;


--
-- Name: soe_unidadesacad_institucion_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY ua
    ADD CONSTRAINT soe_unidadesacad_institucion_fkey FOREIGN KEY (id_institucion) REFERENCES institucion(id_institucion) DEFERRABLE;


--
-- Name: soe_unidadesacad_tipoua_fkey; Type: FK CONSTRAINT; Schema: curso; Owner: -
--

ALTER TABLE ONLY ua
    ADD CONSTRAINT soe_unidadesacad_tipoua_fkey FOREIGN KEY (id_ua_tipo) REFERENCES ua_tipo(id_ua_tipo) DEFERRABLE;


--
-- PostgreSQL database dump complete
--

