<?php
class toba_migracion_2_5_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{		
		/**
		* Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		* it has pending trigger events' de postgres 8.3
		*/
		$sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
		$this->elemento->get_db()->ejecutar($sql);
		$sql = array();		
		
		$sql[] = "ALTER TABLE apex_servicio_web ADD COLUMN tipo text DEFAULT 'soap';";		
		$sql[] = 'ALTER TABLE apex_usuario ADD COLUMN forzar_cambio_pwd SMALLINT NOT NULL DEFAULT 0;';
		$sql[] = 'ALTER TABLE apex_usuario_pwd_usados ADD COLUMN fecha_cambio DATE NOT NULL DEFAULT (\'now\'::text)::date;';
		
		//Elimino FK dependientes y PK de apex_menu
		$sql[] = 'ALTER TABLE apex_proyecto DROP CONSTRAINT "apex_proyecto_fk_menu";';		
		$sql[] = 'ALTER TABLE apex_menu DROP CONSTRAINT "apex_menu_pk";';					
		
		//Renombro tabla y creo nuevamente la PK y FK
		$sql[] = 'ALTER TABLE apex_menu RENAME TO apex_menu_tipos;';		
		$sql[] = 'ALTER TABLE apex_menu_tipos ADD CONSTRAINT "apex_menu_tipos_pk" PRIMARY KEY ("menu");';		
		$sql[] = 'ALTER TABLE apex_proyecto ADD CONSTRAINT "apex_proyecto_fk_menu_tipos"  FOREIGN KEY ("menu") REFERENCES "apex_menu_tipos" ("menu")  ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';
		
		//Creo nueva tabla apex_menu
		$sql[] = 'CREATE TABLE apex_menu
				(
					proyecto						VARCHAR(15)		NOT NULL, 
					menu_id						VARCHAR(50)		NOT NULL, 
					descripcion					TEXT			NULL, 
					tipo_menu					varchar(40)		NOT NULL,
					CONSTRAINT	"apex_menu_pk"	PRIMARY KEY ("proyecto", "menu_id"), 
					CONSTRAINT "apex_menu_proyecto_fk"		FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
					CONSTRAINT "apex_menu_menu_tipos_fk"	FOREIGN KEY ("tipo_menu") REFERENCES "apex_menu_tipos" ("menu") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
				);';

		//Creo secuencia y tabla apex_menu_operaciones
		$sql[] = 'CREATE SEQUENCE apex_menu_operaciones_seq	INCREMENT 1	MINVALUE	1 MAXVALUE 9223372036854775807 CACHE 1;';
		$sql[] = 'CREATE TABLE apex_menu_operaciones
				(
					proyecto						VARCHAR(15)		NOT NULL, 
					menu_id						VARCHAR(50)		NOT NULL, 
					menu_elemento				BIGINT			NOT NULL DEFAULT nextval(\'"apex_menu_operaciones_seq"\'::text), 
					item							VARCHAR(60)		NULL, 
					padre						VARCHAR(60)		NULL,
					descripcion					TEXT			NULL,
					carpeta						SMALLINT		NOT NULL DEFAULT 0,
					CONSTRAINT	"apex_menu_operaciones_pk"	PRIMARY KEY ("proyecto", "menu_id", "menu_elemento"), 
					CONSTRAINT "apex_menu_operaciones_apex_proyecto_fk"	FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
					CONSTRAINT "apex_menu_operaciones_item_fk"	FOREIGN KEY ("proyecto", "item") REFERENCES "apex_item" ("proyecto", "item") ON DELETE NO ACTION  ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE, 
					CONSTRAINT "apex_menu_operaciones_auto_fk" FOREIGN KEY ("proyecto", "menu_id", "menu_elemento") REFERENCES "apex_menu_operaciones" ON DELETE NO ACTION  ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE 
				);';

		//Agrego la columna del menu a la tabla de perfiles funcionales
		$sql[] = 'ALTER TABLE apex_usuario_grupo_acc ADD COLUMN menu_usuario VARCHAR(50)';
		$sql[] = 'ALTER TABLE apex_usuario_grupo_acc  ADD CONSTRAINT "apex_usuario_grupo_acc_menu_fk" 
				FOREIGN KEY (proyecto, menu_usuario) REFERENCES apex_menu (proyecto, menu_id) ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';
		
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}
	
	function proyecto__agregar_config_rest()
	{
			$proyecto = $this->elemento->get_id();
			$dir_proyecto = $this->elemento->get_dir();	
			$destino = $dir_proyecto.'/www/rest.php';
			copy(toba_dir().'/php/modelo/template_proyecto/www/rest.php', $destino);
			$editor = new toba_editor_archivos();
			$editor->agregar_sustitucion( '|__proyecto__|', $proyecto );
			$editor->procesar_archivo($destino);
	}
}
?>
