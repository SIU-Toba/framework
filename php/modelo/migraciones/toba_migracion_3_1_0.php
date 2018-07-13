<?php
class toba_migracion_3_1_0 extends toba_migracion
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

		$sql[] = 'ALTER TABLE apex_objeto_ei_formulario_ef ADD COLUMN editor_config_file TEXT NULL;';
		$this->elemento->get_db()->ejecutar($sql);
		
		$sql = 'SET CONSTRAINTS ALL DEFERRED;';
		$this->elemento->get_db()->ejecutar($sql);
	}	
	
	
	function proyecto__copiar_imagen_calendario()
	{
		$nombre = 'calendario.gif';
		$dir_destino = $this->elemento->get_dir(). '/www/img/';

		$source = toba_dir(). '/php/modelo/template_proyecto/www/img/'. $nombre;		
		$destino_final = toba_manejador_archivos::path_a_plataforma($dir_destino.$nombre);
		if (! toba_manejador_archivos::existe_archivo_en_path($destino_final)) {
			copy($source, $destino_final);
		}
	}
	
}
?>
