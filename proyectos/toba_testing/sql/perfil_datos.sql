


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1480 (class 1259 OID 99202)
-- Dependencies: 6
-- Name: cargo; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE cargo (
    persona integer NOT NULL,
    cargo integer NOT NULL,
    descripcion character varying(200),
    dependencia integer NOT NULL,
    categoria_1 integer NOT NULL,
    categoria_2 character varying(10) NOT NULL
);


--
-- TOC entry 1481 (class 1259 OID 99206)
-- Dependencies: 6
-- Name: categoria; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE categoria (
    categoria_1 integer NOT NULL,
    categoria_2 character varying(10) NOT NULL,
    descripcion character varying(80),
    escalafon_1 integer NOT NULL,
    escalafon_2 character varying(10) NOT NULL
);


--
-- TOC entry 1482 (class 1259 OID 99209)
-- Dependencies: 6
-- Name: dependencia; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE dependencia (
    dependencia integer NOT NULL,
    descripcion character varying(80)
);


--
-- TOC entry 1483 (class 1259 OID 99212)
-- Dependencies: 6
-- Name: escalafon; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE escalafon (
    escalafon_1 integer NOT NULL,
    escalafon_2 character varying(10) NOT NULL,
    descripcion character varying(80) NOT NULL
);


--
-- TOC entry 1484 (class 1259 OID 99215)
-- Dependencies: 6
-- Name: persona; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE persona (
    persona integer NOT NULL,
    descripcion character varying(80)
);


--
-- TOC entry 1485 (class 1259 OID 99218)
-- Dependencies: 6
-- Name: persona_extra; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE persona_extra (
    persona integer NOT NULL,
    descripcion character(80),
    dependencia integer NOT NULL
);


--
-- TOC entry 1479 (class 1259 OID 99200)
-- Dependencies: 1480 6
-- Name: cargo_cargo_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE cargo_cargo_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1792 (class 0 OID 0)
-- Dependencies: 1479
-- Name: cargo_cargo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE cargo_cargo_seq OWNED BY cargo.cargo;


--
-- TOC entry 1793 (class 0 OID 0)
-- Dependencies: 1479
-- Name: cargo_cargo_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('cargo_cargo_seq', 16, true);





--
-- TOC entry 1487 (class 1259 OID 99223)
-- Dependencies: 6 1481
-- Name: categoria_categoria_1_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE categoria_categoria_1_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1796 (class 0 OID 0)
-- Dependencies: 1487
-- Name: categoria_categoria_1_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE categoria_categoria_1_seq OWNED BY categoria.categoria_1;


--
-- TOC entry 1797 (class 0 OID 0)
-- Dependencies: 1487
-- Name: categoria_categoria_1_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('categoria_categoria_1_seq', 8, true);


--
-- TOC entry 1488 (class 1259 OID 99225)
-- Dependencies: 1482 6
-- Name: dependencia_dependencia_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE dependencia_dependencia_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1798 (class 0 OID 0)
-- Dependencies: 1488
-- Name: dependencia_dependencia_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE dependencia_dependencia_seq OWNED BY dependencia.dependencia;


--
-- TOC entry 1799 (class 0 OID 0)
-- Dependencies: 1488
-- Name: dependencia_dependencia_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('dependencia_dependencia_seq', 4, true);


--
-- TOC entry 1489 (class 1259 OID 99227)
-- Dependencies: 6 1483
-- Name: escalafones_escalafon_1_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE escalafones_escalafon_1_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1800 (class 0 OID 0)
-- Dependencies: 1489
-- Name: escalafones_escalafon_1_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE escalafones_escalafon_1_seq OWNED BY escalafon.escalafon_1;


--
-- TOC entry 1801 (class 0 OID 0)
-- Dependencies: 1489
-- Name: escalafones_escalafon_1_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('escalafones_escalafon_1_seq', 1, false);


--
-- TOC entry 1490 (class 1259 OID 99229)
-- Dependencies: 6 1484
-- Name: persona_persona_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE persona_persona_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1802 (class 0 OID 0)
-- Dependencies: 1490
-- Name: persona_persona_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE persona_persona_seq OWNED BY persona.persona;


