<?php
class toba_migracion_3_2_0 extends toba_migracion
{	
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
	
	function proyecto__agregar_ws_reportes()
	{		
		$proyecto = $this->elemento->get_db()->quote($this->elemento->get_id());
		$sql = "INSERT INTO apex_servicio_web ( proyecto, servicio_web, descripcion, tipo, param_to, param_wsa) VALUES (
			   $proyecto, 'rest_arai_reportes', 'Servio Web para acceder a Arai-Reportes', 'rest', NULL, 0);";
		$this->elemento->get_db()->ejecutar($sql);		
	}
}
?>
