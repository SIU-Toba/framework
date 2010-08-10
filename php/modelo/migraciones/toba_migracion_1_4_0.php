<?php

class toba_migracion_1_4_0 extends toba_migracion
{
//	function personalizacion__


	/**
	 *  Se agrega la tabla:
	 *				-  apex_objeto_cuadro_col_cc
	 */
	function instancia__cambios_estructura()
	{
		/**
		 * Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because it has pending trigger events' de postgres 8.3
		 */
		$sql = "SET CONSTRAINTS ALL IMMEDIATE;";
		$this->elemento->get_db()->ejecutar($sql);

		$sql = array();		
		//-----------------Tabla para especificar que columnas participan de la sumatoria de un corte de control----------------------
		$sql[] = 'CREATE TABLE apex_objeto_cuadro_col_cc(
							objeto_cuadro_cc BIGINT NULL,
							objeto_cuadro_proyecto VARCHAR(15) NULL,
							objeto_cuadro BIGINT NULL,
							objeto_cuadro_col BIGINT NULL,
							total SMALLINT NULL DEFAULT 0,
						CONSTRAINT pkapex_objeto_cuadro_col_cc	PRIMARY KEY (objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col)
						);';

		$sql[] = 'ALTER TABLE apex_objeto_cuadro_col_cc ADD CONSTRAINT fk_apex_objeto_cuadro_col_cc_apex_objeto_cuadro_cc FOREIGN KEY (objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro)
					REFERENCES apex_objeto_cuadro_cc (objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro) ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';

		$sql[]= 'ALTER TABLE apex_objeto_cuadro_col_cc ADD CONSTRAINT fk_apex_objeto_cuadro_col_cc_apex_objeto_ei_cuadro_columna FOREIGN KEY (objeto_cuadro_col, objeto_cuadro, objeto_cuadro_proyecto)
					REFERENCES apex_objeto_ei_cuadro_columna (objeto_cuadro_col, objeto_cuadro, objeto_cuadro_proyecto) ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';

