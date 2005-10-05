--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET search_path = public, pg_catalog;

--
-- Data for TOC entry 9 (OID 56992426)
-- Name: ona_pais; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY ona_pais (idpais, nombre, ddi, esuniversidad, modiuniversidad) FROM stdin;
AR	Argentina	54	-1	-1
B 	Brasil	55	0	0
CH	Chile	56	0	0
P 	Paraguay	59	0	0
U 	Uruguay	59	0	0
\.


--
-- Data for TOC entry 10 (OID 56992436)
-- Name: ona_provincia; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY ona_provincia (idpais, idprovincia, nombre, esuniversidad, modiuniversidad) FROM stdin;
AR	A   	Salta	0	-1
AR	B   	Buenos Aires	0	0
AR	C   	Capital Federal	0	0
AR	D   	San Luis	0	0
AR	E   	Entre Ríos	0	0
AR	F   	La Rioja	0	0
AR	G   	Santiago Del Estero	0	0
AR	H   	Chaco	0	0
AR	J   	San Juan	0	0
AR	K   	Catamarca	0	0
AR	L   	La Pampa	0	0
AR	M   	Mendoza	0	0
AR	N   	Misiones	0	0
AR	P   	Formosa	0	0
AR	Q   	Neuquén	0	0
AR	R   	Río Negro	0	0
AR	S   	Santa Fé	0	0
AR	T   	Tucumán	0	0
AR	U   	Chubut	0	0
AR	V   	Tierra Del Fuego	0	0
AR	W   	Corrientes	0	0
AR	X   	Córdoba	0	0
AR	Y   	Jujuy	0	0
AR	Z   	Santa Cruz	0	0
P 	PP  	Una Provincia de Paraguay	0	0
U 	PU  	Una Provincia de Uruguay	0	0
\.


