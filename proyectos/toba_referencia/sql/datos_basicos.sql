SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;



--
-- Name: ref_deportes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('ref_deportes_id_seq', 8, true);

--
-- Name: ref_juegos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('ref_juegos_id_seq', 5, true);

--
-- Name: ref_juegos_oferta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('ref_juegos_oferta_id_seq', 1, false);


--
-- Name: ref_persona_deportes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('ref_persona_deportes_id_seq', 3, true);


--
-- Name: ref_persona_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('ref_persona_id_seq', 2, true);



--
-- Name: ref_persona_juegos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('ref_persona_juegos_id_seq', 3, true);



--
-- Data for Name: ref_deportes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO ref_deportes VALUES (1, 'Voley', NULL, NULL);
INSERT INTO ref_deportes VALUES (3, 'Basquet', NULL, NULL);
INSERT INTO ref_deportes VALUES (5, 'Tenis', NULL, NULL);
INSERT INTO ref_deportes VALUES (7, 'Rugby', NULL, NULL);
INSERT INTO ref_deportes VALUES (8, 'Futbol', NULL, NULL);
INSERT INTO ref_deportes VALUES (6, 'Paddle', NULL, NULL);


--
-- Data for Name: ref_juegos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO ref_juegos VALUES (1, 'Ajedrez', NULL);
INSERT INTO ref_juegos VALUES (2, 'Damas', NULL);
INSERT INTO ref_juegos VALUES (4, 'Go', NULL);
INSERT INTO ref_juegos VALUES (5, 'Reversi', NULL);


--
-- Data for Name: ref_juegos_oferta; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: ref_persona; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO ref_persona VALUES (2, 'Jose', '2000-5-8');
INSERT INTO ref_persona VALUES (1, 'Horacio', '2000-3-3');


--
-- Data for Name: ref_persona_deportes; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO ref_persona_deportes VALUES (1, 1, 5, 3, 17, 19);
INSERT INTO ref_persona_deportes VALUES (2, 2, 6, 4, 17, 19);
INSERT INTO ref_persona_deportes VALUES (3, 1, 7, 3, 17, 19);


--
-- Data for Name: ref_persona_juegos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO ref_persona_juegos VALUES (3, 2, 5, 0, 17, 19);
INSERT INTO ref_persona_juegos VALUES (1, 1, 1, 0, 17, 19);
INSERT INTO ref_persona_juegos VALUES (2, 1, 2, 1, 17, 19);
