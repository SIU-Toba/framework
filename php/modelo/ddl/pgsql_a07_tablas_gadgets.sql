--**************************************************************************************************
--**************************************************************************************************
--*******************************************  GADGETS  ********************************************
--**************************************************************************************************
--**************************************************************************************************

--#################################################################################################
CREATE SEQUENCE apex_gadgets_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_gadgets
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto, gadget
--: dump_where: (	proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  gadget					int8 DEFAULT nextval('"apex_gadgets_seq"'::text) NOT NULL,
  proyecto				    VARCHAR(15) NOT NULL,
  gadget_url			  VARCHAR(250) NULL,
  titulo						 VARCHAR(50) NULL,
  descripcion			 VARCHAR(250) NULL,
  tipo_gadget			 CHAR(1) NOT NULL,
  subclase				   VARCHAR(80) NULL,
  subclase_archivo	VARCHAR(255) NULL,
  CONSTRAINT "apex_gadget_pk" PRIMARY KEY ("proyecto", "gadget"),
  CONSTRAINT "apex_usuario_proyecto_gadgets_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE TABLE apex_usuario_proyecto_gadgets
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto, gadget, usuario
--: dump_where: (	proyecto =	'%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
--: instancia: 1
---------------------------------------------------------------------------------------------------
(
  usuario				  VARCHAR(60) NOT NULL,
  proyecto				  VARCHAR(15) NOT NULL,
  gadget				   INTEGER		NOT NULL,
  orden						INTEGER		NOT NULL DEFAULT 1,
  eliminable			CHAR(1) NOT NULL DEFAULT 'S',
  CONSTRAINT "apex_usuario_proyecto_gadgets_pk" PRIMARY KEY ("usuario", "proyecto", "gadget"),
  CONSTRAINT "apex_usuario_proyecto_gadgets_fk_usuario" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT "apex_usuario_proyecto_gadgets_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT "apex_usuario_proyecto_gadgets_fk_gadget" FOREIGN KEY ("proyecto", "gadget") REFERENCES "apex_gadgets" ("proyecto", "gadget") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);