--
-- Data for TOC entry 11 (OID 56992450)
-- Name: ona_localidad; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY ona_localidad (idpais, idprovincia, codigopostal, nombre, ddn, esuniversidad, modiuniversidad) FROM stdin;
AR	A   	4126      	Candelaria		0	0
AR	A   	4141      	Tolombon		0	0
AR	A   	4190      	Rosario De La Frontera		0	0
AR	A   	4191      	El Naranjo		0	0
AR	A   	4193      	Alte. Brown		0	0
AR	A   	4198      	Arenal		0	0
AR	A   	4400      	Salta		0	0
AR	A   	4401      	Caldera		0	0
AR	A   	4403      	Cerrillos		0	0
AR	A   	4405      	El Manzano		0	0
AR	A   	4407      	Campo Quijano		0	0
AR	A   	4409      	Cachiñal		0	0
AR	A   	4413      	Caipe		0	0
AR	A   	4415      	El Potrero		0	0
AR	A   	4417      	Cachi		0	0
AR	A   	4419      	Luracatao		0	0
AR	A   	4421      	Ampascachi		0	0
AR	A   	4423      	Chicoana		0	0
AR	A   	4425      	Alemania		0	0
AR	A   	4427      	Angastaco		0	0
AR	A   	4430      	Gral. Guemes		0	0
AR	A   	4432      	Campo Santo		0	0
AR	A   	4434      	Cabeza De Buey		0	0
AR	A   	4440      	Metan		0	0
AR	A   	4441      	Metan Viejo		0	0
AR	A   	4444      	El Galpon		0	0
AR	A   	4446      	Ceibalito		0	0
AR	A   	4448      	Cnel. Vidt		0	0
AR	A   	4449      	Apolinario Saravia		0	0
AR	A   	4452      	El Quebrachal		0	0
AR	A   	4530      	San Ramon De La Nueva Oran		0	0
AR	A   	4531      	Colonia Santa Rosa		0	0
AR	A   	4533      	El Tabacal		0	0
AR	A   	4534      	Pichanal		0	0
AR	A   	4535      	Algarrobal		0	0
AR	A   	4537      	Chaguaral		0	0
AR	A   	4538      	Saucelito		0	0
AR	A   	4550      	Embarcacion		0	0
AR	A   	4552      	Campichuelo		0	0
AR	A   	4554      	Cap. Juan Page		0	0
AR	A   	4560      	Tartagal		0	0
AR	A   	4561      	Santa Maria		0	0
AR	A   	4562      	Gral. Enrique Mosconi		0	0
AR	A   	4563      	Campamento Vespucio		0	0
AR	A   	4564      	Piquirenda		0	0
AR	A   	4566      	Aguaray		0	0
AR	A   	4568      	Pocitos		0	0
AR	A   	4633      	Colanzuli		0	0
AR	B   	0000      	San Miguel		-1	-1
AR	B   	1         	Moreno		-1	-1
AR	B   	1054      	Capital Federal		-1	-1
AR	B   	1601      	Isla Martin Garcia		0	0
AR	B   	1605      	Carapachay		0	0
AR	B   	1611      	Vicealmirante E. Montes		0	0
AR	B   	1612      	Adolfo Sourdeaux		0	0
AR	B   	1613      	Yapeyu		0	0
AR	B   	1614      	Polvorines	054	-1	-1
AR	B   	1615      	Km. 38		0	0
AR	B   	1617      	Doctor Ricardo Rojas		0	0
AR	B   	1618      	Gral. Pacheco	\N	-1	-1
AR	B   	1619      	Garin		0	0
AR	B   	1621      	Benavidez		0	0
AR	B   	1623      	Dique Lujan		0	0
AR	B   	1625      	Escobar		0	0
AR	B   	1627      	Matheu		0	0
AR	B   	1629      	Pilar		0	0
AR	B   	1631      	Villa Rosa		0	0
AR	B   	1633      	Fatima		0	0
AR	B   	1635      	Presidente Derqui		0	0
AR	B   	1636      	Olivos		0	0
AR	B   	1640      	Martinez		0	0
AR	B   	1642      	San Isidro		0	0
AR	B   	1643      	Beccar		0	0
AR	B   	1644      	Victoria		0	0
AR	B   	1646      	San Fernando		0	0
AR	B   	1647      	Varadero Del Mini		0	0
AR	B   	1649      	Arroyo Borches		0	0
AR	B   	1650      	Barrio Gral. San Martin		0	0
AR	B   	1653      	Villa Ballester		0	0
AR	B   	1657      	Pablo Podesta		0	0
AR	B   	1659      	Bo. Sgto. Cabral-campo De Mayo		0	0
AR	B   	1661      	Villa Maside		0	0
AR	B   	1663      	Barrio Martin Fierro		0	0
AR	B   	1664      	Trujui		0	0
AR	B   	1665      	El Cruce		0	0
AR	B   	1667      	El Palenque		0	0
AR	B   	1669      	Del Viso		0	0
AR	B   	1678      	3 De Febrero		0	0
AR	B   	1682      	Villa Martin Coronado		0	0
AR	B   	1684      	El Palomar	01	-1	-1
AR	B   	1702      	Villa Jose Ingenieros		0	0
AR	B   	1704      	Ramos Mejia		0	0
AR	B   	1706      	Haedo		0	0
AR	B   	1708      	Moron		0	0
AR	B   	1712      	Castelar.		-1	-1
AR	B   	1713      	Villa Gdor. Udaondo		0	0
AR	B   	1714      	Itusaingo		0	0
AR	B   	1722      	Barrio Parque Gral. San Martin		0	0
AR	B   	1723      	Agustin Ferrari		0	0
AR	B   	1727      	Elias Romero		0	0
AR	B   	1731      	Villars		0	0
AR	B   	1733      	Plomer		0	0
AR	B   	1737      	Gral. Las Heras		0	0
AR	B   	1739      	Hornos		0	0
AR	B   	1741      	Enrique Fynn		0	0
AR	B   	1742      	Villa Gral. Zapiola		0	0
AR	B   	1744      	Barrio Jose A. Cortejarena		0	0
AR	B   	1746      	Agua De Oro		0	0
AR	B   	1748      	La Fraternidad		0	0
AR	B   	1752      	Villa Rebasa		0	0
AR	B   	1754      	Villa Luzuriaga		0	0
AR	B   	1755      	Rafael Castillo		0	0
AR	B   	1759      	Ruta 3 - Km. 29		0	0
AR	B   	1761      	Veinte De Junio		0	0
AR	B   	1763      	Puente Ezcurra		0	0
AR	B   	1765      	Casanova		0	0
AR	B   	1770      	Aldo Bonzi		0	0
AR	B   	1773      	Villa Urbana		0	0
AR	B   	1774      	La Salada		0	0
AR	B   	1776      	Villa Transradio		0	0
AR	B   	1808      	Vicente Casares		0	0
AR	B   	1812      	Maximo Paz		0	0
AR	B   	1814      	La Noria		0	0
AR	B   	1815      	Uribelarrea		0	0
AR	B   	1816      	Colonia Santa Rosa		0	0
AR	B   	1822      	Puente Alsina		0	0
AR	B   	1824      	Lanus		0	0
AR	B   	1825      	Villa Besada		0	0
AR	B   	1826      	Remedios de Escalada		-1	-1
AR	B   	1828      	Villa Centenario		0	0
AR	B   	1832      	Lomas De Zamora		0	0
AR	B   	1834      	Villa La Perla		0	0
AR	B   	1836      	Santa Catalina		0	0
AR	B   	1842      	El Jaguel		0	0
AR	B   	1846      	Barrio Lindo		0	0
AR	B   	1852      	Ministro Rivadavia		0	0
AR	B   	1858      	Villa Numancia		0	0
AR	B   	1862      	Guernica		0	0
AR	B   	1864      	Alejandro Korn		0	0
AR	B   	1865      	San Vicente		0	0
AR	B   	1870      	Avellaneda		0	0
AR	B   	1875      	Wilde		0	0
AR	B   	1876      	Bernal		0	0
AR	B   	1878      	Quilmes		0	0
AR	B   	1884      	Villa España		0	0
AR	B   	1885      	Platanos		0	0
AR	B   	1886      	Villa Giambruno		0	0
AR	B   	1888      	Villa Gral. Manuel Caraballo		0	0
AR	B   	1889      	El Rocio		0	0
AR	B   	1890      	Juan Maria Gutierrez		0	0
AR	B   	1891      	Ing. Juan Allan		0	0
AR	B   	1893      	Ruta 2 - Km. 44,500		0	0
AR	B   	1894      	Pereyra		0	0
AR	B   	1895      	Arturo Segui		0	0
AR	B   	1896      	City Bell		0	0
AR	B   	1897      	Manuel B. Gonnet		0	0
AR	B   	1900      	La Plata		0	0
AR	B   	1901      	La Josefa		0	0
AR	B   	1903      	Melchor Romero		0	0
AR	B   	1905      	Poblet		0	0
AR	B   	1907      	El Pino		0	0
AR	B   	1909      	Ignacio Correas		0	0
AR	B   	1911      	Gral. Mansilla		0	0
AR	B   	1913      	Atalaya		0	0
AR	B   	1915      	Vieytes		0	0
AR	B   	1917      	Veronica		0	0
AR	B   	1919      	Base Aerea De Punta Indio		0	0
AR	B   	1921      	Pipinas		0	0
AR	B   	1923      	Berisso		0	0
AR	B   	1925      	Ensenada		0	0
AR	B   	1927      	Esc. Nav. Militar Rio Santiago		0	0
AR	B   	1929      	Base Naval De Rio Santiago		0	0
AR	B   	1931      	Piria		0	0
AR	B   	1980      	Cnel. Brandsen Estaf. Nro 1		0	0
AR	B   	1981      	Oliden		0	0
AR	B   	1983      	Gomez		0	0
AR	B   	1984      	Domselaar		0	0
AR	B   	1986      	Jeppener		0	0
AR	B   	1987      	Ranchos		0	0
AR	B   	2700      	Fontezuela		0	0
AR	B   	2701      	Pergamino		-1	-1
AR	B   	2703      	Roberto Cano		0	0
AR	B   	2705      	Rojas		0	0
AR	B   	2707      	Hunter		0	0
AR	B   	2709      	Los Indios		0	0
AR	B   	2711      	Paraje Santa Rosa		0	0
AR	B   	2713      	Manuel Ocampo		0	0
AR	B   	2715      	La Vanguardia		0	0
AR	B   	2717      	Juan A. De La Peña		0	0
AR	B   	2718      	Urquiza		0	0
AR	B   	2720      	Colon		0	0
AR	B   	2721      	Sarasa		0	0
AR	B   	2740      	Villa Sanguinetti		0	0
AR	B   	2741      	Salto		0	0
AR	B   	2743      	Tacuari		0	0
AR	B   	2745      	Gahan		0	0
AR	B   	2747      	Ines Indart		0	0
AR	B   	2751      	La Violeta		0	0
AR	B   	2752      	La Luisa		0	0
AR	B   	2754      	Todd		0	0
AR	B   	2760      	San Antonio De Areco		0	0
AR	B   	2761      	Villa Lia		0	0
AR	B   	2764      	Solis		0	0
AR	B   	2800      	Zarate		0	0
AR	B   	2801      	Escalada		0	0
AR	B   	2802      	Otamendi		0	0
AR	B   	2804      	Campana		0	0
AR	B   	2805      	La Horqueta		0	0
AR	B   	2806      	Lima		0	0
AR	B   	2808      	Atucha		0	0
AR	B   	2812      	Capilla Del Señor		0	0
AR	B   	2813      	Arroyo De La Cruz		0	0
AR	B   	2814      	Los Cardales		0	0
AR	B   	2900      	San Nicolas		0	0
AR	B   	2901      	La Emilia		0	0
AR	B   	2903      	Lopez Arias		0	0
AR	B   	2905      	Gral. Rojo		0	0
AR	B   	2907      	Ing. Urcelay		0	0
AR	B   	2912      	Sanchez		0	0
AR	B   	2914      	Villa Ramallo		0	0
AR	B   	2915      	Ramallo		0	0
AR	B   	2916      	El Paraiso		0	0
AR	B   	2930      	San Pedrito		0	0
AR	B   	2931      	Isla Los Laureles		0	0
AR	B   	2933      	Colonia Velaz		0	0
AR	B   	2935      	El Descanso		0	0
AR	B   	2938      	Alsina		0	0
AR	B   	2942      	Baradero		0	0
AR	B   	2943      	Ireneo Portela		0	0
AR	B   	2944      	Rio Tala		0	0
AR	B   	2946      	Vuelta De Obligado		0	0
AR	B   	3314      	San Miguel		-1	-1
AR	B   	3432      	Macedo		0	0
AR	B   	6000      	Junin		0	0
AR	B   	6001      	Rafael Obligado		0	0
AR	B   	6003      	La Trinidad		0	0
AR	B   	6005      	Ham		0	0
AR	B   	6007      	Arribeños		0	0
AR	B   	6013      	Laplacette		0	0
AR	B   	6015      	Gral. Viamonte		0	0
AR	B   	6017      	Chancay		0	0
AR	B   	6018      	Zavalia		0	0
AR	B   	6022      	Las Parvas		0	0
AR	B   	6030      	Vedia		0	0
AR	B   	6031      	El Dorado		0	0
AR	B   	6032      	Leandro N. Alem		0	0
AR	B   	6034      	Juan Bautista Alberdi		0	0
AR	B   	6042      	Dos Hermanos		0	0
AR	B   	6050      	Gral. Pinto		0	0
AR	B   	6051      	Pichincha		0	0
AR	B   	6053      	Germania		0	0
AR	B   	6058      	Pazos Kanki		0	0
AR	B   	6062      	Cnel. Granada		0	0
AR	B   	6063      	Porvenir		0	0
AR	B   	6064      	Volta		0	0
AR	B   	6065      	Blaquier		0	0
AR	B   	6070      	Lincoln		0	0
AR	B   	6071      	Triunvirato		0	0
AR	B   	6073      	El Triunfo		0	0
AR	B   	6075      	Roberts		0	0
AR	B   	6077      	Encina		0	0
AR	B   	6078      	Bayuca		0	0
AR	B   	6105      	Santa Regina		0	0
AR	B   	6223      	Cnel. Charlone		0	0
AR	B   	6230      	Moores		0	0
AR	B   	6231      	Pradere		0	0
AR	B   	6233      	Hereford		0	0
AR	B   	6235      	Villa Sauze		0	0
AR	B   	6237      	America		0	0
AR	B   	6239      	Meridiano V		0	0
AR	B   	6241      	Piedritas		0	0
AR	B   	6242      	Elordi		0	0
AR	B   	6244      	Banderalo		0	0
AR	B   	6335      	Quenuma		0	0
AR	B   	6337      	Ing. Thompson		0	0
AR	B   	6338      	Leubuco		0	0
AR	B   	6339      	Salliquelo		0	0
AR	B   	6341      	Francisco Murature		0	0
AR	B   	6343      	Thames		0	0
AR	B   	6346      	Pellegrini		0	0
AR	B   	6348      	Bocayuva		0	0
AR	B   	6400      	Trenque Lauquen		0	0
AR	B   	6401      	Valentin Gomez		0	0
AR	B   	6403      	Villa Sena		0	0
AR	B   	6405      	Albariño		0	0
AR	B   	6407      	Tronge		0	0
AR	B   	6409      	Jose Maria Blanco		0	0
AR	B   	6411      	Garre		0	0
AR	B   	6417      	Casbas		0	0
AR	B   	6422      	Primera Junta		0	0
AR	B   	6424      	Berutti		0	0
AR	B   	6430      	Adolfo Alsina		0	0
AR	B   	6431      	Lago Epecuen		0	0
AR	B   	6435      	Guamini		0	0
AR	B   	6437      	Arroyo Venado		0	0
AR	B   	6438      	Masurel		0	0
AR	B   	6439      	Bonifacio		0	0
AR	B   	6441      	Rivera		0	0
AR	B   	6443      	Arano		0	0
AR	B   	6450      	Pehuajo		0	0
AR	B   	6451      	Magdala		0	0
AR	B   	6453      	Carlos Salas		0	0
AR	B   	6455      	Carlos Tejedor		0	0
AR	B   	6457      	Timote		0	0
AR	B   	6459      	Colonia Sere		0	0
AR	B   	6461      	Cap. Castro		0	0
AR	B   	6463      	Alagon		0	0
AR	B   	6465      	Henderson		0	0
AR	B   	6467      	Maria Lucila		0	0
AR	B   	6469      	Asturias		0	0
AR	B   	6471      	La Carreta		0	0
AR	B   	6472      	Francisco Madero		0	0
AR	B   	6474      	Juan Jose Paso		0	0
AR	B   	6475      	Francisco Magnano		0	0
AR	B   	6476      	Guanaco		0	0
AR	B   	6500      	Nueve De Julio		0	0
AR	B   	6501      	Doce De Octubre		0	0
AR	B   	6503      	Patricios		0	0
AR	B   	6505      	Dudignac		0	0
AR	B   	6507      	Morea		0	0
AR	B   	6509      	Del Valle		0	0
AR	B   	6511      	Villa Sanz		0	0
AR	B   	6513      	La Niña		0	0
AR	B   	6515      	El Tejar		0	0
AR	B   	6516      	Dennehy		0	0
AR	B   	6530      	Carlos Casares		0	0
AR	B   	6531      	Mauricio Hirsch		0	0
AR	B   	6533      	Ramon J. Neild		0	0
AR	B   	6535      	Bellocq		0	0
AR	B   	6537      	Ordoqui		0	0
AR	B   	6538      	La Dorita		0	0
AR	B   	6550      	Bolivar		0	0
AR	B   	6551      	Pirovano		0	0
AR	B   	6553      	Urdampilleta		0	0
AR	B   	6555      	Daireaux		0	0
AR	B   	6557      	Arboleda		0	0
AR	B   	6559      	Recalde		0	0
AR	B   	6561      	San Bernardo		0	0
AR	B   	6600      	Mercedes		0	0
AR	B   	6601      	Tuyuti		0	0
AR	B   	6603      	Juan Jose Almeyra		0	0
AR	B   	6605      	Gonzalez Risos		0	0
AR	B   	6607      	Anasagasti		0	0
AR	B   	6608      	Gowland		0	0
AR	B   	6612      	Suipacha		0	0
AR	B   	6614      	Goldney		0	0
AR	B   	6616      	Castilla		0	0
AR	B   	6620      	Chivilcoy - Agencia Aca		0	0
AR	B   	6621      	Henry Bell		0	0
AR	B   	6623      	Indacochea		0	0
AR	B   	6625      	Villa Moquehua		0	0
AR	B   	6627      	Achupallas		0	0
AR	B   	6628      	Palemon Huergo		0	0
AR	B   	6632      	Gorostiaga		0	0
AR	B   	6634      	Alberti		0	0
AR	B   	6640      	Bragado		0	0
AR	B   	6641      	Comodoro Py		0	0
AR	B   	6643      	Araujo		0	0
AR	B   	6645      	Maximo Fernandez		0	0
AR	B   	6646      	Warnes		0	0
AR	B   	6648      	Mechita		0	0
AR	B   	6652      	Olascoaga		0	0
AR	B   	6660      	25 De Mayo		0	0
AR	B   	6661      	San Enrique		0	0
AR	B   	6663      	Norberto De La Riestra		0	0
AR	B   	6665      	Ernestina		0	0
AR	B   	6667      	Agustin Mosconi		0	0
AR	B   	6700      	Lujan		0	0
AR	B   	6701      	Carlos Keen		0	0
AR	B   	6703      	Etchegoyen		0	0
AR	B   	6705      	Villa Ruiz		0	0
AR	B   	6706      	Jauregui		0	0
AR	B   	6708      	Open Door		0	0
AR	B   	6712      	Villa Espil		0	0
AR	B   	6720      	San Andres De Giles		0	0
AR	B   	6721      	Azcuenaga		0	0
AR	B   	6723      	Heavy		0	0
AR	B   	6725      	Carmen De Areco		0	0
AR	B   	6727      	Gouin		0	0
AR	B   	6734      	Rawson		0	0
AR	B   	6740      	Chacabuco		0	0
AR	B   	6743      	Coliqueo		0	0
AR	B   	6746      	Cucha-cucha		0	0
AR	B   	6748      	Membrillar		0	0
AR	B   	7000      	Tandil		0	0
AR	B   	7001      	La Pastora		0	0
AR	B   	7003      	Gardey		0	0
AR	B   	7005      	Claraz		0	0
AR	B   	7007      	San Manuel		0	0
AR	B   	7009      	Iraola		0	0
AR	B   	7011      	Juan N. Fernandez		0	0
AR	B   	7013      	De La Canal		0	0
AR	B   	7020      	Benito Juarez		0	0
AR	B   	7021      	Alzaga		0	0
AR	B   	7100      	Dolores		0	0
AR	B   	7101      	Canal 15		0	0
AR	B   	7103      	Gral. Lavalle		0	0
AR	B   	7105      	San Clemente Del Tuyu		0	0
AR	B   	7106      	Las Toninas		0	0
AR	B   	7107      	Santa Teresita		0	0
AR	B   	7108      	Mar Del Tuyu		0	0
AR	B   	7109      	Mar De Ajo		0	0
AR	B   	7111      	Playa San Bernardo		0	0
AR	B   	7112      	Costa Azul		0	0
AR	B   	7113      	La Lucila Del Mar		0	0
AR	B   	7114      	Castelli		0	0
AR	B   	7116      	Lezama		0	0
AR	B   	7118      	Gral. Guido		0	0
AR	B   	7119      	Monsalvo		0	0
AR	B   	7130      	Chascomus - Estaf. Nro 4		0	0
AR	B   	7135      	Don Cipriano		0	0
AR	B   	7136      	Gandara		0	0
AR	B   	7150      	Ayacucho		0	0
AR	B   	7151      	Solanet		0	0
AR	B   	7153      	Fair		0	0
AR	B   	7160      	Maipu		0	0
AR	B   	7161      	Labarden		0	0
AR	B   	7163      	Gral. Madariaga		0	0
AR	B   	7165      	Villa Gesell		0	0
AR	B   	7167      	Pinamar		0	0
AR	B   	7169      	Juancho		0	0
AR	B   	7172      	Gral. Piran		0	0
AR	B   	7174      	Cnel. Vidal		0	0
AR	B   	7200      	Las Flores		0	0
AR	B   	7201      	Miranda		0	0
AR	B   	7203      	Chapaleofu		0	0
AR	B   	7205      	Rosas		0	0
AR	B   	7207      	El Trigo		0	0
AR	B   	7208      	Cnel. Boerr		0	0
AR	B   	7212      	Doctor Domingo Harosteguy		0	0
AR	B   	7214      	Cachari		0	0
AR	B   	7220      	Monte		0	0
AR	B   	7221      	Gdor. Udaondo		0	0
AR	B   	7223      	Chas		0	0
AR	B   	7225      	Real Audiencia		0	0
AR	B   	7226      	Zenon Videla Dorna		0	0
AR	B   	7228      	Abbott		0	0
AR	B   	7240      	Lobos		0	0
AR	B   	7241      	Salvador Maria		0	0
AR	B   	7243      	La Blanqueada		0	0
AR	B   	7245      	Roque Perez		0	0
AR	B   	7247      	Carlos Beguerie		0	0
AR	B   	7249      	Empalme Lobos		0	0
AR	B   	7260      	Saladillo		0	0
AR	B   	7261      	San Benito		0	0
AR	B   	7263      	Gral. Alvear		0	0
AR	B   	7265      	Del Carril		0	0
AR	B   	7267      	Juan Blaquier		0	0
AR	B   	7300      	Azul		0	0
AR	B   	7301      	Arroyo De Los Huesos		0	0
AR	B   	7303      	Tapalque		0	0
AR	B   	7305      	Velloso		0	0
AR	B   	7307      	Crotto		0	0
AR	B   	7311      	Chillar		0	0
AR	B   	7313      	Dieciseis De Julio		0	0
AR	B   	7316      	Parish		0	0
AR	B   	7318      	Hinojo		0	0
AR	B   	7400      	Olavarria		0	0
AR	B   	7401      	Santa Luisa		0	0
AR	B   	7403      	Sierras Bayas		0	0
AR	B   	7404      	San Jorge		0	0
AR	B   	7406      	Gral. La Madrid		0	0
AR	B   	7407      	Libano		0	0
AR	B   	7408      	La Colina		0	0
AR	B   	7412      	Voluntad		0	0
AR	B   	7414      	Laprida		0	0
AR	B   	7500      	Tres Arroyos		0	0
AR	B   	7501      	Indio Rico		0	0
AR	B   	7503      	Cristiano Muerto		0	0
AR	B   	7505      	Balneario Claromeco		0	0
AR	B   	7507      	Micaela Cascallares		0	0
AR	B   	7509      	Oriente		0	0
AR	B   	7511      	Pueblo Balneario Reta		0	0
AR	B   	7513      	Adolfo Gonzalez Chaves		0	0
AR	B   	7515      	De La Garma		0	0
AR	B   	7517      	La Sortija		0	0
AR	B   	7519      	Vasquez		0	0
AR	B   	7521      	San Cayetano		0	0
AR	B   	7530      	Krabbe		0	0
AR	B   	7531      	El Divisorio		0	0
AR	B   	7533      	Quiñihual		0	0
AR	B   	7535      	Pontaut		0	0
AR	B   	7536      	Reserva		0	0
AR	B   	7540      	Cnel. Suarez		0	0
AR	B   	7541      	Col. Nro 3 Cnel. Suarez		0	0
AR	B   	7543      	La Primavera		0	0
AR	B   	7545      	Zentena		0	0
AR	B   	7547      	Cascada		0	0
AR	B   	7548      	Curumalan		0	0
AR	B   	7600      	Mar Del Plata		0	0
AR	B   	7601      	Barrio Batan		0	0
AR	B   	7603      	Cdte. Nicanor Otamendi		0	0
AR	B   	7605      	Chapadmalal		0	0
AR	B   	7607      	Mar Del Sud		0	0
AR	B   	7609      	Balneario Mar Chiquita		0	0
AR	B   	7612      	Vivorata		0	0
AR	B   	7613      	Ricardo Gaviña		0	0
AR	B   	7620      	Balcarce		0	0
AR	B   	7621      	Ramos Otero		0	0
AR	B   	7623      	Las Nutrias		0	0
AR	B   	7630      	Necochea		0	0
AR	B   	7631      	Quequen		0	0
AR	B   	7633      	Pieres		0	0
AR	B   	7635      	Loberia		0	0
AR	B   	7637      	Nicanor Olivera		0	0
AR	B   	7639      	Lumb		0	0
AR	B   	7641      	Energia		0	0
AR	B   	8000      	Bahia Blanca		0	0
AR	B   	8101      	Galvan		0	0
AR	B   	8103      	Ing. White		0	0
AR	B   	8105      	Gral. Cerri		0	0
AR	B   	8107      	Base Aerea Cdte. Espora		0	0
AR	B   	8109      	Punta Alta		0	0
AR	B   	8111      	Arroyo Pareja		0	0
AR	B   	8113      	Baterias		0	0
AR	B   	8115      	Bajo Hondo		0	0
AR	B   	8117      	Pelicura		0	0
AR	B   	8118      	Cabildo		0	0
AR	B   	8122      	Naposta		0	0
AR	B   	8124      	Berraondo		0	0
AR	B   	8126      	Villa Iris		0	0
AR	B   	8127      	Estela		0	0
AR	B   	8129      	Felipe Sola		0	0
AR	B   	8132      	Medanos		0	0
AR	B   	8134      	Mascota		0	0
AR	B   	8136      	Algarrobo		0	0
AR	B   	8142      	Hilario Ascasubi		0	0
AR	B   	8144      	Tte. Origone		0	0
AR	B   	8146      	Mayor Buratovich		0	0
AR	B   	8148      	Pedro Luro		0	0
AR	B   	8150      	Cnel. Dorrego		0	0
AR	B   	8151      	Zubiaurre		0	0
AR	B   	8153      	Balneario Monte Hermoso		0	0
AR	B   	8154      	Calvo		0	0
AR	B   	8156      	Jose A. Guisasola		0	0
AR	B   	8158      	Aparicio		0	0
AR	B   	8160      	Tornquist		0	0
AR	B   	8162      	Garcia Del Rio		0	0
AR	B   	8164      	Dufaur		0	0
AR	B   	8166      	Saldungaray		0	0
AR	B   	8168      	Sierra De La Ventana		0	0
AR	B   	8170      	Pigue		0	0
AR	B   	8171      	Espartillar		0	0
AR	B   	8172      	Arroyo Corto		0	0
AR	B   	8174      	Saavedra		0	0
AR	B   	8175      	Goyena		0	0
AR	B   	8180      	Puan		0	0
AR	B   	8181      	Altavista		0	0
AR	B   	8183      	Darragueira		0	0
AR	B   	8185      	Colonia Lapin		0	0
AR	B   	8187      	Bordenave		0	0
AR	B   	8504      	Faro Segunda Barranca		0	0
AR	B   	8506      	Bahia San Blas		0	0
AR	B   	8508      	Emilio Lamarca		0	0
AR	B   	8512      	Igarzabal		0	0
AR	B   	9999      	San Miguel		-1	-1
AR	C   	1000      	Correo Central		0	0
AR	C   	1020      	Capital Federal		-1	-1
AR	C   	1030      	Capital Federal		-1	-1
AR	C   	1033      	Capital Federal		-1	-1
AR	C   	1034      	Capital Federal	\N	-1	-1
AR	C   	1039      	Capital Federal		-1	-1
AR	C   	1041      	Capital Federal		0	0
AR	C   	1042      	Capital Federal Congreso		-1	-1
AR	C   	1053      	Capital Federal		0	0
AR	C   	1057      	Capital Federal		-1	-1
AR	C   	1060      	Capital Federal		-1	-1
AR	C   	1061      	Capital Federal		-1	-1
AR	C   	1063      	Av.Juan de Garay 125	01	-1	-1
AR	C   	1069      	Capital federal	01	0	0
AR	C   	1073      	Capital Federal		-1	-1
AR	C   	1078      	Capital Federal	01	-1	-1
AR	C   	1084      	Capital Federal		-1	-1
AR	C   	1093      	Capital Federal		-1	-1
AR	C   	1095      	Capital Federal	011	0	0
AR	C   	1104      	Capital Federal		0	0
AR	C   	1107      	Capital Federal		-1	-1
AR	C   	1115      	Capital Federal	01	0	0
AR	C   	1120      	Capital Federal		0	0
AR	C   	1127      	Capital Federal		-1	-1
AR	C   	1147      	Capital Federal		-1	-1
AR	C   	1175      	Capital Federal		-1	-1
AR	C   	1179      	Capital Federal	\N	-1	-1
AR	C   	1182      	Capital Federal		-1	-1
AR	C   	1186      	Capital Federal		-1	-1
AR	C   	1198      	Capital Federal		-1	-1
AR	C   	1200      	Capital Federal		-1	-1
AR	C   	1401      	Domingo F. Sarmiento		0	0
AR	C   	1402      	Capital Federal		0	0
AR	C   	1403      	Capital Federal		0	0
AR	C   	1404      	Capital Federal		0	0
AR	C   	1405      	Capital Federal		0	0
AR	C   	1406      	Capital Federal		0	0
AR	C   	1407      	Capital Federal		0	0
AR	C   	1408      	Capital Federal		0	0
AR	C   	1409      	Capital Federal		0	0
AR	C   	1410      	Capital Federal		0	0
AR	C   	1411      	Capital Federal		0	0
AR	C   	1412      	Capital Federal		0	0
AR	C   	1413      	Capital Federal		0	0
AR	C   	1414      	Capital Federal		0	0
AR	C   	1415      	Capital Federal		0	0
AR	C   	1416      	Esteban Echeverria		0	0
AR	C   	1417      	Capital Federal		0	0
AR	C   	1418      	Capital Federal		0	0
AR	C   	1419      	Capital Federal		0	0
AR	C   	1420      	Capital Federal		0	0
AR	C   	1421      	Capital Federal		0	0
AR	C   	1422      	Capital Federal		0	0
AR	C   	1423      	Capital Federal		0	0
AR	C   	1424      	Capital Federal		0	0
AR	C   	1425      	Capital Federal		0	0
AR	C   	1426      	Roberto Arlt		0	0
AR	C   	1427      	Capital Federal		0	0
AR	C   	1428      	Capital Federal		0	0
AR	C   	1429      	Capital Federal		0	0
AR	C   	1430      	Capital Federal		0	0
AR	C   	1431      	Alberto Gerchunoff		0	0
AR	C   	1432      	Capital Federal		0	0
AR	C   	1433      	Fray Mocho		0	0
AR	C   	1434      	Capital Federal		0	0
AR	C   	1435      	Capital Federal		0	0
AR	C   	1436      	Capital Federal		0	0
AR	C   	1437      	Capital Federal		0	0
AR	C   	1438      	Capital Federal		0	0
AR	C   	1439      	Capital Federal		0	0
AR	C   	1440      	Capital Federal		0	0
AR	C   	1441      	Capital Federal		0	0
AR	C   	1442      	Capital Federal		0	0
AR	C   	1443      	Capital Federal		0	0
AR	C   	1444      	Capital Federal		0	0
AR	C   	1445      	Capital Federal		0	0
AR	C   	1446      	Capital Federal		0	0
AR	C   	1447      	Capital Federal		0	0
AR	C   	1448      	Capital Federal		0	0
AR	C   	1449      	Capital Federal		0	0
AR	C   	1450      	Capital Federal		0	0
AR	C   	1451      	Capital Federal		0	0
AR	C   	1452      	Capital Federal		0	0
AR	C   	1453      	Capital Federal		0	0
AR	C   	1454      	Capital Federal		0	0
AR	C   	1455      	Capital Federal		0	0
AR	C   	1456      	Capital Federal		0	0
AR	C   	1457      	Capital Federal		0	0
AR	C   	1458      	Eduardo Gutierrez		0	0
AR	C   	1459      	Capital Federal		0	0
AR	C   	1460      	Capital Federal		0	0
AR	C   	1461      	Capital Federal		0	0
AR	C   	1462      	Capital Federal		0	0
AR	C   	1463      	Capital Federal		0	0
AR	C   	1464      	Capital Federal		0	0
AR	C   	1465      	Capital Federal		0	0
AR	D   	5421      	La Tranca		0	0
AR	D   	5598      	Desaguadero		0	0
AR	D   	5700      	San Luis		0	0
AR	D   	5701      	Balde De La Isla		0	0
AR	D   	5703      	Arbol Solo		0	0
AR	D   	5705      	Balde De Quines		0	0
AR	D   	5707      	Balde De Puertas		0	0
AR	D   	5709      	Lujan		0	0
AR	D   	5711      	Los Molles		0	0
AR	D   	5713      	Puesto Roberto		0	0
AR	D   	5715      	Balde De Escudero		0	0
AR	D   	5719      	Balzora		0	0
AR	D   	5721      	Alto Pelado		0	0
AR	D   	5722      	Eleodoro Lobos		0	0
AR	D   	5724      	Alto Pencoso		0	0
AR	D   	5730      	Cnel. Alzogaray		0	0
AR	D   	5731      	El Morro		0	0
AR	D   	5733      	Cramer		0	0
AR	D   	5735      	Juan Llerena		0	0
AR	D   	5736      	Cdte. Granville		0	0
AR	D   	5743      	Nueva Escocia		0	0
AR	D   	5750      	La Toma		0	0
AR	D   	5751      	La Totora		0	0
AR	D   	5753      	Casa De Piedra		0	0
AR	D   	5755      	San Martin		0	0
AR	D   	5759      	La Esquina		0	0
AR	D   	5770      	Concaran		0	0
AR	D   	5771      	Guanaco Pampa		0	0
AR	D   	5773      	Guzman		0	0
AR	D   	5775      	Renca		0	0
AR	D   	5777      	Ojo Del Rio		0	0
AR	D   	5779      	La Chilca		0	0
AR	D   	5835      	El Tala		0	0
AR	D   	5881      	Merlo		0	0
AR	D   	5883      	Alto Lindo		0	0
AR	D   	6216      	Bagual		0	0
AR	D   	6277      	Buena Esperanza		0	0
AR	D   	6389      	Anchorena		0	0
AR	E   	2100      	Charigue		0	0
AR	E   	2820      	Gualeguaychu		0	0
AR	E   	2821      	Arroyo Del Cura		0	0
AR	E   	2823      	Ceibas		0	0
AR	E   	2824      	Colonia Gdor. Basavilbaso		0	0
AR	E   	2826      	Aldea San Antonio		0	0
AR	E   	2828      	Escriña		0	0
AR	E   	2840      	Gualeguay		0	0
AR	E   	2841      	Aldea Asuncion		0	0
AR	E   	2843      	Gral. Galarza		0	0
AR	E   	2845      	Gdor. Echague		0	0
AR	E   	2846      	Holt		0	0
AR	E   	2848      	Medanos		0	0
AR	E   	2852      	Alarcon		0	0
AR	E   	2854      	Las Mercedes		0	0
AR	E   	3100      	Bajada Grande		0	0
AR	E   	3101      	Aldea Brasilera		0	0
AR	E   	3102      	Paraná	\N	-1	-1
AR	E   	3103      	Puiggari		0	0
AR	E   	3105      	Diamante		0	0
AR	E   	3107      	Distrito Espinillo		0	0
AR	E   	3109      	Crucesitas 7ma. Seccion		0	0
AR	E   	3111      	Las Tunas		0	0
AR	E   	3113      	Colonia Celina		0	0
AR	E   	3114      	Aldea Maria Luisa		0	0
AR	E   	3116      	Aldea Eigenfeld		0	0
AR	E   	3117      	El Taller		0	0
AR	E   	3118      	Colonia Nueva		0	0
AR	E   	3122      	Cerrito		0	0
AR	E   	3123      	Aldea Santa Maria		0	0
AR	E   	3125      	Antonio Tomas		0	0
AR	E   	3127      	Hernandarias		0	0
AR	E   	3129      	Colonia Hernandarias		0	0
AR	E   	3132      	El Pingo		0	0
AR	E   	3133      	Arroyo Maria		0	0
AR	E   	3134      	Antonio Tomas Sud		0	0
AR	E   	3136      	Alcaraz Norte		0	0
AR	E   	3137      	Alcaraz Sud		0	0
AR	E   	3138      	Alcaraz		0	0
AR	E   	3142      	Colonia Avigdor		0	0
AR	E   	3144      	Arroyo Del Medio		0	0
AR	E   	3150      	Laurencena		0	0
AR	E   	3151      	Antelo		0	0
AR	E   	3153      	Victoria		0	0
AR	E   	3155      	Laguna Del Pescado		0	0
AR	E   	3156      	Betbeder		0	0
AR	E   	3158      	Colonia La Llave		0	0
AR	E   	3162      	Chilcas Sud		0	0
AR	E   	3164      	Camps		0	0
AR	E   	3170      	Basavilbaso		0	0
AR	E   	3172      	Rocamora		0	0
AR	E   	3174      	Altamirano Sud		0	0
AR	E   	3176      	Sola		0	0
AR	E   	3177      	Guardamonte		0	0
AR	E   	3180      	Federal		0	0
AR	E   	3181      	Colonia Federal		0	0
AR	E   	3183      	La Calandria		0	0
AR	E   	3187      	La Esmeralda		0	0
AR	E   	3188      	Conscripto Bernardi		0	0
AR	E   	3190      	Arroyo Hondo		0	0
AR	E   	3191      	Colonia Oficial Nro 14		0	0
AR	E   	3192      	El Quebracho		0	0
AR	E   	3200      	Concordia		0	0
AR	E   	3201      	Camba Paso		0	0
AR	E   	3203      	Arroyo Grande		0	0
AR	E   	3204      	Colonia La Gloria		0	0
AR	E   	3206      	Colonia La Argentina		0	0
AR	E   	3208      	Santa Ana		0	0
AR	E   	3212      	El Redomon		0	0
AR	E   	3214      	Estacion Yerua		0	0
AR	E   	3216      	Gral. Campos		0	0
AR	E   	3218      	Colonia Nueva Alemania		0	0
AR	E   	3228      	Colonia Ensanche Sauce		0	0
AR	E   	3229      	Colonia Freitas		0	0
AR	E   	3240      	Villaguay		0	0
AR	E   	3241      	Laguna Larga		0	0
AR	E   	3244      	Libaros		0	0
AR	E   	3246      	Dominguez		0	0
AR	E   	3248      	Estacion Urquiza		0	0
AR	E   	3252      	Clara		0	0
AR	E   	3254      	Colonia La Pampa		0	0
AR	E   	3260      	Concepcion Del Uruguay		0	0
AR	E   	3261      	Colonia Elia		0	0
AR	E   	3262      	Palacio San Jose		0	0
AR	E   	3263      	Primero De Mayo		0	0
AR	E   	3265      	Colonia Hoker		0	0
AR	E   	3267      	Cañada De Las Ovejas		0	0
AR	E   	3269      	Colonia Bailina		0	0
AR	E   	3272      	Herrera		0	0
AR	E   	3280      	Colon		0	0
AR	E   	3281      	Colonia Hughes		0	0
AR	E   	3283      	Colonia Mabragaña		0	0
AR	E   	3285      	Berduc		0	0
AR	E   	3287      	Arroyo Concepcion		0	0
AR	F   	5263      	El Medano		0	0
AR	F   	5274      	Milagro		0	0
AR	F   	5275      	Olpas		0	0
AR	F   	5276      	Castro Barros		0	0
AR	F   	5300      	La Rioja	0822	0	0
AR	F   	5301      	Agua Blanca		0	0
AR	F   	5303      	Anjullon		0	0
AR	F   	5304      	Talamuyuna		0	0
AR	F   	5310      	Aimogasta		0	0
AR	F   	5311      	Arauco		0	0
AR	F   	5313      	Estacion Mazan		0	0
AR	F   	5321      	Schaqui		0	0
AR	F   	5325      	Alpasinche		0	0
AR	F   	5327      	Chaupihuasi		0	0
AR	F   	5329      	Los Robles		0	0
AR	F   	5350      	Villa Union		0	0
AR	F   	5351      	Banda Florida		0	0
AR	F   	5353      	El Zapallar		0	0
AR	F   	5355      	El Condado		0	0
AR	F   	5357      	Vinchina		0	0
AR	F   	5359      	Bajo Jague		0	0
AR	F   	5360      	Chilecito		0	0
AR	F   	5361      	Aicuña		0	0
AR	F   	5363      	Anguinan		0	0
AR	F   	5365      	Famatina		0	0
AR	F   	5367      	Miranda		0	0
AR	F   	5369      	Pagancillo		0	0
AR	F   	5372      	Monogasta		0	0
AR	F   	5374      	Vichigasta		0	0
AR	F   	5380      	Chamical		0	0
AR	F   	5381      	Bella Vista		0	0
AR	F   	5383      	Olta		0	0
AR	F   	5384      	Punta De Los Llanos		0	0
AR	F   	5385      	Alcazar		0	0
AR	F   	5386      	Amana		0	0
AR	F   	5470      	Chepes		0	0
AR	F   	5471      	Corral De Isaac		0	0
AR	F   	5473      	Aguayo		0	0
AR	F   	5474      	Desiderio Tello		0	0
AR	F   	5475      	Ambil		0	0
AR	F   	5717      	El Calden		0	0
AR	G   	2132      	Funes		0	0
AR	G   	2185      	San Jose De La Esquina		0	0
AR	G   	2354      	Argentina		0	0
AR	G   	2356      	Pinto		0	0
AR	G   	2357      	Colonia Santa Rosa Aguirre		0	0
AR	G   	2374      	Palo Negro		0	0
AR	G   	3062      	Desvio Pozo Dulce		0	0
AR	G   	3064      	Bandera		0	0
AR	G   	3141      	Agustina Libarona		0	0
AR	G   	3712      	Cnel. Manuel Leoncio Rico		0	0
AR	G   	3714      	Atahualpa		0	0
AR	G   	3731      	Sachayoj		0	0
AR	G   	3736      	Campo Del Cielo		0	0
AR	G   	3740      	Quimili		0	0
AR	G   	3741      	Aerolito		0	0
AR	G   	3743      	Tintina		0	0
AR	G   	3745      	El Hoyo		0	0
AR	G   	3747      	Campo Gallo		0	0
AR	G   	3749      	Campo Alegre		0	0
AR	G   	3752      	Nasalo		0	0
AR	G   	3760      	Añatuya		0	0
AR	G   	3761      	El Malacara		0	0
AR	G   	3763      	Los Juries		0	0
AR	G   	3766      	Averias		0	0
AR	G   	4184      	El Rincon		0	0
AR	G   	4186      	El Palomar		0	0
AR	G   	4187      	Bobadal		0	0
AR	G   	4189      	Campo Grande		0	0
AR	G   	4195      	El Remate		0	0
AR	G   	4197      	Ahi Veremos		0	0
AR	G   	4200      	Santiago Del Estero		0	0
AR	G   	4201      	Aragones		0	0
AR	G   	4203      	Guampacha		0	0
AR	G   	4205      	Beltran - Loreto		0	0
AR	G   	4206      	Arraga		0	0
AR	G   	4208      	Loreto		0	0
AR	G   	4212      	Guanaco Sombriana		0	0
AR	G   	4220      	Chañar Pozo De Abajo		0	0
AR	G   	4221      	La Donosa		0	0
AR	G   	4223      	Vinara		0	0
AR	G   	4225      	Amicha		0	0
AR	G   	4230      	Frias		0	0
AR	G   	4233      	Ancajan		0	0
AR	G   	4234      	Lavalle		0	0
AR	G   	4237      	Las Peñas		0	0
AR	G   	4238      	Guasayan		0	0
AR	G   	4300      	La Banda		0	0
AR	G   	4301      	Aguas Coloradas		0	0
AR	G   	4302      	Ardiles		0	0
AR	G   	4304      	Chañar Pozo		0	0
AR	G   	4306      	Cashico		0	0
AR	G   	4308      	Beltran		0	0
AR	G   	4312      	Forres		0	0
AR	G   	4313      	Atojpozo		0	0
AR	G   	4315      	Atamisqui		0	0
AR	G   	4317      	Soconcho		0	0
AR	G   	4319      	Barrancas		0	0
AR	G   	4321      	Anga		0	0
AR	G   	4322      	Fernandez		0	0
AR	G   	4324      	Garza		0	0
AR	G   	4326      	Caloj		0	0
AR	G   	4328      	Guañagasta		0	0
AR	G   	4332      	Blanca Pozo		0	0
AR	G   	4334      	Icaño		0	0
AR	G   	4336      	Abra Grande		0	0
AR	G   	4338      	Clodomira		0	0
AR	G   	4339      	Simbolar		0	0
AR	G   	4350      	Suncho Corral		0	0
AR	G   	4351      	El Pertigo		0	0
AR	G   	4353      	Amama		0	0
AR	G   	4354      	Colonia El Simbolar Robles		0	0
AR	G   	4356      	Colonia Siegel		0	0
AR	G   	4823      	Villa Guasayan		0	0
AR	G   	5250      	Los Pozos		0	0
AR	G   	5251      	Amiman		0	0
AR	G   	5253      	Arbol Solo		0	0
AR	G   	5255      	Baez		0	0
AR	G   	5257      	Oratorio		0	0
AR	G   	5258      	Km. 49		0	0
AR	H   	3500      	Resistencia		0	0
AR	H   	3503      	Barranqueras		0	0
AR	H   	3505      	Colonia Baranda		0	0
AR	H   	3507      	La Eduviges		0	0
AR	H   	3509      	Campo El Bermejo		0	0
AR	H   	3511      	Presidencia Roca		0	0
AR	H   	3513      	Cote-lai		0	0
AR	H   	3514      	Fontana		0	0
AR	H   	3515      	Cap. Solari		0	0
AR	H   	3518      	Las Palmas		0	0
AR	H   	3522      	Gral. Vedia		0	0
AR	H   	3524      	Puerto Bermejo		0	0
AR	H   	3530      	Quitilipi		0	0
AR	H   	3531      	Colonia Aborigen Chaco		0	0
AR	H   	3532      	Fortin Aguilar		0	0
AR	H   	3534      	Km. 22		0	0
AR	H   	3540      	Villa Angela		0	0
AR	H   	3543      	Enrique Urien		0	0
AR	H   	3545      	Villa Berthet		0	0
AR	H   	3700      	Presidencia Roque Saenz Peña		0	0
AR	H   	3701      	Colonia Jose Marmol		0	0
AR	H   	3703      	Fortin Lavalle		0	0
AR	H   	3705      	Colonia Juan Jose Castelli		0	0
AR	H   	3706      	Avia Terai		0	0
AR	H   	3708      	Concepcion Del Bermejo		0	0
AR	H   	3716      	Campo Largo		0	0
AR	H   	3718      	Corzuela		0	0
AR	H   	3722      	Las Breñas		0	0
AR	H   	3730      	Charata		0	0
AR	H   	3732      	Gral. Pinedo		0	0
AR	H   	3733      	Hermoso Campo		0	0
AR	H   	3734      	Gancedo		0	0
AR	H   	9007      	Garayalde		0	0
AR	J   	5400      	San Juan		0	0
AR	J   	5400RIV   	Rivadavia		-1	-1
AR	J   	5401      	La Isla		0	0
AR	J   	5403      	Calingasta		0	0
AR	J   	5405      	Barreal		0	0
AR	J   	5407      	Bebida		0	0
AR	J   	5409      	Mogna		0	0
AR	J   	5411      	La Legua		0	0
AR	J   	5413      	Chimbas		0	0
AR	J   	5415      	Domingo De Oro		0	0
AR	J   	5417      	Angaco Sud		0	0
AR	J   	5419      	Albardon		0	0
AR	J   	5423      	Cap. Lazo		0	0
AR	J   	5425      	Villa Gral. Acha		0	0
AR	J   	5427      	Quinto Cuartel		0	0
AR	J   	5429      	Pocito		0	0
AR	J   	5431      	Cañana Honda		0	0
AR	J   	5435      	Campo De Batalla		0	0
AR	J   	5436      	Colonia Zapata		0	0
AR	J   	5438      	Alto De Sierra		0	0
AR	J   	5439      	Dos Acequias		0	0
AR	J   	5442      	Caucete		0	0
AR	J   	5443      	Cuyo		0	0
AR	J   	5444      	Bermejo		0	0
AR	J   	5446      	Marayes		0	0
AR	J   	5447      	Astica		0	0
AR	J   	5449      	Balde Del Rosario		0	0
AR	J   	5460      	Jachal		0	0
AR	J   	5461      	Boca De La Quebrada		0	0
AR	J   	5463      	Huaco		0	0
AR	J   	5465      	Rodeo		0	0
AR	J   	5467      	Angualasto		0	0
AR	J   	5577      	Rivadavia		-1	-1
AR	K   	4139      	Agua Amarilla		0	0
AR	K   	4231      	Albigasta		0	0
AR	K   	4235      	Bella Vista		0	0
AR	K   	4700      	San Fernando del Valle de Catamarca		0	0
AR	K   	4701      	Amana		0	0
AR	K   	4705      	Antofagasta De La Sierra		0	0
AR	K   	4707      	San Antonio De Fray M. Esquiu		0	0
AR	K   	4709      	Piedra Blanca		0	0
AR	K   	4711      	Casas Viejas		0	0
AR	K   	4713      	Las Pirquitas		0	0
AR	K   	4715      	El Rodeo		0	0
AR	K   	4716      	Amadores		0	0
AR	K   	4718      	La Merced		0	0
AR	K   	4719      	Balcosna		0	0
AR	K   	4722      	La Viña		0	0
AR	K   	4723      	Alijilan		0	0
AR	K   	4724      	Los Angeles		0	0
AR	K   	4726      	Capayan		0	0
AR	K   	4728      	Chumbicha		0	0
AR	K   	4740      	Andalgala		0	0
AR	K   	4741      	Agua De Las Palomas		0	0
AR	K   	4743      	Alto De La Junta		0	0
AR	K   	4750      	Belen		0	0
AR	K   	4751      	Condor Huasi De Belen		0	0
AR	K   	4753      	Londres		0	0
AR	K   	5260      	Km. 969		0	0
AR	K   	5261      	Divisadero		0	0
AR	K   	5264      	Km. 1008		0	0
AR	K   	5265      	Baviano		0	0
AR	K   	5266      	Quiros		0	0
AR	K   	5315      	El Pajonal		0	0
AR	K   	5317      	Mutquin		0	0
AR	K   	5319      	Colpes		0	0
AR	K   	5331      	Cerro Negro		0	0
AR	K   	5333      	Copacabana		0	0
AR	K   	5340      	Tinogasta		0	0
AR	K   	5341      	Antinaco		0	0
AR	K   	5343      	Santa Rosa		0	0
AR	K   	5345      	Fiambala		0	0
AR	K   	5625      	Icaño		0	0
AR	L   	6200      	Realico		0	0
AR	L   	6203      	Embajador Martini		0	0
AR	L   	6205      	Ing. Luiggi		0	0
AR	L   	6207      	Alta Italia		0	0
AR	L   	6212      	Adolfo Van Praet		0	0
AR	L   	6213      	Parera		0	0
AR	L   	6214      	Chamaico		0	0
AR	L   	6220      	Bernardo Larroude		0	0
AR	L   	6221      	Ceballos		0	0
AR	L   	6228      	Cnel. Hilario Lagos		0	0
AR	L   	6300      	Santa Rosa		0	0
AR	L   	6301      	Ataliva Roca		0	0
AR	L   	6303      	Cachirulo		0	0
AR	L   	6305      	Atreuco		0	0
AR	L   	6307      	Macachin		0	0
AR	L   	6309      	Alpachiri		0	0
AR	L   	6311      	Colonia Santa Teresa		0	0
AR	L   	6313      	Winifreda		0	0
AR	L   	6315      	Colonia Baron		0	0
AR	L   	6317      	Luan Toro		0	0
AR	L   	6319      	Carro Quemado		0	0
AR	L   	6321      	Telen		0	0
AR	L   	6323      	Algarrobo Del Aguila		0	0
AR	L   	6325      	Naico		0	0
AR	L   	6326      	Anguil		0	0
AR	L   	6330      	Catrilo		0	0
AR	L   	6331      	Miguel Cane		0	0
AR	L   	6333      	Alfredo Peña		0	0
AR	L   	6352      	Lonquimay		0	0
AR	L   	6354      	Uriburu		0	0
AR	L   	6360      	Gral. Pico		0	0
AR	L   	6361      	Agustoni		0	0
AR	L   	6365      	Dorila		0	0
AR	L   	6367      	Metileo		0	0
AR	L   	6369      	Trenel		0	0
AR	L   	6380      	Boeuf		0	0
AR	L   	6381      	Conhelo		0	0
AR	L   	6383      	Monte Nievas		0	0
AR	L   	6385      	Arata		0	0
AR	L   	6387      	Caleufu		0	0
AR	L   	8138      	Anzoategui		0	0
AR	L   	8200      	Gral. Acha		0	0
AR	L   	8201      	Colonia 25 De Mayo (aca)		0	0
AR	L   	8203      	Utracan		0	0
AR	L   	8204      	Bernasconi		0	0
AR	L   	8206      	Gral. San Martin		0	0
AR	L   	8208      	Jacinto Arauz		0	0
AR	L   	8212      	Abramo		0	0
AR	L   	8214      	Colonia Santa Maria		0	0
AR	L   	8307      	Puelen		0	0
AR	M   	5500      	Mendoza		0	0
AR	M   	5501      	Godoy Cruz		0	0
AR	M   	5503      	Paso De Los Andes		0	0
AR	M   	5505      	Carrodilla		0	0
AR	M   	5507      	Lujan De Cuyo		0	0
AR	M   	5509      	Agrelo		0	0
AR	M   	5511      	Gral. Gutierrez		0	0
AR	M   	5513      	Barrio Jardin Luzuriaga		0	0
AR	M   	5515      	Maipu		0	0
AR	M   	5517      	Barrancas		0	0
AR	M   	5519      	Cnel. Dorrego		0	0
AR	M   	5521      	Villa Nueva De Guaymallen		0	0
AR	M   	5523      	Buena Nueva		0	0
AR	M   	5525      	Colonia Segovia		0	0
AR	M   	5527      	Colonia Santa Teresa		0	0
AR	M   	5529      	Pedregal		0	0
AR	M   	5531      	Blanco Encalada		0	0
AR	M   	5532      	Los Eucaliptos		0	0
AR	M   	5533      	Bermejo		0	0
AR	M   	5535      	Costa De Araujo		0	0
AR	M   	5537      	Arroyito		0	0
AR	M   	5539      	Espejo		0	0
AR	M   	5541      	El Algarrobal		0	0
AR	M   	5543      	Capdeville		0	0
AR	M   	5544      	Gdor. Benegas		0	0
AR	M   	5545      	Termas Villavicencio		0	0
AR	M   	5547      	Villa Hipodromo		0	0
AR	M   	5549      	Cacheuta		0	0
AR	M   	5551      	Estacion Uspallata		0	0
AR	M   	5553      	Punta De Vacas		0	0
AR	M   	5555      	Puente Del Inca		0	0
AR	M   	5557      	Las Cuevas		0	0
AR	M   	5560      	Tunuyan		0	0
AR	M   	5561      	Cordon Del Plata		0	0
AR	M   	5563      	Los Arboles De Villegas		0	0
AR	M   	5565      	Campo Los Andes		0	0
AR	M   	5567      	La Consulta		0	0
AR	M   	5569      	Chilecito		0	0
AR	M   	5570      	Buen Orden		0	0
AR	M   	5571      	Chivilcoy		0	0
AR	M   	5573      	Villa De Junin		0	0
AR	M   	5575      	Andrade		0	0
AR	M   	5579      	Campamentos		0	0
AR	M   	5582      	Alto Verde		0	0
AR	M   	5584      	Palmira		0	0
AR	M   	5585      	Los Barriales		0	0
AR	M   	5587      	Barcala		0	0
AR	M   	5589      	Chapanay		0	0
AR	M   	5590      	Cadetes De Chile		0	0
AR	M   	5592      	La Dormida		0	0
AR	M   	5594      	Cdte. Salas		0	0
AR	M   	5595      	Ñacuñan		0	0
AR	M   	5596      	La Costa		0	0
AR	M   	5600      	San Rafael		0	0
AR	M   	5601      	Cap. Montoya		0	0
AR	M   	5603      	Colonia Elena		0	0
AR	M   	5605      	Balloffet		0	0
AR	M   	5607      	Cuadro Nacional		0	0
AR	M   	5609      	Aristides Villanueva		0	0
AR	M   	5611      	Bardas Blancas		0	0
AR	M   	5613      	Malargue		0	0
AR	M   	5615      	Colonia Pascual Lacarini		0	0
AR	M   	5620      	El Juncalito		0	0
AR	M   	5621      	Agua Escondida		0	0
AR	M   	5622      	Villa Atuel		0	0
AR	M   	5623      	Colonia Lopez		0	0
AR	M   	5624      	Palermo Chico		0	0
AR	M   	5632      	Colonia Alvear Oeste		0	0
AR	M   	5634      	Bowen		0	0
AR	M   	5636      	Canalejas		0	0
AR	M   	5637      	Corral De Lorca		0	0
AR	M   	M5620DFC  	General Alvear	02625	-1	-1
AR	N   	3300      	Posadas		0	0
AR	N   	3304      	Fachinal		0	0
AR	N   	3306      	Parada Leis		0	0
AR	N   	3308      	Candelaria		0	0
AR	N   	3309      	Bella Vista		0	0
AR	N   	3311      	Picada Galitziana		0	0
AR	N   	3313      	Arroyo Del Medio		0	0
AR	N   	3315      	Dos Arroyos		0	0
AR	N   	3316      	Loreto		0	0
AR	N   	3317      	Bonpland		0	0
AR	N   	3318      	Colonia Martires		0	0
AR	N   	3322      	Colonia Domingo Savio		0	0
AR	N   	3324      	Gdor. Roca		0	0
AR	N   	3326      	Colonia Polana		0	0
AR	N   	3327      	Corpus		0	0
AR	N   	3328      	Hipolito Yrigoyen		0	0
AR	N   	3332      	Cainguas		0	0
AR	N   	3334      	Puerto Rico		0	0
AR	N   	3350      	Apostoles		0	0
AR	N   	3353      	Arroyo Santa Maria		0	0
AR	N   	3355      	Concepcion De La Sierra		0	0
AR	N   	3357      	San Javier		0	0
AR	N   	3358      	Estacion Apostoles		0	0
AR	N   	3360      	Obera		0	0
AR	N   	3361      	Campo Ramon		0	0
AR	N   	3362      	Campo Grande		0	0
AR	N   	3363      	Alba Posse		0	0
AR	N   	3364      	Aristobulo Del Valle		0	0
AR	N   	3366      	Bernardo De Irigoyen		0	0
AR	N   	3370      	Iguazu		0	0
AR	N   	3371      	Cabure I		0	0
AR	N   	3374      	Libertad		0	0
AR	N   	3376      	Colonia Wanda		0	0
AR	N   	3378      	Puerto Esperanza		0	0
AR	N   	3380      	Eldorado		0	0
AR	N   	3381      	Colonia Maria Magdalena		0	0
AR	N   	3382      	Colonia Victoria		0	0
AR	N   	3384      	Montecarlo		0	0
AR	N   	3386      	Colonia Caraguatay		0	0
AR	P   	3526      	Cabo Adriano Ayala		0	0
AR	P   	3600      	Formosa		0	0
AR	P   	3601      	Banco Payagua		0	0
AR	P   	3603      	El Colorado		0	0
AR	P   	3604      	Gran Guardia		0	0
AR	P   	3606      	Loma Senes		0	0
AR	P   	3608      	Desvio Los Matacos		0	0
AR	P   	3610      	Clorinda		0	0
AR	P   	3611      	Florentino Ameghino		0	0
AR	P   	3613      	Laguna Blanca		0	0
AR	P   	3615      	Buena Vista		0	0
AR	P   	3620      	Cdte. Fontana		0	0
AR	P   	3621      	Fortin Lugones		0	0
AR	P   	3622      	Bartolome De Las Casas		0	0
AR	P   	3624      	Ibarreta		0	0
AR	P   	3626      	Colonia Union Escuela		0	0
AR	P   	3628      	Paso De Naite		0	0
AR	P   	3630      	Las Lomitas		0	0
AR	P   	3632      	Chiriguanos		0	0
AR	P   	3634      	Laguna Yema		0	0
AR	P   	3636      	Gral. Enrique Mosconi		0	0
AR	Q   	8300      	Neuquen		0	0
AR	Q   	8301      	Colonia Valentina		0	0
AR	Q   	8305      	Añelo		0	0
AR	Q   	8309      	Centenario		0	0
AR	Q   	8311      	Villa El Chocon		0	0
AR	Q   	8313      	Picun Leufu		0	0
AR	Q   	8315      	Piedra Del Aguila		0	0
AR	Q   	8316      	Plotier		0	0
AR	Q   	8318      	Plaza Huincul		0	0
AR	Q   	8319      	Cpto.nro 1 Y.p.f.plaza Huincul		0	0
AR	Q   	8322      	Cutral-co		0	0
AR	Q   	8340      	Zapala		0	0
AR	Q   	8341      	Espinazo Del Zorro		0	0
AR	Q   	8345      	Alumine		0	0
AR	Q   	8347      	Las Lajas		0	0
AR	Q   	8349      	Copahue		0	0
AR	Q   	8351      	Bajada Del Agrio		0	0
AR	Q   	8353      	Barrancas		0	0
AR	Q   	8370      	San Martin De Los Andes		0	0
AR	Q   	8371      	Junin De Los Andes		0	0
AR	Q   	8373      	Pampa Del Malleo		0	0
AR	Q   	8375      	Huechahue		0	0
AR	Q   	8401      	El Cruce		0	0
AR	Q   	8403      	Traful		0	0
AR	Q   	8407      	Villa La Angostura		0	0
AR	R   	8303      	Cinco Saltos		0	0
AR	R   	8324      	Cipolletti		0	0
AR	R   	8326      	Cervantes		0	0
AR	R   	8328      	Allen		0	0
AR	R   	8332      	Gral. Roca		0	0
AR	R   	8333      	Aguada Guzman		0	0
AR	R   	8334      	Ing. Huergo		0	0
AR	R   	8336      	Villa Regina	\N	-1	-1
AR	R   	8360      	Choele Choel		0	0
AR	R   	8361      	Luis Beltran		0	0
AR	R   	8363      	Colonia Josefa		0	0
AR	R   	8364      	Cnel. Belisle		0	0
AR	R   	8366      	Chelforo		0	0
AR	R   	8400      	San Carlos De Bariloche		0	0
AR	R   	8409      	Llao Llao		0	0
AR	R   	8411      	Puerto Blest		0	0
AR	R   	8412      	Las Bayas		0	0
AR	R   	8415      	Cerro Mesa		0	0
AR	R   	8416      	Clemente Onelli		0	0
AR	R   	8417      	Cañadon Chileno		0	0
AR	R   	8418      	Ing. Jacobacci		0	0
AR	R   	8422      	Maquinchao		0	0
AR	R   	8424      	Aguada De Guerra		0	0
AR	R   	8430      	El Bolson		0	0
AR	R   	8500      	Viedma		0	0
AR	R   	8501      	Balneario El Condor		0	0
AR	R   	8503      	Gral. Conesa		0	0
AR	R   	8505      	Boca De La Travesia		0	0
AR	R   	8514      	Gral. Lorenzo Vintter		0	0
AR	R   	8520      	San Antonio Oeste		0	0
AR	R   	8521      	Arroyo De La Ventana		0	0
AR	R   	8532      	Sierra Grande		0	0
AR	R   	8534      	Sierra Colorada		0	0
AR	R   	8536      	Musters		0	0
AR	S   	2000      	Rosario		0	0
AR	S   	2101      	Albarellos		0	0
AR	S   	2103      	Cnel. Bogado		0	0
AR	S   	2105      	Cañada Rica		0	0
AR	S   	2107      	Alvarez		0	0
AR	S   	2109      	Acebal		0	0
AR	S   	2111      	Santa Teresa		0	0
AR	S   	2113      	Peyrano		0	0
AR	S   	2115      	Maximo Paz		0	0
AR	S   	2117      	Alcorta		0	0
AR	S   	2119      	Arminda		0	0
AR	S   	2121      	Perez		0	0
AR	S   	2123      	Cnel. Arnold		0	0
AR	S   	2123ZAV   	Zavalla		-1	-1
AR	S   	2124      	Villa Gdor. Galvez		0	0
AR	S   	2126      	Alvear		0	0
AR	S   	2128      	Arroyo Seco		0	0
AR	S   	2134      	Roldan		0	0
AR	S   	2136      	San Jeronimo Sud		0	0
AR	S   	2138      	Carcaraña		0	0
AR	S   	2142      	Ibarlucea		0	0
AR	S   	2144      	Larguia		0	0
AR	S   	2146      	Clason		0	0
AR	S   	2147      	San Genaro Norte		0	0
AR	S   	2148      	Casas		0	0
AR	S   	2152      	Estacion Aeronautica Paganini		0	0
AR	S   	2154      	Cap. Bermudez		0	0
AR	S   	2156      	Fray Luis Beltran		0	0
AR	S   	2170      	Casilda		0	0
AR	S   	2173      	Chabas		0	0
AR	S   	2175      	Villa Mugueta		0	0
AR	S   	2177      	Bigand		0	0
AR	S   	2179      	Bombal		0	0
AR	S   	2181      	Los Molinos		0	0
AR	S   	2183      	Arequito		0	0
AR	S   	2187      	Arteaga		0	0
AR	S   	2200      	San Lorenzo		0	0
AR	S   	2201      	Ricardone		0	0
AR	S   	2202      	Puerto Gral. San Martin		0	0
AR	S   	2204      	Timbues		0	0
AR	S   	2206      	Oliveros		0	0
AR	S   	2208      	Gaboto		0	0
AR	S   	2212      	Monje		0	0
AR	S   	2214      	Aldao		0	0
AR	S   	2216      	Serodino		0	0
AR	S   	2218      	Carrizales		0	0
AR	S   	2222      	Diaz		0	0
AR	S   	2240      	Coronda		0	0
AR	S   	2241      	Larrechea		0	0
AR	S   	2242      	Arijon		0	0
AR	S   	2246      	Barrancas		0	0
AR	S   	2248      	Bernardo De Irigoyen		0	0
AR	S   	2252      	Galvez		0	0
AR	S   	2253      	Gessler		0	0
AR	S   	2255      	Lopez		0	0
AR	S   	2257      	Colonia Belgrano		0	0
AR	S   	2258      	Santa Clara De Buena Vista		0	0
AR	S   	2300      	Barrio Puzzi		0	0
AR	S   	2301      	Colonia Castellanos		0	0
AR	S   	2302      	Rafaela	\N	-1	-1
AR	S   	2303      	Angelica		0	0
AR	S   	2305      	Lehmann		0	0
AR	S   	2307      	Ataliva		0	0
AR	S   	2309      	Humberto 1ro.		0	0
AR	S   	2311      	Capivara		0	0
AR	S   	2313      	Moises Ville		0	0
AR	S   	2315      	Estacion Saguier		0	0
AR	S   	2317      	Casablanca		0	0
AR	S   	2318      	Aurelia		0	0
AR	S   	2322      	Cabaña El Cisne		0	0
AR	S   	2324      	Colonia Taculares		0	0
AR	S   	2326      	Colonia Bossi		0	0
AR	S   	2340      	Ceres		0	0
AR	S   	2341      	Colonia Montefiore		0	0
AR	S   	2342      	Curupaity		0	0
AR	S   	2344      	Arrufo		0	0
AR	S   	2345      	Villa Trinidad		0	0
AR	S   	2347      	Colonia Rosa		0	0
AR	S   	2349      	Monte Obscuridad		0	0
AR	S   	2352      	Ambrosetti		0	0
AR	S   	2401      	Colonia Castelar		0	0
AR	S   	2403      	Bauer Y Sigel		0	0
AR	S   	2405      	Colonia Cello		0	0
AR	S   	2407      	Clucellas		0	0
AR	S   	2409      	Estrada		0	0
AR	S   	2438      	Frontera		0	0
AR	S   	2440      	Sastre		0	0
AR	S   	2441      	Crispi		0	0
AR	S   	2443      	Colonia Margarita		0	0
AR	S   	2445      	Maria Juana		0	0
AR	S   	2447      	Los Sembrados		0	0
AR	S   	2449      	San Martin De Las Escobas		0	0
AR	S   	2451      	Las Petacas		0	0
AR	S   	2453      	Carlos Pellegrini		0	0
AR	S   	2454      	Cañada Rosquin		0	0
AR	S   	2456      	Esmeralda		0	0
AR	S   	2500      	Cañada De Gomez		0	0
AR	S   	2501      	Maria Luisa Correa		0	0
AR	S   	2503      	Villa Eloisa		0	0
AR	S   	2505      	Las Parejas		0	0
AR	S   	2506      	Correa		0	0
AR	S   	2508      	Armstrong		0	0
AR	S   	2512      	Tortugas		0	0
AR	S   	2520      	La California		0	0
AR	S   	2521      	Iturraspe		0	0
AR	S   	2523      	Bouquet		0	0
AR	S   	2527      	Maria Susana		0	0
AR	S   	2529      	Piamonte		0	0
AR	S   	2531      	Landeta		0	0
AR	S   	2533      	Los Cardos		0	0
AR	S   	2535      	El Trebol		0	0
AR	S   	2600      	Venado Tuerto		0	0
AR	S   	2601      	La Chispa		0	0
AR	S   	2603      	Chapuy		0	0
AR	S   	2605      	Rastreador Fournier		0	0
AR	S   	2607      	Villa Cañas		0	0
AR	S   	2609      	Colonia Morgan		0	0
AR	S   	2611      	Runciman		0	0
AR	S   	2613      	San Gregorio		0	0
AR	S   	2615      	San Eduardo		0	0
AR	S   	2617      	Sancti Spiritu		0	0
AR	S   	2618      	Carmen		0	0
AR	S   	2622      	Maggiolo		0	0
AR	S   	2630      	Firmat		0	0
AR	S   	2631      	Pueblo Miguel Torres		0	0
AR	S   	2633      	Chovet		0	0
AR	S   	2635      	Cañada Del Ucle		0	0
AR	S   	2637      	Colonia Hansen		0	0
AR	S   	2639      	Berabevu		0	0
AR	S   	2643      	Cafferata		0	0
AR	S   	2722      	Wheelwright		0	0
AR	S   	2723      	Juncal		0	0
AR	S   	2725      	Hughes		0	0
AR	S   	2726      	Labordeboy		0	0
AR	S   	2728      	Melincue		0	0
AR	S   	2729      	Carreras		0	0
AR	S   	2732      	El Jardin		0	0
AR	S   	2918      	Empalme Villa Constitucion		0	0
AR	S   	2919      	Villa Constitucion		0	0
AR	S   	2921      	Godoy		0	0
AR	S   	3000      	Santa Fe		0	0
AR	S   	3001      	Alto Verde		0	0
AR	S   	3003      	Helvecia		0	0
AR	S   	3005      	Colonia Francesa		0	0
AR	S   	3007      	Empalme San Carlos		0	0
AR	S   	3009      	Franck		0	0
AR	S   	3011      	San Mariano		0	0
AR	S   	3013      	Colonia Matilde		0	0
AR	S   	3014      	Angel Gallardo		0	0
AR	S   	3016      	San Jose		0	0
AR	S   	3017      	Sauce Viejo		0	0
AR	S   	3018      	Candioti		0	0
AR	S   	3020      	Reynaldo Cullen		0	0
AR	S   	3021      	Campo Andino		0	0
AR	S   	3023      	Cululu		0	0
AR	S   	3025      	Maria Luisa		0	0
AR	S   	3027      	La Pelada		0	0
AR	S   	3029      	Desvio Arauz		0	0
AR	S   	3032      	Nelson		0	0
AR	S   	3036      	Aromos		0	0
AR	S   	3038      	Cayastacito		0	0
AR	S   	3040      	San Justo		0	0
AR	S   	3041      	Cacique Ariacaiquin		0	0
AR	S   	3042      	Abipones		0	0
AR	S   	3044      	Gdor. Crespo		0	0
AR	S   	3045      	Colonia Dolores		0	0
AR	S   	3046      	Las Cañas		0	0
AR	S   	3048      	Luciano Leiva		0	0
AR	S   	3050      	Calchaqui		0	0
AR	S   	3051      	Alejandra		0	0
AR	S   	3052      	Colonia La Blanca		0	0
AR	S   	3054      	Colonia La Negra		0	0
AR	S   	3056      	Colonia La Maria		0	0
AR	S   	3057      	La Gallareta		0	0
AR	S   	3060      	Independencia		0	0
AR	S   	3061      	Antonio Pini		0	0
AR	S   	3066      	Campo Garay		0	0
AR	S   	3070      	San Cristobal		0	0
AR	S   	3071      	Aguara Grande		0	0
AR	S   	3072      	La Lucila		0	0
AR	S   	3074      	La Cabral		0	0
AR	S   	3076      	Huanqueros		0	0
AR	S   	3080      	Esperanza		0	0
AR	S   	3081      	Cavour		0	0
AR	S   	3083      	Grutly		0	0
AR	S   	3085      	Pilar		0	0
AR	S   	3087      	Felicia		0	0
AR	S   	3089      	Ing. Boasi		0	0
AR	S   	3516      	Florencia		0	0
AR	S   	3536      	Fortin Charrua		0	0
AR	S   	3541      	Gato Colorado		0	0
AR	S   	3550      	Vera		0	0
AR	S   	3551      	Cañada Ombu		0	0
AR	S   	3553      	Campo Duran		0	0
AR	S   	3555      	Las Palmas		0	0
AR	S   	3557      	Caraguatay		0	0
AR	S   	3560      	Reconquista		0	0
AR	S   	3561      	Avellaneda		0	0
AR	S   	3563      	Colonia San Manuel		0	0
AR	S   	3565      	El Tajamar		0	0
AR	S   	3567      	Destaca. Aer. Mil. Reconquista		0	0
AR	S   	3569      	Berna		0	0
AR	S   	3572      	Campo Ramseyer		0	0
AR	S   	3574      	Guadalupe Norte		0	0
AR	S   	3575      	Arroyo Ceibal		0	0
AR	S   	3580      	Villa Ocampo		0	0
AR	S   	3581      	Campo Redondo		0	0
AR	S   	3583      	Villa Ana		0	0
AR	S   	3585      	El Sombrerito		0	0
AR	S   	3586      	Las Toscas		0	0
AR	S   	3587      	San Antonio De Obligado		0	0
AR	S   	3589      	Villa Guillermina		0	0
AR	S   	3592      	Campo Hardy		0	0
AR	S   	3765      	Tomas Young		0	0
AR	S   	6009      	San Marcelo		0	0
AR	S   	6036      	Diego De Alvear		0	0
AR	S   	6039      	Christophersen		0	0
AR	S   	6100      	Rufino		0	0
AR	S   	6103      	Amenabar		0	0
AR	S   	6106      	Castellanos		0	0
AR	T   	4000      	San Miguel De Tucuman		0	0
AR	T   	4101      	Alta Gracia - Burruyacu		0	0
AR	T   	4103      	Tafi Viejo		0	0
AR	T   	4105      	Barrio Miguel Lillo		0	0
AR	T   	4107      	Yerba Buena		0	0
AR	T   	4109      	Banda Del Rio Sali		0	0
AR	T   	4111      	Colombres		0	0
AR	T   	4113      	El Guardamonte		0	0
AR	T   	4115      	Agua Dulce		0	0
AR	T   	4117      	Delfin Gallo		0	0
AR	T   	4119      	Benjamin Araoz		0	0
AR	T   	4122      	Benjamin Paz		0	0
AR	T   	4124      	Leocadio Paz		0	0
AR	T   	4128      	Lules		0	0
AR	T   	4129      	Ingenio Lules		0	0
AR	T   	4132      	El Cruce		0	0
AR	T   	4133      	Padilla		0	0
AR	T   	4134      	Acheral		0	0
AR	T   	4135      	Caspinchango		0	0
AR	T   	4137      	Amaicha Del Valle		0	0
AR	T   	4142      	Cap. Caceres		0	0
AR	T   	4143      	Independencia		0	0
AR	T   	4144      	Amberes		0	0
AR	T   	4145      	Rio Seco		0	0
AR	T   	4146      	Concepcion		0	0
AR	T   	4147      	Arcadia		0	0
AR	T   	4149      	Alpachiri		0	0
AR	T   	4151      	Los Gucheas		0	0
AR	T   	4152      	Aguilares		0	0
AR	T   	4153      	Alto Verde		0	0
AR	T   	4155      	Ingenio Santa Ana		0	0
AR	T   	4157      	Ingenio Santa Barbara		0	0
AR	T   	4158      	El Batiruano		0	0
AR	T   	4159      	Campo Bello		0	0
AR	T   	4161      	Domingo Millan		0	0
AR	T   	4162      	La Cocha		0	0
AR	T   	4163      	Huasa Pampa Norte		0	0
AR	T   	4164      	Huasa Pampa		0	0
AR	T   	4166      	Manchala		0	0
AR	T   	4168      	Amaicha Del Llano		0	0
AR	T   	4171      	Buena Vista		0	0
AR	T   	4172      	Macio		0	0
AR	T   	4174      	Arroyo		0	0
AR	T   	4176      	Arboles Grandes		0	0
AR	T   	4178      	Alderetes		0	0
AR	T   	4182      	Finca Mayo		0	0
AR	T   	4242      	Taco Ralo		0	0
AR	U   	8431      	Lago Puelo		0	0
AR	U   	9000      	Comodoro Rivadavia		0	0
AR	U   	9001      	Astra		0	0
AR	U   	9003      	Comodoro Rivadavia		0	0
AR	U   	9009      	Cañadon Lagarto		0	0
AR	U   	9020      	Valle Hermoso		0	0
AR	U   	9021      	Colhue Huapi		0	0
AR	U   	9023      	Buen Pasto		0	0
AR	U   	9030      	Sgto. Rugestein		0	0
AR	U   	9031      	Facundo		0	0
AR	U   	9033      	Alto Rio Senguer		0	0
AR	U   	9035      	Doctor Ricardo Rojas		0	0
AR	U   	9037      	Alto Rio Mayo		0	0
AR	U   	9100      	Trelew		0	0
AR	U   	9101      	Bajada Del Diablo		0	0
AR	U   	9103      	Rawson		0	0
AR	U   	9105      	Gaiman		0	0
AR	U   	9107      	Dolavon		0	0
AR	U   	9111      	Cabo Raso		0	0
AR	U   	9113      	Florentino Ameghino		0	0
AR	U   	9120      	Puerto Madryn		0	0
AR	U   	9121      	El Escorial		0	0
AR	U   	9200      	Esquel		0	0
AR	U   	9201      	Cajo De Ginebre Chico		0	0
AR	U   	9203      	Trevelin		0	0
AR	U   	9207      	Cerro Condor		0	0
AR	U   	9210      	El Maiten		0	0
AR	U   	9211      	Cushamen		0	0
AR	U   	9213      	Lepa		0	0
AR	U   	9217      	Cholila		0	0
AR	U   	9220      	Jose De San Martin		0	0
AR	U   	9221      	Valle Hondo		0	0
AR	U   	9223      	Alto Rio Pico		0	0
AR	U   	9225      	Frontera De Rio Pico		0	0
AR	U   	9227      	La Laurita		0	0
AR	U   	9297      	Paso Moreno		0	0
AR	U   	9339      	Lago Blanco		0	0
AR	V   	9410      	Petrel - Agencia Aca		0	0
AR	V   	9411      	Base Aerea Tte. Matienzo		0	0
AR	V   	9420      	Rio Grande		0	0
AR	V   	9421      	Frigorifico C.a.p.		0	0
AR	W   	3185      	Rincon De Tunas		0	0
AR	W   	3194      	Guayquiraro		0	0
AR	W   	3196      	Esquina		0	0
AR	W   	3197      	Cnel. Abraham Schweizer		0	0
AR	W   	3199      	Los Laureles		0	0
AR	W   	3220      	El Ceibo		0	0
AR	W   	3222      	Juan Pujol		0	0
AR	W   	3224      	Colonia Libertad		0	0
AR	W   	3226      	Mocoreta		0	0
AR	W   	3230      	Paso De Los Libres		0	0
AR	W   	3231      	Mirunga		0	0
AR	W   	3232      	Cabred		0	0
AR	W   	3234      	Paso Ledesma		0	0
AR	W   	3302      	Apipe Grande		0	0
AR	W   	3340      	Santo Tome		0	0
AR	W   	3342      	Caza Pava		0	0
AR	W   	3344      	Alvear		0	0
AR	W   	3346      	La Cruz		0	0
AR	W   	3351      	Garruchos		0	0
AR	W   	3400      	Corrientes		0	0
AR	W   	3401      	Arroyo Ponton		0	0
AR	W   	3403      	Cavia Cue		0	0
AR	W   	3405      	Cerrito		0	0
AR	W   	3407      	Capillita		0	0
AR	W   	3409      	Paso De La Patria		0	0
AR	W   	3412      	Ensenada Grande		0	0
AR	W   	3414      	Itati		0	0
AR	W   	3416      	El Sombrero		0	0
AR	W   	3418      	Empedrado		0	0
AR	W   	3420      	Saladas		0	0
AR	W   	3421      	Batel		0	0
AR	W   	3423      	Concepcion		0	0
AR	W   	3425      	Loma Alta		0	0
AR	W   	3427      	El Pago		0	0
AR	W   	3428      	Estacion Saladas		0	0
AR	W   	3433      	Carrizal Norte		0	0
AR	W   	3440      	Colonia Cecilio Echevarria		0	0
AR	W   	3441      	Cruz De Los Milagros		0	0
AR	W   	3443      	Lavalle		0	0
AR	W   	3445      	Costa Batel		0	0
AR	W   	3446      	Manuel Florencio Mantilla		0	0
AR	W   	3448      	Arroyito		0	0
AR	W   	3449      	Boliche Lata		0	0
AR	W   	3450      	Colonia Mercedes Cossio		0	0
AR	W   	3451      	Colonia Carolina		0	0
AR	W   	3453      	Ifran		0	0
AR	W   	3454      	Buena Esperanza		0	0
AR	W   	3460      	Curuzu-cuatia		0	0
AR	W   	3461      	Perugorria		0	0
AR	W   	3463      	Sauce		0	0
AR	W   	3465      	Cap. Joaquin Madariaga		0	0
AR	W   	3466      	Acuña		0	0
AR	W   	3470      	Mercedes		0	0
AR	W   	3471      	Alen Cue		0	0
AR	W   	3472      	Felipe Yofre		0	0
AR	W   	3474      	Chavarria		0	0
AR	W   	3476      	Solari		0	0
AR	W   	3480      	Ita-ibate		0	0
AR	W   	3481      	Arerungua		0	0
AR	W   	3483      	Loreto		0	0
AR	W   	3485      	Colonia El Caiman		0	0
AR	W   	3486      	Villa Olivari		0	0
AR	X   	2189      	Cruz Alta		0	0
AR	X   	2400      	San Francisco	\N	-1	-1
AR	X   	2411      	Luxardo		0	0
AR	X   	2413      	Colonia Anita		0	0
AR	X   	2415      	Porteña		0	0
AR	X   	2417      	Altos De Chipion		0	0
AR	X   	2419      	Brinkmann		0	0
AR	X   	2421      	Morteros		0	0
AR	X   	2423      	Colonia Prosperidad		0	0
AR	X   	2424      	Colonia Marina		0	0
AR	X   	2426      	Colonia San Bartolome		0	0
AR	X   	2428      	El Fuertecito		0	0
AR	X   	2432      	El Tio		0	0
AR	X   	2433      	Las Delicias		0	0
AR	X   	2434      	Arroyito		0	0
AR	X   	2435      	Colonia Coyunda		0	0
AR	X   	2436      	Plaza Bruno		0	0
AR	X   	2525      	Saira		0	0
AR	X   	2550      	Bell Ville		0	0
AR	X   	2551      	Cuatro Caminos		0	0
AR	X   	2553      	Justiniano Posse		0	0
AR	X   	2555      	Ordoñez		0	0
AR	X   	2557      	Idiazabal		0	0
AR	X   	2559      	Cintra		0	0
AR	X   	2561      	Chilibroste		0	0
AR	X   	2563      	Noetinger		0	0
AR	X   	2564      	Monte Leña		0	0
AR	X   	2566      	San Marcos Sud		0	0
AR	X   	2568      	Las Lagunitas		0	0
AR	X   	2572      	Ballesteros Sud		0	0
AR	X   	2580      	Marcos Juarez		0	0
AR	X   	2581      	Los Surgentes		0	0
AR	X   	2583      	Gral. Baldissera		0	0
AR	X   	2585      	Camilo Aldao		0	0
AR	X   	2587      	Inriville		0	0
AR	X   	2589      	Monte Buey		0	0
AR	X   	2592      	Gral. Roca		0	0
AR	X   	2594      	Leones		0	0
AR	X   	2619      	Km. 57		0	0
AR	X   	2624      	Arias		0	0
AR	X   	2625      	Cavanagh		0	0
AR	X   	2627      	Guatimozin		0	0
AR	X   	2645      	Cap. Bernardo O'higgins		0	0
AR	X   	2650      	Canals		0	0
AR	X   	2651      	Aldea Santa Maria		0	0
AR	X   	2655      	Wenceslao Escalante		0	0
AR	X   	2657      	Laborde		0	0
AR	X   	2659      	Colonia Barge		0	0
AR	X   	2661      	Isla Verde		0	0
AR	X   	2662      	Alejo Ledesma		0	0
AR	X   	2664      	Benjamin Gould		0	0
AR	X   	2670      	La Carlota		0	0
AR	X   	2671      	Assunta		0	0
AR	X   	2675      	Chazon		0	0
AR	X   	2677      	Ucacha		0	0
AR	X   	2679      	Pascanas		0	0
AR	X   	2681      	Etruria		0	0
AR	X   	2684      	Los Cisnes		0	0
AR	X   	2686      	Alejandro		0	0
AR	X   	4275      	Villa Huidobro		0	0
AR	X   	5000      	Córdoba		0	0
AR	X   	5009      	Rodriguez Del Busto		0	0
AR	X   	5016      	Córdoba	\N	-1	-1
AR	X   	5101      	Bajo Grande		0	0
AR	X   	5103      	Guarnicion Aerea Cordoba		0	0
AR	X   	5105      	Villa Allende		0	0
AR	X   	5107      	Agua De Oro		0	0
AR	X   	5109      	Unquillo		0	0
AR	X   	5111      	La Estancita		0	0
AR	X   	5113      	Salsipuedes		0	0
AR	X   	5115      	La Granja		0	0
AR	X   	5117      	Ascochinga		0	0
AR	X   	5119      	Bouwer		0	0
AR	X   	5121      	Despeñaderos		0	0
AR	X   	5123      	Ferreyra		0	0
AR	X   	5125      	Blas De Rosales		0	0
AR	X   	5127      	Los Guindos		0	0
AR	X   	5129      	Comechingones		0	0
AR	X   	5131      	El Alcalde		0	0
AR	X   	5133      	Santa Rosa De Rio Primero		0	0
AR	X   	5135      	Buey Muerto		0	0
AR	X   	5136      	La Quinta		0	0
AR	X   	5137      	La Para		0	0
AR	X   	5139      	Marull		0	0
AR	X   	5141      	Balnearia		0	0
AR	X   	5143      	Miramar		0	0
AR	X   	5145      	Juarez Celman		0	0
AR	X   	5147      	Arguello		0	0
AR	X   	5149      	Cassaffousth		0	0
AR	X   	5151      	Casa Bamba		0	0
AR	X   	5152      	Villa Carlos Paz		0	0
AR	X   	5153      	Copina		0	0
AR	X   	5155      	Cavalango		0	0
AR	X   	5158      	Bialetmasse		0	0
AR	X   	5162      	Casa Grande		0	0
AR	X   	5164      	Domingo Funes		0	0
AR	X   	5165      	Sanatorio Santa Maria		0	0
AR	X   	5166      	Cosquin		0	0
AR	X   	5168      	Valle Hermoso		0	0
AR	X   	5172      	El Vallecito		0	0
AR	X   	5174      	Huerta Grande		0	0
AR	X   	5176      	Villa Giardino		0	0
AR	X   	5178      	Cruz Chica		0	0
AR	X   	5182      	Los Cocos		0	0
AR	X   	5184      	Capilla Del Monte		0	0
AR	X   	5186      	Alta Gracia		0	0
AR	X   	5187      	La Falda Del Carmen		0	0
AR	X   	5189      	Bajo Chico		0	0
AR	X   	5191      	Calmayo		0	0
AR	X   	5192      	Dique Los Molinos		0	0
AR	X   	5194      	Atos Pampa		0	0
AR	X   	5196      	Santa Rosa De Calamuchita		0	0
AR	X   	5197      	El Parador De La Montaña		0	0
AR	X   	5199      	Amboy		0	0
AR	X   	5200      	Canteras Km. 428		0	0
AR	X   	5201      	Copacabana		0	0
AR	X   	5203      	Alto De Flores		0	0
AR	X   	5205      	El Cerrito		0	0
AR	X   	5209      	Cachi Yaco		0	0
AR	X   	5211      	Macha		0	0
AR	X   	5212      	Avellaneda		0	0
AR	X   	5214      	Km. 881		0	0
AR	X   	5216      	Km. 907		0	0
AR	X   	5218      	Chuña		0	0
AR	X   	5220      	Jesus Maria		0	0
AR	X   	5221      	Agua De Las Piedras		0	0
AR	X   	5223      	Caroya		0	0
AR	X   	5225      	Atahona		0	0
AR	X   	5227      	La Posta		0	0
AR	X   	5229      	Cañada De Luque		0	0
AR	X   	5231      	Campo Grande		0	0
AR	X   	5233      	El Zapallar		0	0
AR	X   	5236      	Villa Del Totoral		0	0
AR	X   	5238      	Las Peñas		0	0
AR	X   	5242      	Simbolar		0	0
AR	X   	5244      	Caminiaga		0	0
AR	X   	5246      	Chañar Viejo		0	0
AR	X   	5248      	Eufrasio Loza		0	0
AR	X   	5249      	Candelaria		0	0
AR	X   	5270      	Iglesia Vieja		0	0
AR	X   	5271      	Piedrita Blanca		0	0
AR	X   	5272      	El Chacho		0	0
AR	X   	5280      	Cruz Del Eje		0	0
AR	X   	5281      	Canteras De Quilpo		0	0
AR	X   	5282      	Charbonier		0	0
AR	X   	5284      	Aguas De Ramon		0	0
AR	X   	5285      	Bañado De Soto		0	0
AR	X   	5287      	Capilla La Candelaria		0	0
AR	X   	5289      	Cienaga Del Coro		0	0
AR	X   	5291      	Estancia De Guadalupe		0	0
AR	X   	5293      	El Durazno Minas		0	0
AR	X   	5295      	Salsacate		0	0
AR	X   	5297      	Cañada Del Puerto		0	0
AR	X   	5299      	Ambul		0	0
AR	X   	5738      	Paunero		0	0
AR	X   	5800      	Rio Cuarto		0	0
AR	X   	5801      	Alpa Corral		0	0
AR	X   	5803      	Paso Del Durazno		0	0
AR	X   	5805      	Las Higueras		0	0
AR	X   	5807      	Bengolea		0	0
AR	X   	5809      	Gral. Cabrera		0	0
AR	X   	5811      	Cnel. Baigorria		0	0
AR	X   	5813      	Gigena		0	0
AR	X   	5815      	Elena		0	0
AR	X   	5817      	Berrotaran		0	0
AR	X   	5819      	Cañada De Alvarez		0	0
AR	X   	5821      	Cano		0	0
AR	X   	5823      	Los Condores		0	0
AR	X   	5825      	Holmberg		0	0
AR	X   	5827      	Las Vertientes		0	0
AR	X   	5829      	Sampacho		0	0
AR	X   	5831      	Estacion Achiras		0	0
AR	X   	5833      	Achiras		0	0
AR	X   	5837      	Chajan		0	0
AR	X   	5839      	Estacion Punta Del Agua		0	0
AR	X   	5841      	San Basilio		0	0
AR	X   	5843      	Adelia Maria		0	0
AR	X   	5845      	Bulnes		0	0
AR	X   	5847      	Cnel. Moldes		0	0
AR	X   	5848      	La Gilda		0	0
AR	X   	5850      	Fabrica Militar - Rio Tercero		0	0
AR	X   	5851      	Colonia Santa Catalina		0	0
AR	X   	5853      	Corralito		0	0
AR	X   	5854      	Almafuerte		0	0
AR	X   	5856      	Embalse		0	0
AR	X   	5857      	Embalse (sucursal Nro 1)		0	0
AR	X   	5859      	Arroyo San Antonio		0	0
AR	X   	5862      	Villa Del Dique		0	0
AR	X   	5864      	Villa Rumipal		0	0
AR	X   	5870      	La Cañada		0	0
AR	X   	5871      	Altautina		0	0
AR	X   	5873      	Capilla De Romero		0	0
AR	X   	5875      	Cruz De Caña		0	0
AR	X   	5877      	Yacanto		0	0
AR	X   	5879      	La Paz		0	0
AR	X   	5885      	Hornillos		0	0
AR	X   	5887      	Nono		0	0
AR	X   	5889      	Mina Clavero		0	0
AR	X   	5891      	Cienaga De Allende		0	0
AR	X   	5893      	Alto Grande		0	0
AR	X   	5900      	Las Playas		0	0
AR	X   	5901      	Ausonia		0	0
AR	X   	5902      	Villa María	\N	-1	-1
AR	X   	5903      	Villa Nueva		0	0
AR	X   	5905      	Ana Zumaran		0	0
AR	X   	5907      	Alto Alegre		0	0
AR	X   	5909      	Arroyo Algodon		0	0
AR	X   	5911      	La Playosa		0	0
AR	X   	5913      	Pozo Del Molle		0	0
AR	X   	5915      	Carrilobo		0	0
AR	X   	5917      	Arroyo Cabral		0	0
AR	X   	5919      	Dalmacio Velez		0	0
AR	X   	5921      	Las Perdices		0	0
AR	X   	5923      	Gral. Deheza		0	0
AR	X   	5925      	La Palestina		0	0
AR	X   	5929      	Hernando		0	0
AR	X   	5931      	Las Isletillas		0	0
AR	X   	5933      	Gral. Fotheringham		0	0
AR	X   	5935      	Villa Ascasubi		0	0
AR	X   	5936      	San Antonio De Yucat		0	0
AR	X   	5940      	Las Varillas		0	0
AR	X   	5941      	Las Varas		0	0
AR	X   	5943      	Saturnino Laspur		0	0
AR	X   	5945      	Sacanta		0	0
AR	X   	5947      	El Arañado		0	0
AR	X   	5949      	Alhuampa		0	0
AR	X   	5951      	El Fortin		0	0
AR	X   	5960      	Rio Segundo		0	0
AR	X   	5961      	Cañada De Machado		0	0
AR	X   	5963      	Cañada De Machado Sud		0	0
AR	X   	5965      	Calchin Oeste		0	0
AR	X   	5967      	Luque		0	0
AR	X   	5969      	Estacion Calchin		0	0
AR	X   	5972      	Pilar		0	0
AR	X   	5974      	Laguna Larga		0	0
AR	X   	5980      	Oliva		0	0
AR	X   	5981      	Colonia Vidal Abal		0	0
AR	X   	5984      	James Craik		0	0
AR	X   	5986      	Oncativo		0	0
AR	X   	5987      	Colonia Almada		0	0
AR	X   	5988      	Manfredi		0	0
AR	X   	6101      	La Cesira		0	0
AR	X   	6120      	Guardia Vieja		0	0
AR	X   	6121      	El Rastreador		0	0
AR	X   	6123      	Melo		0	0
AR	X   	6125      	Serrano		0	0
AR	X   	6127      	Jovita		0	0
AR	X   	6128      	Leguizamon		0	0
AR	X   	6130      	Curapaligue		0	0
AR	X   	6132      	Gral. Levalle		0	0
AR	X   	6134      	Rio Bamba		0	0
AR	X   	6140      	Pretot Freyre		0	0
AR	X   	6141      	Tosquita		0	0
AR	X   	6142      	Gral. Soler		0	0
AR	X   	6144      	Laguna Oscura		0	0
AR	X   	6225      	Hipolito Bouchard		0	0
AR	X   	6227      	Onagoity		0	0
AR	X   	6270      	Huinca Renanco		0	0
AR	X   	6271      	De La Serna		0	0
AR	X   	6273      	Lecueder		0	0
AR	X   	6275      	La Nacional		0	0
AR	X   	6279      	La Penca		0	0
AR	Y   	4411      	Sey		0	0
AR	Y   	4431      	Aguas Calientes		0	0
AR	Y   	4500      	El Arenal		0	0
AR	Y   	4501      	El Fuerte		0	0
AR	Y   	4503      	La Esperanza		0	0
AR	Y   	4504      	Chalican		0	0
AR	Y   	4506      	Fraile Pintado		0	0
AR	Y   	4512      	Ldor. Gral. San Martin		0	0
AR	Y   	4513      	Pampichuela		0	0
AR	Y   	4514      	Calilegua		0	0
AR	Y   	4516      	Caimancito		0	0
AR	Y   	4518      	Yuto		0	0
AR	Y   	4522      	La Mendieta		0	0
AR	Y   	4542      	El Talar		0	0
AR	Y   	4600      	San Salvador De Jujuy		0	0
AR	Y   	4601      	Huaico Chico		0	0
AR	Y   	4603      	Perico Del Carmen		0	0
AR	Y   	4605      	Perico De San Antonio		0	0
AR	Y   	4606      	Los Lapachos		0	0
AR	Y   	4608      	Bordo La Isla		0	0
AR	Y   	4612      	Centro Forestal		0	0
AR	Y   	4616      	Barcena		0	0
AR	Y   	4618      	Colorados		0	0
AR	Y   	4622      	Maimara		0	0
AR	Y   	4624      	Abramayo		0	0
AR	Y   	4626      	Huacalera		0	0
AR	Y   	4630      	Humahuaca		0	0
AR	Y   	4631      	Caspala		0	0
AR	Y   	4632      	Chaupi Rodero		0	0
AR	Y   	4634      	Abralaite		0	0
AR	Y   	4638      	Tres Cruces		0	0
AR	Y   	4640      	Abra Pampa		0	0
AR	Y   	4641      	Abdon Castro Tolay		0	0
AR	Y   	4643      	Arbolito Nuevo		0	0
AR	Y   	4644      	Cangrejillos		0	0
AR	Y   	4650      	La Quiaca		0	0
AR	Y   	4651      	Yavi		0	0
AR	Y   	4653      	Casira		0	0
AR	Y   	4655      	Cabreria		0	0
AR	Z   	9011      	Caleta Olivia		0	0
AR	Z   	9013      	Cañadon Seco		0	0
AR	Z   	9015      	Pico Truncado		0	0
AR	Z   	9017      	El Pluma		0	0
AR	Z   	9019      	Fitz Roy		0	0
AR	Z   	9040      	El Portezuelo		0	0
AR	Z   	9041      	Los Antiguos		0	0
AR	Z   	9050      	Gdor. Moyano		0	0
AR	Z   	9053      	Jaramillo		0	0
AR	Z   	9300      	Puerto Santa Cruz		0	0
AR	Z   	9301      	La Florida		0	0
AR	Z   	9303      	Cdte. Luis Piedra Buena		0	0
AR	Z   	9305      	Puerto Coyle		0	0
AR	Z   	9310      	Puerto San Julian		0	0
AR	Z   	9311      	Gdor. Gregores		0	0
AR	Z   	9313      	El Salado		0	0
AR	Z   	9315      	Bajo Caracoles		0	0
AR	Z   	9316      	Laura		0	0
AR	Z   	9400      	Rio Gallegos		0	0
AR	Z   	9401      	Fuentes Del Coyle		0	0
AR	Z   	9405      	Bahia Tranquila		0	0
AR	Z   	9407      	El Turbio		0	0
\.