		//----------------- Tabla para asociar objetos a las pantallas-------------------------------------
		$sql[] = 'CREATE TABLE apex_objetos_pantalla(
							proyecto VARCHAR(15) NULL,
							pantalla BIGINT NULL,
							objeto_ci BIGINT NULL,
							orden SMALLINT NULL,
							dep_id BIGINT NULL,
							CONSTRAINT apex_objetos_pantalla_pk	PRIMARY KEY (proyecto, objeto_ci, pantalla, dep_id)
						);';
		$sql[] = 'ALTER TABLE apex_objetos_pantalla ADD CONSTRAINT apex_objetos_pantalla_apex_objeto_ci_pantalla_fk FOREIGN KEY (pantalla, objeto_ci, proyecto) 
						REFERENCES apex_objeto_ci_pantalla (pantalla, objeto_ci, objeto_ci_proyecto) ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_objetos_pantalla ADD CONSTRAINT apex_objetos_pantalla_apex_objeto_dependencias_fk FOREIGN KEY (dep_id, proyecto, objeto_ci) 
						REFERENCES apex_objeto_dependencias (dep_id, proyecto, objeto_consumidor) ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';

		//----------------- Tabla para asociar eventos a las pantallas-------------------------------------
		$sql[] ='CREATE TABLE apex_eventos_pantalla(
							pantalla BIGINT NULL,
							objeto_ci BIGINT NULL,
							evento_id BIGINT NULL,
							proyecto VARCHAR(15) NULL,
							CONSTRAINT pkapex_eventos_pantalla	PRIMARY KEY (pantalla, objeto_ci, proyecto, evento_id)
						);';

		$sql[] ='ALTER TABLE apex_eventos_pantalla ADD CONSTRAINT apex_eventos_pantalla_apex_objeto_ci_pantalla_fk FOREIGN KEY (pantalla, objeto_ci, proyecto)
						REFERENCES apex_objeto_ci_pantalla (pantalla, objeto_ci, objeto_ci_proyecto) ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER TABLE apex_eventos_pantalla ADD CONSTRAINT apex_eventos_pantalla_apex_objeto_eventos_fk FOREIGN KEY (evento_id, proyecto)
						REFERENCES apex_objeto_eventos (evento_id, proyecto) ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';

		//----------------- Tabla para asociar columnas relacionadas entre datos tabla -------------------------------------
		$sql[]= 'CREATE TABLE apex_objeto_rel_columnas_asoc	(
						asoc_id				BIGINT NULL,
						objeto				 BIGINT NULL,
						proyecto			VARCHAR(15) NULL,
						hijo_clave			BIGINT NULL,
						hijo_objeto			BIGINT NULL,
						padre_objeto	BIGINT NULL,
						padre_clave		BIGINT NULL,
						CONSTRAINT "apex_objeto_rel_columnas_asoc_pk" PRIMARY KEY ("asoc_id", "objeto", "proyecto", "padre_objeto", "hijo_objeto", "padre_clave", "hijo_clave")
					);';

		$sql[] = 'ALTER TABLE apex_objeto_rel_columnas_asoc ADD CONSTRAINT apex_columna_objeto_hijo_fk FOREIGN KEY (hijo_clave, hijo_objeto, proyecto)
						REFERENCES apex_objeto_db_registros_col (col_id, objeto, objeto_proyecto) ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';

		$sql[] = 'ALTER TABLE apex_objeto_rel_columnas_asoc ADD CONSTRAINT apex_columna_objeto_padre_fk FOREIGN KEY (padre_objeto, padre_clave, proyecto)
						REFERENCES apex_objeto_db_registros_col (objeto, col_id, objeto_proyecto) ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';
		
		$sql[] = 'ALTER TABLE apex_objeto_rel_columnas_asoc ADD CONSTRAINT apex_obj_datos_rel_asoc_fk FOREIGN KEY (asoc_id, objeto, proyecto)
						REFERENCES apex_objeto_datos_rel_asoc (asoc_id, objeto, proyecto) ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';
		
		//-------------------------Agregado de la columna dato_estricto para la carga de columnas externas---------------------
		$sql[] = 'ALTER TABLE apex_objeto_db_registros_ext ADD COLUMN dato_estricto SMALLINT DEFAULT 1;';

		//---------------------Agregado de columnas para marcar el metodo de carga de columnas externas en dt ---------
		$sql[] = 'ALTER TABLE apex_objeto_db_registros_ext ADD COLUMN carga_dt BIGINT;';
		$sql[] = 'ALTER TABLE apex_objeto_db_registros_ext ADD COLUMN carga_consulta_php BIGINT;';

		//---------------------------- Agregado de columna para marcar la posicion de la botonera en los Eis ---------------------
		$sql[] = 'ALTER TABLE apex_objeto ADD COLUMN posicion_botonera VARCHAR(10);';

		//-------------------------- Agrego la columna para representar lo que antes hacia el 'redirecciona' ------------------------
		$sql[] = 'ALTER TABLE apex_item ADD COLUMN retrasar_headers SMALLINT  DEFAULT 0;';

		//-------------------------- Agrego columnas para determinar si se usa el estado no-par en los ef_seleccion ------------
		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN carga_permite_no_seteado SMALLINT NOT NULL DEFAULT 0;';
		$sql[] = 'ALTER TABLE apex_objeto_ei_filtro_col ADD COLUMN carga_permite_no_seteado SMALLINT  NOT NULL DEFAULT 0;';

		//--------------------------- Columnas que permiten la carga masiva de valores para columnas externas -----------------
		$sql[] = 'ALTER TABLE apex_objeto_db_registros_ext ADD COLUMN permite_carga_masiva SMALLINT NOT NULL DEFAULT 0;';
		$sql[] = 'ALTER TABLE apex_objeto_db_registros_ext ADD COLUMN metodo_masivo	VARCHAR(100);';

		//--------------------------- Columna que marca si un evento de seleccion es multiple o no ------------------------------------
		$sql[] = 'ALTER TABLE apex_objeto_eventos ADD COLUMN es_seleccion_multiple SMALLINT NOT NULL DEFAULT 0;';

		//--------------------------- Locking optimista en datos_relacion ------------------------------------
		$sql[] = 'ALTER TABLE apex_objeto_datos_rel ADD COLUMN sinc_lock_optimista SMALLINT  NULL DEFAULT 1;';

		//--------------------------- Revision svn del proyecto ------------------------------------
		$sql[] = 'ALTER TABLE apex_revision ADD COLUMN proyecto VARCHAR(15)  NULL';
		
		//--------------------------- Configuraciones del proyecto ------------------------------------
		$sql[] = 'ALTER TABLE apex_proyecto ADD COLUMN item_pre_sesion_popup smallint NULL';
		$sql[] = 'ALTER TABLE apex_proyecto ADD COLUMN navegacion_ajax smallint NULL';
		
		//--------------------------- Fuentes de datos ------------------------------------
		$sql[] = 'ALTER TABLE apex_fuente_datos ADD COLUMN tiene_auditoria smallint NOT NULL default 0';
		$sql[] = 'ALTER TABLE apex_fuente_datos ADD COLUMN parsea_errores smallint NOT NULL default 0';

		//--------------------------- Botonera del item ------------------------------------
		$sql[] = 'ALTER TABLE  apex_objeto_mt_me ADD COLUMN botonera_barra_item smallint NULL';
		
		//--------------------------- Template ------------------------------------
		$sql[] = 'ALTER TABLE  apex_objeto_ci_pantalla  ADD COLUMN template VARCHAR NULL';
		$sql[] = 'ALTER TABLE  apex_objeto_ut_formulario  ADD COLUMN template VARCHAR NULL';

		 //--------------------------- Configuraciones del proyecto ------------------------------------
		 $sql[] = 'ALTER TABLE apex_proyecto ADD COLUMN codigo_ga_tracker VARCHAR(20) NULL;';
		 //--------------------------- Asociaciones entre CNs ------------------------------------
		$sql[] = 'CREATE TABLE apex_objeto_dep_consumo(
					proyecto							varchar(15)			NULL,
					consumo_id							int8				NULL, 
					objeto_consumidor					int8				NULL,
					objeto_proveedor					int8				NULL,
					identificador						varchar(40)			NULL,
					parametros_a						varchar(255)		NULL,
					parametros_b						varchar(255)		NULL,
					parametros_c						varchar(255)		NULL,
					inicializar							smallint			NULL,
					CONSTRAINT	"apex_objeto_consumo_depen_pk"	 PRIMARY	KEY ("consumo_id")
			);';
		 
		$this->elemento->get_db()->ejecutar($sql);		
		
		$sql = "SET CONSTRAINTS ALL DEFERRED;";
		$this->elemento->get_db()->ejecutar($sql);
	}

	/**
	 *  Se deserializan los campos de la sumatoria del corte de control y se generan registros nuevos
	 */
	function proyecto__normalizar_suma_cortes_cuadro()
	{
		$sql = "SELECT col.objeto_cuadro_proyecto, col.objeto_cuadro, col.objeto_cuadro_col, col.total_cc
					 FROM    apex_objeto_ei_cuadro_columna col
					WHERE
							col.total_cc IS NOT NULL AND
							col.total_cc <> '' AND
							col.objeto_cuadro_proyecto = '{$this->elemento->get_id()}'; ";

		$datos = $this->elemento->get_db()->consultar($sql);
		toba_logger::instancia()->debug('Sql ejecutada: '. $sql . "\n datos devueltos:");
		toba_logger::instancia()->var_dump($datos);

		$sql = array();
		foreach($datos as $corte){
			$cols_involucradas = explode(',' , $corte['total_cc']);
			$cols_involucradas = array_map('trim', $cols_involucradas);
			foreach($cols_involucradas as $columna){
				$sql[] = "INSERT INTO apex_objeto_cuadro_col_cc(objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, total) 
								(SELECT  objeto_cuadro_cc, '{$corte['objeto_cuadro_proyecto']}',
												   '{$corte['objeto_cuadro']}', '{$corte['objeto_cuadro_col']}', '1'
								 FROM	apex_objeto_cuadro_cc
								 WHERE
									objeto_cuadro_proyecto =  '{$corte['objeto_cuadro_proyecto']}' AND
									objeto_cuadro = '{$corte['objeto_cuadro']}' AND
									identificador = '$columna'
								);";
			}
		}
		$this->elemento->get_db()->ejecutar($sql);
	}

	/**
	 * Se deserializa el campo que contiene las dependencias asociadas a las pantallas y se envia a una relacion
	 */
	function proyecto__normalizar_dependencias_pantallas()
	{
		$sql = "SELECT	cp.objeto_ci,
										cp.pantalla,
										cp.objetos
					FROM	apex_objeto_ci_pantalla cp
					WHERE
								cp.objeto_ci_proyecto = '{$this->elemento->get_id()}'
								AND cp.objetos IS NOT NULL
								AND cp.objetos <> ''
					ORDER BY objeto_ci, pantalla, orden;";
		
		$datos = $this->elemento->get_db()->consultar($sql);
		$sql = array();
		foreach($datos as $pant){
			$orden = 1;
			$obj_involucrados = explode(',' , $pant['objetos']);
			$obj_involucrados = array_map('trim' , $obj_involucrados);
			$obj_involucrados = array_unique($obj_involucrados);
			foreach($obj_involucrados as $dep){
				$sql = "INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id)
								(SELECT proyecto, '{$pant['pantalla']}', objeto_consumidor, '$orden',  dep_id
								FROM	apex_objeto_dependencias
								WHERE
									proyecto = '{$this->elemento->get_id()}' AND
									objeto_consumidor = '{$pant['objeto_ci']}' AND
									identificador = '{$dep}'
								); ";
				$this->elemento->get_db()->ejecutar($sql);
				$orden++;
			}
		}
		$sql = "UPDATE apex_objeto_ci_pantalla SET objetos = NULL WHERE objeto_ci_proyecto = '{$this->elemento->get_id()}'; ";
		$this->elemento->get_db()->ejecutar($sql);
	}

	/**
	 * Se deserializa el campo que contiene los eventos asociados a la pantalla y se envia a una relacion
	 */
	function proyecto__normalizar_eventos_pantallas()
	{
		$sql = "SELECT cp.objeto_ci, cp.pantalla, cp.eventos
					 FROM	apex_objeto_ci_pantalla cp
					 WHERE	cp.objeto_ci_proyecto = '{$this->elemento->get_id()}'
					AND cp.eventos IS NOT NULL
					AND cp.eventos <> ''
					ORDER BY objeto_ci, pantalla, orden";
		$datos = $this->elemento->get_db()->consultar($sql);

		$sql = array();
		foreach($datos as $pant){
			$evt_involucrados = explode(',', $pant['eventos' ]);
			$evt_involucrados = array_map('trim', $evt_involucrados);
			foreach($evt_involucrados as $evento){
				$sql[] = "INSERT INTO apex_eventos_pantalla (proyecto, objeto_ci, pantalla, evento_id)
								(SELECT proyecto, objeto, '{$pant['pantalla']}', evento_id
								 FROM	apex_objeto_eventos
								 WHERE
										proyecto = '{$this->elemento->get_id()}' AND
										objeto = '{$pant['objeto_ci']}' AND
										identificador = '{$evento}' );";
			}
		}
		$sql[] = "UPDATE apex_objeto_ci_pantalla SET eventos = NULL WHERE objeto_ci_proyecto = '{$this->elemento->get_id()}'; ";
		$this->elemento->get_db()->ejecutar($sql);
	}

	/**
	 * Se deserializan las columnas que asocian las tablas en una relacion
	 */
	function proyecto__normalizar_columnas_relaciones()
	{
		$sql = "SELECT proyecto, objeto, asoc_id, padre_objeto, padre_id,
									  padre_clave, hijo_objeto, hijo_id, hijo_clave
					 FROM	apex_objeto_datos_rel_asoc
					WHERE	 proyecto = '{$this->elemento->get_id()}'
					ORDER BY asoc_id, orden;";
		$datos = $this->elemento->get_db()->consultar($sql);
		
		$sql = array();
		foreach($datos as $columnas_relacionadas){
			$columnas_padre = explode(',', $columnas_relacionadas['padre_clave']);
			$columnas_padre = array_map('trim', $columnas_padre);
			$columnas_hijas = explode(',', $columnas_relacionadas['hijo_clave']);
			$columnas_hijas = array_map('trim', $columnas_hijas);

			foreach(array_keys($columnas_padre) as $id){
				$sql[] = "INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave)
								(SELECT		rel.proyecto, rel.objeto, rel.asoc_id, rel.padre_objeto, padre.col_id,rel.hijo_objeto,hijo.col_id													 
								 FROM
											apex_objeto_datos_rel_asoc rel,
											apex_objeto_db_registros_col padre,
											apex_objeto_db_registros_col hijo
								WHERE
											rel.padre_proyecto = padre.objeto_proyecto AND
											rel.padre_objeto = padre.objeto AND
											padre.columna = '{$columnas_padre[$id]}' AND
											rel.hijo_proyecto = hijo.objeto_proyecto AND
											rel.hijo_objeto = hijo.objeto AND
											hijo.columna = '{$columnas_hijas[$id]}' AND
											rel.proyecto =  '{$this->elemento->get_id()}'	AND
											rel.objeto = '{$columnas_relacionadas['objeto']}'			AND
											rel.asoc_id = '{$columnas_relacionadas['asoc_id']}');";
			}
		}
		$sql[] = "UPDATE apex_objeto_datos_rel_asoc SET padre_clave = NULL , hijo_clave = NULL WHERE proyecto =  '{$this->elemento->get_id()}';";
		$this->elemento->get_db()->ejecutar($sql);
	}

	/**
	 * Se cambia:
	 *	evt__limpieza_memoria por limpiar_memoria
	 */
	function proyecto__cambio_api_cn()
	{
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion("|evt__limpieza_memoria|","limpiar_memoria");
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->elemento->get_dir(), '|.php|', true);
		$editor->procesar_archivos($archivos);
	}

	/**
	 * Setea la carga de columnas externas como datos obligatorios. Esto es salta excepcion si no encuentra el dato.
	 */
	function proyecto__restringir_carga_col_ext()
	{
		$sql = "UPDATE apex_objeto_db_registros_ext SET dato_estricto = '1' WHERE objeto_proyecto = '{$this->elemento->get_id()}'; ";
		$this->elemento->get_db()->ejecutar($sql);
	}


	/**
	 * Se deja de lado el archivo PROYECTO y VERSION y se incluyen en el archivo proyecto.ini en la raiz junto a otras configuraciones del instalador
	 */
	function proyecto__configuraciones_ini()
	{
		$origen_ini = toba_dir().'/php/modelo/template_proyecto/proyecto.ini';
		$destino_ini = $this->elemento->get_dir().'/proyecto.ini';
		$archivo_proyecto = $this->elemento->get_dir().'/PROYECTO';
		$archivo_version = $this->elemento->get_dir().'/VERSION';
		
		$version = '1.0.0';
		if (file_exists($archivo_proyecto)) {
			unlink($archivo_proyecto);
		}
		if (file_exists($archivo_version)) {
			$version = trim(file_get_contents($archivo_version));
			unlink($archivo_version);
		}

		if (! file_exists($destino_ini)) {
			if (! copy($origen_ini, $destino_ini)) {
				throw new toba_error("Imposible copiar de $origen_ini a $destino_ini");
			}
			$editor = new toba_editor_archivos();
			$editor->agregar_sustitucion('|__proyecto__|', $this->elemento->get_id());
			$editor->agregar_sustitucion('|__version__|', $version);
			$editor->procesar_archivos(array($destino_ini));
		}
	}

	/**
	 * Por defecto se activa el control de locking optimista en las relaciones existentes
	 */
	function proyecto__lock_optimista_relaciones()
	{
		$sql = "
			UPDATE apex_objeto_datos_rel
			SET
				sinc_lock_optimista = 1
			WHERE
				proyecto = '{$this->elemento->get_id()}'
		";
		$this->elemento->get_db()->ejecutar($sql);
	}

	/**
	 *  Agrega a ciertos Eis la botonera, aprovecho para cambiar el lugar de donde lee el ci.
	 */
	function proyecto__agregar_botonera_eis()
	{
		//-- Primeramente seteo el valor default para aquellos objetos que lo necesitan
		$sql = "UPDATE apex_objeto SET posicion_botonera = 'abajo'
					WHERE clase IN ('toba_ei_filtro', 'toba_ei_cuadro', 'toba_ei_formulario', 'toba_ei_formulario_ml')
					AND proyecto = '{$this->elemento->get_id()}';";
		$this->elemento->get_db()->ejecutar($sql);

		//-- Copio el valor de los objetos_ci que ya poseen un valor definido.
		$sql = "UPDATE apex_objeto SET posicion_botonera = ci.posicion_botonera
					FROM apex_objeto_mt_me as ci
					WHERE
						proyecto = ci.objeto_mt_me_proyecto
						AND objeto = ci.objeto_mt_me
						AND proyecto = '{$this->elemento->get_id()}';";
		$this->elemento->get_db()->ejecutar($sql);
	}

	function proyecto__migrar_retraso_headers()
	{
		$sql = "UPDATE apex_item SET retrasar_headers = redirecciona
					 WHERE	proyecto = '{$this->elemento->get_id()}';";
		$this->elemento->get_db()->ejecutar($sql);

		$sql = "UPDATE apex_item SET redirecciona = 0
					 WHERE	proyecto = '{$this->elemento->get_id()}';";
		$this->elemento->get_db()->ejecutar($sql);
	}

	function proyecto__permite_estado_no_seteado()
	{
		//--------------- Primero trato el problema en los formularios comunes --------------------------------
		$sql = "UPDATE apex_objeto_ei_formulario_ef SET carga_permite_no_seteado = 0
					 WHERE	objeto_ei_formulario_proyecto = '{$this->elemento->get_id()}' AND carga_no_seteado IS NULL;";
		$this->elemento->get_db()->ejecutar($sql);

		$sql = "UPDATE apex_objeto_ei_formulario_ef SET carga_permite_no_seteado = 1
					 WHERE	objeto_ei_formulario_proyecto = '{$this->elemento->get_id()}' AND carga_no_seteado IS NOT NULL ;";
		$this->elemento->get_db()->ejecutar($sql);

		//----------------- Ahora lo trato en los ei_filtro nuevos ---------------------------------------------------------
		$sql = "UPDATE apex_objeto_ei_filtro_col SET carga_permite_no_seteado = 0
					 WHERE	objeto_ei_filtro_proyecto = '{$this->elemento->get_id()}' AND carga_no_seteado IS NULL;";
		$this->elemento->get_db()->ejecutar($sql);

		$sql = "UPDATE apex_objeto_ei_filtro_col SET carga_permite_no_seteado = 1
					 WHERE	objeto_ei_filtro_proyecto = '{$this->elemento->get_id()}' AND carga_no_seteado IS NOT NULL ;";
		$this->elemento->get_db()->ejecutar($sql);
	}

}

?>