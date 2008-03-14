-- Creacion de constraints que no son necesarios en el esquema de instancia minima

	ALTER TABLE apex_proyecto ADD CONSTRAINT	"apex_proyecto_fk_menu" 
	FOREIGN KEY ("menu") 
	REFERENCES	"apex_menu" ("menu") 
	ON DELETE NO ACTION	
	ON	UPDATE NO ACTION 
	DEFERRABLE 
	INITIALLY
	IMMEDIATE;

	ALTER TABLE apex_proyecto ADD CONSTRAINT	"apex_proyecto_fk_pagina_tipo" 
	FOREIGN KEY ("proyecto", "pagina_tipo") 
	REFERENCES	"apex_pagina_tipo" ("proyecto","pagina_tipo") 
	ON DELETE NO ACTION	
	ON	UPDATE NO ACTION 
	DEFERRABLE 
	INITIALLY
	IMMEDIATE;



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

	---- Se comenta este constraint porque no permite crear una instancia sin el editor
	-- ALTER TABLE apex_clase ADD CONSTRAINT	"apex_clase_fk_dr" 
	-- FOREIGN	KEY ("objeto_dr_proyecto","objeto_dr")	
	-- REFERENCES "apex_objeto" ("proyecto","objeto") 
	-- ON DELETE NO ACTION 
 	-- ON UPDATE NO ACTION 
	-- DEFERRABLE	
	-- INITIALLY 
	-- IMMEDIATE;