--
-- TOC entry 1803 (class 0 OID 0)
-- Dependencies: 1490
-- Name: persona_persona_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('persona_persona_seq', 8, true);


--
-- TOC entry 1757 (class 2604 OID 99205)
-- Dependencies: 1480 1479 1480
-- Name: cargo; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE cargo ALTER COLUMN cargo SET DEFAULT nextval('cargo_cargo_seq'::regclass);


--
-- TOC entry 1758 (class 2604 OID 99231)
-- Dependencies: 1486 1480
-- Name: persona; Type: DEFAULT; Schema: public; Owner: -
--


--
-- TOC entry 1759 (class 2604 OID 99232)
-- Dependencies: 1487 1481
-- Name: categoria_1; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE categoria ALTER COLUMN categoria_1 SET DEFAULT nextval('categoria_categoria_1_seq'::regclass);


--
-- TOC entry 1760 (class 2604 OID 99233)
-- Dependencies: 1488 1482
-- Name: dependencia; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE dependencia ALTER COLUMN dependencia SET DEFAULT nextval('dependencia_dependencia_seq'::regclass);


--
-- TOC entry 1761 (class 2604 OID 99234)
-- Dependencies: 1489 1483
-- Name: escalafon_1; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE escalafon ALTER COLUMN escalafon_1 SET DEFAULT nextval('escalafones_escalafon_1_seq'::regclass);


--
-- TOC entry 1762 (class 2604 OID 99235)
-- Dependencies: 1490 1484
-- Name: persona; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE persona ALTER COLUMN persona SET DEFAULT nextval('persona_persona_seq'::regclass);


