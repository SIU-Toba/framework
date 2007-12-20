

--
-- Name: ref_deportes; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_deportes (
    id serial NOT NULL,
    nombre character varying(60) NOT NULL,
    descripcion character varying(255),
    fecha_inicio date
);


--
-- Name: ref_juegos; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_juegos (
    id serial NOT NULL,
    nombre character varying(30) NOT NULL,
    descripcion character varying(255)
);


--
-- Name: ref_juegos_oferta; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_juegos_oferta (
    id serial NOT NULL,
    juego integer NOT NULL,
    jugador integer NOT NULL,
    publicacion timestamp(0) without time zone DEFAULT ('now'::text)::timestamp(6) with time zone
);


--
-- Name: ref_persona; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_persona (
    id serial NOT NULL,
    nombre character varying(60) NOT NULL,
    fecha_nac date,
    imagen bytea
);


--
-- Name: ref_persona_deportes; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ref_persona_deportes (
    id serial NOT NULL,
    persona integer NOT NULL,
    deporte integer NOT NULL,
    dia_semana integer,
    hora_inicio integer,
    hora_fin integer
);

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