--
-- Data for TOC entry 12 (OID 56992468)
-- Name: soe_jurisdicciones; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY soe_jurisdicciones (jurisdiccion, descripcion, estado) FROM stdin;
1	Nacional	A
2	Privada	A
3	Provincial	A
4	Internacional	A
0	Indefinida	A
5	Instituto Universitario Nacional-Ley 24.521 art. 77	A
6	Privada con Autorización Definitiva	A
7	Privada con Autorización Provisoria	A
8	Extranjera	A
8889	AQQQQQQQ QQQQQQQ QQQQ QQQQQQ QQQQQQQQ QQQQQ QQQQQQQ QQQQQQ QQQQQQQ QQQQQQQQ QQQQQQ QQQQQQQQ QQQQQQQZ	A
\.


--
-- Data for TOC entry 13 (OID 56992474)
-- Name: soe_tiposinstit; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY soe_tiposinstit (tipoinstit, descripcion, detalle, estado) FROM stdin;
\.


--
-- Data for TOC entry 14 (OID 56992481)
-- Name: soe_instituciones; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY soe_instituciones (institucion, nombre_completo, nombre_abreviado, sigla, jurisdiccion, tipoinstit) FROM stdin;
1	UNIVERSIDAD DE BUENOS AIRES	UBA	UBA	1	\N
2	UNIVERSIDAD NACIONAL DE CATAMARCA	CATAMARCA	UNCAT	1	\N
3	UNIVERSIDAD NACIONAL DEL CENTRO DE LA PROVINCIA DE BUENOS AIRES	CENTRO	UNCPBA	1	\N
4	UNIVERSIDAD NACIONAL DEL COMAHUE	COMAHUE	UNCOM	1	\N
5	UNIVERSIDAD NACIONAL DE CORDOBA	CORDOBA	UNC	1	\N
6	UNIVERSIDAD NACIONAL DE CUYO	CUYO	UNCUY	1	\N
7	UNIVERSIDAD NACIONAL DE ENTRE RIOS	ENTRE RIOS	UNER	1	\N
8	UNIVERSIDAD NACIONAL DE FORMOSA	FORMOSA	UNFO	1	\N
9	UNIVERSIDAD NACIONAL DE GENERAL SAN MARTIN	SAN MARTIN	UNGSM	1	\N
10	UNIVERSIDAD NACIONAL DE GENERAL SARMIENTO	SARMIENTO	UNGSAR	1	\N
11	UNIVERSIDAD NACIONAL DE JUJUY	JUJUY	UNJU	1	\N
12	UNIVERSIDAD NACIONAL DE LA MATANZA	MATANZA	UNLM	1	\N
13	UNIVERSIDAD NACIONAL DE LANUS	LANUS	UNLA	1	\N
14	UNIVERSIDAD NACIONAL DE LA PAMPA	LA PAMPA	UNLAPAMPA	1	\N
15	UNIVERSIDAD NACIONAL DE LA PATAGONIA AUSTRAL	LA PATAGONIA AUSTRAL	UNLPAU	1	\N
16	UNIVERSIDAD NACIONAL DE LA PATAGONIA SAN JUAN BOSCO	LA PATAG. SAN JUAN BOSCO	UNLPSJB	1	\N
17	UNIVERSIDAD NACIONAL DE LA PLATA	LA PLATA	UNLP	1	\N
18	UNIVERSIDAD NACIONAL DE LA RIOJA	LA RIOJA	UNLR	1	\N
19	UNIVERSIDAD NACIONAL DEL LITORAL	LITORAL	UNL	1	\N
20	UNIVERSIDAD NACIONAL DE LOMAS DE ZAMORA	LOMAS DE ZAMORA	UNLZ	1	\N
21	UNIVERSIDAD NACIONAL DE LUJAN	LUJAN	UNLU	1	\N
22	UNIVERSIDAD NACIONAL DE MAR DEL PLATA	MAR DEL PLATA	UNMP	1	\N
23	UNIVERSIDAD NACIONAL DE MISIONES	MISIONES	UNAM	1	\N
24	UNIVERSIDAD NACIONAL DEL NORDESTE	NORDESTE	UNNE	1	\N
25	UNIVERSIDAD NACIONAL DE QUILMES	QUILMES	UNQUI	1	\N
26	UNIVERSIDAD NACIONAL DE RIO CUARTO	RIO CUARTO	UNRC	1	\N
27	UNIVERSIDAD NACIONAL DE ROSARIO	ROSARIO	UNR	1	\N
28	UNIVERSIDAD NACIONAL DE SALTA	SALTA	UNAS	1	\N
29	UNIVERSIDAD NACIONAL DE SAN JUAN	SAN JUAN	UNSJ	1	\N
30	UNIVERSIDAD NACIONAL DE SAN LUIS	SAN LUIS	UNSL	1	\N
31	UNIVERSIDAD NACIONAL DE SANTIAGO DEL ESTERO	SANTIAGO DEL ESTERO	UNSE	1	\N
32	UNIVERSIDAD NACIONAL DEL SUR	SUR	UNS	1	\N
33	UNIVERSIDAD NACIONAL DE TUCUMAN	TUCUMAN	UNT	1	\N
34	UNIVERSIDAD NACIONAL DE VILLA MARIA	VILLA MARIA	UNVM	1	\N
35	UNIVERSIDAD TECNOLOGICA NACIONAL	UTN	UTN	1	\N
36	UNIVERSIDAD NACIONAL DE TRES DE FEBRERO	TRES DE FEBRERO	UNTFE	1	\N
37	INSTITUTO DE ENSEÑANZA SUPERIOR DEL EJERCITO	IESE	IESE	1	\N
38	INSTITUTO UNIVERSITARIO AERONAUTICO	IAERONAUTICO	IUA	1	\N
39	INSTITUTO UNIVERSITARIO NAVAL	INAVAL	IUN	1	\N
40	INSTITUTO UNIVERSITARIO DE LA POLICIA FEDERAL ARGENTINA	IPOLICIA	IUPF	1	\N
41	UNIVERSIDAD NOTARIAL ARGENTINA	NOTARIAL	UNOTA	2	\N
42	UNIVERSIDAD CEMA	CEMA	UCEM	2	\N
43	ESCUELA UNIVERSITARIA DE TEOLOGIA	ESCUELA DE TEOLOGIA	EUTEO	2	\N
44	INSTITUTO UNIVERSITARIO NACIONAL DEL ARTE	IUNA	IUNA	1	\N
45	INSTITUTO TECNOLOGICO DE BUENOS AIRES	ITBA	ITBA	2	\N
46	UNIVERSIDAD FAVALORO	FAVALORO	UFAV	2	\N
47	INSTITUTO UNIVERSITARIO DE CS. DE LA SALUD - FUNDACION UNIVERSITARIA HECTOR A. BARCELO	BARCELO	IUCSAL	2	\N
48	PONTIFICIA UNIVERSIDAD CATOLICA ARGENTINA SANTA MARIA DE LOS BUENOS AIRES	UCA	UCA	2	\N
49	UNIVERSIDAD ABIERTA INTERAMERICANA	ABIERTA INTERAMERICANA	UAI	2	\N
50	UNIVERSIDAD ADVENTISTA DEL PLATA	ADVENTISTA	UAPL	2	\N
51	UNIVERSIDAD ARGENTINA DE LA EMPRESA	UADE	UADE	2	\N
52	UNIVERSIDAD ARGENTINA JOHN F. KENNEDY	KENNEDY	UAJFK	2	\N
53	UNIVERSIDAD ATLANTIDA ARGENTINA	ATLANTIDA	UAA	2	\N
54	UNIVERSIDAD AUSTRAL	AUSTRAL	UA	2	\N
55	UNIVERSIDAD BLAS PASCAL	BLAS PASCAL	UBP	2	\N
56	UNIVERSIDAD CENTRO DE ALTOS ESTUDIOS EN CIENCIAS EXACTAS	CAECE	CAECE	2	\N
57	UNIVERSIDAD CATOLICA DE CORDOBA	CATOLICA CORDOBA	UCCOR	2	\N
58	UNIVERSIDAD CATOLICA DE CUYO	CATOLICA DE CUYO	UCCUY	2	\N
59	UNIVERSIDAD CATOLICA DE LA PLATA	CATOLICA DE LA PLATA	UCLP	2	\N
60	UNIVERSIDAD CATOLICA DE SALTA	CATOLICA DE SALTA	UCS	2	\N
61	UNIVERSIDAD CATOLICA DE SANTA FE	CATOLICA DE SANTA FE	UCSFE	2	\N
62	UNIVERSIDAD CATOLICA DE SANTIAGO DEL ESTERO	CATOLICA DE SANTIAGO DEL ESTERO	UCSE	2	\N
63	UNIVERSIDAD CHAMPAGNAT	CHAMPAGNAT	UCHAM	2	\N
64	UNIVERSIDAD DE BELGRANO	BELGRANO	UB	2	\N
65	UNIVERSIDAD DE CIENCIAS EMPRESARIALES Y SOCIALES	CIENCIAS EMPRESARIALES	UCES	2	\N
66	UNIVERSIDAD DE CONCEPCION DEL URUGUAY	CONCEPCIO DEL URUGUAY	UCU	2	\N
67	UNIVERSIDAD DE CONGRESO	CONGRESO	UCON	2	\N
68	UNIVERSIDAD DE FLORES	FLORES	UFLO	2	\N
69	UNIVERSIDAD DE MENDOZA	MENDOZA	UMEN	2	\N
70	UNIVERSIDAD DE MORON	MORON	UM	2	\N
71	UNIVERSIDAD DE PALERMO	PALERMO	UP	2	\N
72	UNIVERSIDAD DE SAN ANDRES	SAN ANDRES	USA	2	\N
73	UNIVERSIDAD DEL ACONCAGUA	ACONCAGUA	UAC	2	\N
74	UNIVERSIDAD DEL CENTRO EDUCATIVO LATINOAMERICANO	CENTRO EDUCATIVO LATINOAMERICANO	UCEL	2	\N
75	UNIVERSIDAD DEL CINE	DEL CINE	UC	2	\N
76	UNIVERSIDAD DEL MUSEO SOCIAL ARGENTINO	MUSEO SOCIAL	UMSA	2	\N
77	UNIVERSIDAD DEL NORTE SANTO TOMAS DE AQUINO	SANTO TOMAS	UNSTA	2	\N
78	UNIVERSIDAD DEL SALVADOR	SALVADOR	USAL	2	\N
79	UNIVERSIDAD DE LA CUENCA DEL PLATA	CUENCA DEL PLATA	UCUPLA	2	\N
80	UNIVERSIDAD DE LA FRATERNIDAD Y AGRUPACIONES SANTO TOMAS DE AQUINO (FASTA)	F.A.S.T.A.	FASTA	2	\N
81	UNIVERSIDAD DE LA MARINA MERCANTE	MARINA MERCANTE	UMM	2	\N
82	UNIVERSIDAD EMPRESARIAL SIGLO XXI	SIGLO XXI	UESXXI	2	\N
83	UNIVERSIDAD HEBREA BAR ILAN - (INSTITUCION CERRADA - R.M.083/00)	BAR ILAN	UHBI	2	\N
84	UNIVERSIDAD JUAN AGUSTIN MAZA	MAZA	UJAM	2	\N
85	UNIVERSIDAD MAIMONIDES	MAIMONIDES	UMA	2	\N
87	UNIVERSIDAD TORCUATO DI TELLA	DI TELLA	UTDT	2	\N
88	INSTITUTO UNIVERSITARIO CEMIC	CEMIC	CEMIC	2	\N
89	INSTITUTO UNIVERSITARIO GASTON DACHARY	GASTON DACHARY	IUGD	2	\N
90	INSTITUTO UNIVERSITARIO DE LA FUNDACION ISALUD	ISALUD	ISALUD	2	\N
91	INSTITUTO UNIVERSITARIO ESCUELA SUPERIOR DE ECONOMIA Y ADMINISTRACION DE EMPRESAS (ESEADE)	ESEADE	ESEADE	2	\N
92	INSTITUTO UNIVERSITARIO ESCUELA DE MEDICINA DEL HOSPITAL ITALIANO	ITALIANO DE BUENOS AIRES	EMHI	2	\N
93	INSTITUTO UNIVERSITARIO ITALIANO DE ROSARIO	ITALIANO DE ROSARIO	UNIR	0	\N
94	REPRESENTACION EN LA REPUBLICA ARGENTINA DE LA UNIVERSIDAD DE BOLOGNA	BOLOGNA	UNIBO	2	\N
95	UNIVERSIDAD AUTONOMA DE ENTRE RIOS	AUTONOMA DE ENTRE RIOS	UADER	3	\N
96	INSTITUTO UNIVERSITARIO IDEA	IDEA	IDEA	2	\N
97	INSTITUTO UNIVERSITARIO DE SEGURIDAD MARITIMA	SEGURIDAD MARITIMA	IUSM	2	\N
99	UNIVERSIDAD NACIONAL DEL NOROESTE DE LA PROVINCIA DE BUENOS AIRES	JUNIN	UNNO	1	\N
100	INSTITUTO UNIVERSITARIO ISEDET	ISEDET	ISEDET	2	\N
101	FACULTAD LATINOAMERICANA DE CIENCIAS SOCIALES	FLACSO	FLACSO	4	\N
102	INSTITUTO UNIVERSITARIO ESCUELA ARGENTINA DE NEGOCIOS	ESCUELA ARGENTINA DE NEGOCIOS	IUEAN	2	\N
103	UNIVERSIDAD NACIONAL DE CHILECITO	\N	UNCHI	1	\N
8889	Institución 01	Institución 01	Institución 01	6	\N
\.


