<?php

class toba_migracion_2_0_0 extends toba_migracion
{
    function instancia__cambios_estructura()
	{
		/**
		 * Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
		 * it has pending trigger events' de postgres 8.3
		 */
		$sql = "SET CONSTRAINTS ALL IMMEDIATE;";
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = array();
		
		// AP MULTITABLA
		$sql[] = 'ALTER TABLE apex_proyecto ADD extension_toba boolean  DEFAULT FALSE;';
		$sql[] = 'ALTER TABLE apex_proyecto ADD extension_proyecto boolean  DEFAULT FALSE;';

		$sql[] = 'ALTER TABLE apex_objeto_db_registros ADD tabla_ext text;';
		$sql[] = 'ALTER TABLE apex_objeto_db_registros_col ADD tabla varchar(200);';
		$sql[] = 'ALTER TABLE apex_estilo ADD es_css3 smallint	NOT NULL DEFAULT 0';
		

		$sql[] = 'CREATE SEQUENCE apex_objeto_db_columna_fks_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;';
		$sql[] = '
			CREATE TABLE apex_objeto_db_columna_fks
			(
				id								int8			DEFAULT nextval(\'"apex_objeto_db_columna_fks_seq"\'::text) 		NOT NULL,
				objeto_proyecto    			   	varchar(15)		NOT NULL,
				objeto 		                	int8       		NOT NULL,
				tabla							varchar(200)	NOT NULL,
				columna							varchar(200)	NOT NULL,
				tabla_ext						varchar(200)	NOT NULL,
				columna_ext						varchar(200)	NOT NULL,
				CONSTRAINT  "apex_obj_db_col_fks_pk" PRIMARY KEY ("id", "objeto", "objeto_proyecto"),
				CONSTRAINT  "apex_obj_db_col_fks_reg" FOREIGN KEY ("objeto_proyecto", "objeto") REFERENCES "apex_objeto_db_registros" ("objeto_proyecto", "objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
			);
		';

		// PUNTOS DE MONTAJE
		$sql[] = 'CREATE SEQUENCE apex_puntos_montaje_seq INCREMENT 1 MINVALUE 1	MAXVALUE	9223372036854775807 CACHE 1;';
		$sql[] = '
			CREATE TABLE apex_puntos_montaje
			(
				id									int8				DEFAULT nextval(\'"apex_puntos_montaje_seq"\'::text)	NOT NULL,
				etiqueta							varchar(50)			NOT NULL,
				proyecto							varchar(15)			NOT NULL,
				proyecto_ref						varchar(15)			NULL,
				descripcion							TEXT				NULL,
				path_pm								TEXT				NOT NULL,
				tipo								varchar(20)			NOT NULL,

				UNIQUE								("etiqueta","proyecto"),
				CONSTRAINT	"apex_punto_montaje_pk"	PRIMARY KEY ("id", "proyecto"),
				CONSTRAINT	"apex_proyecto_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
			);
		';
		$sql[] = 'ALTER TABLE apex_proyecto ADD pm_contexto int8;';
		//$sql[] = 'ALTER TABLE apex_proyecto ADD CONSTRAINT "apex_objeto_fk_pm_contexto" FOREIGN KEY ("pm_contexto") REFERENCES "apex_puntos_montaje"	("id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_proyecto ADD pm_sesion int8;';
		//$sql[] = 'ALTER TABLE apex_proyecto ADD CONSTRAINT "apex_objeto_fk_pm_sesion" FOREIGN KEY ("pm_sesion") REFERENCES "apex_puntos_montaje"	("id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_proyecto ADD pm_usuario int8;';
		//$sql[] = 'ALTER TABLE apex_proyecto ADD CONSTRAINT "apex_objeto_fk_pm_usuario" FOREIGN KEY ("pm_usuario") REFERENCES "apex_puntos_montaje"	("id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_proyecto ADD pm_impresion int8;';
		//$sql[] = 'ALTER TABLE apex_proyecto ADD CONSTRAINT "apex_objeto_fk_pm_impresion" FOREIGN KEY ("pm_impresion") REFERENCES "apex_puntos_montaje"	("id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_objeto ADD punto_montaje int8;';
		$sql[] = 'ALTER TABLE apex_objeto ADD CONSTRAINT "apex_objeto_fk_puntos_montaje" FOREIGN KEY ("punto_montaje", "proyecto") REFERENCES "apex_puntos_montaje"	("id", "proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_item_zona ADD punto_montaje int8;';
		$sql[] = 'ALTER TABLE apex_item_zona ADD CONSTRAINT "apex_objeto_fk_puntos_montaje" FOREIGN KEY ("punto_montaje", "proyecto") REFERENCES "apex_puntos_montaje"	("id", "proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';		
		
		$sql[] = 'ALTER TABLE apex_objeto_datos_rel ADD punto_montaje int8;';
		$sql[] = 'ALTER TABLE apex_objeto_datos_rel ADD CONSTRAINT "apex_objeto_fk_puntos_montaje" FOREIGN KEY ("punto_montaje", "proyecto") REFERENCES "apex_puntos_montaje"	("id","proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_objeto_db_registros ADD punto_montaje int8;';
		$sql[] = 'ALTER TABLE apex_objeto_db_registros ADD CONSTRAINT "apex_objeto_fk_puntos_montaje" FOREIGN KEY ("punto_montaje", "objeto_proyecto") REFERENCES "apex_puntos_montaje"	("id", "proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';
		
		$sql[] = 'ALTER TABLE apex_pagina_tipo ADD punto_montaje int8;';
		$sql[] = 'ALTER TABLE apex_pagina_tipo ADD CONSTRAINT "apex_objeto_fk_puntos_montaje" FOREIGN KEY ("punto_montaje", "proyecto") REFERENCES "apex_puntos_montaje"	("id", "proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_consulta_php ADD punto_montaje int8;';
		$sql[] = 'ALTER TABLE apex_consulta_php ADD CONSTRAINT "apex_objeto_fk_puntos_montaje" FOREIGN KEY ("punto_montaje", "proyecto") REFERENCES "apex_puntos_montaje"	("id", "proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_objeto_ci_pantalla ADD punto_montaje int8;';
		$sql[] = 'ALTER TABLE apex_objeto_ci_pantalla ADD CONSTRAINT "apex_objeto_fk_puntos_montaje" FOREIGN KEY ("punto_montaje", "objeto_ci_proyecto") REFERENCES "apex_puntos_montaje"	("id", "proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD punto_montaje int8;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD CONSTRAINT "apex_objeto_fk_puntos_montaje" FOREIGN KEY ("punto_montaje", "objeto_ei_formulario_proyecto") REFERENCES "apex_puntos_montaje"	("id", "proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		//Tabla para los ei_mapa
		$sql[] = 'CREATE TABLE apex_objeto_mapa
						(
						   objeto_mapa_proyecto   	varchar(15)		NOT NULL,
						   objeto_mapa            	int8			NOT NULL,
						   mapfile_path				varchar(200)	NULL,
						   CONSTRAINT  "apex_objeto_mapa_pk" PRIMARY KEY ("objeto_mapa_proyecto","objeto_mapa"),
						   CONSTRAINT  "apex_objeto_mapa_fk_objeto"  FOREIGN KEY ("objeto_mapa_proyecto","objeto_mapa") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
						  );';

		//Tablas para los ei_graficos
		$sql[] = 'ALTER TABLE apex_grafico ALTER COLUMN grafico TYPE varchar(20);';
		
		$sql [] = 'CREATE TABLE apex_objeto_grafico
						(
						   objeto_grafico_proyecto   	varchar(15)		NOT NULL,
						   objeto_grafico            	int8			NOT NULL,
						   descripcion            	   	varchar(80)  	NULL,
						   grafico						varchar(20)		NOT NULL,
						   ancho						varchar(10)		NULL,
						   alto							varchar(10)		NULL,
						   CONSTRAINT  "apex_objeto_grafico_pk" PRIMARY KEY ("objeto_grafico_proyecto","objeto_grafico"),
						   CONSTRAINT  "apex_objeto_grafico_fk_objeto"  FOREIGN KEY ("objeto_grafico_proyecto","objeto_grafico") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
						   CONSTRAINT  "apex_objeto_grafico_fk_grafico"  FOREIGN KEY ("grafico") REFERENCES "apex_grafico" ("grafico") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
						);';
		
		$sql[] = 'CREATE TABLE apex_objeto_codigo
						(
						   objeto_codigo_proyecto   	varchar(15)		NOT NULL,
						   objeto_codigo            	int8			NOT NULL,
						   descripcion            	   	varchar(80)  	NULL,
						   ancho						varchar(10)		NULL,
						   alto							varchar(10)		NULL,
						   CONSTRAINT  "apex_objeto_codigo_pk" PRIMARY KEY ("objeto_codigo_proyecto","objeto_codigo"),
						   CONSTRAINT  "apex_objeto_codigo_fk_objeto"  FOREIGN KEY ("objeto_codigo_proyecto","objeto_codigo") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
						);';

		$sql[] = 'ALTER TABLE apex_objeto_ei_filtro_col ADD COLUMN carga_maestros TEXT  NULL;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_filtro_col ADD COLUMN punto_montaje bigint  NULL;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_filtro_col ADD CONSTRAINT	"apex_ei_filtro_col_fk_puntos_montaje" FOREIGN KEY ("punto_montaje", "objeto_ei_filtro_proyecto")	REFERENCES "apex_puntos_montaje"	("id", "proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		$sql[] = "INSERT INTO apex_elemento_formulario (elemento_formulario, padre, descripcion, proyecto, obsoleto) VALUES('ef_editable_fecha_hora', 'ef_editable', 'fecha hora', 'toba', '0');";

		$sql[] = 'ALTER TABLE apex_fuente_datos ADD punto_montaje  int8;';
		$sql[] = 'ALTER TABLE apex_fuente_datos  ADD CONSTRAINT "apex_fuente_datos_fk_punto_montaje" FOREIGN KEY ("punto_montaje", "proyecto") REFERENCES "apex_puntos_montaje"	("id", "proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_objeto_db_registros_ext ADD punto_montaje  int8;';
		$sql[] = 'ALTER TABLE apex_objeto_db_registros_ext  ADD CONSTRAINT "apex_obj_dbr_ext_fk_punto_montaje" FOREIGN KEY ("punto_montaje", "objeto_proyecto") REFERENCES "apex_puntos_montaje"  ("id", "proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE;';

		// Agregar registros por defecto del proyecto que se está migrando
		$this->elemento->get_db()->ejecutar($sql);

		$sql = "SET CONSTRAINTS ALL DEFERRED;";
		$this->elemento->get_db()->ejecutar($sql);
	}

	function proyecto__puntos_montaje()
	{
		$proyecto = $this->elemento->get_db()->quote($this->elemento->get_id());
		$sql = "
			INSERT INTO
				apex_puntos_montaje (
					etiqueta, proyecto, proyecto_ref, descripcion, path_pm, tipo
				)
				VALUES (
					'proyecto', $proyecto, $proyecto, 'punto de montaje por defecto proyectos toba', 'php', 'proyecto_toba'
				)
		;";
		$this->elemento->get_db()->ejecutar($sql);
		$id_pm = $this->elemento->get_db()->recuperar_secuencia('apex_puntos_montaje_seq');
//		$id_pm = $this->elemento->get_db()->consultar_fila("SELECT id FROM apex_puntos_montaje WHERE etiqueta='proyecto' AND proyecto=$proyecto");
//		$id_pm = $this->elemento->get_db()->quote($id_pm['id']);

		$sql = array();
		$sql[] = "UPDATE apex_proyecto SET pm_contexto=$id_pm WHERE proyecto=$proyecto";
		$sql[] = "UPDATE apex_proyecto SET pm_sesion=$id_pm WHERE proyecto=$proyecto";
		$sql[] = "UPDATE apex_proyecto SET pm_usuario=$id_pm WHERE proyecto=$proyecto";
		$sql[] = "UPDATE apex_proyecto SET pm_impresion=$id_pm WHERE proyecto=$proyecto";
		$sql[] = "UPDATE apex_objeto SET punto_montaje=$id_pm WHERE proyecto=$proyecto";
		$sql[] = "UPDATE apex_item_zona SET punto_montaje=$id_pm WHERE proyecto=$proyecto";
		$sql[] = "UPDATE apex_objeto_datos_rel SET punto_montaje=$id_pm WHERE proyecto=$proyecto";
		$sql[] = "UPDATE apex_objeto_db_registros SET punto_montaje=$id_pm WHERE objeto_proyecto=$proyecto";
		$sql[] = "UPDATE apex_pagina_tipo SET punto_montaje=$id_pm WHERE proyecto=$proyecto";
		$sql[] = "UPDATE apex_consulta_php SET punto_montaje=$id_pm WHERE proyecto=$proyecto";
		$sql[] = "UPDATE apex_objeto_ci_pantalla SET punto_montaje=$id_pm WHERE objeto_ci_proyecto=$proyecto";
		$sql[] = "UPDATE apex_objeto_ei_formulario_ef SET punto_montaje=$id_pm WHERE objeto_ei_formulario_proyecto=$proyecto";				
		$sql[] = "UPDATE apex_fuente_datos SET punto_montaje=$id_pm WHERE proyecto=$proyecto";
		$sql[] = "UPDATE apex_objeto_db_registros_ext SET punto_montaje=$id_pm WHERE objeto_proyecto=$proyecto";
		$sql[] = "UPDATE apex_objeto_ei_filtro_col SET punto_montaje=$id_pm WHERE objeto_ei_filtro_proyecto=$proyecto";
		
		$this->elemento->get_db()->ejecutar($sql);
	}

	function proyecto__autoload()
	{
		$this->elemento->generar_autoload($this->manejador_interface, true);
	}
}
?>
