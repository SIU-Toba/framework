
	ALTER TABLE apex_permiso_grupo_acc ADD CONSTRAINT   apex_per_grupo_acc_per_fk 
	FOREIGN KEY ( permiso , proyecto ) 
	REFERENCES  apex_permiso  ( permiso , proyecto ) 
	ON	DELETE NO ACTION 
	ON UPDATE	NO	ACTION 
	 
	 
	;

	ALTER TABLE apex_usuario_grupo_acc_item ADD CONSTRAINT	 apex_usu_item_fk_item 	
	FOREIGN KEY	( proyecto , item ) 
	REFERENCES  apex_item  ( proyecto , item )	
	ON	DELETE CASCADE 
	ON UPDATE	NO	ACTION 
	 
	 
	;

	ALTER TABLE apex_proyecto ADD CONSTRAINT	 apex_proyecto_fk_menu  
	FOREIGN KEY ( menu ) 
	REFERENCES	 apex_menu  ( menu ) 
	ON DELETE NO ACTION	
	ON	UPDATE NO ACTION 
	 
	
	;

	ALTER TABLE apex_proyecto ADD CONSTRAINT	 apex_proyecto_fk_pagina_tipo  
	FOREIGN KEY ( proyecto ,  pagina_tipo ) 
	REFERENCES	 apex_pagina_tipo  ( proyecto , pagina_tipo ) 
	ON DELETE NO ACTION	
	ON	UPDATE NO ACTION 
	 
	
	;



	ALTER TABLE apex_usuario_grupo_acc ADD CONSTRAINT  apex_usu_g_acc_fk_proy 
	FOREIGN KEY ( proyecto )
	REFERENCES  apex_proyecto  ( proyecto )
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	
	
	;

	ALTER TABLE apex_usuario_proyecto ADD CONSTRAINT  apex_usu_proy_fk_proyecto 
	FOREIGN KEY ( proyecto )
	REFERENCES  apex_proyecto  ( proyecto )
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	
	
	;