--
-- Data for TOC entry 15 (OID 56992496)
-- Name: soe_tiposede; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY soe_tiposede (tiposede, descripcion, detalle, estado) FROM stdin;
\.


--
-- Data for TOC entry 16 (OID 56992503)
-- Name: soe_sedes; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY soe_sedes (institucion, sede, nombre, tiposede, idpais, idprovincia, codigopostal) FROM stdin;
1	1	SedePrincipal - UBA	\N	AR	C   	1427      
1	2	Sede - 00002 de Facultad de Ciencias Económicas	\N	AR	C   	1120      
2	1	SedePrincipal - CATAMARCA	\N	AR	K   	4700      
3	1	Sede Principal - UNICEN TANDIL	\N	AR	B   	7000      
4	1	SedePrincipal - COMAHUE	\N	AR	Q   	8300      
4	2	Sede - 00002 de Facultad de Ciencias Agrarias	\N	AR	R   	8303      
4	3	Sede - 00003 de Asentamiento Universitario San Martín de los Andes	\N	AR	Q   	8370      
4	4	Sede - 00004 de Asentamiento Universitario Villa Regina	\N	AR	R   	8336      
4	5	Sede - 00005 de Asentamiento Universitario Zapala	\N	AR	Q   	8340      
4	6	Sede - 00006 de Facultad de Derecho y Ciencias Sociales, General Roca	\N	AR	R   	8332      
4	7	Sede - 00007 de Facultad de Ciencias de la Educación, Sede Cipolletti	\N	AR	R   	8324      
4	8	Sede - 00008 de Centro Universitario Regional Zona Atlántica	\N	AR	R   	8500      
4	9	Sede - 00009 de Centro Regional Universitario Bariloche	\N	AR	R   	8400      
4	10	Sede - 00010 de Módulo Chos Malal - Fac. de Economía y Administración	\N	AR	Q   	8353      
4	11	Sede - 00011 de Sede San Antonio Oeste . C.U.R.Z.A.	\N	AR	R   	8520      
4	12	Sede - 00012 de Módulo El Hoyo - Facultad de Turismo	\N	AR	U   	9000      
4	13	Sede - 00013 de Módulo Allen  de Enfernería - I.U.C.S.	\N	AR	R   	8328      
5	1	SedePrincipal - CORDOBA	\N	AR	X   	5000      
6	1	Sede Principal - CUYO	\N	AR	M   	5500      
6	2	Sede - 00002 de Facultad de Ciencias Agrarias	\N	AR	M   	5507      
6	3	Sede - 00003 de Instituto Balseiro	\N	AR	R   	8400      
6	4	Sede - 00004 de Facultad de Ciencias Económicas - Delegación San Rafael	\N	AR	M   	5600      
6	5	Sede - 00005 de Inst.Tecnológico Univ. (Sede Gral.Alvear)	\N	AR	M   	M5620DFC  
6	6	Sede - 00006 de Inst.Tecnológico Univ. (Sede Tunuyán)	\N	AR	M   	5560      
7	1	SedePrincipal - ENTRE RIOS	\N	AR	E   	3260      
7	2	Sede - 00002 de Facultad de Ciencias Agropecuarias	\N	AR	E   	3100      
7	3	Sede - 00003 de Facultad de Ciencias de la Alimentación	\N	AR	E   	3200      
7	4	Sede - 00004 de Facultad de Bromatología	\N	AR	E   	2820      
8	1	SedePrincipal - FORMOSA	\N	AR	P   	3600      
9	1	SedePrincipal - SAN MARTIN	\N	AR	B   	1653      
9	2	Sede - 00002 de Escuela de Economía y Negocios	\N	AR	B   	1650      
9	3	Sede - 00003 de Instituto de Ciencias de la Rehabilitación	\N	AR	C   	1428      
9	4	Sede - 00004 de Escuela de Política y Gobierno	\N	AR	C   	1030      
10	1	SedePrincipal - SARMIENTO	\N	AR	B   	9999      
11	1	SedePrincipal - JUJUY	\N	AR	Y   	4600      
12	1	Sede Principal - SAN JUSTO	\N	AR	B   	1754      
13	1	SedePrincipal - LANUS	\N	AR	B   	1826      
14	1	Sede Principal - SANTA ROSA - LA PAMPA	\N	AR	L   	6300      
14	2	Sede GENERAL PICO	\N	AR	L   	6360      
58	4	Instituto Cervantes - CORDOBA	\N	AR	X   	5800      
15	1	SedePrincipal - Patagonia Austral	\N	AR	Z   	9400      
15	2	Sede - 00002 Unidad Academica Caleta Olivia	\N	AR	Z   	9011      
15	3	Sede - 00003 Unidad Academica Puerto San Julian	\N	AR	Z   	9310      
15	4	Sede - 00004 Unidad Academica Rio Turbio	\N	AR	Z   	9407      
16	1	Sede Principal - Comodoro Rivadavia	\N	AR	U   	9000      
16	2	Subsede Trelew	\N	AR	U   	9100      
16	3	Subsede Esquel	\N	AR	U   	9200      
16	4	Subsede Puerto Madryn	\N	AR	U   	9120      
16	5	Subsede Ushuaia	\N	AR	V   	9410      
17	1	Sede Principal - LA PLATA	\N	AR	B   	1900      
18	1	Sede Principal - LA RIOJA	\N	AR	F   	5300      
18	2	Sede Chamical	\N	AR	F   	5380      
101	2	ROSARIO	\N	AR	S   	2000      
18	4	Sede Villa Unión	\N	AR	F   	5350      
18	5	Sede Chepes	\N	AR	F   	5470      
18	6	Sede Aimogasta	\N	AR	F   	5310      
19	1	SedePrincipal - LITORAL	\N	AR	S   	3000      
67	2	LOCALIZACION CORDOBA	\N	AR	X   	\N
80	3	Sede BARILOCHE	\N	AR	R   	8400      
78	18	Subsede Neuquén	\N	AR	Q   	8300      
20	1	Sede Principal - LOMAS DE ZAMORA	\N	AR	B   	1832      
21	1	SedePrincipal - LUJAN	\N	AR	B   	6700      
21	2	Sede - 00002 de Centro Regional Campana	\N	AR	B   	2804      
21	3	Sede - 00003 de Centro Regional Chivilcoy	\N	AR	B   	6620      
21	4	Sede - 00004 de Centro Regional General Sarmiento	\N	AR	B   	3314      
21	5	Sede - 00005 de Delegación Académica Escobar	\N	AR	B   	1625      
21	6	Sede - 00006 de Delegación Académica San Fernando	\N	AR	B   	1646      
21	7	Sede - 00007 de Delegación Académica Pilar	\N	AR	B   	1629      
21	8	Sede - 00008 de Delegación Académica Pergamino	\N	AR	B   	6660      
21	9	Sede - 00009 de Delegación Académica 9 de Julio	\N	AR	B   	6500      
21	10	Sede - 00010 de Delegacion Academica Mercedes	\N	AR	B   	6600      
21	11	Sede - 00011 de Delegación Académica Capital Federal	\N	AR	C   	1412      
22	1	SedePrincipal - MAR DEL PLATA	\N	AR	B   	7600      
22	2	Sede - 00002 de Facultad de Ciencias Agrarias	\N	AR	B   	7620      
23	1	Sede Principal POSADAS	\N	AR	N   	3300      
23	2	Subsede ELDORADO	\N	AR	N   	3380      
23	3	Subsede OBERA	\N	AR	N   	3360      
24	1	SedePrincipal - NORDESTE	\N	AR	W   	3400      
24	2	Sede 00002 -Resistencia -Chaco	\N	AR	H   	3500      
24	3	Sede 00003 -Presidencia Roque Sáenz Peña -Chaco	\N	AR	H   	3700      
24	4	Sede  00004 -Paso de los Libres- de Carreras a Término en Comercio Exterior	\N	AR	W   	3230      
24	5	Sede  00005 -Curuzú Cuatiá-de Instituto de Administración de Empresas Agropecuarias	\N	AR	W   	3460      
25	1	SedePrincipal - QUILMES	\N	AR	B   	1876      
26	1	SedePrincipal - RIO CUARTO	\N	AR	X   	5800      
27	1	SedePrincipal - ROSARIO	\N	AR	S   	2000      
27	2	Sede ZAVALLA - 00002 Facultad de Ciencias Agrarias	\N	AR	S   	2123ZAV   
27	3	Sede CASILDA - 00003 Facultad de Ciencias Veterinarias	\N	AR	S   	2170      
28	1	SedePrincipal - SALTA	\N	AR	A   	4400      
28	2	Sede - 00002 de Sede Regional Orán	\N	AR	A   	4530      
28	3	Sede - 00003 de Sede Regional Tartagal	\N	AR	A   	4560      
29	1	SedePrincipal - SAN JUAN	\N	AR	J   	5400      
29	2	Sede - 00002 de Facultad de Ciencias Sociales	\N	AR	J   	5577      
30	1	SedePrincipal - SAN LUIS	\N	AR	D   	5700      
31	1	SedePrincipal - SANTIAGO DEL ESTERO	\N	AR	G   	4200      
32	1	SedePrincipal - SUR	\N	AR	B   	8000      
32	2	Sede - 00002 de Departamento de Derecho	\N	AR	S   	3000      
33	1	Sede Principal - TUCUMAN	\N	AR	T   	4000      
34	2	Extensión Pilar	\N	AR	X   	5972      
34	1	SedePrincipal - VILLA MARIA	\N	AR	X   	5903      
34	3	Extensión Laboulaye	\N	AR	X   	\N
35	1	SedePrincipal - UTN	\N	AR	C   	1427      
35	2	Sede - 00002 de Facultad Regional Avellaneda	\N	AR	B   	1870      
35	3	Sede - 00003 de Facultad Regional Bahía Blanca	\N	AR	B   	8000      
35	4	Sede - 00004 de Facultad Regional Buenos Aires	\N	AR	C   	1179      
35	5	Sede - 00005 de Facultad Regional Concepción del Uruguay	\N	AR	E   	3260      
35	6	Sede - 00006 de Facultad Regional Córdoba	\N	AR	X   	5016      
35	7	Sede - 00007 de Facultad Regional Delta	\N	AR	B   	2804      
35	8	Sede - 00008 de Facultad Regional General Pacheco	\N	AR	B   	1618      
35	9	Sede - 00009 de Facultad Regional Haedo	\N	AR	B   	1706      
35	10	Sede - 00010 de Facultad Regional La Plata	\N	AR	B   	1900      
35	11	Sede - 00011 de Facultad Regional Mendoza	\N	AR	M   	5500      
35	12	Sede - 00012 de Facultad Regional Paraná	\N	AR	E   	3102      
35	13	Sede - 00013 de Facultad Regional Resistencia	\N	AR	H   	3500      
35	14	Sede - 00014 de Facultad Regional Rosario	\N	AR	S   	2000      
35	15	Sede - 00015 de Facultad Regional San Francisco	\N	AR	X   	2400      
35	16	Sede - 00016 de Facultad Regional San Nicolás	\N	AR	B   	2900      
35	17	Sede - 00017 de Facultad Regional San Rafael	\N	AR	M   	5600      
35	18	Sede - 00018 de Facultad Regional Santa Fé	\N	AR	S   	3000      
35	19	Sede - 00019 de Facultad Regional Tucumán	\N	AR	T   	4000      
35	20	Sede - 00020 de Facultad Regional Villa María	\N	AR	X   	5902      
35	21	Sede - 00021 de Unidad Académica Concordia	\N	AR	E   	3200      
35	22	Sede - 00022 de Unidad Académica Confluencia	\N	AR	Q   	8318      
35	23	Sede - 00023 de Unidad Académica Chubut	\N	AR	U   	9120      
35	24	Sede - 00024 de Unidad Académica La Rioja	\N	AR	F   	5300      
35	25	Sede - 00025 de Unidad Académica Rafaela	\N	AR	S   	2302      
35	26	Sede - 00026 de Unidad Académica Reconquista	\N	AR	S   	3560      
35	27	Sede - 00027 de Unidad Académica Rio Gallegos	\N	AR	Z   	9400      
35	28	Sede - 00028 de Unidad Académica Río Grande	\N	AR	V   	9420      
35	29	Sede - 00029 de Unidad Académica Trenque Lauquen	\N	AR	B   	6400      
35	30	Sede - 00030 de Unidad Académica Venado Tuerto	\N	AR	S   	2600      
36	1	SedeRectorado - TRES DE FEBRERO	\N	AR	B   	1678      
37	1	SedePrincipal - IESE	\N	AR	C   	1425      
37	2	Sede - 00002 de Unidad Académica Colegio Militar de la Nación	\N	AR	B   	1684      
37	3	Sede - 00003 de Unidad Académica Escuela de Defensa Nacional (Asociada)	\N	AR	C   	1084      
38	1	SedePrincipal - IAERONAUTICO	\N	AR	X   	5000      
38	2	Sede - 00002 de Escuela de Ingeniería Aeronáutica	\N	AR	C   	1084      
39	1	SedePrincipal - INAVAL	\N	AR	C   	1429      
39	2	Sede - 00002 de Unidad Academica Escuela Nacional de Nautica	\N	AR	C   	1104      
39	3	Sede - 00003 de Unidad Académica Escuela Naval Militar	\N	AR	B   	1929      
39	4	Sede - 00004 de Unidad Académica Escuela de Oficiales de la Armada	\N	AR	B   	8109      
39	5	Sede - 00005 de Unidad Académica Escuela de Ciencias del Mar	\N	AR	C   	1408      
39	6	Sede - 00006 de Unidad Academica Escuela Ciencias del Mar	\N	AR	C   	1404      
40	1	SedePrincipal - IPOLICIA	\N	AR	C   	1427      
41	1	SedePrincipal - La Plata	\N	AR	B   	1900      
42	1	SedePrincipal - CEMA	\N	AR	C   	1000      
42	2	Sede - 00002 de Departamento de Economía	\N	AR	B   	1054      
43	1	SedePrincipal - ESCUELA DE TEOLOGIA	\N	AR	B   	7600      
44	1	SedePrincipal - IUNA	\N	AR	C   	1115      
44	2	Departamento de Artes Visuales "Prilidiano Pueyrredón"	\N	AR	B   	1054      
44	3	Departamento de Artes del Movimiento "Maria Ruanova"	\N	AR	C   	1020      
45	1	SedePrincipal - ITBA	\N	AR	C   	1427      
46	1	Sede Principal - CAPITAL FEDERAL	\N	AR	C   	1427      
47	1	Sede Principal - CAPITAL FEDERAL	\N	AR	C   	1000      
48	1	Sede Principal - UCA CIUDAD BUENOS AIRES	\N	AR	C   	1107      
101	3	SAN JUAN	\N	AR	J   	\N
48	3	CENTRO REGIONAL PERGAMINO - BS. AS.	\N	AR	B   	2701      
48	4	SEDE ROSARIO - SANTA FE	\N	AR	S   	2000      
101	4	CORDOBA	\N	AR	X   	\N
48	6	SEDE PARANÁ - ENTRE RÍOS	\N	AR	E   	3102      
48	7	SEDE MENDOZA	\N	AR	M   	5500      
102	1	Sede Principal CAPITAL FEDERAL	\N	AR	C   	\N
49	1	SedePrincipal - ABIERTA INTERAMERICANA	\N	AR	C   	1069      
49	2	Sede - 00002 de Facultad de Tecnología Informática	\N	AR	C   	1147      
49	3	Sede - 00003 de Facultad de Arquitectura.	\N	AR	C   	1428      
49	4	Sede - 00004 de Facultad de Desarrollo e Investigación Educativos.	\N	AR	B   	1712      
49	5	Sede - 00005 de Facultad de Motricidad Humana y Deportes	\N	AR	C   	1182      
50	1	Sede Principal - Va. LIB. SAN MARTIN (ENTRE RIOS)	\N	AR	E   	3103      
51	1	SedePrincipal - UADE	\N	AR	C   	1427      
51	2	Sede - 00002 de Facultad de Ciencias Agrarias	\N	AR	C   	1107      
52	1	SedePrincipal - KENNEDY	\N	AR	C   	1427      
52	2	Sede - 00002 de Escuela de Graduados	\N	AR	C   	1041      
53	1	SEDE CENTRAL - Mar de Ajo	\N	AR	B   	7109      
53	2	Anexo -  00002 Dolores	\N	AR	B   	7100      
53	4	Anexo -  00004 General Madariaga	\N	AR	B   	7163      
53	5	Anexo -  00005 Pinamar	\N	AR	B   	7167      
53	6	Anexo -  00006 Mar del Plata	\N	AR	B   	7600      
54	1	Sede Principal - CAPITAL FEDERAL	\N	AR	C   	1053      
54	2	Sede PILAR	\N	AR	B   	1629      
54	3	Sede ROSARIO	\N	AR	S   	2000      
56	1	SedePrincipal - CAECE	\N	AR	C   	1198      
56	2	Sede - 00002 de Departamento de Sistemas	\N	AR	C   	1084      
57	1	Sede Principal - CORDOBA	\N	AR	X   	5000      
58	1	Sede Principal - SAN JUAN	\N	AR	J   	5400      
59	1	SedePrincipal - CATOLICA DE LA PLATA	\N	AR	B   	1900      
59	2	Sede - 00002 de Unidad Académica Bernal. Facultad de Arquitectura	\N	AR	B   	1876      
60	1	SedePrincipal - CATOLICA DE SALTA	\N	AR	A   	4400      
60	2	Sede - 00002 de Subsede Académica Buenos Aires	\N	AR	C   	1041      
61	1	Sede Principal - SANTA FE	\N	AR	S   	3000      
61	2	SUBSEDE POSADAS	\N	AR	N   	3300      
62	1	SedePrincipal - CATOLICA DE SANTIAGO DEL ESTERO	\N	AR	G   	4200      
62	2	Sede - 00002 de Departamento Académico San Salvador	\N	AR	Y   	4600      
62	3	Sede - 00003 de Departamento Académico Buenos Aires	\N	AR	B   	1636      
62	4	Sede - 00004 de Departamento Académico Rafaela	\N	AR	S   	2300      
63	1	SedePrincipal - CHAMPAGNAT	\N	AR	M   	5501      
63	2	Sede - 00002 de Facultad de Derecho	\N	AR	M   	5500      
64	1	SedePrincipal - BELGRANO	\N	AR	C   	1427      
64	2	Sede - 00002 de Escuela de Economía	\N	AR	C   	1060      
65	1	Sede Principal - UCES BUENOS AIRES	\N	AR	C   	1000      
79	2	Delegación Gobernador Virasoro	\N	AR	W   	\N
79	3	Delegación Monte Caseros	\N	AR	W   	\N
79	4	Delegación Santa Isabel	\N	AR	P   	\N
79	5	Delegación Presidencia Roque Saenz Peña	\N	AR	H   	\N
14	3	Localización CASTEX	\N	AR	L   	\N
66	1	Sede Principal - CONCEPCION DEL URUGUAY	\N	AR	E   	3260      
66	2	Centro Regional GUALEGUAYCHU	\N	AR	E   	2820      
67	1	SEDE PRINCIPAL  - MENDOZA	\N	AR	M   	5500      
68	1	Sede Principal - CAPITAL FEDERAL	\N	AR	C   	1406      
69	1	SedePrincipal - MENDOZA	\N	AR	M   	5500      
70	1	SedePrincipal - MORON	\N	AR	B   	1708      
71	1	SedePrincipal - PALERMO	\N	AR	C   	1427      
71	2	Sede - 00002 de Facultad de Arquitectura	\N	AR	C   	1425      
71	3	Sede - 00003 de Facultad de Ciencias Económicas y Empresariales	\N	AR	C   	1175      
72	1	Sede Principal - SAN ANDRES	\N	AR	B   	1644      
14	4	Localización 9 de Julio	\N	AR	B   	6500      
73	1	SedePrincipal - ACONCAGUA	\N	AR	M   	5500      
73	2	Sede - Tunuyan	\N	AR	M   	5560      
73	3	Sede - Facultad de Ciencias Sociales y Administrativas	\N	AR	M   	5500      
74	1	Sede Principal - UCEL	\N	AR	S   	2000      
75	1	SedePrincipal - DEL CINE	\N	AR	C   	1000      
76	1	SedePrincipal - MUSEO SOCIAL	\N	AR	C   	1427      
76	2	Sede - 00002 Facultad de Ciencias Economicas, de la Administracion y de los Negocios	\N	AR	C   	1042      
76	3	Sede - 00003 Facultad de Ciencias Juridicas y Politicas	\N	AR	C   	1041      
77	1	SedePrincipal - SANTO TOMAS	\N	AR	T   	4000      
77	2	Sede - 00002 de Centro Universitario de Concepción	\N	AR	T   	4146      
77	3	Sede - 00003 de Centro de Estudios Institucionales (Bs.As)	\N	AR	C   	1020      
78	1	Sede Principal - Ciudad de Buenos Aires	\N	AR	C   	1427      
44	4	Departamento de Artes Audiovisuales	\N	AR	B   	1054      
23	4	Delegación CIUDAD DE BUENOS AIRES	\N	AR	C   	1033      
95	2	Sede  Basavilbaso	\N	AR	E   	3170      
95	3	Sede Chajarí	\N	AR	E   	\N
78	6	Subsede Mercedes - Campus N. S. de Luján	\N	AR	B   	6600      
78	7	Subsede Pilar - Campus N. S. del Pilar	\N	AR	B   	1629      
78	8	Delegación Corrientes - Campus San Roque Gonzalez de Santa Cruz	\N	AR	W   	3340      
79	1	SedePrincipal - CUENCA DEL PLATA	\N	AR	W   	3400      
80	1	Sede Principal - MAR DEL PLATA	\N	AR	B   	7600      
81	1	Sede Principal - CAPITAL FEDERAL	\N	AR	C   	1034      
82	1	SedePrincipal - SIGLO XXI	\N	AR	X   	5000      
83	1	SedePrincipal - BAR ILAN	\N	AR	C   	1000      
83	2	Sede - 00002 de Facultad de Ciencias Biologicas	\N	AR	C   	1198      
84	1	SedePrincipal - MAZA	\N	AR	M   	5519      
84	2	Sede - 00002 de Facultad de Ciencias Empresariales	\N	AR	M   	5500      
85	1	SedePrincipal - MAIMONIDES	\N	AR	C   	1000      
85	2	Sede - 00002 de Facultad de Medicina	\N	AR	M   	5500      
85	3	Sede - 00003 de Escuela de Comunicación Multimedial y Gráfica	\N	AR	C   	1411      
87	1	SedePrincipal - DI TELLA	\N	AR	C   	1428      
88	1	Sede Principal - CAPITAL FEDERAL	\N	AR	C   	1425      
89	1	Sede Principal - POSADAS	\N	AR	N   	3300      
90	1	Sede Principal - CAPITAL FEDERAL	\N	AR	C   	1095      
91	1	SedePrincipal - CAPITAL FEDERAL	\N	AR	C   	1425      
92	1	Sede Principal - CAPITAL FEDERAL	\N	AR	C   	\N
93	1	Sede Principal - ROSARIO	\N	AR	C   	\N
94	1	Sede Principal - BOLOGNA	\N	\N	\N	\N
95	1	SedePrincipal - AUTONOMA DE ENTRE RIOS	\N	\N	\N	\N
96	1	Sede Principal - IDEA ROSARIO	\N	AR	S   	2000      
97	1	SedePrincipal - SEGURIDAD MARITIMA	\N	\N	\N	\N
99	1	Sede Principal - JUNIN	\N	AR	B   	6000      
100	1	Sede Principal - CAPITAL FEDERAL	\N	AR	C   	\N
101	1	Sede Principal - CAPITAL FEDERAL	\N	AR	C   	\N
56	3	SEDE MAR DEL PLATA	\N	AR	B   	7600      
78	9	Delegación Posadas	\N	AR	N   	3300      
78	10	Subsede Córdoba	\N	AR	X   	\N
78	11	Subsede Venado Tuerto	\N	AR	S   	2600      
78	12	Subsede Bahía Blanca	\N	AR	B   	\N
78	13	Subsede Salta	\N	AR	A   	4400      
78	14	Subsede Gualeguaychú	\N	AR	E   	2820      
78	15	Subsede Rosario	\N	AR	S   	2000      
78	16	Subsede Santa Rosa	\N	AR	L   	6300      
78	17	Subsede Río Grande - Ushuaia	\N	AR	V   	9420      
56	4	SAN ISIDRO	\N	AR	B   	1642      
68	2	Sede COMAHUE -CIPOLLETTI	\N	AR	R   	8324      
3	2	Subsede AZUL	\N	AR	B   	7300      
3	3	Subsede OLAVARRIA	\N	AR	B   	7400      
96	2	Sede IDEA BUENOS AIRES	\N	AR	C   	1033      
65	7	Subsede RAFAELA	\N	AR	S   	2302      
65	8	Subsede UTN - SAN FRANCISCO	\N	AR	X   	2400      
65	9	Subsede SAN ISIDRO - BUENOS AIRES	\N	AR	B   	1642      
74	2	Sede del IUCS BARCELO - BUENOS AIRES	\N	AR	C   	1033      
27	4	BAHIA BLANCA - BUENOS AIRES	\N	AR	B   	8000      
95	4	Sede Concepción del Uruguay	\N	AR	E   	3260      
95	5	Sede Crespo	\N	AR	E   	\N
95	6	Sede Diamante	\N	AR	E   	3105      
95	7	Sede Federación	\N	AR	E   	\N
95	8	Sede Gualeguay	\N	AR	E   	2840      
95	9	Sede Gualeguaychú	\N	AR	E   	2820      
95	10	Sede La Picada	\N	AR	E   	\N
95	11	Sede Oro Verde	\N	AR	E   	\N
95	12	Sede Ramirez	\N	AR	E   	\N
95	13	Sede Villaguay	\N	AR	E   	\N
51	3	Sede - 00003 de Escuela de Direccion de Empresas	\N	AR	C   	\N
36	2	Sede Caseros	\N	AR	B   	1678      
36	3	Sede Aromos	\N	AR	B   	1678      
36	4	Sede Centro Cultural Borges	\N	AR	B   	1678      
36	5	Sede Saenz Peña	\N	AR	B   	1678      
24	6	Sede 00006 -Paso de los Libres -Instituto de Comercio Exterior	\N	AR	W   	\N
71	4	Sede - 0004 Escuela de Educacion Superior	\N	\N	\N	\N
97	2	Sede - Olivos	\N	AR	B   	1636      
41	2	Capital Federal	\N	AR	B   	1054      
58	2	Sede MENDOZA	\N	AR	M   	5500      
58	3	Sede SAN LUIS	\N	AR	D   	5700      
102	2	Localización MARTINEZ (PCIA. BUENOS AIRES)	\N	AR	B   	\N
103	1	Sede Principal - CHILECITO	\N	AR	F   	5360      
47	2	Sede LA RIOJA	\N	AR	F   	5300      
47	3	Sede SANTO TOME - CORRIENTES	\N	AR	W   	3340      
55	3	Sede Campus	\N	AR	X   	5147      
55	4	Sede Centro	\N	AR	X   	5000      
88	2	Localización SAN ISIDRO - BS. AS.	\N	AR	B   	1642      
89	2	Localización OBERA	\N	AR	N   	3360      
99	2	Sede Pergamino	\N	AR	B   	2701      
18	3	Centro Científico Regional Catuna	\N	AR	F   	\N
16	6	Localizacion Petrel - Agencia ACA	\N	AR	V   	9410      
17	2	Localización CNEL. PRINGLES	\N	AR	B   	\N
17	3	Localización JUNIN	\N	AR	B   	\N
20	2	Subsede CORRIENTES	\N	AR	W   	\N
20	3	Subsede SANTA FE	\N	AR	S   	3560      
33	2	Localización AGUILARES	\N	AR	T   	4152      
66	3	Centro Regional FEDERACION	\N	AR	E   	3102      
66	4	Centro Regional PARANA	\N	AR	E   	3102      
66	5	Localización ROSARIO	\N	AR	S   	2000      
47	4	Localización ROSARIO (UCEL)	\N	AR	S   	2000      
37	4	Rectorado	\N	AR	B   	1054      
80	2	Localización TANDIL	\N	AR	B   	7000      
8889	1119	Sede 01	\N	AR	A   	4566      
8889	2229	Sede 02	\N	AR	A   	4566      
\.


