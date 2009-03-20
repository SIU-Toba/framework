<?php

class toba_migracion_1_4_0 extends toba_migracion
{
	/**
	 *  Se agrega la tabla:
	 *				-  apex_objeto_cuadro_col_cc
	 */
	function instancia__cambios_estructura()
	{
		$sql = array();
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
				$sql[] = "INSERT INTO desarrollo.apex_objeto_cuadro_col_cc(objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, total) 
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
}

?>