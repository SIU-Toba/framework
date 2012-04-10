-- Creación del schema de testing y las tablas necesarias para probar la personalización de tablas a través del ap multitabla

CREATE SCHEMA "testing" AUTHORIZATION "postgres";
set search_path = testing, public, pg_catalog;

CREATE SEQUENCE "testing"."seq_maestra" INCREMENT 1  MINVALUE 1 MAXVALUE 9223372036854775807  START 149 CACHE 1;
CREATE TABLE "testing"."maestra" (
  "nombre" VARCHAR, 
  "proyecto" INTEGER DEFAULT nextval(('"seq_maestra"'::text)::regclass) NOT NULL, 
  "identificador" VARCHAR NOT NULL, 
  CONSTRAINT "maestra_idx" PRIMARY KEY("proyecto", "identificador")
) WITH OIDS;

CREATE SEQUENCE "testing"."seq_esclava" INCREMENT 1  MINVALUE 1 MAXVALUE 9223372036854775807  START 209 CACHE 1;
CREATE TABLE "testing"."esclava" (
  "id" BIGINT DEFAULT nextval(('"seq_esclava"'::text)::regclass) NOT NULL, 
  "fk_proyecto" INTEGER, 
  "fk_identificador" VARCHAR, 
  "apellido" VARCHAR, 
  CONSTRAINT "esclava_pkey" PRIMARY KEY("id"), 
  CONSTRAINT "esclava_fk" FOREIGN KEY ("fk_proyecto", "fk_identificador")
    REFERENCES "testing"."maestra"("proyecto", "identificador")
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) WITH OIDS;
