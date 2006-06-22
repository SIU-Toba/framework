--
-- PostgreSQL database dump
--

SET search_path = public, pg_catalog;

--
-- TOC entry 4 (OID 4348758)
-- Name: ref_juegos; Type: TABLE; Schema: public; Owner: dba
--

CREATE TABLE ref_juegos (
    id serial NOT NULL,
    nombre character varying(30) NOT NULL,
    descripcion character varying(255)
);


--
-- TOC entry 5 (OID 4348765)
-- Name: ref_persona; Type: TABLE; Schema: public; Owner: dba
--

CREATE TABLE ref_persona (
    id serial NOT NULL,
    nombre character varying(60) NOT NULL,
    fecha_nac date
);


--
-- TOC entry 6 (OID 4348772)
-- Name: ref_persona_juegos; Type: TABLE; Schema: public; Owner: dba
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
-- TOC entry 7 (OID 4348787)
-- Name: ref_deportes; Type: TABLE; Schema: public; Owner: dba
--

CREATE TABLE ref_deportes (
    id serial NOT NULL,
    nombre character varying(60) NOT NULL,
    descripcion character varying(255),
    fecha_inicio date
);


--
-- TOC entry 8 (OID 4348794)
-- Name: ref_persona_deportes; Type: TABLE; Schema: public; Owner: dba
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
-- TOC entry 9 (OID 4348809)
-- Name: log_persona; Type: TABLE; Schema: public; Owner: dba
--

CREATE TABLE log_persona (
    id serial NOT NULL,
    persona integer,
    observaciones character varying(255)
);


--
-- TOC entry 10 (OID 4348816)
-- Name: log_juegos; Type: TABLE; Schema: public; Owner: dba
--

CREATE TABLE log_juegos (
    id serial NOT NULL,
    juego integer NOT NULL,
    observaciones character varying(255)
);


--
-- TOC entry 11 (OID 4348831)
-- Name: ref_juegos_oferta; Type: TABLE; Schema: public; Owner: dba
--

CREATE TABLE ref_juegos_oferta (
    id serial NOT NULL,
    juego integer NOT NULL,
    jugador integer NOT NULL,
    publicacion timestamp(0) without time zone DEFAULT ('now'::text)::timestamp(6) with time zone
);


--
-- Data for TOC entry 30 (OID 4348758)
-- Name: ref_juegos; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY ref_juegos (id, nombre, descripcion) FROM stdin;
1	Ajedrez	\N
2	Damas	\N
4	Go	\N
5	Reversi	\N
\.


--
-- Data for TOC entry 31 (OID 4348765)
-- Name: ref_persona; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY ref_persona (id, nombre, fecha_nac) FROM stdin;
2	Jose	\N
1	Horacio	\N
\.


--
-- Data for TOC entry 32 (OID 4348772)
-- Name: ref_persona_juegos; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY ref_persona_juegos (id, persona, juego, dia_semana, hora_inicio, hora_fin) FROM stdin;
3	2	5	0	17	19
1	1	1	0	17	19
2	1	2	1	17	19
\.


--
-- Data for TOC entry 33 (OID 4348787)
-- Name: ref_deportes; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY ref_deportes (id, nombre, descripcion, fecha_inicio) FROM stdin;
1	Voley	\N	\N
3	Basquet	\N	\N
5	Tenis	\N	\N
7	Rugby	\N	\N
8	Futbol	\N	\N
6	Paddle	\N	\N
\.


--
-- Data for TOC entry 34 (OID 4348794)
-- Name: ref_persona_deportes; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY ref_persona_deportes (id, persona, deporte, dia_semana, hora_inicio, hora_fin) FROM stdin;
1	1	5	3	17	19
2	2	6	4	17	19
3	1	7	3	17	19
\.


--
-- Data for TOC entry 35 (OID 4348809)
-- Name: log_persona; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY log_persona (id, persona, observaciones) FROM stdin;
\.


--
-- Data for TOC entry 36 (OID 4348816)
-- Name: log_juegos; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY log_juegos (id, juego, observaciones) FROM stdin;
\.


--
-- Data for TOC entry 37 (OID 4348831)
-- Name: ref_juegos_oferta; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY ref_juegos_oferta (id, juego, jugador, publicacion) FROM stdin;
\.


--
-- TOC entry 22 (OID 4348845)
-- Name: ref_persona_juegos_persona_juego_key; Type: INDEX; Schema: public; Owner: dba
--

CREATE UNIQUE INDEX ref_persona_juegos_persona_juego_key ON ref_persona_juegos USING btree (persona, juego);


