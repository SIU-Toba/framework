-- Creacion de constraints que no son necesarios en el esquema de instancia minima

	ALTER TABLE apex_usuario_grupo_acc ADD CONSTRAINT "apex_usu_g_acc_fk_proy"
	FOREIGN KEY ("proyecto")
	REFERENCES "apex_proyecto" ("proyecto")
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	DEFERRABLE
	INITIALLY
	IMMEDIATE;

	ALTER TABLE apex_usuario_proyecto ADD CONSTRAINT "apex_usu_proy_fk_proyecto"
	FOREIGN KEY ("proyecto")
	REFERENCES "apex_proyecto" ("proyecto")
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	DEFERRABLE
	INITIALLY
	IMMEDIATE;