--
-- TOC entry 1781 (class 0 OID 99202)
-- Dependencies: 1480
-- Data for Name: cargo; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (1, 1, 'Persona A (depC 1) | depD 1 | Categoria 1b - Escalafon A (1a)', 1, 1, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (2, 3, 'Persona B (depC 1) | depD 1 | Categoria 2b - Escalafon B (2a)', 1, 2, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (3, 2, 'Persona C (depC 2) | depD 2 | Categoria 3b - Escalafon C (3a)', 2, 3, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (4, 4, 'Persona D (depC 2) | depD 2 | Categoria 4b - Escalafon D (4a)', 2, 4, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (5, 5, 'Persona E (depC 3) | depD 3 | Categoria 5b - Escalafon A (1a)', 3, 5, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (6, 6, 'Persona F (depC 3) | depD 3 | Categoria 6b - Escalafon B (2a)', 3, 6, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (7, 7, 'Persona G (depC 4) | depD 4 | Categoria 7b - Escalafon C (3a)', 4, 7, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (8, 8, 'Persona H (depC 4) | depD 4 | Categoria 8b - Escalafon D (4a)', 4, 8, 'b');

INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (1,  9, 'Persona A (depC 1) | depD 4 | Categoria 8b - Escalafon D (4a)', 4, 8, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (2, 11, 'Persona B (depC 1) | depD 4 | Categoria 7b - Escalafon C (3a)', 4, 7, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (3, 10, 'Persona C (depC 2) | depD 3 | Categoria 6b - Escalafon B (2a)', 3, 6, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (4, 12, 'Persona D (depC 2) | depD 3 | Categoria 5b - Escalafon A (1a)', 3, 5, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (5, 13, 'Persona E (depC 3) | depD 2 | Categoria 4b - Escalafon D (4a)', 2, 4, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (6, 14, 'Persona F (depC 3) | depD 2 | Categoria 3b - Escalafon C (3a)', 2, 3, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (7, 15, 'Persona G (depC 4) | depD 1 | Categoria 2b - Escalafon B (2a)', 1, 2, 'b');
INSERT INTO cargo (persona, cargo, descripcion, dependencia, categoria_1, categoria_2) VALUES (8, 16, 'Persona H (depC 4) | depD 1 | Categoria 1b - Escalafon A (1a)', 1, 1, 'b');

--
-- TOC entry 1782 (class 0 OID 99206)
-- Dependencies: 1481
-- Data for Name: categoria; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO categoria (categoria_1, categoria_2, descripcion, escalafon_1, escalafon_2) VALUES (1, 'b', 'Categoria 1b - Escalafon A (1a)', 1, 'a');
INSERT INTO categoria (categoria_1, categoria_2, descripcion, escalafon_1, escalafon_2) VALUES (2, 'b', 'Categoria 2b - Escalafon B (2a)', 2, 'a');
INSERT INTO categoria (categoria_1, categoria_2, descripcion, escalafon_1, escalafon_2) VALUES (3, 'b', 'Categoria 3b - Escalafon C (3a)', 3, 'a');
INSERT INTO categoria (categoria_1, categoria_2, descripcion, escalafon_1, escalafon_2) VALUES (4, 'b', 'Categoria 4b - Escalafon D (4a)', 4, 'a');
INSERT INTO categoria (categoria_1, categoria_2, descripcion, escalafon_1, escalafon_2) VALUES (5, 'b', 'Categoria 5b - Escalafon A (1a)', 1, 'a');
INSERT INTO categoria (categoria_1, categoria_2, descripcion, escalafon_1, escalafon_2) VALUES (6, 'b', 'Categoria 6b - Escalafon B (2a)', 2, 'a');
INSERT INTO categoria (categoria_1, categoria_2, descripcion, escalafon_1, escalafon_2) VALUES (7, 'b', 'Categoria 7b - Escalafon C (3a)', 3, 'a');
INSERT INTO categoria (categoria_1, categoria_2, descripcion, escalafon_1, escalafon_2) VALUES (8, 'b', 'Categoria 8b - Escalafon D (4a)', 4, 'a');


--
-- TOC entry 1783 (class 0 OID 99209)
-- Dependencies: 1482
-- Data for Name: dependencia; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO dependencia (dependencia, descripcion) VALUES (1, 'Dependencia A');
INSERT INTO dependencia (dependencia, descripcion) VALUES (2, 'Dependencia B');
INSERT INTO dependencia (dependencia, descripcion) VALUES (3, 'Dependencia C');
INSERT INTO dependencia (dependencia, descripcion) VALUES (4, 'Dependencia D');


--
-- TOC entry 1784 (class 0 OID 99212)
-- Dependencies: 1483
-- Data for Name: escalafon; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO escalafon (escalafon_1, escalafon_2, descripcion) VALUES (1, 'a', 'Escalafon A (1a)');
INSERT INTO escalafon (escalafon_1, escalafon_2, descripcion) VALUES (2, 'a', 'Escalafon B (2a)');
INSERT INTO escalafon (escalafon_1, escalafon_2, descripcion) VALUES (3, 'a', 'Escalafon C (3a)');
INSERT INTO escalafon (escalafon_1, escalafon_2, descripcion) VALUES (4, 'a', 'Escalafon D (4a)');


--
-- TOC entry 1785 (class 0 OID 99215)
-- Dependencies: 1484
-- Data for Name: persona; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO persona (persona, descripcion) VALUES (1, 'Persona A');
INSERT INTO persona (persona, descripcion) VALUES (2, 'Persona B');
INSERT INTO persona (persona, descripcion) VALUES (3, 'Persona C');
INSERT INTO persona (persona, descripcion) VALUES (4, 'Persona D');
INSERT INTO persona (persona, descripcion) VALUES (5, 'Persona E');
INSERT INTO persona (persona, descripcion) VALUES (6, 'Persona F');
INSERT INTO persona (persona, descripcion) VALUES (7, 'Persona G');
INSERT INTO persona (persona, descripcion) VALUES (8, 'Persona H');


--
-- TOC entry 1786 (class 0 OID 99218)
-- Dependencies: 1485
-- Data for Name: persona_extra; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO persona_extra (persona, descripcion, dependencia) VALUES (1, 'Persona A (depC 1)                                                              ', 1);
INSERT INTO persona_extra (persona, descripcion, dependencia) VALUES (3, 'Persona C (depC 2)                                                              ', 2);
INSERT INTO persona_extra (persona, descripcion, dependencia) VALUES (2, 'Persona B (depC 1)                                                              ', 1);
INSERT INTO persona_extra (persona, descripcion, dependencia) VALUES (4, 'Persona D (depC 2)                                                              ', 2);
INSERT INTO persona_extra (persona, descripcion, dependencia) VALUES (5, 'Persona E (depC 3)                                                              ', 3);
INSERT INTO persona_extra (persona, descripcion, dependencia) VALUES (6, 'Persona F (depC 3)                                                              ', 3);
INSERT INTO persona_extra (persona, descripcion, dependencia) VALUES (7, 'Persona G (depC 4)                                                              ', 4);
INSERT INTO persona_extra (persona, descripcion, dependencia) VALUES (8, 'Persona H (depC 4)                                                              ', 4);


--
-- TOC entry 1764 (class 2606 OID 99237)
-- Dependencies: 1480 1480 1480
-- Name: cargos_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cargo
    ADD CONSTRAINT cargos_pkey PRIMARY KEY (persona, cargo);


--
-- TOC entry 1766 (class 2606 OID 99239)
-- Dependencies: 1481 1481 1481
-- Name: categoria_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (categoria_1, categoria_2);


--
-- TOC entry 1768 (class 2606 OID 99241)
-- Dependencies: 1482 1482
-- Name: dependencia_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY dependencia
    ADD CONSTRAINT dependencia_pkey PRIMARY KEY (dependencia);


--
-- TOC entry 1770 (class 2606 OID 99243)
-- Dependencies: 1483 1483 1483
-- Name: escalafones_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY escalafon
    ADD CONSTRAINT escalafones_pkey PRIMARY KEY (escalafon_1, escalafon_2);


--
-- TOC entry 1774 (class 2606 OID 99245)
-- Dependencies: 1485 1485
-- Name: persona_extra_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY persona_extra
    ADD CONSTRAINT persona_extra_pkey PRIMARY KEY (persona);


--
-- TOC entry 1772 (class 2606 OID 99247)
-- Dependencies: 1484 1484
-- Name: persona_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY persona
    ADD CONSTRAINT persona_pkey PRIMARY KEY (persona);


--
-- TOC entry 1775 (class 2606 OID 99248)
-- Dependencies: 1765 1480 1480 1481 1481
-- Name: cargos_categoria_1_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY cargo
    ADD CONSTRAINT cargos_categoria_1_fkey FOREIGN KEY (categoria_1, categoria_2) REFERENCES categoria(categoria_1, categoria_2);


--
-- TOC entry 1776 (class 2606 OID 99253)
-- Dependencies: 1480 1767 1482
-- Name: cargos_dependencia_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY cargo
    ADD CONSTRAINT cargos_dependencia_fkey FOREIGN KEY (dependencia) REFERENCES dependencia(dependencia);


--
-- TOC entry 1777 (class 2606 OID 99258)
-- Dependencies: 1771 1484 1480
-- Name: cargos_persona_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY cargo
    ADD CONSTRAINT cargos_persona_fkey FOREIGN KEY (persona) REFERENCES persona(persona);


--
-- TOC entry 1778 (class 2606 OID 99263)
-- Dependencies: 1481 1481 1483 1483 1769
-- Name: categoria_escalafon_1_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY categoria
    ADD CONSTRAINT categoria_escalafon_1_fkey FOREIGN KEY (escalafon_1, escalafon_2) REFERENCES escalafon(escalafon_1, escalafon_2);


--
-- TOC entry 1779 (class 2606 OID 99268)
-- Dependencies: 1767 1485 1482
-- Name: persona_extra_dependencia_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY persona_extra
    ADD CONSTRAINT persona_extra_dependencia_fkey FOREIGN KEY (dependencia) REFERENCES dependencia(dependencia);


--
-- TOC entry 1780 (class 2606 OID 99273)
-- Dependencies: 1484 1485 1771
-- Name: persona_extra_persona_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY persona_extra
    ADD CONSTRAINT persona_extra_persona_fkey FOREIGN KEY (persona) REFERENCES persona(persona);


--
-- TOC entry 1791 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: -
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2008-06-12 19:05:05

--
-- PostgreSQL database dump complete
--

