<?php
class toba_migracion_1_5_0 extends toba_migracion
{
		function instancia__cambios_estructura()
		{
			/**
			 * Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because it has pending trigger events' de postgres 8.3
			 */
			$sql = "SET CONSTRAINTS ALL IMMEDIATE;";
			$this->elemento->get_db()->ejecutar($sql);

			$sql = array();
			//------------- Nueva tabla para guardar el checksum de los proyectos, relacionado a la sincro_svn ---------
			$sql[] = "CREATE TABLE			apex_checksum_proyectos
							(
								checksum						varchar(200)	NOT NULL,
								proyecto							varchar(15)		 NOT NULL,
								--ultima_modificacion		timestamp(0) without	time zone	DEFAULT current_timestamp NOT NULL,
								CONSTRAINT 'apex_checksum_proyectos_pk' PRIMARY KEY ('proyecto'),
								CONSTRAINT 'apex_checksum_proyectos_fk'	FOREIGN KEY ('proyecto') REFERENCES 'apex_proyecto' ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;
							);";

			//--------------- Cambios para que los 'Vinculos' del cuadro ahora se disparen como eventos -----------------
			$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna ADD COLUMN evento_asociado BIGINT;';
			$sql[] = 'ALTER TABLE apex_objeto_ei_cuadro_columna ADD CONSTRAINT apex_col_cuadro_evento_asoc_fk FOREIGN KEY (objeto_cuadro_proyecto, objeto_cuadro, evento_asociado)
							REFERENCES apex_objeto_eventos (proyecto, objeto, evento_id) ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;';

			//--------------------------------- Ahora se marcan los autovinculos de manera explicita --------------------------------
			$sql[] = 'ALTER TABLE apex_objeto_eventos ADD COLUMN es_autovinculo SMALLINT NOT NULL DEFAULT 0;';

			//--------------------------------- Cambio el tamaño de la columna indice ---------------------------------------------------
			$sql[] = 'ALTER TABLE apex_msg ALTER indice TYPE character varying(40);';
			$sql[] = 'ALTER TABLE apex_item_msg ALTER indice TYPE character varying(40);';
			$sql[] = 'ALTER TABLE apex_objeto_msg ALTER indice TYPE character varying(40);';

			//------------------------------------ Define si se puede actualizar automaticamente mediante wizard ----------------------
			$sql[] = 'ALTER TABLE apex_objeto_db_registros ADD COLUMN permite_actualizacion_automatica smallint NOT NULL DEFAULT 1;';

			//----------------------------------- Define la columna descripcion que se usara para una respuesta_popup ------------
			$sql[] = 'ALTER TABLE apex_objeto_cuadro ADD COLUMN columna_descripcion TEXT NULL;';

			//---------------------------------- Define si se muestra o no la leyenda del paginado -------------------------------------------
			$sql[] = 'ALTER TABLE apex_objeto_cuadro ADD COLUMN mostrar_total_registros SMALLINT NOT NULL DEFAULT 0;';
			$sql[] = 'ALTER TABLE apex_objeto_cuadro ADD COLUMN siempre_con_titulo SMALLINT NOT NULL DEFAULT 0;';

			//---------------------------------- Nuevo comportamiento del solo lectura del ef --------------------------------------------------
			$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN solo_lectura_inteligente SMALLINT NOT NULL DEFAULT 0;';

			$this->elemento->get_db()->ejecutar($sql);

			$sql = "SET CONSTRAINTS ALL DEFERRED;";
			$this->elemento->get_db()->ejecutar($sql);
		}