--
-- Data for TOC entry 17 (OID 56992522)
-- Name: soe_edificios; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY soe_edificios (edificio, institucion, sede, nombre, calle, numero, piso, depto) FROM stdin;
1	1	1	Edificio de SedePrincipal - UBA	Viamonte	444	\N	\N
2	2	1	Edificio de SedePrincipal - CATAMARCA	Esquiú	612	\N	\N
3	3	1	Edificio de SedePrincipal - CENTRO	Gral. Pinto	99	\N	\N
4	4	1	Edificio de SedePrincipal - COMAHUE	Buenos Aires	1400	\N	\N
5	5	1	Edificio de SedePrincipal - CORDOBA	Obispo Trejo y Sanabria	242	\N	\N
6	6	1	Edificio de SedePrincipal - CUYO	Parque Gral. San Martín	\N	\N	\N
7	7	1	Edificio de SedePrincipal - ENTRE RIOS	Eva Perón	24	\N	\N
8	8	1	Edificio de SedePrincipal - FORMOSA	Avda. Gobernador Gutnisky	3200	\N	\N
9	9	1	Edificio de SedePrincipal - SAN MARTIN	Calle 91	3391	\N	\N
10	10	1	Edificio de SedePrincipal - SARMIENTO	Julio A. Roca	850	\N	\N
11	11	1	Edificio de SedePrincipal - JUJUY	Bolivia	235	\N	\N
12	12	1	Edificio de SedePrincipal - MATANZA	Florencio Varela	1903	\N	\N
13	13	1	Edificio de SedePrincipal - LANUS	29 de Septiembre	3901	\N	Recto
14	14	1	Edificio de SedePrincipal - LA PAMPA	Coronel Gil	353	3°	Estad
15	15	1	Edificio de SedePrincipal - LA PATAGONIA AUSTRAL	Lisandro de la Torre	860	\N	\N
16	16	1	Edificio de SedePrincipal - LA PATAG. SAN JUAN BOSCO	Ruta Provincial Nº 1 - km 4	\N	4	Dir.
17	18	1	Edificio de SedePrincipal - LA RIOJA	Avda. Laprida y Vicente Bustos	\N	\N	\N
18	19	1	Edificio de SedePrincipal - LITORAL	Bv. Pellegrini	2750	\N	\N
19	20	1	Edificio de SedePrincipal - LOMAS DE ZAMORA	Camino de Cintura - km. 2	\N	\N	\N
20	21	1	Edificio de SedePrincipal - LUJAN	Ruta 5 - km. 70	\N	\N	\N
21	22	1	Edificio de SedePrincipal - MAR DEL PLATA	Bv. Juan Bautista Alberdi	2695	\N	\N
22	23	1	Edificio de SedePrincipal - MISIONES	Ruta 12 - km. 7,5	\N	\N	\N
23	24	1	Edificio de SedePrincipal - NORDESTE	25 de Mayo	868	\N	Decan
24	25	1	Edificio de SedePrincipal - QUILMES	Roque Saenz Peña	180	\N	\N
25	26	1	Edificio de SedePrincipal - RIO CUARTO	Ruta 6 y 36 - km. 603	\N	\N	\N
26	27	1	Edificio de SedePrincipal - ROSARIO	Córdoba	1814	\N	\N
27	28	1	Edificio de SedePrincipal - SALTA	Buenos Aires	177	\N	\N
28	29	1	Edificio de SedePrincipal - SAN JUAN	Avda. José Ignacio de la Roza	391	\N	\N
29	30	1	Edificio de SedePrincipal - SAN LUIS	Ejercito de los Andes	950	\N	\N
30	31	1	Edificio de SedePrincipal - SANTIAGO DEL ESTERO	Avda. Balgrano Sur	1912	\N	\N
31	32	1	Edificio de SedePrincipal - SUR	Avda. Colón	80	\N	\N
32	33	1	Edificio de SedePrincipal - TUCUMAN	Ayacucho	491	\N	\N
33	34	1	Edificio de SedePrincipal - VILLA MARIA	Lisandro de la Torre	252	\N	\N
34	35	1	Edificio de SedePrincipal - UTN	Sarmiento	440	\N	\N
35	36	1	Edificio de SedePrincipal - TRES DE FEBRERO	Av.San Martin	2921	\N	\N
36	37	1	Edificio de SedePrincipal - IESE	Avda. Luis María Campos	230	\N	\N
37	38	1	Edificio de SedePrincipal - IAERONAUTICO	Avda. Fuerza Aerea Argentina	6500	\N	\N
38	39	1	Edificio de SedePrincipal - INAVAL	Av. del Libertador	8209	\N	\N
39	40	1	Edificio de SedePrincipal - IPOLICIA	Rosario	532	\N	\N
40	41	1	Edificio de SedePrincipal - NOTARIAL	Calle 51	435	\N	\N
41	42	1	Edificio de SedePrincipal - CEMA	Avda. Córdoba	637	\N	\N
42	43	1	Edificio de SedePrincipal - ESCUELA DE TEOLOGIA	Pasaje Catedral	1750	1	\N
43	44	1	Edificio de SedePrincipal - IUNA	Paraguay	786	1	\N
44	45	1	Edificio de SedePrincipal - ITBA	Avda. Eduardo Madero	351	\N	\N
45	46	1	Edificio de SedePrincipal - FAVALORO	Solís	453	\N	\N
46	47	1	Edificio de SedePrincipal - BARCELO	Larrea	770	\N	\N
47	48	1	Edificio de SedePrincipal - UCA	Av. Alicia Moreau de Justo	1300	2	Recto
48	49	1	Edificio de SedePrincipal - ABIERTA INTERAMERICANA	Chacabuco	90	1	\N
49	50	1	Edificio de SedePrincipal - ADVENTISTA	25 de Mayo	99	\N	\N
50	51	1	Edificio de SedePrincipal - UADE	Lima	761	\N	\N
51	52	1	Edificio de SedePrincipal - KENNEDY	Bartolomé Mitre	1407	\N	\N
52	53	1	Edificio de SedePrincipal - ATLANTIDA	Diag. Rivadavia	515	\N	\N
53	54	1	Edificio de SedePrincipal - AUSTRAL	Avda. Juan de Garay	125	\N	\N
54	56	1	Edificio de SedePrincipal - CAECE	Tte. Gral. J.D.Perón	2933	\N	\N
55	57	1	Edificio de SedePrincipal - CATOLICA CORDOBA	Obispo Trejo y Sanabria	323	\N	\N
56	58	1	Edificio de SedePrincipal - CATOLICA DE CUYO	Avda. José Ignacio de la Roza	1516	\N	\N
57	59	1	Edificio de SedePrincipal - CATOLICA DE LA PLATA	Calle 13	1227	\N	\N
58	60	1	Edificio de SedePrincipal - CATOLICA DE SALTA	Campo Casdtañares	s/n	\N	\N
59	61	1	Edificio de SedePrincipal - CATOLICA DE SANTA FE	Echagüe	7151	\N	\N
60	62	1	Edificio de SedePrincipal - CATOLICA DE SANTIAGO DEL ESTERO	Avda. Alsina y Dalmacio Velez Sarsfield	s/n	\N	\N
61	63	1	Edificio de SedePrincipal - CHAMPAGNAT	San Martín	866	\N	\N
62	64	1	Edificio de SedePrincipal - BELGRANO	Zabala	1851	\N	\N
63	65	1	Edificio de SedePrincipal - CIENCIAS EMPRESARIALES	Paraguay	1345	\N	\N
64	66	1	Edificio de SedePrincipal - CONCEPCIO DEL URUGUAY	8 de Junio	522	\N	\N
65	67	1	Edificio de SedePrincipal - CONGRESO	Avda. Mitre	617	\N	\N
66	68	1	Edificio de SedePrincipal - FLORES	Camacuá	282	-	-
67	69	1	Edificio de SedePrincipal - MENDOZA	Avda. Bulogne Sur Mer	665	\N	\N
68	70	1	Edificio de SedePrincipal - MORON	Cabildo	134	\N	\N
69	71	1	Edificio de SedePrincipal - PALERMO	Mario Bravo	1302	\N	\N
70	72	1	Edificio de SedePrincipal - SAN ANDRES	Vito Dumas	284	\N	\N
71	73	1	Edificio de SedePrincipal - ACONCAGUA	Catamarca	147	\N	\N
72	74	1	Edificio de SedePrincipal - CENTRO EDUCATIVO LATINOAMERICANO	Avda. Pellegrini	1352	\N	\N
73	75	1	Edificio de SedePrincipal - DEL CINE	Pje. J. M. Giuffa	330	\N	\N
74	76	1	Edificio de SedePrincipal - MUSEO SOCIAL	Avda. Corrientes	1723	\N	\N
75	77	1	Edificio de SedePrincipal - SANTO TOMAS	9 de Julio	165	\N	\N
76	78	1	Edificio de SedePrincipal - SALVADOR	Viamonte	1856	\N	\N
77	79	1	Edificio de SedePrincipal - CUENCA DEL PLATA	Plácido Martínez	964	\N	\N
78	80	1	Edificio de Sede Principal - F.A.S.T.A.	Gascón	3145	\N	\N
79	81	1	Edificio de Sede Principal - MARINA MERCANTE	Rivadavia	2258	\N	\N
80	82	1	Edificio de SedePrincipal - SIGLO XXI	Rondeau	165	\N	\N
81	83	1	Edificio de SedePrincipal - BAR ILAN	Teniente General Juan Domingo Perón	3460	\N	\N
82	84	1	Edificio de SedePrincipal - MAZA	Avda. Acceso Este Lateral Sur	2245	\N	Secre
83	85	1	Edificio de SedePrincipal - MAIMONIDES	Talcahuano	456	\N	\N
84	23	4	DELEGACION BUENOS AIRES	SARMIENTO	1462	\N	\N
85	88	1	Edificio de SedePrincipal - CEMIC	Sánchez de Bustamante	2560	\N	\N
86	89	1	Edificio de SedePrincipal - GASTON DACHARY	Salta	1968	\N	\N
87	90	1	Edificio de SedePrincipal - ISALUD	Venezuela	925/3	\N	\N
88	91	1	Edificio de SedePrincipal - ESEADE	Uriarte	2472	\N	\N
89	1	1	\N	\N	\N	\N	\N
90	1	1	\N	\N	\N	\N	\N
91	1	1	\N	\N	\N	\N	\N
92	1	1	\N	\N	\N	\N	\N
93	1	1	\N	\N	\N	\N	\N
94	1	1	\N	\N	\N	\N	\N
95	1	2	\N	\N	\N	\N	\N
96	1	1	\N	\N	\N	\N	\N
97	1	1	\N	\N	\N	\N	\N
98	1	1	\N	\N	\N	\N	\N
99	1	1	\N	\N	\N	\N	\N
100	1	1	\N	\N	\N	\N	\N
101	1	1	\N	\N	\N	\N	\N
102	1	1	\N	\N	\N	\N	\N
103	1	1	\N	\N	\N	\N	\N
104	1	1	\N	\N	\N	\N	\N
105	2	1	Edificio de Sede 00001 - Avenida Belgrano y Maestro Qiroga	Avenida Belgrano y Maestro Qiroga	\N	\N	\N
106	2	1	Edificio de Sede 00001 - Maximo Victoria	Maximo Victoria	55	\N	\N
107	2	1	Edificio de Sede 00001 - Maestro Quiroga	Maestro Quiroga	s/n	\N	\N
108	2	1	Edificio de Sede 00001 - Avenida Belgrano	Avenida Belgrano	300	\N	\N
109	2	1	Edificio de Sede 00001 - Avenidad Belgrano	Avenidad Belgrano	300	\N	\N
110	2	1	Edificio de Sede 00001 - Maximo Victoria 1era Cuadra	Maximo Victoria 1era Cuadra	s/n	\N	\N
111	2	1	\N	\N	\N	\N	\N
112	3	1	\N	\N	\N	\N	\N
113	3	1	\N	\N	\N	\N	\N
114	3	1	\N	\N	\N	\N	\N
115	3	1	\N	\N	\N	\N	\N
116	3	1	\N	\N	\N	\N	\N
117	3	1	\N	\N	\N	\N	\N
118	3	1	\N	\N	\N	\N	\N
119	3	1	\N	\N	\N	\N	\N
120	3	1	\N	\N	\N	\N	\N
121	4	2	Edificio de Sede 00002 - Ruta 151  Km 12,5	Ruta 151  Km 12,5	\N	\N	\N
122	4	3	Edificio de Sede 00003 - Pasaje de la Paz	Pasaje de la Paz	235	\N	\N
123	4	4	Edificio de Sede 00004 - 25 de Mayo y Reconquista	25 de Mayo y Reconquista	S/N	\N	\N
124	4	5	Edificio de Sede 00005 - Av.12 de Julio y Rahue	Av.12 de Julio y Rahue	\N	\N	\N
125	4	6	Edificio de Sede 00006 - Mendoza	Mendoza	1050	\N	\N
126	4	1	Edificio de Sede 00001 - Buenos Airres	Buenos Airres	1400	\N	\N
127	4	6	Edificio de Sede 00006 - Mendoza	Mendoza	2150	\N	\N
128	4	7	Edificio de Sede 00007 - Irigoyen	Irigoyen	2000	\N	\N
129	4	8	Edificio de Sede 00008 - Mons.Esandi y Ayacucho	Mons.Esandi y Ayacucho	\N	\N	\N
130	4	9	Edificio de Sede 00009 - B° Jardín Botánico- calle Quintral	B° Jardín Botánico- calle Quintral	s/nº	\N	\N
131	4	10	Edificio de Sede 00010 - Belgrano	Belgrano	325	\N	\N
132	4	11	Edificio de Sede 00011 - Guemes	Guemes	1030	\N	\N
133	4	7	Edificio de Sede 00007 - Toschi y Arrayanes	Toschi y Arrayanes	\N	\N	\N
134	4	12	Edificio de Sede 00012 - bb	bb	12	1	w
135	4	13	Edificio de Sede 00013 - Guemes y Eisntein	Guemes y Eisntein	\N	\N	\N
136	4	1	\N	\N	\N	\N	\N
137	5	1	Edificio de Sede 00001 - Av. Valparaíso y Rogelio Martínez - C.U.	Av. Valparaíso y Rogelio Martínez - C.U.	\N	\N	\N
138	5	1	Edificio de Sede 00001 - Av. Velez Sarfiel	Av. Velez Sarfiel	264	\N	\N
139	5	1	Edificio de Sede 00001 - Av.Velez Sarfield	Av.Velez Sarfield	299	\N	\N
140	5	1	Edificio de Sede 00001 - Pabellón Argentina, ala 1	Pabellón Argentina, ala 1	\N	\N	\N
141	5	1	Edificio de Sede 00001 - Av. Valparaíso - Ciudad Universitaria	Av. Valparaíso - Ciudad Universitaria	s/n	\N	\N
142	5	1	Edificio de Sede 00001 - Obispo Trejo	Obispo Trejo	242	\N	\N
143	5	1	Edificio de Sede 00001 - Enrique Barros s/n C. Universitaria	Enrique Barros s/n C. Universitaria	\N	\N	\N
144	5	1	Edificio de Sede 00001 - Pabellón Residencial - C. Universitaria	Pabellón Residencial - C. Universitaria	\N	\N	\N
145	5	1	Edificio de Sede 00001 - Av. Velez Sarfield	Av. Velez Sarfield	187	\N	\N
146	5	1	Edificio de Sede 00001 - Pabellón Perú - Ciudad Universitaria	Pabellón Perú - Ciudad Universitaria	\N	\N	\N
147	5	1	Edificio de Sede 00001 - Enrique Barros - Ciudad Universitaria	Enrique Barros - Ciudad Universitaria	\N	\N	\N
148	5	1	Edificio de Sede 00001 - Pabellón Argentina - Ciudad Universitari	Pabellón Argentina - Ciudad Universitari	\N	\N	\N
149	5	1	Edificio de Sede 00001 - Av. Haya de la Torre - Ciud. Universitar	Av. Haya de la Torre - Ciud. Universitar	\N	\N	\N
150	5	1	Edificio de Sede 00001 - Pabellón Argentina (ala dcha, P.B) C.U.	Pabellón Argentina (ala dcha, P.B) C.U.	\N	\N	\N
151	5	1	Edificio de Sede 00001 - Pabellón Argentina (ala Dcha. P. B)	Pabellón Argentina (ala Dcha. P. B)	\N	\N	\N
152	5	1	Edificio de Sede 00001 - Pabellón Argentina - Ciud. Universitaria	Pabellón Argentina - Ciud. Universitaria	\N	\N	\N
153	5	1	Edificio de Sede 00001 - Av. Valparaíso y E. Barros	Av. Valparaíso y E. Barros	\N	\N	\N
154	5	1	Edificio de Sede 00001 - Ciudad Universitaria	Ciudad Universitaria	\N	\N	\N
155	5	1	Edificio de Sede 00001 - Av. Valparaíso y R. Martínez - C. Univer	Av. Valparaíso y R. Martínez - C. Univer	\N	\N	\N
156	5	1	Edificio de Sede 00001 - Velez Sarfield	Velez Sarfield	153	\N	\N
157	5	1	Edificio de Sede 00001 - Avenida Valparaíso	Avenida Valparaíso	s/n	\N	\N
158	5	1	\N	\N	\N	\N	\N
159	6	2	Edificio de Sede 00002 - Almirante Brown	Almirante Brown	500	\N	\N
160	6	1	Edificio de Sede 00001 - Centro Univ. Parque Gral. San Martín	Centro Univ. Parque Gral. San Martín	\N	\N	\N
161	6	3	Edificio de Sede 00003 - Ctro. Atómico Bariloche-Av. E. Bustillo	Ctro. Atómico Bariloche-Av. E. Bustillo	9500	\N	\N
162	6	1	Edificio de Sede 00001 - Centro Univ. - Parque Gral. San Martín	Centro Univ. - Parque Gral. San Martín	\N	\N	\N
163	6	4	Edificio de Sede 00004 - Bernardo de Yrigoyen	Bernardo de Yrigoyen	343	\N	\N
164	6	1	Edificio de Sede 00001 - Centro Univ.- Parque Gral. San Martín	Centro Univ.- Parque Gral. San Martín	\N	\N	\N
165	6	1	Edificio de Sede 00001 - Centro Univ. - Parque Gral San Martín	Centro Univ. - Parque Gral San Martín	\N	\N	\N
166	6	1	Edificio de Sede 00001 - Sobremonte	Sobremonte	81	\N	\N
167	6	1	Edificio de Sede 00001 - Centro Univ. - Parque Gral. San  Martín	Centro Univ. - Parque Gral. San  Martín	\N	\N	\N
168	6	1	Edificio de Sede 00001 - Centro Univ. - Parque Gral. San Martin	Centro Univ. - Parque Gral. San Martin	\N	\N	\N
169	6	1	Edificio de Sede 00001 - Facultad de Ciencias Médicas	Facultad de Ciencias Médicas	\N	\N	\N
170	6	4	Edificio de Sede 00004 - San Martín	San Martín	352	\N	\N
171	6	1	Edificio de Sede 00001 - Centro Univ.- Parque Gral. San Martín	Centro Univ.- Parque Gral. San Martín	242	\N	\N
172	6	1	Edificio de Sede 00001 - Facultad de Medicina	Facultad de Medicina	\N	\N	\N
173	6	1	\N	\N	\N	\N	\N
174	6	5	Edificio de Sede 00005 - Chapeaurouge	Chapeaurouge	163	\N	\N
175	6	4	Edificio de Sede 00004 - Bernardo de Irigoyen	Bernardo de Irigoyen	343	\N	\N
176	6	6	Edificio de Sede 00006 - Alem y Sarmiento	Alem y Sarmiento	\N	\N	\N
178	7	2	Edificio de Sede 00002 - Ruta Pcial.11, Km. 10.cc 24. Suc. 3	Ruta Pcial.11, Km. 10.cc 24. Suc. 3	\N	\N	\N
179	7	2	Edificio de Sede 00002 - Ruta Pcial. 11, Km. 10. cc 57 Suc. 3	Ruta Pcial. 11, Km. 10. cc 57 Suc. 3	\N	\N	\N
180	7	3	Edificio de Sede 00003 - Mons. Tavella	Mons. Tavella	1450	\N	\N
181	7	4	Edificio de Sede 00004 - 25 de Mayo	25 de Mayo	709	\N	\N
182	7	3	Edificio de Sede 00003 - Mons. Tavella	Mons. Tavella	1424	\N	\N
183	7	2	Edificio de Sede 00002 - Urquiza	Urquiza	552	\N	\N
184	7	2	Edificio de Sede 00002 - Rioja	Rioja	6	\N	\N
185	7	2	Edificio de Sede 00002 - Rivadavia	Rivadavia	106	\N	\N
186	7	1	Edificio de Sede 00001 - 8 de Junio	8 de Junio	600	\N	\N
187	7	1	\N	\N	\N	\N	\N
188	8	1	Edificio de Sede 00001 - Av. Gobernador Gutnisky	Av. Gobernador Gutnisky	3200	\N	\N
189	8	1	Edificio de Sede 00001 - GUTNISKY	GUTNISKY	3200	\N	\N
190	8	1	Edificio de Sede 00001 - Gutnisky	Gutnisky	3200	\N	\N
191	8	1	Edificio de Sede 00001 - Av. 9 de Julio	Av. 9 de Julio	1125	pb	\N
192	8	1	\N	\N	\N	\N	\N
193	8	1	Edificio de Sede 00001 - Av.Gutnizky	Av.Gutnizky	3200	\N	\N
194	8	1	\N	\N	\N	\N	\N
195	9	2	Edificio de Sede 00002 - Caseros	Caseros	2241	\N	\N
196	9	2	Edificio de Sede 00002 - Calle 78	Calle 78	3901	\N	\N
197	9	2	Edificio de Sede 00002 - Belgrano	Belgrano	3563	\N	\N
198	9	2	Edificio de Sede 00002 - Avda. Gral Paz	Avda. Gral Paz	s/n	\N	\N
199	9	2	Edificio de Sede 00002 - Avda. Gral Paz entre albarellos y Consti	Avda. Gral Paz entre albarellos y Consti	s/n	\N	\N
200	9	2	Edificio de Sede 00002 - Yapeyu	Yapeyu	2068	\N	\N
201	9	3	Edificio de Sede 00003 - Ramsay	Ramsay	2250	\N	\N
202	9	2	Edificio de Sede 00002 - Avda. Gral paz/Albarellos y constituyent	Avda. Gral paz/Albarellos y constituyent	s/n	\N	\N
203	9	4	Edificio de Sede 00004 - Paraná	Paraná	145	5	\N
204	11	1	Edificio de Sede 00001 - Alberdi	Alberdi	47	\N	\N
205	11	1	Edificio de Sede 00001 - Gorriti	Gorriti	237	\N	\N
206	11	1	Edificio de Sede 00001 - Alvear	Alvear	843	\N	\N
207	11	1	Edificio de Sede 00001 - Otero	Otero	262	\N	\N
208	11	1	\N	\N	\N	\N	\N
209	12	1	Edificio de Sede 00001 - F.Varela	F.Varela	1903	\N	\N
210	12	1	Edificio de Sede 00001 - F.varela	F.varela	1903	\N	\N
211	12	1	\N	\N	\N	\N	\N
212	14	1	Edificio de Sede 00001 - Ruta 35 Km. 334	Ruta 35 Km. 334	\N	\N	\N
213	14	1	Edificio de Sede 00001 - Avda. Uruguay  y Perú	Avda. Uruguay  y Perú	\N	\N	\N
214	14	1	Edificio de Sede 00001 - C. Gil	C. Gil	353	1	\N
215	14	1	Edificio de Sede 00001 - C.Gil	C.Gil	353	2	\N
216	15	1	Edificio de Sede 00001 - LISANDRO DE LA TORRE	LISANDRO DE LA TORRE	1070	\N	\N
217	15	2	Edificio de Sede 00002 - RUTA NACIONAL 3 - ACC. NORTE	RUTA NACIONAL 3 - ACC. NORTE	\N	\N	\N
218	15	3	Edificio de Sede 00003 - COLON esq. SARMIENTO - Bº 200 VIV.	COLON esq. SARMIENTO - Bº 200 VIV.	\N	\N	\N
219	15	4	Edificio de Sede 00004 - AV. DE LOS MINEROS	AV. DE LOS MINEROS	\N	\N	\N
220	15	1	\N	\N	\N	\N	\N
221	16	1	Edificio de Sede 00001 - Ruta Provincial Nº 1 - Km4	Ruta Provincial Nº 1 - Km4	\N	1	\N
222	16	1	Edificio de Sede 00001 - Ruta Provincial Nº 1- Km 4	Ruta Provincial Nº 1- Km 4	\N	2	\N
223	16	2	Edificio de Sede 00002 - San Martín esquina Pellegrini	San Martín esquina Pellegrini	407	\N	\N
224	16	1	Edificio de Sede 00001 - Ruta Provincial Nº 1- Km4	Ruta Provincial Nº 1- Km4	\N	4	\N
225	16	3	Edificio de Sede 00003 - Ruta 258 - km 4	Ruta 258 - km 4	\N	\N	\N
226	16	4	Edificio de Sede 00004 - Boulevard Almirante Brown	Boulevard Almirante Brown	3700	\N	\N
227	16	2	Edificio de Sede 00002 - Belgrano	Belgrano	504	2	\N
228	16	5	Edificio de Sede 00005 - Darwin y Canga	Darwin y Canga	\N	\N	\N
229	16	3	Edificio de Sede 00003 - Ruta Nacional Nº 259 - Km4	Ruta Nacional Nº 259 - Km4	\N	\N	\N
230	16	3	Edificio de Sede 00003 - Ruta Nacional 259 - km 4	Ruta Nacional 259 - km 4	\N	\N	\N
231	16	2	Edificio de Sede 00002 - Rawson entre Gales y Belgrano	Rawson entre Gales y Belgrano	\N	1	\N
232	16	3	Edificio de Sede 00003 - Sarmiento	Sarmiento	849	\N	\N
233	16	2	Edificio de Sede 00002 - 9 de Julio y Belgrano	9 de Julio y Belgrano	\N	\N	\N
234	16	5	Edificio de Sede 00005 - Intevu VI casa	Intevu VI casa	70	\N	\N
235	17	1	Edificio de Sede 00001 - 60 y 117	60 y 117	\N	\N	\N
236	17	1	Edificio de Sede 00001 - 60 y 116	60 y 116	\N	\N	\N
237	17	1	Edificio de Sede 00001 - 116 e/ 47 y 48	116 e/ 47 y 48	\N	\N	\N
238	17	1	Edificio de Sede 00001 - 1 y 47	1 y 47	\N	\N	\N
239	17	1	Edificio de Sede 00001 - 115 y 47	115 y 47	\N	\N	\N
240	17	1	Edificio de Sede 00001 - Bosque de La Plata	Bosque de La Plata	\N	\N	\N
241	17	1	Edificio de Sede 00001 - 6 e/ 47 y 48	6 e/ 47 y 48	\N	\N	\N
242	17	1	Edificio de Sede 00001 - 48 e/ 6 y 7	48 e/ 6 y 7	\N	\N	\N
243	17	1	Edificio de Sede 00001 - 44 e/ 8 y 9	44 e/ 8 y 9	\N	\N	\N
244	17	1	Edificio de Sede 00001 - 7 y 60	7 y 60	\N	\N	\N
245	17	1	Edificio de Sede 00001 - 60 y 118	60 y 118	\N	\N	\N
246	17	1	Edificio de Sede 00001 - 9 y 63	9 y 63	\N	\N	\N
247	17	1	Edificio de Sede 00001 - 50 y 115	50 y 115	\N	\N	\N
248	17	1	\N	\N	\N	\N	\N
249	18	1	Edificio de Sede 00001 - Av. Ortiz de Ocampo	Av. Ortiz de Ocampo	1700	\N	\N
250	18	2	Edificio de Sede 00002 - Castro Barros	Castro Barros	S/Nº	\N	\N
251	18	4	Edificio de Sede 00004 - Av. Lavalle	Av. Lavalle	S/Nº	\N	\N
252	18	5	Edificio de Sede 00005 - Av. San Martin	Av. San Martin	462	\N	\N
253	18	6	Edificio de Sede 00006 - Hipolito Yrigoyen S/N	Hipolito Yrigoyen S/N	\N	\N	\N
254	18	1	\N	\N	\N	\N	\N
255	19	1	Edificio de Sede 00001 - Bv. Pellegrini	Bv. Pellegrini	2947	\N	\N
256	19	1	Edificio de Sede 00001 - Santiago del Estero	Santiago del Estero	2829	\N	\N
257	19	1	Edificio de Sede 00001 - Ciudad Universitaria - Paraje El Pozo	Ciudad Universitaria - Paraje El Pozo	\N	\N	\N
258	19	1	Edificio de Sede 00001 - 25 de Mayo	25 de Mayo	1783	\N	\N
259	19	1	Edificio de Sede 00001 - Candido Pujato	Candido Pujato	2751	\N	\N
260	19	1	Edificio de Sede 00001 - tftftftftf	tftftftftf	12122	\N	\N
261	19	1	Edificio de Sede 00001 - fgggfgff	fgggfgff	56565	\N	\N
262	19	1	Edificio de Sede 00001 - gfgfgfgf	gfgfgfgf	45454	\N	\N
263	19	1	Edificio de Sede 00001 - asasasaa	asasasaa	78787	\N	\N
264	19	1	Edificio de Sede 00001 - jhjhjhjh	jhjhjhjh	32332	\N	\N
265	19	1	Edificio de Sede 00001 - 9 de Julio	9 de Julio	2655	\N	\N
266	19	1	\N	\N	\N	\N	\N
267	20	1	Edificio de Sede 00001 - Ruta Provincial 4 Km. 2	Ruta Provincial 4 Km. 2	\N	\N	\N
268	20	1	Edificio de Sede 00001 - Juan XXIII y Camino de Cintura	Juan XXIII y Camino de Cintura	\N	\N	\N
269	20	1	Edificio de Sede 00001 - Juan  XXIII y Camino de Cintura	Juan  XXIII y Camino de Cintura	\N	\N	\N
270	20	1	Edificio de Sede 00001 - Camino de Cintura  Km. 2	Camino de Cintura  Km. 2	\N	\N	\N
271	20	1	\N	\N	\N	\N	\N
272	21	2	Edificio de Sede 00002 - Avda. Sarmiento	Avda. Sarmiento	479	\N	\N
273	21	3	Edificio de Sede 00003 - Balcarce	Balcarce	120	\N	\N
274	21	4	Edificio de Sede 00004 - Farias	Farias	1590	\N	\N
275	21	5	Edificio de Sede 00005 - Rivadavia	Rivadavia	886	\N	\N
276	21	6	Edificio de Sede 00006 - Av. del Libertador	Av. del Libertador	1800	\N	\N
277	21	7	Edificio de Sede 00007 - Intituto Carlos Pellegrini ruta 5 km 3	Intituto Carlos Pellegrini ruta 5 km 3	\N	\N	\N
278	21	8	Edificio de Sede 00008 - Florida	Florida	629	\N	\N
279	21	9	Edificio de Sede 00009 - Robbio	Robbio	322	\N	\N
280	21	8	Edificio de Sede 00008 - Esc. Forencio Molina Campos	Esc. Forencio Molina Campos	\N	\N	\N
281	21	10	Edificio de Sede 00010 - Inst. Saturnino Unzue calle 26 y 47	Inst. Saturnino Unzue calle 26 y 47	\N	\N	\N
282	21	11	Edificio de Sede 00011 - Ecuador	Ecuador	873	\N	\N
283	22	2	Edificio de Sede 00002 - Ruta 226 Km 72,3	Ruta 226 Km 72,3	\N	\N	\N
284	22	1	Edificio de Sede 00001 - Funes  (Complejo Universitario)	Funes  (Complejo Universitario)	3250	\N	\N
285	22	1	Edificio de Sede 00001 - Av. Juan B. Justo	Av. Juan B. Justo	4302	\N	\N
286	22	1	Edificio de Sede 00001 - Funes   (Complejo Universitario)	Funes   (Complejo Universitario)	3250	\N	\N
287	22	1	\N	\N	\N	\N	\N
288	23	2	Edificio de Sede 00002 - AVDA. SAN MARTIN	AVDA. SAN MARTIN	KM. 3	\N	\N
289	23	3	Edificio de Sede 00003 - JUAN M. DE ROSAS	JUAN M. DE ROSAS	325	\N	\N
290	23	1	Edificio de Sede 00001 - FELIX DE AZARA	FELIX DE AZARA	1552	\N	\N
291	23	1	Edificio de Sede 00001 - Avda. Lopez Torres y José María Moreno	Avda. Lopez Torres y José María Moreno	3415	\N	\N
292	23	1	Edificio de Sede 00001 - CAMPUS UNIVERSITARIO - RUTA 12 KM. 7 ½	CAMPUS UNIVERSITARIO - RUTA 12 KM. 7 ½	\N	\N	\N
293	23	1	Edificio de Sede 00001 - TUCUMAN	TUCUMAN	1946	\N	\N
294	23	1	Edificio de Sede 00001 - CARHUÉ	CARHUÉ	832	\N	\N
295	23	1	\N	\N	\N	\N	\N
296	23	2	Edificio de Sede 00002 - Bertoni	Bertoni	152	\N	Eldor
297	24	1	Edificio de Sede 00001 - Sargento Cabral	Sargento Cabral	2131	\N	\N
298	24	1	Edificio de Sede 00001 - Sargento Cabral	Sargento Cabral	2139	\N	\N
299	24	2	Edificio de Sede 00002 - Av. Las Heras	Av. Las Heras	727	\N	\N
300	24	2	Edificio de Sede 00002 - Av. Las Heras	Av. Las Heras	 727	\N	\N
301	24	3	Edificio de Sede 00003 - Comandante Fernández	Comandante Fernández	755	\N	\N
302	24	1	Edificio de Sede 00001 - 9 de Julio	9 de Julio	1449	\N	\N
303	24	2	Edificio de Sede 00002 - Avenida Las Heras	Avenida Las Heras	727	\N	\N
304	24	1	Edificio de Sede 00001 - Av. Libertad	Av. Libertad	5470	\N	\N
305	24	1	Edificio de Sede 00001 - Moreno	Moreno	1240	\N	\N
306	24	1	Edificio de Sede 00001 - Av. Libertad	Av. Libertad	5450	\N	\N
307	24	2	Edificio de Sede 00002 - Las Heras	Las Heras	727	\N	\N
308	24	4	Edificio de Sede 00004 - Madariaga	Madariaga	1300	\N	\N
309	24	5	Edificio de Sede 00005 - Rivadavia	Rivadavia	 886	\N	\N
310	24	1	Edificio de Sede 00001 - Catamarca	Catamarca	375	\N	\N
311	24	1	Edificio de Sede 00001 - San Juan	San Juan	434	\N	\N
312	24	1	\N	\N	\N	\N	\N
313	24	1	\N	\N	\N	\N	\N
314	24	1	\N	\N	\N	\N	\N
315	25	1	\N	\N	\N	\N	\N
316	25	1	\N	\N	\N	\N	\N
317	25	1	Edificio de Sede 00001 - Roque Sáenz Peña	Roque Sáenz Peña	180	\N	\N
318	26	1	Edificio de Sede 00001 - Ruta 36	Ruta 36	Km. 6	\N	\N
319	26	1	Edificio de Sede 00001 - Ruta 36	Ruta 36	Km. 6	\N	\N
320	26	1	Edificio de Sede 00001 - Ruta 36	Ruta 36	Km 60	\N	\N
321	26	1	Edificio de Sede 00001 - Ruta 36	Ruta 36	Km 60	\N	\N
322	26	1	Edificio de Sede 00001 - Ruta 36	Ruta 36	Km 60	\N	\N
323	26	1	Edificio de Sede 00001 - Ruta 36	Ruta 36	Km. 6	\N	\N
324	26	1	\N	\N	\N	\N	\N
325	27	2	Edificio de Sede 00002 - Campo Exp. J.Villarino	Campo Exp. J.Villarino	\N	\N	\N
326	27	3	Edificio de Sede 00003 - Ruta 33 y Ov. Lagos	Ruta 33 y Ov. Lagos	\N	\N	\N
327	27	1	Edificio de Sede 00001 - Riobamba y Berutti C.U.R.	Riobamba y Berutti C.U.R.	\N	\N	\N
328	27	1	Edificio de Sede 00001 - Pellegrini	Pellegrini	250	\N	\N
329	27	1	Edificio de Sede 00001 - Suipacha	Suipacha	531	\N	\N
330	27	1	Edificio de Sede 00001 - Bv. Oroño	Bv. Oroño	1261	\N	\N
331	27	1	Edificio de Sede 00001 - Córdoba	Córdoba	2020	\N	\N
332	27	1	Edificio de Sede 00001 - Riobamba y Berutti . C.U.R	Riobamba y Berutti . C.U.R	\N	\N	\N
333	27	1	Edificio de Sede 00001 - Entre Rios	Entre Rios	758	\N	\N
334	27	1	Edificio de Sede 00001 - Riobamba	Riobamba	250 b	\N	\N
335	27	1	Edificio de Sede 00001 - Santa Fe	Santa Fe	3100	\N	\N
336	27	1	Edificio de Sede 00001 - Santa Fe	Santa Fe	3160	\N	\N
337	27	1	Edificio de Sede 00001 - Av Pellegrini	Av Pellegrini	250	\N	\N
338	27	1	Edificio de Sede 00001 - Balcarce	Balcarce	1240	\N	\N
339	27	1	Edificio de Sede 00001 - Balcarce	Balcarce	1514	\N	\N
340	27	3	Edificio de Sede 00003 - O.Lagos y Ruta 33	O.Lagos y Ruta 33	\N	\N	\N
341	27	1	\N	\N	\N	\N	\N
342	28	1	Edificio de Sede 00001 - BUENOS AIRES	BUENOS AIRES	177	\N	\N
343	28	2	Edificio de Sede 00002 - ALVARADO	ALVARADO	751	\N	\N
344	28	3	Edificio de Sede 00003 - WARNES	WARNES	890	\N	\N
345	28	1	\N	\N	\N	\N	\N
346	29	1	Edificio de Sede 00001 - Av. Lib. Gral San Martín (Oeste)	Av. Lib. Gral San Martín (Oeste)	1109	\N	\N
347	29	1	Edificio de Sede 00001 - Av. José I. de la Roza y Meglioli	Av. José I. de la Roza y Meglioli	S/N	\N	Rivad
348	29	2	Edificio de Sede 00002 - Av. José I. de la Roza	Av. José I. de la Roza	590	\N	\N
349	29	1	Edificio de Sede 00001 - Av. José I. de la Roza (Oeste)	Av. José I. de la Roza (Oeste)	230	\N	\N
350	29	1	\N	\N	\N	\N	\N
351	30	1	Edificio de Sede 00001 - Avda. 25 de Mayo	Avda. 25 de Mayo	  384	\N	\N
352	30	1	Edificio de Sede 00001 - Avda. Ejercito de los Andes	Avda. Ejercito de los Andes	    9	 2º	Edifi
353	30	1	Edificio de Sede 00001 - Avda. Ejercito de los Andes	Avda. Ejercito de los Andes	   95	2º	Edifi
354	30	1	Edificio de Sede 00001 - Avda. Ejercito de los Andes	Avda. Ejercito de los Andes	   95	  1	Edifi
355	30	1	Edificio de Sede 00001 - Avda. Ejercito de los Andes	Avda. Ejercito de los Andes	  950	  2	Edifi
356	30	1	Edificio de Sede 00001 - Av. Ejercito de los Andes	Av. Ejercito de los Andes	950	2º	\N
357	30	1	\N	\N	\N	\N	\N
358	31	1	Edificio de Sede 00001 - Av. Belgrano (S)	Av. Belgrano (S)	1912	\N	\N
359	31	1	Edificio de Sede 00001 - Av. Belgrano (s)	Av. Belgrano (s)	1912	\N	\N
360	31	1	\N	\N	\N	\N	\N
361	32	1	Edificio de Sede 00001 - Alem	Alem	1253	1	\N
362	32	1	Edificio de Sede 00001 - Alem 1253	Alem 1253	\N	1	\N
363	32	1	Edificio de Sede 00001 - Avenida Alem	Avenida Alem	1253	1	\N
364	32	1	Edificio de Sede 00001 - San Juan	San Juan	670	1	\N
365	32	1	Edificio de Sede 00001 - 12 de Octubre y San Juan	12 de Octubre y San Juan	\N	8	\N
366	32	1	Edificio de Sede 00001 - 12 de octubre y San juan	12 de octubre y San juan	\N	7	\N
367	32	1	Edificio de Sede 00001 - 12 de octubre y San Juan	12 de octubre y San Juan	\N	4	\N
368	32	2	Edificio de Sede 00002 - Paraje El Pozo	Paraje El Pozo	\N	\N	\N
369	32	1	Edificio de Sede 00001 - 11 de abril 475	11 de abril 475	\N	\N	\N
370	32	1	Edificio de Sede 00001 - 11 de abril	11 de abril	475	\N	\N
371	32	1	Edificio de Sede 00001 - Sarmiento al 2000	Sarmiento al 2000	\N	\N	\N
372	32	1	\N	\N	\N	\N	\N
373	33	1	Edificio de Sede 00001 - Av. Roca	Av. Roca	1900	\N	\N
374	33	1	Edificio de Sede 00001 - Av. Roca	Av. Roca	1800	\N	\N
375	33	1	Edificio de Sede 00001 - Av. Independencia	Av. Independencia	1800	\N	\N
376	33	1	Edificio de Sede 00001 - Miguel Lillo	Miguel Lillo	205	\N	\N
377	33	1	Edificio de Sede 00001 - Av Independencia	Av Independencia	1900	\N	\N
378	33	1	Edificio de Sede 00001 - 25 de Mayo	25 de Mayo	471	\N	\N
379	33	1	Edificio de Sede 00001 - Av. Benjamin Araoz	Av. Benjamin Araoz	800	\N	\N
380	33	1	Edificio de Sede 00001 - Av. Benjamin Araoz	Av. Benjamin Araoz	751	\N	\N
381	33	1	Edificio de Sede 00001 - Bolivar	Bolivar	700	\N	\N
382	33	1	Edificio de Sede 00001 - Chacabuco	Chacabuco	242	\N	\N
383	33	1	Edificio de Sede 00001 - Lamadrid	Lamadrid	875	\N	\N
384	33	1	Edificio de Sede 00001 - General Paz	General Paz	875	\N	\N
385	33	1	\N	\N	\N	\N	\N
386	33	1	\N	\N	\N	\N	\N
387	34	1	\N	\N	\N	\N	\N
388	35	2	Edificio de Sede 00002 - Avda. Mitre	Avda. Mitre	750	\N	\N
389	35	3	Edificio de Sede 00003 - 11 de abril	11 de abril	461	\N	\N
390	35	4	Edificio de Sede 00004 - Medrano	Medrano	951	\N	\N
391	35	5	Edificio de Sede 00005 - Ing. Pereyra	Ing. Pereyra	676	\N	\N
392	35	6	Edificio de Sede 00006 - Maestro M. López esq.Cruz Roja Argentina	Maestro M. López esq.Cruz Roja Argentina	\N	\N	Ciuda
393	35	7	Edificio de Sede 00007 - San Martín	San Martín	1171	\N	\N
394	35	8	Edificio de Sede 00008 - Hipólito Yrigoyen	Hipólito Yrigoyen	288	\N	\N
395	35	9	Edificio de Sede 00009 - París	París	532	\N	\N
396	35	10	Edificio de Sede 00010 - Calle 60 esq. 124	Calle 60 esq. 124	\N	\N	\N
397	35	11	Edificio de Sede 00011 - Rodríguez	Rodríguez	273	\N	\N
398	35	12	Edificio de Sede 00012 - Almafuerte	Almafuerte	1033	\N	\N
399	35	13	Edificio de Sede 00013 - French	French	414	\N	\N
400	35	14	Edificio de Sede 00014 - Estanislao Zeballos	Estanislao Zeballos	1341	\N	\N
401	35	15	Edificio de Sede 00015 - Avda. de la Universidad	Avda. de la Universidad	501	\N	\N
402	35	16	Edificio de Sede 00016 - Colón	Colón	332	\N	\N
403	35	17	Edificio de Sede 00017 - Comandante Salas	Comandante Salas	370	\N	\N
404	35	18	Edificio de Sede 00018 - Lavaise	Lavaise	610	\N	\N
405	35	19	Edificio de Sede 00019 - Rivadavia	Rivadavia	1050	\N	\N
406	35	20	Edificio de Sede 00020 - Av. Universidad - Barrio Bello Horizonte	Av. Universidad - Barrio Bello Horizonte	450	\N	\N
407	35	21	Edificio de Sede 00021 - Salta	Salta	277	\N	\N
408	35	22	Edificio de Sede 00022 - Pedro Rotter - Barrio Uno	Pedro Rotter - Barrio Uno	s/n	\N	\N
409	35	23	Edificio de Sede 00023 - Roberts	Roberts	61	\N	\N
410	35	24	Edificio de Sede 00024 - San Nicolás de Bari (E)	San Nicolás de Bari (E)	1100	\N	\N
411	35	25	Edificio de Sede 00025 - Bv. Roca y Artigas	Bv. Roca y Artigas	\N	\N	\N
412	35	26	Edificio de Sede 00026 - Presidente Roca	Presidente Roca	1250	\N	\N
413	35	27	Edificio de Sede 00027 - Solís y Béccar	Solís y Béccar	\N	\N	\N
414	35	28	Edificio de Sede 00028 - Islas Malvinas	Islas Malvinas	1650	\N	\N
415	35	29	Edificio de Sede 00029 - Villegas	Villegas	980	\N	\N
416	35	30	Edificio de Sede 00030 - Castelli	Castelli	501	\N	\N
417	35	25	Edificio de Sede 00025 - Bvard. Roca y Artigas	Bvard. Roca y Artigas	\N	\N	\N
418	35	1	\N	\N	\N	\N	\N
419	35	1	\N	\N	\N	\N	\N
420	37	2	Edificio de Sede 00002 - Av Matienzo y Ruta 201	Av Matienzo y Ruta 201	\N	\N	\N
421	37	1	Edificio de Sede 00001 - Av Cabildo	Av Cabildo	15	\N	\N
422	37	1	Edificio de Sede 00001 - Av Luis María Campos	Av Luis María Campos	480	\N	\N
423	37	3	Edificio de Sede 00003 - Maipú	Maipú	262	\N	\N
424	38	1	Edificio de Sede 00001 - Av. Fuerza Aérea Argentina	Av. Fuerza Aérea Argentina	6500	-.-	\N
425	38	1	Edificio de Sede 00001 - Av. Fuerza Aérea Km 6,5	Av. Fuerza Aérea Km 6,5	-.-	-.-	\N
426	38	1	\N	\N	\N	\N	\N
427	39	2	Edificio de Sede 00002 - Av. Antartida Argentina	Av. Antartida Argentina	1535	\N	\N
428	39	3	Edificio de Sede 00003 - Rio Santiago	Rio Santiago	\N	\N	\N
429	39	4	Edificio de Sede 00004 - Puerto Belgrano	Puerto Belgrano	\N	\N	\N
430	39	1	Edificio de Sede 00001 - Avda. del Libertador	Avda. del Libertador	8071	\N	\N
431	39	5	Edificio de Sede 00005 - Avda. Montes de Oca	Avda. Montes de Oca	2124	\N	\N
432	39	6	Edificio de Sede 00006 - Av. Montes de Oca	Av. Montes de Oca	2124	\N	\N
433	39	1	Edificio de Sede 00001 - Av. del Libertador	Av. del Libertador	8071	\N	\N
434	40	1	\N	\N	\N	\N	\N
435	41	1	\N	\N	\N	\N	\N
436	42	2	Edificio de Sede 00002 - Córdoba	Córdoba	374	\N	\N
437	44	2	Edificio de Sede 00002 - Las Heras	Las Heras	1749	\N	\N
438	44	2	Edificio de Sede 00002 - Avda Cordoba	Avda Cordoba	2445	\N	\N
439	44	3	Edificio de Sede 00003 - Sanchez de  Loria	Sanchez de  Loria	443	\N	\N
440	44	3	Edificio de Sede 00003 - French	French	3614	\N	\N
441	44	3	Edificio de Sede 00003 - Sanchez de Loria	Sanchez de Loria	443	\N	\N
442	44	3	Edificio de Sede 00003 - Piedras	Piedras	1655	\N	\N
443	45	1	\N	\N	\N	\N	\N
444	45	1	\N	\N	\N	\N	\N
445	48	1	Edificio de Sede 00001 - Av. Alicia Moreau de Justo	Av. Alicia Moreau de Justo	1500	\N	\N
446	48	1	Edificio de Sede 00001 - Av. Alicia Moreau de Justo	Av. Alicia Moreau de Justo	1400	\N	\N
447	48	3	Edificio de Sede 00003 - 11 de septiembre	11 de septiembre	646	\N	\N
448	48	4	Edificio de Sede 00004 - E. Zeballos	E. Zeballos	668	\N	\N
449	48	4	Edificio de Sede 00004 - Av. Salta	Av. Salta	2763	\N	\N
450	48	6	Edificio de Sede 00006 - Buenos Aires	Buenos Aires	249	\N	\N
451	48	7	Edificio de Sede 00007 - Perú	Perú	1160	\N	\N
452	48	4	Edificio de Sede 00004 - Mendoza	Mendoza	4197	\N	\N
453	48	7	Edificio de Sede 00007 - Patricias Mendocinas	Patricias Mendocinas	1475	\N	\N
454	49	2	Edificio de Sede 00002 - Av. San Juan	Av. San Juan	983	\N	\N
455	49	3	Edificio de Sede 00003 - Montañeses	Montañeses	2759	\N	\N
456	49	4	Edificio de Sede 00004 - Arias	Arias	3550	\N	\N
457	49	5	Edificio de Sede 00005 - Palestina	Palestina	748	\N	\N
458	49	2	Edificio de Sede 00002 - Av.San Juan	Av.San Juan	983	\N	\N
459	51	1	\N	\N	\N	\N	\N
460	51	1	\N	\N	\N	\N	\N
461	51	1	\N	\N	\N	\N	\N
462	51	1	\N	\N	\N	\N	\N
463	51	1	\N	\N	\N	\N	\N
464	52	1	\N	\N	\N	\N	\N
465	52	1	\N	\N	\N	\N	\N
466	52	1	\N	\N	\N	\N	\N
467	52	1	\N	\N	\N	\N	\N
468	52	1	\N	\N	\N	\N	\N
469	52	1	\N	\N	\N	\N	\N
470	52	1	\N	\N	\N	\N	\N
471	52	1	\N	\N	\N	\N	\N
472	52	1	\N	\N	\N	\N	\N
473	52	1	\N	\N	\N	\N	\N
474	52	1	\N	\N	\N	\N	\N
475	52	1	\N	\N	\N	\N	\N
476	52	1	\N	\N	\N	\N	\N
477	52	1	\N	\N	\N	\N	\N
478	52	1	\N	\N	\N	\N	\N
479	52	1	\N	\N	\N	\N	\N
480	52	1	\N	\N	\N	\N	\N
481	52	1	\N	\N	\N	\N	\N
482	52	1	\N	\N	\N	\N	\N
483	52	1	\N	\N	\N	\N	\N
484	52	1	\N	\N	\N	\N	\N
485	52	1	\N	\N	\N	\N	\N
486	52	1	\N	\N	\N	\N	\N
487	52	2	Edificio de Sede 00002 - Estados Unidos	Estados Unidos	929	\N	\N
488	52	1	\N	\N	\N	\N	\N
489	52	1	\N	\N	\N	\N	\N
490	52	1	\N	\N	\N	\N	\N
491	52	1	\N	\N	\N	\N	\N
492	52	1	\N	\N	\N	\N	\N
493	52	1	\N	\N	\N	\N	\N
494	53	2	Edificio de Sede 00002 - calle Buenos Aires	calle Buenos Aires	1280	\N	\N
495	53	1	Edificio de Sede 00001 - RIVADAVIA	RIVADAVIA	515	\N	\N
496	53	4	Edificio de Sede 00004 - AV BUENOS AIRES Y URRUTIA	AV BUENOS AIRES Y URRUTIA	\N	\N	\N
497	53	2	Edificio de Sede 00002 - LAMADRID	LAMADRID	341	\N	\N
498	53	5	Edificio de Sede 00005 - Intermedanos S/N	Intermedanos S/N	\N	\N	\N
499	53	6	Edificio de Sede 00006 - ALMIRANTE BROWN	ALMIRANTE BROWN	1074	\N	\N
500	54	2	Edificio de Sede 00002 - Juan Domingo Perón	Juan Domingo Perón	1500	\N	\N
501	54	3	Edificio de Sede 00003 - Paraguay	Paraguay	1950	\N	\N
502	54	1	Edificio de Sede 00001 - Avanida Juan de Garay	Avanida Juan de Garay	125	5º	\N
503	54	1	Edificio de Sede 00001 - Avenida Juan de Garay	Avenida Juan de Garay	125	2º	\N
504	54	2	Edificio de Sede 00002 - Mariano Acosta. Derqui	Mariano Acosta. Derqui	S/N	\N	\N
505	54	1	Edificio de Sede 00001 -  Avenida Juan de Garay	 Avenida Juan de Garay	125	3º	\N
506	54	1	\N	\N	\N	\N	\N
507	56	2	Edificio de Sede 00002 - Av. de Mayo	Av. de Mayo	866	9	Dto.
508	56	1	\N	\N	\N	\N	\N
509	57	1	Edificio de Sede 00001 - Camino a Alta Gracia Km. 10	Camino a Alta Gracia Km. 10	\N	\N	\N
510	57	1	Edificio de Sede 00001 - Jacinto Ríos	Jacinto Ríos	571	\N	\N
511	58	1	\N	\N	\N	\N	\N
512	58	1	\N	\N	\N	\N	\N
513	59	1	Edificio de Sede 00001 - Diagonal 73	Diagonal 73	2137	1	\N
514	59	1	Edificio de Sede 00001 - Avenida 51	Avenida 51	807	PB	\N
515	59	1	Edificio de Sede 00001 - Calle 57	Calle 57	936	1	\N
516	59	2	Edificio de Sede 00002 - Calle 25 de Mayo	Calle 25 de Mayo	51	PB	\N
517	59	1	\N	\N	\N	\N	\N
518	60	1	Edificio de Sede 00001 - Campo Castañares	Campo Castañares	s/n	\N	\N
519	60	1	\N	\N	\N	\N	\N
520	60	1	Edificio de Sede 00001 - Pellegrini	Pellegrini	790	\N	\N
521	60	1	\N	\N	\N	\N	\N
522	60	2	Edificio de Sede 00002 - Avda. Paseo Colon	Avda. Paseo Colon	533	\N	\N
523	60	1	\N	\N	\N	\N	\N
524	60	1	\N	\N	\N	\N	\N
525	60	1	\N	\N	\N	\N	\N
526	61	1	Edificio de Sede 00001 - Echacgüe	Echacgüe	7151	\N	\N
527	61	1	Edificio de Sede 00001 - Eschagüe	Eschagüe	7151	\N	\N
528	61	1	Edificio de Sede 00001 - Pascual Echague	Pascual Echague	7151	\N	\N
529	61	2	Edificio de Sede 00002 - Rademacher 3943	Rademacher 3943	\N	\N	\N
530	61	1	\N	\N	\N	\N	\N
531	62	1	Edificio de Sede 00001 - Av. Alsina Y Dalmacio Vélez Sarsfield	Av. Alsina Y Dalmacio Vélez Sarsfield	---	---	Matem
532	62	2	Edificio de Sede 00002 - Lavalle	Lavalle	333	---	\N
533	62	3	Edificio de Sede 00003 - Corrientes	Corrientes	180	--	\N
534	62	4	Edificio de Sede 00004 - Boulevard Hipólito Irigoyen	Boulevard Hipólito Irigoyen	1502	---	\N
535	62	1	\N	\N	\N	\N	\N
536	63	1	\N	\N	\N	\N	\N
537	63	1	\N	\N	\N	\N	\N
538	63	1	\N	\N	\N	\N	\N
539	63	2	\N	\N	\N	\N	\N
540	64	1	Edificio de Sede 00001 - Federico Lacroze	Federico Lacroze	1955	\N	\N
541	64	1	Edificio de Sede 00001 - Zabala	Zabala	1837	16	\N
542	64	1	Edificio de Sede 00001 - Villanueva	Villanueva	1324	\N	\N
543	64	1	Edificio de Sede 00001 - Federico Lacroze	Federico Lacroze	1947	\N	\N
544	64	1	Edificio de Sede 00001 - Jose Hernandez	Jose Hernandez	1820	\N	\N
545	64	2	Edificio de Sede 00002 - M. T. de Alvear	M. T. de Alvear	1560	\N	\N
546	64	1	\N	\N	\N	\N	\N
547	66	1	Edificio de Sede 00001 - Eurasquin	Eurasquin	158	\N	\N
548	66	1	Edificio de Sede 00001 - 8 de junio	8 de junio	552	\N	\N
549	66	1	Edificio de Sede 00001 - 8 de Junio	8 de Junio	552	\N	\N
550	66	1	\N	\N	\N	\N	\N
551	66	1	Edificio de Sede 00001 - Las Violetas	Las Violetas	853	\N	\N
552	66	2	Edificio de Sede 00002 - 25 de Mayo	25 de Mayo	737	1	\N
553	66	1	\N	\N	\N	\N	\N
554	67	1	Edificio de Sede 00001 - Av. Colón	Av. Colón	90	\N	\N
555	69	1	Edificio de Sede 00001 - Dag Hammarskjold	Dag Hammarskjold	750	\N	\N
556	69	1	Edificio de Sede 00001 - Av. Boulogme Sur Mer	Av. Boulogme Sur Mer	665	\N	\N
557	69	1	Edificio de Sede 00001 - Arístides Villanueva	Arístides Villanueva	773	\N	\N
558	69	1	\N	\N	\N	\N	\N
559	70	1	\N	\N	\N	\N	\N
560	70	1	\N	\N	\N	\N	\N
561	71	2	Edificio de Sede 00002 - Soler	Soler	3666	\N	\N
562	71	2	Edificio de Sede 00002 - Anchorena	Anchorena	1314	\N	\N
563	71	3	Edificio de Sede 00003 - Mario Bravo	Mario Bravo	1259	\N	\N
565	71	1	\N	\N	\N	\N	\N
566	73	2	Edificio de Sede 00002 - José Antonio Cabrera	José Antonio Cabrera	3507	\N	\N
567	73	1	Edificio de Sede 00001 - Lavalle	Lavalle	393	\N	\N
568	73	1	\N	\N	\N	\N	\N
569	74	1	Edificio de Sede 00001 - Pellegrini	Pellegrini	1332	\N	\N
570	75	1	\N	\N	\N	\N	\N
571	75	1	\N	\N	\N	\N	\N
572	75	1	\N	\N	\N	\N	\N
573	76	3	Edificio de Sede 00003 - Av. Corrientes	Av. Corrientes	1723	\N	\N
574	76	1	\N	\N	\N	\N	\N
575	77	1	Edificio de Sede 00001 - 9 de julio	9 de julio	165	\N	\N
576	77	1	\N	\N	\N	\N	\N
577	77	1	Edificio de Sede 00001 - 9 de Julio 165	9 de Julio 165	\N	\N	\N
578	77	1	\N	\N	\N	\N	\N
579	77	2	Edificio de Sede 00002 - Roca	Roca	\N	\N	\N
580	77	1	\N	\N	\N	\N	\N
581	77	1	\N	\N	\N	\N	\N
582	77	3	Edificio de Sede 00003 - .	.	\N	\N	\N
583	78	6	Edificio de Sede 00006 - Calle 29	Calle 29	317	\N	\N
584	78	7	Edificio de Sede 00007 - Champagnat	Champagnat	1599	\N	\N
585	78	8	Edificio de Sede 00008 - Gob. Virasoro	Gob. Virasoro	\N	\N	\N
586	78	1	\N	\N	\N	\N	\N
587	79	1	Edificio de Sede 00001 - Plácido Martínez	Plácido Martínez	886	\N	\N
588	79	1	Edificio de Sede 00001 - La Rioja	La Rioja	455	\N	\N
589	82	1	Edificio de Sede 00001 - Rondeau	Rondeau	 165	\N	\N
590	83	2	Edificio de Sede 00002 - Teniente General Juan Domingo Perón	Teniente General Juan Domingo Perón	2933	\N	\N
591	84	1	Edificio de Sede 00001 - Avenida de Acceso Este	Avenida de Acceso Este	2245	\N	\N
592	84	1	Edificio de Sede 00001 - Avenida de Acceso  Este	Avenida de Acceso  Este	2245	\N	\N
593	84	2	Edificio de Sede 00002 - Espejo	Espejo	256	\N	\N
594	84	1	\N	\N	\N	\N	\N
595	85	1	\N	\N	\N	\N	\N
596	85	1	\N	\N	\N	\N	\N
597	85	3	\N	\N	\N	\N	\N
598	89	1	\N	\N	\N	\N	\N
599	89	1	\N	\N	\N	\N	\N
\.


