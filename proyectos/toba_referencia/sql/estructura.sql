
--
-- Name: ref_deportes; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_deportes (
    id serial NOT NULL,
    nombre character varying(60) NOT NULL,
    descripcion character varying(255),
    fecha_inicio date
);
COMMENT ON TABLE ref_deportes IS 'Deportes';
COMMENT ON COLUMN ref_deportes.id IS 'Clave';
COMMENT ON COLUMN ref_deportes.nombre IS 'Nombre';
COMMENT ON COLUMN ref_deportes.descripcion IS 'Descripción';
COMMENT ON COLUMN ref_deportes.fecha_inicio IS 'Fecha de inicio';

--
-- Name: ref_juegos; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_juegos (
    id serial NOT NULL,
    nombre character varying(30) NOT NULL,
    descripcion character varying(255),
    de_mesa BOOLEAN NOT NULL DEFAULT FALSE
);
COMMENT ON TABLE ref_juegos IS 'Juegos';
COMMENT ON COLUMN ref_juegos.id IS 'Clave';
COMMENT ON COLUMN ref_juegos.nombre IS 'Nombre';
COMMENT ON COLUMN ref_juegos.descripcion IS 'Descripción';
COMMENT ON COLUMN ref_juegos.de_mesa IS 'Es un juego de mesa';


--
-- Name: ref_juegos_oferta; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_juegos_oferta (
    id serial NOT NULL,
    juego integer NOT NULL,
    jugador integer NOT NULL,
    publicacion timestamp(0) without time zone DEFAULT ('now'::text)::timestamp(6) with time zone
);
COMMENT ON TABLE ref_juegos_oferta IS 'Ofertas de Juegos';
COMMENT ON COLUMN ref_juegos_oferta.id IS 'Clave';
COMMENT ON COLUMN ref_juegos_oferta.juego IS 'Juego';
COMMENT ON COLUMN ref_juegos_oferta.jugador IS 'Jugador';

--
-- Name: ref_persona; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_persona (
    id serial NOT NULL,
    nombre character varying(60) NOT NULL,
    fecha_nac date,
    imagen bytea,
	planilla_pdf		bytea,
	planilla_pdf_firmada smallint NOT NULL DEFAULT 0
);
COMMENT ON TABLE ref_persona IS 'Personas';
COMMENT ON COLUMN ref_persona.id IS 'Clave';
COMMENT ON COLUMN ref_persona.nombre IS 'Nombre';
COMMENT ON COLUMN ref_persona.fecha_nac IS 'Fecha de nacimiento';
COMMENT ON COLUMN ref_persona.imagen IS 'Foto';
COMMENT ON COLUMN ref_persona.planilla_pdf IS 'Planilla PDF';

--
-- Name: ref_persona_deportes; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_persona_deportes (
    id serial NOT NULL,
    persona integer NOT NULL,
    deporte integer NOT NULL,
    dia_semana integer,
    hora_inicio time without time zone null,
    hora_fin time without time zone null
);
COMMENT ON TABLE ref_persona_deportes IS 'Deportes de personas';
COMMENT ON COLUMN ref_persona_deportes.id IS 'Clave';
COMMENT ON COLUMN ref_persona_deportes.persona IS 'Persona';
COMMENT ON COLUMN ref_persona_deportes.deporte IS 'Deporte';
COMMENT ON COLUMN ref_persona_deportes.dia_semana IS 'Día de la semana';
COMMENT ON COLUMN ref_persona_deportes.hora_inicio IS 'Hora de inicio';
COMMENT ON COLUMN ref_persona_deportes.hora_fin IS 'Hora de fin';

--
-- Name: ref_persona_juegos; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_persona_juegos (
    id serial NOT NULL,
    persona integer NOT NULL,
    juego integer NOT NULL,
    dia_semana integer,
    hora_inicio integer,
    hora_fin integer
);
COMMENT ON TABLE ref_persona_juegos IS 'Juegos de personas';
COMMENT ON COLUMN ref_persona_juegos.id IS 'Clave';
COMMENT ON COLUMN ref_persona_juegos.persona IS 'Persona';
COMMENT ON COLUMN ref_persona_juegos.juego IS 'Deporte';
COMMENT ON COLUMN ref_persona_juegos.dia_semana IS 'Día de la semana';
COMMENT ON COLUMN ref_persona_juegos.hora_inicio IS 'Hora de inicio';
COMMENT ON COLUMN ref_persona_juegos.hora_fin IS 'Hora de fin';



CREATE TABLE iso_countries (
  rowId int NOT NULL,
  countryId int NOT NULL,
  locale varchar(10) NOT NULL default 'en',
  countryCode char(2) NOT NULL,
  countryName varchar(200)  default NULL,
  phonePrefix varchar(50) default NULL,
  PRIMARY KEY  (rowId)
);


--
-- Name: ref_deportes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ref_deportes
    ADD CONSTRAINT ref_deportes_pkey PRIMARY KEY (id);


--
-- Name: ref_juegos_oferta_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ref_juegos_oferta
    ADD CONSTRAINT ref_juegos_oferta_pkey PRIMARY KEY (id);


--
-- Name: ref_juegos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ref_juegos
    ADD CONSTRAINT ref_juegos_pkey PRIMARY KEY (id);


--
-- Name: ref_persona_deportes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ref_persona_deportes
    ADD CONSTRAINT ref_persona_deportes_pkey PRIMARY KEY (id);


--
-- Name: ref_persona_juegos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ref_persona_juegos
    ADD CONSTRAINT ref_persona_juegos_pkey PRIMARY KEY (id);


--
-- Name: ref_persona_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ref_persona
    ADD CONSTRAINT ref_persona_pkey PRIMARY KEY (id);


--
-- Name: new_index; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX new_index ON ref_persona_deportes USING btree (persona, deporte);


--
-- Name: ref_persona_juegos_persona_juego_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX ref_persona_juegos_persona_juego_key ON ref_persona_juegos USING btree (persona, juego);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ref_persona_juegos
    ADD CONSTRAINT "$1" FOREIGN KEY (persona) REFERENCES ref_persona(id) ON DELETE CASCADE DEFERRABLE;


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ref_persona_deportes
    ADD CONSTRAINT "$1" FOREIGN KEY (persona) REFERENCES ref_persona(id) ON DELETE CASCADE DEFERRABLE;


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ref_juegos_oferta
    ADD CONSTRAINT "$1" FOREIGN KEY (juego, jugador) REFERENCES ref_persona_juegos(persona, juego) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE;


--
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ref_persona_juegos
    ADD CONSTRAINT "$2" FOREIGN KEY (juego) REFERENCES ref_juegos(id) ON DELETE CASCADE DEFERRABLE;


--
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ref_persona_deportes
    ADD CONSTRAINT "$2" FOREIGN KEY (deporte) REFERENCES ref_deportes(id);