		/**
		 * Se pasa la info de las columnas 'vinculo' a un evento que luego se asocia
		 */
		function proyecto__asociar_evento_vinculo()
		{
			$sql = "SELECT *
						 FROM apex_objeto_ei_cuadro_columna col
						WHERE
						col.usar_vinculo = 1 AND
						col.objeto_cuadro_proyecto = '{$this->elemento->get_id()}';";
			$datos = $this->elemento->get_db()->consultar($sql);

			if (! empty($datos)) {
				foreach($datos as $col) {
					$cuadro_id = $col['objeto_cuadro'];
					$columna =  $col['objeto_cuadro_col'];
					$nombre_evento = 'evt_'. $col['clave'];
					
					$sql_ins = "INSERT INTO apex_objeto_eventos
					(proyecto, objeto, identificador, maneja_datos, accion,en_botonera, 
					accion_vinculo_carpeta, accion_vinculo_item, es_autovinculo, accion_vinculo_popup,
					accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda, accion_vinculo_servicio)
					VALUES ('{$col['objeto_cuadro_proyecto']}', '{$col['objeto_cuadro']}', '$nombre_evento', 0, 'V', 0,";

					$sql_ins .= (isset($col['vinculo_carpeta'])) ?  "'{$col['vinculo_carpeta']}'," : 'NULL,';
					if (isset($col['vinculo_item'])) { //Seteo item y autovinculo en false
						$sql_ins .= "'{$col['vinculo_item']}', 0," ;
					} else { //Si no hay item es autovinculo
						$sql_ins .= 'NULL, 1,';
					}
					$sql_ins .= (isset($col['vinculo_popup'])) ?  "'{$col['vinculo_popup']}'," : 'NULL,';
					$sql_ins .= (isset($col['vinculo_popup_param'])) ?  "'{$col['vinculo_popup_param']}'," : 'NULL,';
					$sql_ins .= (isset($col['vinculo_target'])) ?  "'{$col['vinculo_target']}'," : 'NULL,';
					$sql_ins .= (isset($col['vinculo_celda'])) ?  "'{$col['vinculo_celda']}'," : 'NULL,';
					$sql_ins .= (isset($col['vinculo_servicio'])) ?  "'{$col['vinculo_servicio']}'" : 'NULL';
					$sql_ins .= ");";
					$this->elemento->get_db()->ejecutar($sql_ins);


					//Recupero la secuencia del evento
					$evt = $this->elemento->get_db()->recuperar_secuencia("apex_objeto_eventos_seq");
					
					$sql_up = "UPDATE apex_objeto_eventos a
										SET orden = (SELECT COALESCE(MAX(b.orden) + 1, 1)
																 FROM	apex_objeto_eventos b
																WHERE b.proyecto = a.proyecto AND b.objeto = a.objeto)
					WHERE	a.proyecto = '{$this->elemento->get_id()}' AND a.objeto = '{$col['objeto_cuadro']}'
					AND a.evento_id = '$evt';";
					$this->elemento->get_db()->ejecutar($sql_up);

					//Asocio el nuevo evento al cuadro y blanqueo las columnas en el mismo paso.
					$sql_up = "UPDATE apex_objeto_ei_cuadro_columna SET evento_asociado = '$evt'
					
					WHERE	objeto_cuadro_proyecto = '{$this->elemento->get_id()}'  AND
					objeto_cuadro = '$cuadro_id' AND objeto_cuadro_col = '$columna'; ";
					$this->elemento->get_db()->ejecutar($sql_up);
				}
			}
		}

		/**
		 * Se explicitan las segundas columnas de los cuadros con respuesta_popup
		 * como columnas descripcion
		 */
		function proyecto__explicitar_descripcion_respuesta_popup()
		{
			//Tengo que recuperar los que tienen accion = 'P'
			$sql = "SELECT	aoc.objeto_cuadro,
										    aoc.columnas_clave
						  FROM apex_objeto_eventos aoe
						  JOIN apex_objeto_cuadro aoc
						  ON aoe.proyecto = aoc.objeto_cuadro_proyecto AND aoe.objeto = aoc.objeto_cuadro
						  WHERE  aoe.proyecto = '{$this->elemento->get_id()}'  AND
										   aoe.accion = 'P'
						  GROUP BY aoc.objeto_cuadro, aoc.columnas_clave ;";
			$datos = $this->elemento->get_db()->consultar($sql);

			foreach($datos as $evento) {
				$columnas = explode(',' , $evento['columnas_clave']);		//Separo las columnas
				$largo = count($columnas);
				$col_desc = null;
				if (isset($columnas[$largo-1]) && $columnas[$largo-1] != '') {	//Si no hay error
					$col_desc = $columnas[$largo-1];
					unset($columnas[$largo-1]);
					$columnas = implode(',' , $columnas);
				}				
				if (! is_null($col_desc) && $columnas != '') {
					$sql = 'UPDATE apex_objeto_cuadro SET columna_descripcion = ' . $this->elemento->get_db()->quote($col_desc) .
					 ', columnas_clave = ' . $this->elemento->get_db()->quote($columnas);
					$sql .= " WHERE objeto_cuadro_proyecto = '{$this->elemento->get_id()}'  AND objeto_cuadro = '{$evento['objeto_cuadro']}'; ";
					$this->elemento->get_db()->ejecutar($sql);
				}
			}
		}
}
?>