--
-- Data for TOC entry 18 (OID 56992533)
-- Name: soe_tiposua; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY soe_tiposua (tipoua, descripcion, detalle, estado) FROM stdin;
\.


--
-- Data for TOC entry 19 (OID 56992540)
-- Name: soe_unidadesacad; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY soe_unidadesacad (unidadacad, institucion, nombre, tipoua) FROM stdin;
1	1	Facultad de Agronomía	\N
2	1	Facultad de Ciencias Veterinarias	\N
3	1	Facultad de Arquitectura Diseño y Urbanismo	\N
4	1	Facultad de Ingeniería	\N
5	1	Facultad de Ciencias Exactas y Naturales	\N
6	1	Facultad de Farmacia y Bioquímica	\N
7	1	Facultad de Ciencias Económicas	\N
8	1	Facultad de Ciencias Sociales	\N
9	1	Facultad de Filosofía y Letras	\N
10	1	Facultad de Psicología	\N
11	1	Facultad de Medicina	\N
12	1	Facultad de Odontología	\N
13	1	Facultad de Derecho	\N
14	1	Ciclo Básico Común	\N
15	1	Rectorado	\N
16	2	Facultad de Ciencias Agrarias	\N
17	2	Facultad de Tecnología y Ciencias Aplicadas	\N
18	2	Facultad de Ciencias Económicas y de Administración	\N
19	2	Facultad de Humanidades	\N
20	2	Escuela de Arqueología	\N
21	2	Facultad de Ciencias de la Salud	\N
22	2	Escuela de Derecho	\N
23	2	Facultad de Ciencias Exactas y Naturales	\N
24	2	Facultad de Derecho	\N
25	37	Rectorado	\N
26	3	Facultad de Ciencias Veterinarias	\N
27	80	Sede BARILOCHE	\N
28	3	Facultad de Ciencias Exactas	\N
29	3	Facultad de Ciencias Económicas	\N
30	3	Facultad de Ciencias Humanas	\N
31	80	Localización TANDIL	\N
32	87	Escuela de Gobierno	\N
33	78	Subsede Neuquén	\N
34	4	Facultad de Ciencias Agrarias	\N
35	4	Asentamiento Universitario San Martín de los Andes	\N
36	4	Facultad de Ingeniería	\N
37	4	Asentamiento Universitario Villa Regina	\N
38	4	Asentamiento Universitario Zapala	\N
39	4	Facultad de Economía y Administración	\N
40	4	Facultad de Derecho y Ciencias Sociales, General Roca	\N
41	4	Facultad de Turismo	\N
42	4	Facultad de Humanidades	\N
43	4	Escuela Superior de Idiomas	\N
44	4	Facultad de Ciencias de la Educación, Sede Cipolletti	\N
45	4	Módulo Neuquén - Fac. de Derecho y Cs. Sociales	\N
46	4	Instituto Universitario de Ciencias para la Salud	\N
47	4	Centro Universitario Regional Zona Atlántica	\N
48	4	Centro Regional Universitario Bariloche	\N
49	4	Módulo Chos Malal - Fac. de Economía y Administración	\N
50	4	Sede San Antonio Oeste . C.U.R.Z.A.	\N
51	4	Carrera de Medicina	\N
52	4	Módulo El Hoyo - Facultad de Turismo	\N
53	4	Módulo Chos Malal  - Facultad de Humanidades	\N
54	4	Módulo Allen  de Enfernería - I.U.C.S.	\N
55	5	Facultad de Ciencias Agropecuarias	\N
56	5	Facultad de Arquitectura, Urbanismo y Diseño Industrial	\N
57	5	Facultad de Ciencias Exactas, Físicas y Naturales	\N
58	5	Facultad de Ciencias Químicas	\N
59	5	Facultad de Ciencias Económicas	\N
60	5	Facultad de Derecho y Ciencias Sociales	\N
61	5	Escuela de Trabajo Social	\N
62	5	Facultad de Filosofía y Humanidades	\N
63	5	Escuela Superior de Lenguas	\N
64	5	Facultad de Ciencias Médicas	\N
65	5	Escuela de Nutrición	\N
66	5	Escuela deTecnología Médica	\N
67	5	Escuela de Enfermería	\N
68	5	Escuela de Fonoaudiología	\N
69	5	Escuela de Kinesiología y Fisioterapia	\N
70	5	Facultad de Odontología	\N
71	5	Escuela de Ciencias de Información	\N
72	5	Facultad de Psicología	\N
73	5	Facultad de Matemáticas, Astronomía y Física	\N
74	5	Centro de Estudios Avanzados	\N
75	5	Instituto de Investigación y Formación en la Administración Pública	\N
76	5	Facultad	\N
77	5	Facultad de Lenguas	\N
78	6	Facultad de Ciencias Agrarias	\N
79	6	Facultad de Ingeniería	\N
80	6	Instituto Balseiro	\N
81	6	Facultad de Ciencias Económicas	\N
82	6	Facultad de Ciencias Económicas - Delegación San Rafael	\N
83	6	Facultad de Derecho	\N
84	6	Facultad de Ciencias Políticas y Sociales	\N
85	6	Facultad de Filosofía y Letras	\N
86	6	Facultad de Educación Elemental y Especial	\N
87	6	Facultad de Artes	\N
88	6	Escuela de Artes Plásticas	\N
89	6	Escuela de Cerámica	\N
90	6	Escuela de Diseño	\N
91	6	Escuela de Música	\N
92	6	Escuela de Teatro	\N
93	6	Facultad de Ciencias Médicas	\N
94	6	Escuela de Enfermería	\N
95	6	Facultad de Odontología	\N
96	6	Facultad de Ciencias Aplicadas a la Industria	\N
97	6	Inst.Tecnológico Univ. (Sede Luján de Cuyo)	\N
98	6	Instituto Tecnológico Universitario	\N
99	6	Convenio entre: Facultad de Ciencias Médicas y Facultad de Ciencias Agrarias	\N
100	6	Convenio entre: Fac.Cs.Económicas-Fac.de Ingen.-Min.Econ.y Hac.Mza-Ecole Nationale Des Ponts Chausse	\N
101	6	Convenio entre: Facultad de Ciencias Agrarias - Instituto Nacional de Tecnología Agropecuaria	\N
102	6	Convenio entre: Facultad de Ciencias Políticas y Sociales y Fac. de Ciencias Económicas	\N
103	6	Secretaría Académica - Rectorado	\N
104	6	Escuela de Técnicos Asistenciales de Salud	\N
105	6	Facultad de Artes y Diseño	\N
106	6	Convenio Fac.Ciencias Agrarias - Fac.Cs. Aplicadas a la Industria	\N
107	6	Inst.Tecnológico Univ. (Sede Gral.Alvear)	\N
108	6	Inst.Tecnológico Univ. (Sede San Rafael)	\N
109	6	Inst.Tecnológico Univ. (Sede Tunuyán)	\N
110	6	Inst.Tecnológico Univ. (Sede Rivadavia)	\N
111	7	Facultad de Ciencias Agropecuarias	\N
112	7	Facultad de Ingeniería	\N
113	7	Facultad de Ciencias de la Alimentación	\N
114	7	Facultad de Bromatología	\N
115	7	Facultad de Ciencias de la Administración	\N
116	7	Facultad de Ciencias Económicas	\N
117	7	Facultad de Trabajo Social	\N
118	7	Facultad de Ciencias de la Educación	\N
119	7	Facultad de Ciencias de la Salud	\N
120	8	Facultad de Recursos Naturales	\N
121	8	Facultad de Humanidades	\N
122	8	Facultad de Ciencias de la Salud	\N
123	8	Facultad de Administración, Economía y Negocios	\N
124	8	PROGRAMA NUEVAS OFERTAS ACADEMICAS	\N
125	8	Facultad de Administración Economía y Negocios	\N
126	8	Instituto Universitario	\N
127	9	Escuela de Economía y Negocios	\N
128	9	Escuela de Ciencia y Tecnología	\N
129	9	Secretaría General Académica	\N
130	9	Escuela de Posgrado	\N
131	9	Instituto de Tecnología	\N
132	9	Instituto de Investigaciones Biotecnológicas	\N
133	9	Escuela de Humanidades	\N
134	9	Instituto de Ciencias de la Rehabilitación	\N
135	9	Instituto de Tecnología "Prof. Jorge Sábato""	\N
136	9	Instituto de Ciencias de la Rehabilitación y el Movimiento	\N
137	9	Escuela de Política y Gobierno	\N
138	10	Instituto de Ciencias	\N
139	10	Instituto del Conurbano	\N
140	10	Instituto del Desarrollo Humano	\N
141	10	Instituto de Industria	\N
142	10	Instituto de Industrias	\N
143	11	Facultad de Ciencias Agrarias	\N
144	11	Facultad de Ingeniería	\N
145	11	Facultad de Ciencias Económicas	\N
146	11	Facultad de Humanidades y Ciencias Sociales	\N
147	12	Departamento de Ciencias Económicas	\N
148	12	Departamento de Ingeniería e Investigaciones Tecnológicas	\N
149	12	Departamento de Humanidades y Ciencias Sociales	\N
150	48	Facultad de Filosofía y Letras	\N
151	12	Instituto de Postgrado	\N
152	41	Capital Federal	\N
153	13	Departamento de Desarrollo Productivo y Trabajo	\N
154	13	Departamento de Planificación y Políticas Públicas	\N
155	13	Departamento de Humanidades y Artes	\N
156	13	Departamento de Salud Comunitaria	\N
157	13	Secretaría Académica	\N
158	14	Facultad de Agronomía	\N
159	14	Facultad de Ciencias Veterinarias	\N
160	14	Facultad de Ingeniería	\N
161	14	Facultad de Ciencias Exactas y Naturales	\N
162	69	Rectorado	\N
163	14	Facultad de Ciencias Humanas	\N
164	69	Subsede San Rafael	\N
165	24	Instituto de Relaciones Laborales, Comunicación Social y Turismo(Rectorado)	\N
166	14	Facultad de Ciencias Económicas y Jurídicas	\N
167	51	Escuela de Direccion de Empresas	\N
168	15	Unidad Academica Rio Gallegos	\N
169	15	Unidad Academica Caleta Olivia	\N
170	15	Unidad Academica Puerto San Julian	\N
171	15	Unidad Academica Rio Turbio	\N
172	15	Rectorado	\N
173	16	Facultad de Ingeniería	\N
174	16	Facultad de Ciencias Naturales	\N
175	16	Facultad de Ciencias Económicas - Sede Trelew	\N
176	16	Facultad de Humanidades y Ciencias Sociales	\N
177	36	Caseros	\N
178	16	Facultad  de Ciencias Naturales - Sede Esquel	\N
179	16	Facultad de Ingenieria - Sede Puerto Madryn	\N
180	16	Facultad de Ingeniería - Sede Trelew	\N
181	16	Facultad de Ingeniería - Sede Ushuaia	\N
182	16	Facultad de Ingenieria  - Sede Esquel	\N
183	16	Facultad de Ciencia Naturales - Sede Puerto Madryn	\N
184	16	Facultad de Ciencias Naturales - Sede Trelew	\N
185	16	Facultad de Ciencias Económicas	\N
186	16	Facultad de Ciencias Económicas - Sede Esquel	\N
187	16	Facultad de Humanidades y Ciencias Sociales - Sede Trelew	\N
188	16	Facultad Humanidades y Ciencias Sociales - Sede Ushuaia	\N
189	16	Escuela Superior de Derecho - Sede Esquel	\N
190	36	Aromos	\N
191	36	Centro Cultural Borges	\N
192	16	Escuela Superior de Derecho - Sede Puerto Madryn	\N
193	16	Escuela Superior de Derecho	\N
194	36	Saenz Peña	\N
195	18	Departamento Académico de Ciencias Exactas, Físicas y Naturales	\N
196	18	Departamento Académico de Ciencias Sociales, Jurídicas y Económicas	\N
197	18	Departamento Académico de Ciencias y Tecnologías Aplicadas a la Producción, al Ambiente y al Urbanismo	\N
198	18	Departamento Académico de Humanidades	\N
199	17	Facultad de Ciencias Agrarias y Forestales	\N
200	17	Facultad de Ciencias Veterinarias	\N
201	17	Facultad de Arquitectura y Urbanismo	\N
202	17	Facultad de Ingeniería	\N
203	17	Facultad de Ciencias Exactas	\N
204	17	Facultad de Ciencias Naturales y Museo	\N
205	17	Facultad de Ciencias Astronómicas y Geofísicas	\N
206	17	Facultad de Ciencias Económicas	\N
207	17	Facultad de Ciencias Jurídicas y Sociales	\N
208	17	Facultad de Periodismo y Comunicación Social	\N
209	17	Facultad de Humanidades y Ciencias de la Educación	\N
210	17	Facultad de Bellas Artes	\N
211	17	Facultad de Ciencias Médicas	\N
212	17	Escuela Universitaria de Recursos Humanos y Técnicos del Equipo de Salud	\N
213	17	Escuela Superior de Trabajo Social	\N
214	17	Facultad de Odontología	\N
215	17	Facultad de Informática	\N
216	67	Córdoba	\N
217	18	Departamento Académico de Ciencias de la Salud y la Educación	\N
218	18	Sede Chamical	\N
219	24	Instituto de Comercio Exterior -Sede Paso de los Libres	\N
220	18	Sede Villa Unión	\N
221	18	Sede Chepes	\N
222	18	Sede Aimogasta	\N
223	76	Facultad de Ciencias Economicas, de la Administracion y de los Negocios	\N
224	62	Rectorado	\N
225	19	Facultad de Arquitectura, Diseño y Urbanismo	\N
226	19	Facultad de Ingeniería Química	\N
227	19	Facultad de Ingeniería y Ciencias Hídricas	\N
228	77	Rectorado	\N
229	9	Instituto de Altos Estudios Sociales	\N
230	19	Facultad de Bioquímica y Ciencias Biológicas	\N
231	19	Facultad de Ciencias Económicas	\N
232	19	Facultad de Ciencias Jurídicas y Sociales	\N
233	9	Instituto de Calidad Industrial	\N
234	45	Centro de Actualización Permanente en Ingeniería de Software e Ingeniería del Conocimiento	\N
235	87	Escuela de Negocios	\N
236	34	Instituto Académico Pedagógico de Ciencias Humanas	\N
237	19	Facultad de Ciencias Veterinarias	\N
238	34	Instituto Académico Pedagógico de Ciencias Sociales	\N
239	34	Pilar	\N
240	34	Laboulaye	\N
241	19	Facultad de Humanidades y Ciencias	\N
242	32	Rectorado	\N
243	20	Facultad de Ingeniería y Ciencias Agrarias	\N
244	20	Facultad de Ingeniería	\N
245	20	Facultad de Ciencias Econòmicas	\N
246	20	Facultad de Derecho	\N
247	20	Facultad de Ciencias Sociales	\N
248	20	Rectorado	\N
249	20	Facultad de  Ciencias Agrarias	\N
250	21	Departamento de Ciencias Básicas	\N
251	21	Centro Regional Campana	\N
252	21	Centro Regional Chivilcoy	\N
253	21	Centro Regional General Sarmiento	\N
254	21	Delegación Académica Escobar	\N
255	21	Delegación Académica San Fernando	\N
256	21	Delegación Académica Pilar	\N
257	21	Delegación Académica Pergamino	\N
258	21	Delegación Académica 9 de Julio	\N
259	21	Delegacion Academica Moreno	\N
260	21	Delegacion Academica Mercedes	\N
261	21	Delegación Académica Capital Federal	\N
262	21	Departamento de Ciencias Sociales	\N
263	22	Facultad de Ciencias Agrarias	\N
264	22	Facultad de Arquitectura y Urbanismo	\N
265	22	Facultad de Ingeniería	\N
266	22	Facultad de Ciencias Exactas y Naturales	\N
267	22	Facultad de Ciencias Económicas y Sociales	\N
268	22	Facultad de Derecho	\N
269	22	Facultad de Humanidades	\N
270	22	Facultad de Psicología	\N
271	22	Facultad de Ciencias de la Salud y del Servicio Social	\N
272	23	Facultad de Ciencias Forestales	\N
273	23	Facultad de Ingeniería	\N
274	76	Facultad de Lenguas Modernas	\N
275	23	Escuela de Enfermería	\N
276	23	Facultad de Ciencias Económicas	\N
277	23	Facultad de Humanidades y Ciencias Sociales	\N
278	12	Departamento de Derecho y Ciencias Políticas	\N
279	76	Facultad de Ciencias de la Interaccion Social - Escuela de Bibliotecologia	\N
280	76	Facultad de Ciencias de la Interaccion Social - Escuela de Periodismo	\N
281	24	Facultad de Ciencias Agrarias	\N
282	24	Facultad de Ciencias Veterinarias	\N
283	24	Facultad de Arquitectura y Urbanismo -Sede Resistencia -Chaco	\N
284	24	Facultad de Ingeniería -Sede Resistencia -Chaco	\N
285	24	Facultad de Agroindustrias -Sede Presidencia Roque Saenz Peña -Chaco	\N
286	24	Facultad de Ciencias Exactas y Naturales y Agrimensura	\N
287	24	Facultad de Ciencias Económicas -Sede Resistencia -Chaco	\N
288	24	Facultad de Humanidades -Sede Resistencia -Chaco	\N
289	24	Facultad de Derecho, Ciencias Sociales y Políticas	\N
290	24	Facultad de Medicina	\N
291	24	Facultad de Odontología	\N
292	24	Dirección de Bibliotecas	\N
293	24	Carreras a Término en Comercio Exterior -Sede Paso de los Libres	\N
294	24	Instituto de Administración de Empresas Agropecuarias -Sede Curuzú Cuatiá	\N
295	24	Instituto de Economía Agropecuaria -Sede Curuzú Cuatiá	\N
296	24	Instituto de Ciencias Criminalísticas y Criminología(Rectorado)	\N
297	24	Carrera a Término de Relaciones Industriales, Comunicación Social y Turismo	\N
298	24	Carrera a Término de Relaciones Laborales, Comunicación Social y Turismo	\N
299	24	Instituto Universitario Formosa	\N
300	24	Taller de Artes Visuales	\N
301	25	Departamento de Ciencia y Tecnología	\N
302	25	Departamento de Ciencias Sociales	\N
303	25	Departamento Centro de Estudios e Investigaciones	\N
304	25	Instituto de Estudios Sociales de la Ciencia y la Tecnología	\N
305	25	Universidad Virtual de Quilmes	\N
306	25	Programa de educación no presencial Universidad Virtual de Quilmes	\N
307	26	Facultad de Agronomía y Veterinaria	\N
308	26	Facultad de Ingeniería	\N
309	26	Facultad de Ciencias Exactas, Físico-Químicas y Naturales	\N
310	26	Facultad de Ciencias Económicas	\N
311	26	Facultad de Ciencias Humanas	\N
312	26	Secretaria Académica	\N
313	27	Facultad de Ciencias Agrarias	\N
314	27	Facultad de Ciencias Veterinarias	\N
315	27	Facultad de Arquitectura, Planeamiento y Diseño	\N
316	27	Facultad de Ciencias Exactas, Ingeniería y Agrimensura	\N
317	27	Facultad de Ciencias Bioquímicas y Farmacéuticas	\N
318	27	Facultad de Ciencias Económicas y Estadística	\N
319	27	Facultad de Derecho	\N
320	27	Facultad de Ciencia Política y Relaciones Internacionales	\N
321	27	Facultad de Humanidades y Artes	\N
322	27	Facultad de Psicología	\N
323	27	Facultad de Ciencias Médicas	\N
324	27	Facultad de Odontología	\N
325	27	Instituto Politécnico Superior General San Martín	\N
326	27	Escuela Superior de Comercio Libertador General San Martín	\N
327	27	Centro de Estudios Interdisciplinarios	\N
328	21	Departamento de Educación	\N
329	21	Departamento de Tecnología	\N
330	79	Licenciatura en Ciencias de la Educación- Ciclo de Licenciatura	\N
331	28	Facultad de Ingeniería	\N
332	28	Facultad de Ciencias Exactas	\N
333	28	Facultad de Ciencias Naturales	\N
334	28	Facultad de Ciencias Económicas, Jurídicas y Sociales	\N
335	28	Facultad de Humanidades	\N
336	28	Facultad de Ciencias de la Salud	\N
337	28	Sede Regional Orán	\N
338	28	Sede Regional Tartagal	\N
339	29	Facultad de Ingeniería	\N
340	29	Facultad de Arquitectura, Urbanismo y Diseño	\N
341	29	Facultad de Ciencias Exactas, Físicas y Naturales	\N
342	29	Facultad de Ciencias Sociales	\N
343	29	Facultad de Filosofía, Humanidades y Artes	\N
344	30	Facultad de Ingeniería y Ciencias Económico-Sociales	\N
345	30	Facultad de Cs. Físico-Matemáticas y Naturales	\N
346	30	Facultad de Química, Bioquímica y Farmacia	\N
347	30	Facultad de Ciencias Humanas	\N
348	30	Departamento de Ens. Téc. Instrumental	\N
349	30	Departamento de Educación a Distancia y Abierta	\N
350	31	Facultad de Agronomía y Agroindustrias	\N
351	31	Facultad de Ciencias Forestales	\N
352	31	Facultad de Ciencias  Exactas y Tecnológicas	\N
353	31	Facultad de Humanidades, Cs. Sociales y de la Salud	\N
354	31	Escuela para la Innovación Educativa	\N
355	31	Secretaría Académica	\N
356	32	Departamento de Agronomía	\N
357	32	Departamento de Ingeniería	\N
358	32	Departamento de Ingeniería Eléctrica	\N
359	32	Departamento de Química e Ingeniería Química	\N
360	32	Departamento de Física	\N
361	32	Departamento de Matemática	\N
362	32	Departamento de Biología, Bioquímica y Farmacia	\N
363	32	Departamento de Geología	\N
364	32	Departamento de Ciencias de la Administración	\N
365	32	Departamento de Economía	\N
366	32	Departamento de Humanidades	\N
367	32	Departamento de Geografía	\N
368	32	Departamento de Ciencias de la Computación	\N
369	32	Departamento de Derecho	\N
370	32	CEMS	\N
371	32	Escuela Normal Superior	\N
372	32	Escuela de Agricultura y Ganadería	\N
373	32	Escuela Ciclo Básico	\N
374	32	Escuela Superior de Comercio	\N
375	32	Departamento de Química	\N
376	32	Departamento de Ingeniería Química	\N
377	33	Facultad de Agronomía y Zootecnia	\N
378	33	Facultad de Arquitectura y Urbanismo	\N
379	33	Facultad de Ciencias Exactas y Tecnología	\N
380	33	Facultad de Ciencias Naturales	\N
381	33	Facultad de Bioquímica, Química y Farmacia	\N
382	33	Facultad de Ciencias Económicas	\N
383	33	Facultad de Derecho y Ciencias Sociales	\N
384	33	Facultad de Filosofía y Letras	\N
385	33	Escuela Universitaria de Educación Física	\N
386	33	Facultad de Artes	\N
387	79	Licenciatura en Ciencias de la Educación- Ciclo de Licenciatura	\N
388	33	Facultad de Medicina	\N
389	33	Escuela Universitaria de Enfermería	\N
390	33	Facultad de Odontología	\N
391	33	Facultad de Psicología	\N
392	79	Licenciatura en Ciencias de la Educación- Ciclo de Licenciatura	\N
393	79	Licenciatura en Ciencias de la Educación- Ciclo de Licenciatura	\N
394	40	Facultad de Ciencias Juridicas y Sociales	\N
395	40	Centro de Educación a Distancia	\N
396	40	Facultad de Ciencias Biomédicas	\N
397	40	Facultad de Ciencias de la Criminalística	\N
398	34	Instituto Académico Pedagógico de Ciencias Básicas y Aplicadas	\N
399	35	Facultad Regional Avellaneda	\N
400	35	Facultad Regional Bahía Blanca	\N
401	35	Facultad Regional Buenos Aires	\N
402	35	Facultad Regional Concepción del Uruguay	\N
403	35	Facultad Regional Córdoba	\N
404	35	Facultad Regional Delta	\N
405	35	Facultad Regional General Pacheco	\N
406	35	Facultad Regional Haedo	\N
407	35	Facultad Regional La Plata	\N
408	35	Facultad Regional Mendoza	\N
409	35	Facultad Regional Paraná	\N
410	35	Facultad Regional Resistencia	\N
411	35	Facultad Regional Rosario	\N
412	35	Facultad Regional San Francisco	\N
413	35	Facultad Regional San Nicolás	\N
414	35	Facultad Regional San Rafael	\N
415	35	Facultad Regional Santa Fé	\N
416	35	Facultad Regional Tucumán	\N
417	35	Facultad Regional Villa María	\N
418	35	Unidad Académica Concordia	\N
419	35	Unidad Académica Confluencia	\N
420	35	Unidad Académica Chubut	\N
421	35	Unidad Académica La Rioja	\N
422	35	Unidad Académica Rafaela	\N
423	35	Unidad Académica Reconquista	\N
424	35	Unidad Académica Rio Gallegos	\N
425	35	Unidad Académica Río Grande	\N
426	35	Unidad Académica Trenque Lauquen	\N
427	35	Unidad Académica Venado Tuerto	\N
428	35	Facultad Regional Rafaela	\N
429	35	Facultad Regional Rio Grande	\N
430	35	Facultad Regional Venado Tuerto	\N
431	35	Facultad Regional Rawson	\N
432	36	Secretaría Académica	\N
433	37	Unidad Académica Colegio Militar de la Nación	\N
434	37	Unidad Académica Escuela Superior Técnica	\N
435	37	Unidad Académica Escuela Superior de Guerra	\N
436	37	Unidad Académica Escuela de Defensa Nacional (Asociada)	\N
437	38	Escuela de Ingeniería Aeronáutica	\N
438	38	Facultad de Educación a Distancia	\N
439	38	Facultad de Ingeniería	\N
440	38	Facultad de Ciencias de la Administración	\N
441	39	Unidad Academica Escuela Nacional de Nautica	\N
442	39	Unidad Académica Escuela Naval Militar	\N
443	39	Unidad Académica Escuela de Oficiales de la Armada	\N
444	39	Unidad Académica Escuela de Guerra Naval	\N
445	39	Unidad Académica Escuela de Ciencias del Mar	\N
446	39	Unidad Academica Escuela Ciencias del Mar	\N
447	39	Unidad Academica Escuela de Guerra Naval	\N
448	40	Sede	\N
449	43	Sede Central	\N
450	44	Departamento de Artes Visuales "Prilidiano Pueyrredón"	\N
451	44	Departamento de Artes Musicales y Sonoras "Carlos Lopez Buchardo"	\N
452	44	Departamento de Artes del Movimiento "Maria Ruanova"	\N
453	44	Departamento de Artes Dramáticas "Antonio Cunill Cabanellas"	\N
454	44	Carreras de Folklore	\N
455	44	Carreras de Formación Docente	\N
456	45	Rectorado	\N
457	48	Facultad de Ciencias Físico Matemáticas e Ingeniería	\N
458	71	Escuela de Educacion Superior	\N
459	48	Facultad de Ciencias Sociales y Económicas	\N
460	48	CENTRO REGIONAL PERGAMINO	\N
461	48	Facultad de Ciencias Económicas del Rosario	\N
462	48	Facultad de Derecho y Ciencias Sociales del Rosario	\N
463	48	Facultad de Derecho	\N
464	75	Rectorado	\N
465	48	Facultad de Humanidades Teresa de Avila	\N
466	48	Facultad de Humanidades y Ciencias de la Educación	\N
467	71	Escuela Politica y Gestion Publica	\N
468	48	Facultad de Artes y Ciencias Musicales	\N
469	48	Facultad de Química e Ingeniería Fray Rogelio Bacon	\N
470	48	Facultad de Ciencias Económicas San Francisco	\N
471	48	Facultad de Derecho Canónico	\N
472	48	Facultad de Teología	\N
473	48	Facultad de Posgrado en Ciencias de la Salud	\N
474	48	Instituto de Comunicación Social, Periodismo y Publicidad	\N
475	76	Rectorado	\N
476	48	Facultad de Ciencias Agrarias	\N
477	97	Escuela de Prefectura "General Matias de Irigoyen"	\N
478	97	Instituto de Formacion, Perfeccionamiento y Actualizacion Docente	\N
479	97	Departamento Academico Buenos Aires "Prefectura Naval Argentina" de la Universidad de Santiago del Estero	\N
480	48	Subsede Paraná de la Facultad de Derecho	\N
481	49	Facultad de Tecnología Informática	\N
482	49	Facultad de Ciencias Jurídicas	\N
483	49	Facultad de Ciencias Empresariales	\N
484	49	Facultad de Arquitectura.	\N
485	49	Facultad de Desarrollo e Investigación Educativos.	\N
486	49	Facultad de Motricidad Humana y Deportes	\N
487	49	Facultad de Medicina.	\N
488	49	Facultad de Psicología	\N
489	49	Facultad de Ciencias de la Comunicación.	\N
490	49	Facultad de Turismo y Hospitalidad	\N
491	51	Facultad de Ciencias Agrarias	\N
492	51	Facultad de Ingeniería	\N
493	51	Facultad de Ciencias de la Administración	\N
494	51	Facultad de Ciencias Económicas	\N
495	51	Facultad de Ciencias Sociales y Jurídicas	\N
496	51	Facultad de Artes y Ciencias	\N
497	52	Escuela de Arquitectura	\N
498	52	Profesorado en Informática	\N
499	52	Escuela de Sistemas	\N
500	52	Escuela de Bioquímica	\N
501	52	Escuela de Farmacia	\N
502	52	Escuela de Química	\N
503	52	Escuela de Administración	\N
504	52	Escuela de Contador Público	\N
505	52	Escuela de Comercialización	\N
506	52	Escuela de Abogacía	\N
507	52	Escuela de Ciencia Política	\N
508	52	Escuela de Sociología	\N
509	52	Escuela de Servicio Social	\N
510	52	Escuela de Periodismo y Comunicaciones	\N
511	52	Escuela de Publicidad	\N
512	52	Escuela de Relaciones Laborales	\N
513	52	Escuela de Relaciones Públicas	\N
514	52	Escuela de Demografía y Turismo	\N
515	52	Escuela de Ciencias de la Educación	\N
516	52	Escuela de Antropología	\N
517	52	Escuela de Psicología	\N
518	52	Escuela de Psicopedagogía	\N
519	52	Escuela de Artes y Ciencias del Teatro	\N
520	52	Escuela de Graduados	\N
521	52	Escuela de Administración Hotelera	\N
522	52	Escuela de Comercio Internacional	\N
523	52	Escuela de Diseño Gráfico	\N
524	52	Escuela de Relaciones Internacionales	\N
525	52	Escuela de Odontología	\N
526	53	Universidad Atlántida Argentina	\N
527	53	Unidad Academica Dolores	\N
528	53	Unidad Academica General Madariaga	\N
529	53	Unidad Academica Pinamar	\N
530	53	Unidad Academica Mar del Plata	\N
531	54	Facultad de Ciencias Biomédicas	\N
532	54	Facultad de Ciencias Empresariales	\N
533	54	Facultad de Ingeniería	\N
534	54	Facultad de Derecho	\N
535	54	Instituto de Altos Estudios Empresariales	\N
536	54	Facultad de Ciencias de la Información	\N
537	56	Departamento de Ciencias Pedagogicas	\N
538	56	Departamento de Sistemas	\N
539	56	Departamento de Matemáticas	\N
540	56	Departamento de Ciencias Biológicas	\N
541	56	Departamento de Administración	\N
542	56	Departamento de Ciencias Interdisciplinarias	\N
543	56	Departamento de Filosofía	\N
544	56	Departamento de Psicopedagogía	\N
545	56	Departamento de Humanidades	\N
546	56	Departamento de Escuela de Posgrado	\N
547	57	Facultad de Ciencias Agropecuarias	\N
548	57	Facultad de Arquitectura	\N
549	57	Facultad de Ingeniería	\N
550	57	Facultad de Ciencias Químicas	\N
551	57	Facultad de Ciencias Económicas y de Administración	\N
552	57	Facultad de Derecho y Ciencias Sociales	\N
553	57	Facultad de Ciencia Política y Relaciones Internacionales	\N
554	57	Facultad de Filosofía y Humanidades	\N
555	57	Facultad de Medicina	\N
556	57	Instituto de Ciencias de la Administración	\N
557	101	Convenio Universidad Nacional de San Juan	\N
558	58	Facultad de Ciencias de la Alimentación, Bioquímicas y Farmacéuticas	\N
559	58	Facultad de Ciencias Económicas y Empresariales	\N
560	58	Facultad de Derecho y Ciencias Sociales	\N
561	101	Convenio Universidad Católica de Cuyo	\N
562	58	Facultad de Filosofía y Humanidades	\N
563	58	Facultad de Ciencias Médicas	\N
564	101	Convenio Universidad Nacional de Rosario	\N
565	59	Facultad de Arquitectura	\N
566	59	Facultad de Matemática Aplicada	\N
567	59	Facultad de Ciencias Económicas	\N
568	59	Facultad de Derecho	\N
569	59	Facultad de Ciencias Sociales	\N
570	59	Facultad de Ciencias de la Educación	\N
571	59	Unidad Académica Bernal. Facultad de Arquitectura	\N
572	59	Unidad Académica Bernal. Facultad de Matemática Aplicada	\N
573	59	Unidad Académica Bernal. Facultad de Ciencias Económicas	\N
574	59	Unidad Académica Bernal. Facultad de Derecho	\N
575	59	Unidad Académica Bernal. Facultad de Ciencias de la Educación	\N
576	60	Facultad de Arquitectura y Urbanismo	\N
577	60	Facultad de Ingeniería e Informática	\N
578	60	Facultad de Economía y Administración	\N
579	60	Facultad de Ciencias Jurídicas	\N
580	60	Escuela de Servicio Social	\N
581	60	Facultad de Artes y Ciencias	\N
582	60	Escuela Universitaria de Educación Física	\N
583	60	Anexo Metan - Escuela Universitaria de Profesorados	\N
584	60	Escuela de Turismo	\N
585	60	Facultad de Economia y Administración - Inst.de Educ.Abierta y a Distancia	\N
586	60	Facultad de Ciencias Informáticas	\N
587	60	Subsede Académica Buenos Aires	\N
588	60	Subsede - Junta Provincial del Oporto	\N
589	60	Rectorado	\N
590	60	Facultad de Ciencias Jurídicas-Inst.de Educ.Abierta y a Distancia	\N
591	60	Subsede Académica Buenos Aires-Inst.de Educ. Abierta y a Distancia	\N
592	61	Facultad de Arquitectura	\N
593	61	Facultad de Ingeniería, Geoecología y Medio Ambiente	\N
594	61	Facultad de Ciencias Económicas	\N
595	61	Facultad de Derecho	\N
596	61	Facultad de Filosofía	\N
597	61	Facultad de Letras	\N
598	61	Facultad de Ciencias de la Educación	\N
599	61	Departamento de Posgrado	\N
600	61	Facultad de Historia	\N
601	61	POSADAS	\N
602	61	Facultad de Ciencias de la Comunicación	\N
603	61	Facultad de Humanidades	\N
604	62	Facultad de Matemática Aplicada	\N
605	62	Facultad de Ciencias Económicas	\N
606	62	Facultad de Ciencias Políticas, Sociales y Jurídicas	\N
607	62	Facultad de Ciencias de la Educación	\N
608	62	Departamento Académico San Salvador	\N
609	62	Departamento Académico Buenos Aires	\N
610	62	Departamento Académico Rafaela	\N
611	63	Facultad de Ciencias Económicas	\N
612	63	Facultad de Informática	\N
613	63	Facultad de Ciencias Sociales	\N
614	63	Facultad de Derecho	\N
615	64	Facultad de Ciencias Agrarias	\N
616	64	Facultad de Arquitectura y Urbanismo	\N
617	64	Facultad de Ingeniería	\N
618	64	Facultad de Tecnología Informática	\N
619	64	Facultad de Ciencias Económicas	\N
620	64	Facultad de Derecho y Ciencias Sociales	\N
621	64	Facultad de Humanidades	\N
622	64	Facultad de Lenguas y Estudios Extranjeros	\N
623	64	Facultad de Ciencias de la Salud	\N
624	64	Facultad de Estudios a Distancia	\N
625	64	Facultad de Ciencias Exactas y Naturales	\N
626	64	Escuela de Economía	\N
627	64	Facultad de Estudios para Graduados	\N
628	40	Facultad de Ciencias de la Seguridad	\N
629	54	Escuela de Direccion de Negocios	\N
630	14	Facultad de Ciencias Humanas	\N
631	14	CASTEX	\N
632	14	9 DE JULIO	\N
633	66	Facultad de Ciencias Agrarias	\N
634	66	Facultad de Arquitectura y Urbanismo	\N
635	66	Facultad de Ciencias Económicas	\N
636	66	Centro Regional Gualeguaychú	\N
637	66	Facultad de Ciencias Jurídicas y Sociales	\N
638	67	Mendoza	\N
639	68	Facultad de Administración	\N
640	68	Facultad de Actividad Física y Deporte	\N
641	68	Facultad de Derecho	\N
642	68	Facultad de Ingeniería	\N
643	68	Facultad de Psicología y Ciencias Sociales	\N
644	68	Facultad de Planeamiento Socio - Ambiental	\N
645	69	Facultad de Arquitectura y Urbanismo	\N
646	69	Facultad de Ingeniería	\N
647	69	Facultad de Ciencias Jurídicas y Sociales	\N
648	69	Facultad de Ciencias de la Salud	\N
649	70	Rectorado	\N
650	70	Facultad de Arquitectura, Diseño, Arte y Urbanismo	\N
651	70	Facultad de Ingeniería	\N
652	70	Facultad de Ciencias Exactas, Químicas y Naturales	\N
653	70	Facultad de Ciencias Económicas y Empresariales	\N
654	70	Facultad de Informática, Ciencias de la Comunicación y Técnicas Especiales	\N
655	70	Facultad de Filosofía, Ciencias de la Educación y Humanidades	\N
656	70	Escuela Diocesana de Servicio Social	\N
657	70	Facultad de Medicina	\N
658	70	Facultad de Agronomía	\N
659	70	Facultad de Derecho y Ciencias Sociales	\N
660	70	Facultad de Estudios Turísticos	\N
661	70	Facultad de Agronomía y Ciencias Agroalimentarias	\N
662	70	Facultad de Derecho, Ciencias Políticas y Sociales	\N
663	70	Facultad de Ciencias  Aplicadas al Turismo y la Población	\N
664	71	Facultad de Arquitectura	\N
665	71	Facultad de Ciencias Económicas y Empresariales	\N
666	71	Facultad de Derecho	\N
667	71	Facultad de Ciencias Sociales	\N
668	71	Facultad de Diseño y Comunicación	\N
669	71	Facultad de Ingenieria	\N
670	44	Departamento de Artes Audiovisuales	\N
671	72	Departamento de Administración	\N
672	72	Departamento de Economia	\N
673	72	Departamento de Humanidades	\N
674	73	Facultad de Ciencias Sociales y Administrativas	\N
675	73	Facultad de Ciencias Sociales y Administrativas	\N
676	73	Facultad de Psicología	\N
677	73	Facultad de Ciencias Médicas	\N
678	73	Escuela Superior de Lenguas Extranjeras	\N
679	73	Facultad de Ciencias Económicas y Jurídicas	\N
680	74	Facultad de Ciencias Económicas y Empresariales	\N
681	74	Facultad de Química	\N
682	75	Facultad de Cinematografía	\N
683	75	Facultad de Comunicación	\N
684	76	Facultad de Ciencias Políticas. Jurídicas y Económicas	\N
685	76	Facultad de Servicio Social	\N
686	76	Facultad de Ciencias de la Información y Opinión	\N
687	76	Escuela Universitaria de Lenguas	\N
688	76	Facultad de Ciencias de la Recuperación Humana	\N
689	76	Facultad de Ciencias Jurídicas y Políticas	\N
690	76	Facultad de Ciencias Económicas, de la Administración y de los Negocios	\N
691	76	Facultad de Ciencias Psicológicas y Pedagógicas	\N
692	24	Rectorado	\N
693	76	Facultad de Ciencias de la Interacción Social-Escuela de Servicio Social	\N
694	76	Facultad de Artes y Ciencias de la Conservación	\N
695	76	Departamento de Posgrado	\N
696	77	Facultad de Ingeniería	\N
697	77	Facultad de Economía y Administración	\N
698	77	Facultad de Ciencias Jurídicas y Sociales	\N
699	77	Facultad de Humanidades	\N
700	77	Centro de Estudios Institucionales	\N
701	77	Centro Universitario de Concepción	\N
702	77	Instituto Superior de Trabajo Social Juan XXIII	\N
703	77	Facultad de Antropologia y Psicologia	\N
704	77	Facultad de Derecho y Ciencias Políticas	\N
705	77	Centro de Estudios Institucionales (Tuc)	\N
706	77	Facultad de Psicologia y Ciencias de la Salud	\N
707	77	Facultad de Filosofía	\N
708	77	Centro de Estudios Institucionales (Bs.As)	\N
709	77	Escuela de Cs. de la Educación	\N
710	44	Rectorado	\N
711	44	Carreras de Multimedia	\N
712	16	Escuela Superior de Derecho - Sede Trelew	\N
713	23	Facultad de Ciencias Exactas, Químicas y Naturales	\N
714	23	Facultad de Artes	\N
715	23	Delegación BUENOS AIRES	\N
716	95	Facultad de Humanidades, Artes y Ciencias Sociales -Sede Principal	\N
717	95	Facultad de Ciencia y Tecnología -Sede Principal	\N
718	95	Facultad de Ciencia de la Gestión -Sede Principal	\N
719	95	Facultad de Ciencias de la Vida y Salud -Sede Principal	\N
720	95	Facultad de Ciencia y Tecnología - Sede Basavilbaso	\N
721	95	Facultad de Ciencia y Tecnología - Sede Chajari	\N
722	95	Facultad de Ciencias de la Gestión - Sede Chajari	\N
723	95	Facultad de Humanidades, Artes y Ciencias Sociales - Sede Concepción del Uruguay	\N
724	95	Facultad de Ciencia y Tecnología - Sede Concepción del Uruguay	\N
725	95	Facultad de Ciencias de la Gestión - Sede concepción del Uruguay	\N
726	78	Pilar - Carreras de Agronomía y Tecnología de los Alimentos	\N
727	95	Facultad de Ciencia y Tecnología -Sede Crespo	\N
728	95	Facultad de Ciencias de la Gestión - Sede Crespo	\N
729	78	Delegación Provincia de Corrientes	\N
730	78	Pilar - Carrera de Veterinaria	\N
731	95	Facultad de Ciencia y Tecnología Sede Diamante	\N
732	48	Instituto de Ciencias Políticas y Relaciones Internacionales	\N
733	79	Facultad de Ciencias Sociales	\N
734	79	Facultad de Ciencias Económicas	\N
735	79	Facultad de Ingeniería	\N
736	80	Facultad de Ingeniería	\N
737	80	Facultad de Ciencias Económicas	\N
738	80	Facultad de Ciencias Jurídicas y Sociales	\N
739	80	Facultad de Ciencias de la Salud	\N
740	80	Facultad de Humanidades	\N
741	82	Facultad Derecho y Letras	\N
742	82	Facultad de Economia y Administracion	\N
743	83	Facultad de Ciencias Biologicas	\N
744	83	Facultad de Humanidades	\N
745	84	Facultad de Ingeniería	\N
746	84	Facultad de Ciencias Físicas, Químicas y  Matemáticas	\N
747	84	Facultad de Farmacia y Bioquímica	\N
748	84	Facultad de Periodismo	\N
749	84	Facultad de Ciencias de la Nutrición	\N
750	84	Facultad Tecnológica de Enología y de Industria Frutihortícola	\N
751	84	Facultad de Ciencias Veterinarias y Ambientales	\N
752	84	Facultad de Kinesiología y Fisioterapia	\N
753	84	Facultad de Ciencias Empresariales	\N
754	84	Rectorado	\N
755	84	Facultad de Educación Física	\N
756	85	Facultad de Medicina	\N
757	85	Facultad de Odontología	\N
758	85	Facultad de Humanidades,Ciencias Sociales y Empresariales	\N
759	85	Escuela de Comunicación Multimedial y Gráfica	\N
760	87	Escuela de Derecho	\N
761	95	Facultad de Ciencia y Tecnología - Sede Federación	\N
762	87	Departamento de Economía	\N
763	87	Departamento de Ciencia Política y Estudios Internacionales	\N
764	87	Departamento de Matemática y Estadística	\N
765	87	Centro de Arquitectura Contemporánea	\N
766	87	Departamento de Historia	\N
767	95	Facultad de Ciencias de la Vida y Salud - Sede Gualeguay	\N
768	89	Departamento de Administración y Comercialización	\N
769	89	Departamento de Informática	\N
770	60	Escuela de Negocios	\N
771	60	Escuela de Educación Permanente y Posgrados en Ciencias de la Salud	\N
772	38	Instituto Nacional de Derecho Aeronáutico y Espacial	\N
773	56	SEDE MAR DEL PLATA	\N
774	59	Facultad de Humanidades	\N
775	19	Facultad de Ciencias Agrarias	\N
776	78	Facultad de Ciencias de la Administración	\N
777	78	Facultad de Ciencias de la Educación y de la Comunicación Social	\N
778	78	Facultad de Ciencias Económicas	\N
779	78	Facultad de Ciencias Jurídicas	\N
780	78	Facultad de Ciencias Sociales	\N
781	78	Facultad de Ciencia y Tecnología	\N
782	78	Facultad de Filosofía. Historia y Letras	\N
783	78	Facultad de Medicina	\N
784	78	Facultad de Psicología y Psicopedagogía	\N
785	78	Escuela de Arte y Arquitectura	\N
786	78	Escuela de Estudios Orientales	\N
787	78	Subsede Mercedes	\N
788	78	Pilar - Facultad de Ciencias Jurídicas	\N
789	78	Vicerrectorado de Investigación y Desarrollo - Instituto de Prevención de la Drogadependencia	\N
790	78	Instituto de Educación	\N
791	78	Pilar - Facultad de Filosofía, Historia y Letras	\N
792	78	Delegación Posadas	\N
793	78	Subsede Córdoba	\N
794	78	Subsede Venado Tuerto	\N
795	78	Subsede Bahía Blanca	\N
796	78	Subsede Salta	\N
797	78	Subsede Gualeguaychú	\N
798	78	Subsede Rosario	\N
799	78	Subsede Santa Rosa	\N
800	78	Subsede Río Grande - Ushuaia	\N
801	78	Pilar - Facultad de Ciencias de la Administración	\N
802	78	Pilar - Facultad de Ciencias de la Educación y de la Comunicación Social	\N
803	78	Pilar - Facultad de Ciencias Sociales	\N
804	78	Pilar - Facultad de Ciencias Económicas	\N
805	78	Pilar - Facultad de Psicología y Psicopedagogía	\N
806	78	Pilar - Escuela de Arte y Arquitectura	\N
807	78	Vicerrectorado Académico	\N
808	72	Departamento de Matemática y Ciencias	\N
809	72	Escuela de Educación	\N
810	56	SAN ISIDRO	\N
811	68	Facultad de Planeamiento Socio - Ambiental - COMAHUE	\N
812	68	Facultad de Psicología y Ciencias Sociales - COMAHUE	\N
813	68	Facultad de Actividad Física y Deporte - COMAHUE	\N
814	68	Facultad de Administracion - COMAHUE	\N
815	5	Escuela de Graduados de la Fac.de Ccias.Económicas	\N
816	19	Facultad de Medicina	\N
817	19	Escuela Universitaria del Alimento	\N
818	19	Escuela Universitaria de Analisis de Alimentos	\N
819	3	Facultad de Agronomía	\N
820	3	Escuela Superior de Derecho	\N
821	96	Rosario	\N
822	3	Facultad de Ingeniería	\N
823	3	Facultad de Ciencias Sociales	\N
824	3	Facultad de Arte	\N
825	3	Escuela Superior de Ciencias de la Salud	\N
826	64	Departamento de Estudios de Postgrados	\N
827	64	Rectorado	\N
828	96	Buenos Aires	\N
829	65	Facultad de Ciencias Empresariales	\N
830	65	Facultad de Ciencias Económicas	\N
831	65	Facultad de Comunicación Social	\N
832	65	Facultad de Ciencias de la Salud	\N
833	65	Facultad de Ciencias Jurídicas y Sociales	\N
834	65	Escuela de Negocios, Masters y Posgrados	\N
835	85	Escuela der Farmacia y Bioquímica	\N
836	65	Subsede RAFAELA	\N
837	65	Subsede SAN FRANCISCO	\N
838	65	Subsede SAN ISIDRO - BUENOS AIRES	\N
839	74	IUCS BARCELO - BUENOS AIRES	\N
840	74	Escuela de Lenguas y Perfeccionamiento Docente	\N
841	27	Subsede BAHIA BLANCA	\N
842	27	Escuela Agrotécnica	\N
843	95	Facultad Ciencias de la Gestión - Sede Gualeguaychú	\N
844	95	Facultad de Humanidades, Artes y Ciencias Sociales - Sede La Picada	\N
845	95	Facultad de Humanidades,Artes y Ciencias Sociales - Sede Oro Verde.	\N
846	95	Facultad de Ciencia y Tecnología - Sede Oro Verde	\N
847	95	Facultad de Ciencias de la Vida y Salud - Sede Ramirez	\N
848	95	Facultad de Ciencias de la Gestión - Sede Villaguay	\N
849	35	Rectorado	\N
850	41	Sede - La Plata	\N
851	41	Rectorado	\N
852	71	Escuela de Turismo y Hoteleria	\N
853	57	Facultad de Educación	\N
854	58	Facultad DON BOSCO de Enología y Ciencias de la Alimentación	\N
855	58	Sede SAN LUIS	\N
856	58	Fundación ALTA DIRECCION	\N
857	101	FLACSO	\N
858	71	Graduate School of Business	\N
859	47	ROSARIO	\N
860	58	Instituto de Formación Docente	\N
861	58	Instituto Cervantes	\N
862	101	Unión de Educadores de la Pcia. de Córdoba	\N
863	102	CAPITAL FEDERAL	\N
864	102	MARTINEZ	\N
865	103	Facultad de Ingeniería	\N
866	91	CAPITAL FEDERAL	\N
867	90	CAPITAL FEDERAL	\N
868	103	Facultad de Ciencias Económicas	\N
869	103	Facultad de Ciencias Jurídicas	\N
870	103	Facultad de Agonomía y Ciencias Naturales	\N
871	81	Facultad de Ingeniería	\N
872	93	Escuela de Medicina	\N
873	93	Escuela de Enfermería	\N
874	92	CAPITAL FEDERAL	\N
875	42	Departamento de Ingenieria	\N
876	42	Rectorado	\N
877	42	Departamento de Finanzas	\N
878	47	Facultad de Medicina	\N
879	47	LA RIOJA	\N
880	47	SANTO TOME	\N
881	55	Sede Campus	\N
882	88	CAPITAL FEDERAL	\N
883	55	Sede Centro (Distancia)	\N
884	88	SAN ISIDRO	\N
885	100	CAPITAL FEDERAL	\N
886	89	Departamento de Informática	\N
887	89	Departamento de Administración y Comercialización	\N
888	73	Colegio de la Universidad del Aconcagua	\N
889	73	Escuela Internacional de Turismo, Hoteleria y Gastronomia de Mendoza	\N
890	50	Facultad de Ciencias de la Salud	\N
891	50	Facultad de Ciencias Económicas y de la Administración	\N
892	50	Facultad de Humanidades, Educación y Ciencias Sociales	\N
893	50	Facultad de Teología	\N
894	16	Localización Petrel - Agencia ACA	\N
895	17	CNEL. PRINGLES	\N
896	17	JUNIN	\N
897	20	Goya	\N
898	20	Mercedes	\N
899	20	Reconquista	\N
900	33	AGUILARES	\N
901	66	Centro Regional Federación	\N
902	66	Centro Regional Paraná	\N
903	66	Facultad de Ciencias de la Comunicación y de la Educación	\N
904	66	ROSARIO	\N
905	46	Facultad de Ingeniería y Ciencias Exactas y Naturales	\N
906	46	Facultad de Ciencias Médicas	\N
907	46	Facultad de Posgrado	\N
908	73	Rectorado	\N
909	55	Rectorado	\N
910	99	Escuela de Ciencias Agrarias, Naturales y Ambientales	\N
911	99	Escuela de Ciencias Sociales y Humanas	\N
912	99	Escuela de Ciencias Económicas y Jurídicas	\N
913	99	Escuela de Tecnología	\N
914	99	Rectorado	\N
915	99	Unidad Académica Pergamino	\N
916	18	Catuna	\N
917	73	Instituto Superior del Profesorado "San Pedro Nolasco"	\N
918	81	Facultad de Administración y Economía	\N
919	81	Facultad de Humanidades y Ciencias Sociales	\N
920	94	Sede Principal - BOLOGNA	\N
921	81	Rectorado	\N
922	93	NUEVA UNIDAD ACADEMICA	\N
923	93	OTRA UNIDAD ACADEMICA	\N
924	8889	Unidad Académica 01	\N
925	8889	Unidad Académica 02	\N
926	8889	Unidad Académica 01	\N
927	8889	Unidad Académica 02	\N
\.