--
-- TOC entry 25 (OID 4348850)
-- Name: new_index; Type: INDEX; Schema: public; Owner: dba
--

CREATE UNIQUE INDEX new_index ON ref_persona_deportes USING btree (persona, deporte);


--
-- TOC entry 20 (OID 4348761)
-- Name: ref_juegos_pkey; Type: CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_juegos
    ADD CONSTRAINT ref_juegos_pkey PRIMARY KEY (id);


--
-- TOC entry 21 (OID 4348768)
-- Name: ref_persona_pkey; Type: CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_persona
    ADD CONSTRAINT ref_persona_pkey PRIMARY KEY (id);


--
-- TOC entry 23 (OID 4348775)
-- Name: ref_persona_juegos_pkey; Type: CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_persona_juegos
    ADD CONSTRAINT ref_persona_juegos_pkey PRIMARY KEY (id);


--
-- TOC entry 24 (OID 4348790)
-- Name: ref_deportes_pkey; Type: CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_deportes
    ADD CONSTRAINT ref_deportes_pkey PRIMARY KEY (id);


--
-- TOC entry 26 (OID 4348797)
-- Name: ref_persona_deportes_pkey; Type: CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_persona_deportes
    ADD CONSTRAINT ref_persona_deportes_pkey PRIMARY KEY (id);


--
-- TOC entry 27 (OID 4348812)
-- Name: log_cambios_persona_pkey; Type: CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY log_persona
    ADD CONSTRAINT log_cambios_persona_pkey PRIMARY KEY (id);


--
-- TOC entry 28 (OID 4348819)
-- Name: log_juegos_pkey; Type: CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY log_juegos
    ADD CONSTRAINT log_juegos_pkey PRIMARY KEY (id);


--
-- TOC entry 29 (OID 4348834)
-- Name: ref_juegos_oferta_pkey; Type: CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_juegos_oferta
    ADD CONSTRAINT ref_juegos_oferta_pkey PRIMARY KEY (id);


--
-- TOC entry 38 (OID 4348777)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_persona_juegos
    ADD CONSTRAINT "$1" FOREIGN KEY (persona) REFERENCES ref_persona(id) ON DELETE CASCADE DEFERRABLE;


--
-- TOC entry 39 (OID 4348781)
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_persona_juegos
    ADD CONSTRAINT "$2" FOREIGN KEY (juego) REFERENCES ref_juegos(id) ON DELETE CASCADE DEFERRABLE;


--
-- TOC entry 40 (OID 4348799)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_persona_deportes
    ADD CONSTRAINT "$1" FOREIGN KEY (persona) REFERENCES ref_persona(id) ON DELETE CASCADE DEFERRABLE;


--
-- TOC entry 41 (OID 4348803)
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_persona_deportes
    ADD CONSTRAINT "$2" FOREIGN KEY (deporte) REFERENCES ref_deportes(id);


--
-- TOC entry 43 (OID 4348821)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY log_juegos
    ADD CONSTRAINT "$1" FOREIGN KEY (juego) REFERENCES ref_juegos(id) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE;


--
-- TOC entry 42 (OID 4348825)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY log_persona
    ADD CONSTRAINT "$1" FOREIGN KEY (persona) REFERENCES ref_persona(id) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE;


--
-- TOC entry 44 (OID 4348846)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: dba
--

ALTER TABLE ONLY ref_juegos_oferta
    ADD CONSTRAINT "$1" FOREIGN KEY (juego, jugador) REFERENCES ref_persona_juegos(persona, juego) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE;


--
-- TOC entry 12 (OID 4348756)
-- Name: ref_juegos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('ref_juegos_id_seq', 5, true);


--
-- TOC entry 13 (OID 4348763)
-- Name: ref_persona_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('ref_persona_id_seq', 2, true);


--
-- TOC entry 14 (OID 4348770)
-- Name: ref_persona_juegos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('ref_persona_juegos_id_seq', 3, true);


--
-- TOC entry 15 (OID 4348785)
-- Name: ref_deportes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('ref_deportes_id_seq', 8, true);


--
-- TOC entry 16 (OID 4348792)
-- Name: ref_persona_deportes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('ref_persona_deportes_id_seq', 3, true);


--
-- TOC entry 17 (OID 4348807)
-- Name: log_persona_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('log_persona_id_seq', 1, false);


--
-- TOC entry 18 (OID 4348814)
-- Name: log_juegos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('log_juegos_id_seq', 1, false);


--
-- TOC entry 19 (OID 4348829)
-- Name: ref_juegos_oferta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('ref_juegos_oferta_id_seq', 1, false);


--
-- TOC entry 2 (OID 2200)
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


