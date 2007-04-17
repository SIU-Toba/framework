<?php

class migracion_1_0_4 extends toba_migracion
{

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