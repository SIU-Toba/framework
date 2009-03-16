<?php

class toba_migracion_1_4_0 extends toba_migracion
{
	function instancia__cambios_estructura()
	{
		$sql = array();
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