--
-- Data for TOC entry 20 (OID 56992553)
-- Name: soe_sedesua; Type: TABLE DATA; Schema: public; Owner: dba
--

COPY soe_sedesua (institucion, sede, unidadacad) FROM stdin;
\.


--
-- TOC entry 2 (OID 56992472)
-- Name: soe_tiposinstit_tipoinstit_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('soe_tiposinstit_tipoinstit_seq', 1, false);


--
-- TOC entry 3 (OID 56992479)
-- Name: soe_instituciones_institucion_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('soe_instituciones_institucion_seq', 1, false);


--
-- TOC entry 4 (OID 56992494)
-- Name: soe_tiposede_tiposede_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('soe_tiposede_tiposede_seq', 1, false);


--
-- TOC entry 5 (OID 56992501)
-- Name: soe_sedes_sede_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('soe_sedes_sede_seq', 1, false);


--
-- TOC entry 6 (OID 56992520)
-- Name: soe_edificios_edificio_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('soe_edificios_edificio_seq', 599, true);


--
-- TOC entry 7 (OID 56992531)
-- Name: soe_tiposua_tipoua_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('soe_tiposua_tipoua_seq', 1, false);


--
-- TOC entry 8 (OID 56992538)
-- Name: soe_unidadesacad_unidadacad_seq; Type: SEQUENCE SET; Schema: public; Owner: dba
--

SELECT pg_catalog.setval('soe_unidadesacad_unidadacad_seq', 927, true);


