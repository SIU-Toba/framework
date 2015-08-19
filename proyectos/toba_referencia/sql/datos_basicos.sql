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

INSERT INTO ref_juegos VALUES (1, 'Ajedrez', NULL, TRUE);
INSERT INTO ref_juegos VALUES (2, 'Damas', NULL, TRUE);
INSERT INTO ref_juegos VALUES (4, 'Go', NULL, TRUE);
INSERT INTO ref_juegos VALUES (5, 'Reversi', NULL, TRUE);
INSERT INTO ref_juegos VALUES (6, 'Rayuela', NULL, FALSE);
INSERT INTO ref_juegos VALUES (7, 'Call of duty 4', NULL, FALSE);
INSERT INTO ref_juegos VALUES (8, 'PES 2015', NULL, FALSE);


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

INSERT INTO ref_persona_deportes VALUES (1, 1, 5, 3, '14:15', '16:35');
INSERT INTO ref_persona_deportes VALUES (2, 2, 6, 4, '17:00', '19:00');
INSERT INTO ref_persona_deportes VALUES (3, 1, 7, 3, '18:00', '20:00');


--
-- Data for Name: ref_persona_juegos; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO ref_persona_juegos VALUES (3, 2, 5, 0, 17, 19);
INSERT INTO ref_persona_juegos VALUES (1, 1, 1, 0, 17, 19);
INSERT INTO ref_persona_juegos VALUES (2, 1, 2, 1, 17, 19);
INSERT INTO ref_persona_juegos VALUES (4, 1, 6, 1, 17, 19);
INSERT INTO ref_persona_juegos VALUES (5, 1, 8, 1, 17, 19);
INSERT INTO ref_persona_juegos VALUES (6, 2, 7, 1, 17, 19);
INSERT INTO ref_persona_juegos VALUES (7, 2, 8, 1, 17, 19);




