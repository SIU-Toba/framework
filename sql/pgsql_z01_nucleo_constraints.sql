-----------------------------------------------------------------------------------
-- Estas sentencias hay que crearlas despues de insertar los registros del dump
-----------------------------------------------------------------------------------

-- item Padre (El dump devuelve los registros desordenados)

	ALTER TABLE apex_item ADD CONSTRAINT "apex_item_fk_padre"
	FOREIGN KEY ("padre","padre_proyecto")
	REFERENCES "apex_item" ("item","proyecto")
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE
	INITIALLY
	IMMEDIATE;
-----------------------------------------------------------------------------------
