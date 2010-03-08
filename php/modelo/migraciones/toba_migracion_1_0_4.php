<?php

class toba_migracion_1_0_4 extends toba_migracion
{
	
	function instancia__cambios_estructura()
	{
		/**
		 * Se evita el mensaje 'ERROR: cannot ALTER TABLE "apex_objeto_ei_formulario_ef" because it has pending trigger events' de postgres 8.3.
		 */
		$sql = "SET CONSTRAINTS ALL IMMEDIATE;";
		$this->elemento->get_db()->ejecutar($sql);

		$sql = array();
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN popup_carga_desc_metodo varchar";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN popup_carga_desc_clase varchar";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN popup_carga_desc_include varchar";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN oculto_relaja_obligatorio varchar";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN selec_ancho varchar";
		$sql[] = "ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN selec_cant_columnas varchar";
		$sql[] = "ALTER TABLE apex_estilo ADD COLUMN proyecto varchar";
		$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN validacion_bloquear_usuario smallint";
		$sql[] = "ALTER TABLE apex_objeto_cuadro_cc ADD COLUMN modo_inicio_colapsado smallint";
		$sql[] = "
				CREATE TABLE apex_ptos_control 
				(
				  proyecto VARCHAR(15) NOT NULL,
				  pto_control          VARCHAR(20) NOT NULL,
				  descripcion          VARCHAR(255) NULL
				);
				
				CREATE TABLE apex_ptos_control_param
				(
				  proyecto VARCHAR(15) NOT NULL,
				  pto_control              VARCHAR(20) NOT NULL,
				  parametro                VARCHAR(60) NULL
				);
				
				CREATE TABLE apex_ptos_control_ctrl
				
				(
				  proyecto VARCHAR(15)  NOT NULL,
				  pto_control             VARCHAR(20)  NOT NULL,
				  clase                   VARCHAR(60)  NOT NULL,
				  archivo                 VARCHAR(255) NULL,
				  actua_como              CHAR(1)      DEFAULT 'M' NOT NULL CHECK (actua_como IN ('E','A','M'))
				);

				CREATE TABLE apex_ptos_control_x_evento
				(
				  proyecto 					VARCHAR(15) NOT NULL,
				  pto_control              	VARCHAR(20) NOT NULL,
				  evento_id                	INTEGER     NOT NULL,
				  objeto					int4		NOT NULL
				);
		";
		$this->elemento->get_db()->ejecutar($sql);
	}		

		/**
		 * Se separa la carga de la cascada del ef_popup (carga de opciones)
		 * de la carga de la descripción de la clave
		 */
		function proyecto__parametros_ef_popup()
		{
			$cant = 0;
			$sql = "
				UPDATE apex_objeto_ei_formulario_ef
				SET 
					popup_carga_desc_metodo = carga_metodo,
					popup_carga_desc_clase = carga_clase,
					popup_carga_desc_include = carga_include,
					carga_metodo = NULL,
					carga_clase = NULL,
					carga_include = NULL
				WHERE
					objeto_ei_formulario_proyecto = '{$this->elemento->get_id()}' AND
					elemento_formulario = 'ef_popup' AND
					carga_metodo IS NOT NULL AND
					carga_maestros IS NOT NULL AND
					carga_maestros != ''
			";
			$cant += $this->elemento->get_db()->ejecutar($sql);
			return $cant;
		}
		

}	


?>