INSERT INTO iso_countries VALUES (1,1,'en','AF','Afghanistan','+93');
INSERT INTO iso_countries VALUES (2,2,'en','AL','Albania','+355');
INSERT INTO iso_countries VALUES (3,3,'en','DZ','Algeria','+213');
INSERT INTO iso_countries VALUES (4,4,'en','AD','Andorra','+376');
INSERT INTO iso_countries VALUES (5,5,'en','AO','Angola','+244');
INSERT INTO iso_countries VALUES (6,6,'en','AG','Antigua and Barbuda','+1-268');
INSERT INTO iso_countries VALUES (7,7,'es','AR','Argentina','+54');
INSERT INTO iso_countries VALUES (8,8,'en','AM','Armenia','+374');
INSERT INTO iso_countries VALUES (9,9,'en','AU','Australia','+61');
INSERT INTO iso_countries VALUES (10,10,'en','AT','Austria','+43');
INSERT INTO iso_countries VALUES (11,11,'en','AZ','Azerbaijan','+994');
INSERT INTO iso_countries VALUES (12,12,'en','BS','Bahamas, The','+1-242');
INSERT INTO iso_countries VALUES (13,13,'en','BH','Bahrain','+973');
INSERT INTO iso_countries VALUES (14,14,'en','BD','Bangladesh','+880');
INSERT INTO iso_countries VALUES (15,15,'en','BB','Barbados','+1-246');
INSERT INTO iso_countries VALUES (16,16,'en','BY','Belarus','+375');
INSERT INTO iso_countries VALUES (17,17,'en','BE','Belgium','+32');
INSERT INTO iso_countries VALUES (18,18,'en','BZ','Belize','+501');
INSERT INTO iso_countries VALUES (19,19,'en','BJ','Benin','+229');
INSERT INTO iso_countries VALUES (20,20,'en','BT','Bhutan','+975');
INSERT INTO iso_countries VALUES (21,21,'es','BO','Bolivia','+591');
INSERT INTO iso_countries VALUES (22,22,'en','BA','Bosnia and Herzegovina','+387');
INSERT INTO iso_countries VALUES (23,23,'en','BW','Botswana','+267');
INSERT INTO iso_countries VALUES (24,24,'en','BR','Brazil','+55');
INSERT INTO iso_countries VALUES (25,25,'en','BN','Brunei','+673');
INSERT INTO iso_countries VALUES (26,26,'en','BG','Bulgaria','+359');
INSERT INTO iso_countries VALUES (27,27,'en','BF','Burkina Faso','+226');
INSERT INTO iso_countries VALUES (28,28,'en','BI','Burundi','+257');
INSERT INTO iso_countries VALUES (29,29,'en','KH','Cambodia','+855');
INSERT INTO iso_countries VALUES (30,30,'en','CM','Cameroon','+237');
INSERT INTO iso_countries VALUES (31,31,'en','CA','Canada','+1');
INSERT INTO iso_countries VALUES (32,32,'en','CV','Cape Verde','+238');
INSERT INTO iso_countries VALUES (33,33,'en','CF','Central African Republic','+236');
INSERT INTO iso_countries VALUES (34,34,'en','TD','Chad','+235');
INSERT INTO iso_countries VALUES (35,35,'es','CL','Chile','+56');
INSERT INTO iso_countries VALUES (36,36,'en','CN','China, People''s Republic of','+86');
INSERT INTO iso_countries VALUES (37,37,'es','CO','Colombia','+57');
INSERT INTO iso_countries VALUES (38,38,'en','KM','Comoros','+269');
INSERT INTO iso_countries VALUES (39,39,'en','CD','Congo, Democratic Republic of the (Congo ? Kinshasa)','+243');
INSERT INTO iso_countries VALUES (40,40,'en','CG','Congo, Republic of the (Congo ? Brazzaville)','+242');
INSERT INTO iso_countries VALUES (41,41,'es','CR','Costa Rica','+506');
INSERT INTO iso_countries VALUES (42,42,'en','CI','Cote d''Ivoire (Ivory Coast)','+225');
INSERT INTO iso_countries VALUES (43,43,'en','HR','Croatia','+385');
INSERT INTO iso_countries VALUES (44,44,'es','CU','Cuba','+53');
INSERT INTO iso_countries VALUES (45,45,'en','CY','Cyprus','+357');
INSERT INTO iso_countries VALUES (46,46,'en','CZ','Czech Republic','+420');
INSERT INTO iso_countries VALUES (47,47,'en','DK','Denmark','+45');
INSERT INTO iso_countries VALUES (48,48,'en','DJ','Djibouti','+253');
INSERT INTO iso_countries VALUES (49,49,'en','DM','Dominica','+1-767');
INSERT INTO iso_countries VALUES (50,50,'es','DO','Dominican Republic','+1-809 and 1-829');
INSERT INTO iso_countries VALUES (51,51,'es','EC','Ecuador','+593');
INSERT INTO iso_countries VALUES (52,52,'en','EG','Egypt','+20');
INSERT INTO iso_countries VALUES (53,53,'es','SV','El Salvador','+503');
INSERT INTO iso_countries VALUES (54,54,'en','GQ','Equatorial Guinea','+240');
INSERT INTO iso_countries VALUES (55,55,'en','ER','Eritrea','+291');
INSERT INTO iso_countries VALUES (56,56,'en','EE','Estonia','+372');
INSERT INTO iso_countries VALUES (57,57,'en','ET','Ethiopia','+251');
INSERT INTO iso_countries VALUES (58,58,'en','FJ','Fiji','+679');
INSERT INTO iso_countries VALUES (59,59,'en','FI','Finland','+358');
INSERT INTO iso_countries VALUES (60,60,'en','FR','France','+33');
INSERT INTO iso_countries VALUES (61,61,'en','GA','Gabon','+241');
INSERT INTO iso_countries VALUES (62,62,'en','GM','Gambia, The','+220');
INSERT INTO iso_countries VALUES (63,63,'en','GE','Georgia','+995');
INSERT INTO iso_countries VALUES (64,64,'en','DE','Germany','+49');
INSERT INTO iso_countries VALUES (65,65,'en','GH','Ghana','+233');
INSERT INTO iso_countries VALUES (66,66,'en','GR','Greece','+30');
INSERT INTO iso_countries VALUES (67,67,'en','GD','Grenada','+1-473');
INSERT INTO iso_countries VALUES (68,68,'es','GT','Guatemala','+502');
INSERT INTO iso_countries VALUES (69,69,'en','GN','Guinea','+224');
INSERT INTO iso_countries VALUES (70,70,'en','GW','Guinea-Bissau','+245');
INSERT INTO iso_countries VALUES (71,71,'en','GY','Guyana','+592');
INSERT INTO iso_countries VALUES (72,72,'en','HT','Haiti','+509');
INSERT INTO iso_countries VALUES (73,73,'es','HN','Honduras','+504');
INSERT INTO iso_countries VALUES (74,74,'en','HU','Hungary','+36');
INSERT INTO iso_countries VALUES (75,75,'en','IS','Iceland','+354');
INSERT INTO iso_countries VALUES (76,76,'en','IN','India','+91');
INSERT INTO iso_countries VALUES (77,77,'en','ID','Indonesia','+62');
INSERT INTO iso_countries VALUES (78,78,'en','IR','Iran','+98');
INSERT INTO iso_countries VALUES (79,79,'en','IQ','Iraq','+964');
INSERT INTO iso_countries VALUES (80,80,'en','IE','Ireland','+353');
INSERT INTO iso_countries VALUES (81,81,'en','IL','Israel','+972');
INSERT INTO iso_countries VALUES (82,82,'en','IT','Italy','+39');
INSERT INTO iso_countries VALUES (83,83,'en','JM','Jamaica','+1-876');
INSERT INTO iso_countries VALUES (84,84,'en','JP','Japan','+81');
INSERT INTO iso_countries VALUES (85,85,'en','JO','Jordan','+962');
INSERT INTO iso_countries VALUES (86,86,'en','KZ','Kazakhstan','+7');
INSERT INTO iso_countries VALUES (87,87,'en','KE','Kenya','+254');
INSERT INTO iso_countries VALUES (88,88,'en','KI','Kiribati','+686');
INSERT INTO iso_countries VALUES (89,89,'en','KP','Korea, Democratic People''s Republic of (North Korea)','+850');
INSERT INTO iso_countries VALUES (90,90,'en','KR','Korea, Republic of  (South Korea)','+82');
INSERT INTO iso_countries VALUES (91,91,'en','KW','Kuwait','+965');
INSERT INTO iso_countries VALUES (92,92,'en','KG','Kyrgyzstan','+996');
INSERT INTO iso_countries VALUES (93,93,'en','LA','Laos','+856');
INSERT INTO iso_countries VALUES (94,94,'en','LV','Latvia','+371');
INSERT INTO iso_countries VALUES (95,95,'en','LB','Lebanon','+961');
INSERT INTO iso_countries VALUES (96,96,'en','LS','Lesotho','+266');
INSERT INTO iso_countries VALUES (97,97,'en','LR','Liberia','+231');
INSERT INTO iso_countries VALUES (98,98,'en','LY','Libya','+218');
INSERT INTO iso_countries VALUES (99,99,'en','LI','Liechtenstein','+423');
INSERT INTO iso_countries VALUES (100,100,'en','LT','Lithuania','+370');
INSERT INTO iso_countries VALUES (101,101,'en','LU','Luxembourg','+352');
INSERT INTO iso_countries VALUES (102,102,'en','MK','Macedonia','+389');
INSERT INTO iso_countries VALUES (103,103,'en','MG','Madagascar','+261');
INSERT INTO iso_countries VALUES (104,104,'en','MW','Malawi','+265');
INSERT INTO iso_countries VALUES (105,105,'en','MY','Malaysia','+60');
INSERT INTO iso_countries VALUES (106,106,'en','MV','Maldives','+960');
INSERT INTO iso_countries VALUES (107,107,'en','ML','Mali','+223');
INSERT INTO iso_countries VALUES (108,108,'en','MT','Malta','+356');
INSERT INTO iso_countries VALUES (109,109,'en','MH','Marshall Islands','+692');
INSERT INTO iso_countries VALUES (110,110,'en','MR','Mauritania','+222');
INSERT INTO iso_countries VALUES (111,111,'en','MU','Mauritius','+230');
INSERT INTO iso_countries VALUES (112,112,'es','MX','Mexico','+52');
INSERT INTO iso_countries VALUES (113,113,'en','FM','Micronesia','+691');
INSERT INTO iso_countries VALUES (114,114,'en','MD','Moldova','+373');
INSERT INTO iso_countries VALUES (115,115,'en','MC','Monaco','+377');
INSERT INTO iso_countries VALUES (116,116,'en','MN','Mongolia','+976');
INSERT INTO iso_countries VALUES (117,117,'en','ME','Montenegro','+382');
INSERT INTO iso_countries VALUES (118,118,'en','MA','Morocco','+212');
INSERT INTO iso_countries VALUES (119,119,'en','MZ','Mozambique','+258');
INSERT INTO iso_countries VALUES (120,120,'en','MM','Myanmar (Burma)','+95');
INSERT INTO iso_countries VALUES (121,121,'en','NA','Namibia','+264');
INSERT INTO iso_countries VALUES (122,122,'en','NR','Nauru','+674');
INSERT INTO iso_countries VALUES (123,123,'en','NP','Nepal','+977');
INSERT INTO iso_countries VALUES (124,124,'en','NL','Netherlands','+31');
INSERT INTO iso_countries VALUES (125,125,'en','NZ','New Zealand','+64');
INSERT INTO iso_countries VALUES (126,126,'es','NI','Nicaragua','+505');
INSERT INTO iso_countries VALUES (127,127,'en','NE','Niger','+227');
INSERT INTO iso_countries VALUES (128,128,'en','NG','Nigeria','+234');
INSERT INTO iso_countries VALUES (129,129,'en','NO','Norway','+47');
INSERT INTO iso_countries VALUES (130,130,'en','OM','Oman','+968');
INSERT INTO iso_countries VALUES (131,131,'en','PK','Pakistan','+92');
INSERT INTO iso_countries VALUES (132,132,'en','PW','Palau','+680');
INSERT INTO iso_countries VALUES (133,133,'es','PA','Panama','+507');
INSERT INTO iso_countries VALUES (134,134,'en','PG','Papua New Guinea','+675');
INSERT INTO iso_countries VALUES (135,135,'es','PY','Paraguay','+595');
INSERT INTO iso_countries VALUES (136,136,'es','PE','Peru','+51');
INSERT INTO iso_countries VALUES (137,137,'en','PH','Philippines','+63');
INSERT INTO iso_countries VALUES (138,138,'en','PL','Poland','+48');
INSERT INTO iso_countries VALUES (139,139,'en','PT','Portugal','+351');
INSERT INTO iso_countries VALUES (140,140,'en','QA','Qatar','+974');
INSERT INTO iso_countries VALUES (141,141,'en','RO','Romania','+40');
INSERT INTO iso_countries VALUES (142,142,'en','RU','Russia','+7');
INSERT INTO iso_countries VALUES (143,143,'en','RW','Rwanda','+250');
INSERT INTO iso_countries VALUES (144,144,'en','KN','Saint Kitts and Nevis','+1-869');
INSERT INTO iso_countries VALUES (145,145,'en','LC','Saint Lucia','+1-758');
INSERT INTO iso_countries VALUES (146,146,'en','VC','Saint Vincent and the Grenadines','+1-784');
INSERT INTO iso_countries VALUES (147,147,'en','WS','Samoa','+685');
INSERT INTO iso_countries VALUES (148,148,'en','SM','San Marino','+378');
INSERT INTO iso_countries VALUES (149,149,'en','ST','Sao Tome and Principe','+239');
INSERT INTO iso_countries VALUES (150,150,'en','SA','Saudi Arabia','+966');
INSERT INTO iso_countries VALUES (151,151,'en','SN','Senegal','+221');
INSERT INTO iso_countries VALUES (152,152,'en','RS','Serbia','+381');
INSERT INTO iso_countries VALUES (153,153,'en','SC','Seychelles','+248');
INSERT INTO iso_countries VALUES (154,154,'en','SL','Sierra Leone','+232');
INSERT INTO iso_countries VALUES (155,155,'en','SG','Singapore','+65');
INSERT INTO iso_countries VALUES (156,156,'en','SK','Slovakia','+421');
INSERT INTO iso_countries VALUES (157,157,'en','SI','Slovenia','+386');
INSERT INTO iso_countries VALUES (158,158,'en','SB','Solomon Islands','+677');
INSERT INTO iso_countries VALUES (159,159,'en','SO','Somalia','+252');
INSERT INTO iso_countries VALUES (160,160,'en','ZA','South Africa','+27');
INSERT INTO iso_countries VALUES (161,161,'es','ES','Spain','+34');
INSERT INTO iso_countries VALUES (162,162,'en','LK','Sri Lanka','+94');
INSERT INTO iso_countries VALUES (163,163,'en','SD','Sudan','+249');
INSERT INTO iso_countries VALUES (164,164,'en','SR','Suriname','+597');
INSERT INTO iso_countries VALUES (165,165,'en','SZ','Swaziland','+268');
INSERT INTO iso_countries VALUES (166,166,'en','SE','Sweden','+46');
INSERT INTO iso_countries VALUES (167,167,'en','CH','Switzerland','+41');
INSERT INTO iso_countries VALUES (168,168,'en','SY','Syria','+963');
INSERT INTO iso_countries VALUES (169,169,'en','TJ','Tajikistan','+992');
INSERT INTO iso_countries VALUES (170,170,'en','TZ','Tanzania','+255');
INSERT INTO iso_countries VALUES (171,171,'en','TH','Thailand','+66');
INSERT INTO iso_countries VALUES (172,172,'en','TL','Timor-Leste (East Timor)','+670');
INSERT INTO iso_countries VALUES (173,173,'en','TG','Togo','+228');
INSERT INTO iso_countries VALUES (174,174,'en','TO','Tonga','+676');
INSERT INTO iso_countries VALUES (175,175,'en','TT','Trinidad and Tobago','+1-868');
INSERT INTO iso_countries VALUES (176,176,'en','TN','Tunisia','+216');
INSERT INTO iso_countries VALUES (177,177,'en','TR','Turkey','+90');
INSERT INTO iso_countries VALUES (178,178,'en','TM','Turkmenistan','+993');
INSERT INTO iso_countries VALUES (179,179,'en','TV','Tuvalu','+688');
INSERT INTO iso_countries VALUES (180,180,'en','UG','Uganda','+256');
INSERT INTO iso_countries VALUES (181,181,'en','UA','Ukraine','+380');
INSERT INTO iso_countries VALUES (182,182,'en','AE','United Arab Emirates','+971');
INSERT INTO iso_countries VALUES (183,183,'en','GB','United Kingdom','+44');
INSERT INTO iso_countries VALUES (184,184,'en','US','United States','+1');
INSERT INTO iso_countries VALUES (185,185,'es','UY','Uruguay','+598');
INSERT INTO iso_countries VALUES (186,186,'en','UZ','Uzbekistan','+998');
INSERT INTO iso_countries VALUES (187,187,'en','VU','Vanuatu','+678');
INSERT INTO iso_countries VALUES (188,188,'en','VA','Vatican City','+379');
INSERT INTO iso_countries VALUES (189,189,'es','VE','Venezuela','+58');
INSERT INTO iso_countries VALUES (190,190,'en','VN','Viet Nam','+84');
INSERT INTO iso_countries VALUES (191,191,'en','YE','Yemen','+967');
INSERT INTO iso_countries VALUES (192,192,'en','ZM','Zambia','+260');
INSERT INTO iso_countries VALUES (193,193,'en','ZW','Zimbabwe','+263');
INSERT INTO iso_countries VALUES (194,194,'en','GE','Abkhazia','+995');
INSERT INTO iso_countries VALUES (195,195,'en','TW','China, Republic of (Taiwan)','+886');
INSERT INTO iso_countries VALUES (196,196,'en','AZ','Nagorno-Karabakh','+374-97');
INSERT INTO iso_countries VALUES (197,197,'en','CY','Northern Cyprus','+90-392');
INSERT INTO iso_countries VALUES (198,198,'en','MD','Pridnestrovie (Transnistria)','+373-533');
INSERT INTO iso_countries VALUES (199,199,'en','SO','Somaliland','+252');
INSERT INTO iso_countries VALUES (200,200,'en','GE','South Ossetia','+995');
INSERT INTO iso_countries VALUES (201,201,'en','AU','Ashmore and Cartier Islands','');
INSERT INTO iso_countries VALUES (202,202,'en','CX','Christmas Island','+61');
INSERT INTO iso_countries VALUES (203,203,'en','CC','Cocos (Keeling) Islands','+61');
INSERT INTO iso_countries VALUES (204,204,'en','AU','Coral Sea Islands','');
INSERT INTO iso_countries VALUES (205,205,'en','HM','Heard Island and McDonald Islands','');
INSERT INTO iso_countries VALUES (206,206,'en','NF','Norfolk Island','+672');
INSERT INTO iso_countries VALUES (207,207,'en','NC','New Caledonia','+687');
INSERT INTO iso_countries VALUES (208,208,'en','PF','French Polynesia','+689');
INSERT INTO iso_countries VALUES (209,209,'en','YT','Mayotte','+269');
INSERT INTO iso_countries VALUES (210,210,'en','PM','Saint Pierre and Miquelon','+508');
INSERT INTO iso_countries VALUES (211,211,'en','WF','Wallis and Futuna','+681');
INSERT INTO iso_countries VALUES (212,212,'en','TF','French Southern and Antarctic Lands','');
INSERT INTO iso_countries VALUES (213,213,'en','PF','Clipperton Island','');
INSERT INTO iso_countries VALUES (214,214,'en','','French Scattered Islands in the Indian Ocean','');
INSERT INTO iso_countries VALUES (215,215,'en','BV','Bouvet Island','');
INSERT INTO iso_countries VALUES (216,216,'en','CK','Cook Islands','+682');
INSERT INTO iso_countries VALUES (217,217,'en','NU','Niue','+683');
INSERT INTO iso_countries VALUES (218,218,'en','TK','Tokelau','+690');
INSERT INTO iso_countries VALUES (219,219,'en','GG','Guernsey','+44');
INSERT INTO iso_countries VALUES (220,220,'en','IM','Isle of Man','+44');
INSERT INTO iso_countries VALUES (221,221,'en','JE','Jersey','+44');
INSERT INTO iso_countries VALUES (222,222,'en','AI','Anguilla','+1-264');
INSERT INTO iso_countries VALUES (223,223,'en','BM','Bermuda','+1-441');
INSERT INTO iso_countries VALUES (224,224,'en','IO','British Indian Ocean Territory','+246');
INSERT INTO iso_countries VALUES (225,225,'en','','British Sovereign Base Areas','+357');
INSERT INTO iso_countries VALUES (226,226,'en','VG','British Virgin Islands','+1-284');
INSERT INTO iso_countries VALUES (227,227,'en','KY','Cayman Islands','+1-345');
INSERT INTO iso_countries VALUES (228,228,'en','FK','Falkland Islands (Islas Malvinas)','+500');
INSERT INTO iso_countries VALUES (229,229,'en','GI','Gibraltar','+350');
INSERT INTO iso_countries VALUES (230,230,'en','MS','Montserrat','+1-664');
INSERT INTO iso_countries VALUES (231,231,'en','PN','Pitcairn Islands','');
INSERT INTO iso_countries VALUES (232,232,'en','SH','Saint Helena','+290');
INSERT INTO iso_countries VALUES (233,233,'en','GS','South Georgia and the South Sandwich Islands','');
INSERT INTO iso_countries VALUES (234,234,'en','TC','Turks and Caicos Islands','+1-649');
INSERT INTO iso_countries VALUES (235,235,'en','MP','Northern Mariana Islands','+1-670');
INSERT INTO iso_countries VALUES (236,236,'es','PR','Puerto Rico','+1-787 and 1-939');
INSERT INTO iso_countries VALUES (237,237,'en','AS','American Samoa','+1-684');
INSERT INTO iso_countries VALUES (238,238,'en','UM','Baker Island','');
INSERT INTO iso_countries VALUES (239,239,'en','GU','Guam','+1-671');
INSERT INTO iso_countries VALUES (240,240,'en','UM','Howland Island','');
INSERT INTO iso_countries VALUES (241,241,'en','UM','Jarvis Island','');
INSERT INTO iso_countries VALUES (242,242,'en','UM','Johnston Atoll','');
INSERT INTO iso_countries VALUES (243,243,'en','UM','Kingman Reef','');
INSERT INTO iso_countries VALUES (244,244,'en','UM','Midway Islands','');
INSERT INTO iso_countries VALUES (245,245,'en','UM','Navassa Island','');
INSERT INTO iso_countries VALUES (246,246,'en','UM','Palmyra Atoll','');
INSERT INTO iso_countries VALUES (247,247,'en','VI','U.S. Virgin Islands','+1-340');
INSERT INTO iso_countries VALUES (248,248,'en','UM','Wake Island','');
INSERT INTO iso_countries VALUES (249,249,'en','HK','Hong Kong','+852');
INSERT INTO iso_countries VALUES (250,250,'en','MO','Macau','+853');
INSERT INTO iso_countries VALUES (251,251,'en','FO','Faroe Islands','+298');
INSERT INTO iso_countries VALUES (252,252,'en','GL','Greenland','+299');
INSERT INTO iso_countries VALUES (253,253,'en','GF','French Guiana','+594');
INSERT INTO iso_countries VALUES (254,254,'en','GP','Guadeloupe','+590');
INSERT INTO iso_countries VALUES (255,255,'en','MQ','Martinique','+596');
INSERT INTO iso_countries VALUES (256,256,'en','RE','Reunion','+262');
INSERT INTO iso_countries VALUES (257,257,'en','AX','Aland','+358-18');
INSERT INTO iso_countries VALUES (258,258,'en','AW','Aruba','+297');
INSERT INTO iso_countries VALUES (259,259,'en','AN','Netherlands Antilles','+599');
INSERT INTO iso_countries VALUES (260,260,'en','SJ','Svalbard','+47');
INSERT INTO iso_countries VALUES (261,261,'en','AC','Ascension','+247');
INSERT INTO iso_countries VALUES (262,262,'en','TA','Tristan da Cunha','');
INSERT INTO iso_countries VALUES (263,263,'en','AQ','Antarctica','');
INSERT INTO iso_countries VALUES (264,264,'en','CS','Kosovo','+381');
INSERT INTO iso_countries VALUES (265,265,'en','PS','Palestinian Territories (Gaza Strip and West Bank)','+970');
INSERT INTO iso_countries VALUES (266,266,'en','EH','Western Sahara','+212');
INSERT INTO iso_countries VALUES (267,267,'en','AQ','Australian Antarctic Territory','');
INSERT INTO iso_countries VALUES (268,268,'en','AQ','Ross Dependency','');
INSERT INTO iso_countries VALUES (269,269,'en','AQ','Peter I Island','');
INSERT INTO iso_countries VALUES (270,270,'en','AQ','Queen Maud Land','');
INSERT INTO iso_countries VALUES (271,271,'en','AQ','British Antarctic Territory','');
INSERT INTO iso_countries VALUES (272,9,'de','AU','Australien',NULL);
INSERT INTO iso_countries VALUES (273,24,'de','BR','Brasilien',NULL);
INSERT INTO iso_countries VALUES (274,31,'de','CA','Kanada',NULL);
INSERT INTO iso_countries VALUES (276,64,'de','DE','Deutschland',NULL);
INSERT INTO iso_countries VALUES (277,74,'de','HU','Ungarn',NULL);
INSERT INTO iso_countries VALUES (278,76,'de','IN','Indien',NULL);
INSERT INTO iso_countries VALUES (279,82,'de','IT','Italien',NULL);
INSERT INTO iso_countries VALUES (280,124,'de','NL','Holland',NULL);
INSERT INTO iso_countries VALUES (281,125,'de','NZ','Neu Seeland',NULL);
INSERT INTO iso_countries VALUES (282,138,'de','PL','Polen',NULL);
INSERT INTO iso_countries VALUES (283,142,'de','RU','Russland',NULL);
INSERT INTO iso_countries VALUES (284,160,'de','ZA','SÃ¼d Afrika',NULL);
INSERT INTO iso_countries VALUES (285,161,'de','ES','Spanien',NULL);
INSERT INTO iso_countries VALUES (286,167,'de','CH','Schweiz',NULL);
INSERT INTO iso_countries VALUES (287,184,'en','US','USA',NULL);