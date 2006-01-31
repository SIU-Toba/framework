CREATE TABLE apex_test_paises 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: pais
--: zona: test
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  pais 					INT4			NOT NULL,
  nombre 				VARCHAR(40)		NOT NULL,
  codigoiso 			CHAR(2),
  CONSTRAINT paises_pk 		PRIMARY KEY(pais)
);
