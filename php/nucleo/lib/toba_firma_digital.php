<?php

class toba_firma_digital
{
	
	/**
	 * Agrega attachments a un archivo .pdf existente. Si falla lanza una excepcion toba_error
	 * @param string $archivo_pdf  Path completo al archivo PDF de entrada 
	 * @param array $paths_attachments Paths completos de archivos a incluir 
	 * @param int $pagina Opcional. Página a la que se agregan los attachments (sino se agregan a nivel del documento)
	 */
	static function pdf_add_attachments($archivo_pdf, $paths_attachments, $pagina = null)
	{
		$archivo_temp = toba::proyecto()->get_path_temp().'/'.md5(uniqid(time()));
		$stdout = null;
		$stderr = null;
		$paths = implode(" ", $paths_attachments);
		$comando = "pdftk $archivo_pdf attach_files $paths output $archivo_temp";
		if (toba_manejador_archivos::ejecutar($comando, $stdout, $stderr) !== 0) {
			throw new toba_error("Error al ejecutar comando '$comando': $stdout\n".$stderr);
		}
		if (! rename($archivo_pdf, $archivo_pdf.'.old')) {
			throw new toba_error("Imposible renombrar '$archivo_pdf' a '$archivo_pdf.old'");
		}
		if (! rename($archivo_temp, $archivo_pdf)) {
			throw new toba_error("Imposible renombrar '$archivo_temp' a '$archivo_pdf'");
		}		
		unlink($archivo_pdf.'.old');
	}
	
	/**
	 * Extrae los attachments PDFs de un documento. Requiere tener instalado en el path el ejecutable 'pdftk'. Si falla lanza una excepcion toba_error
	 * @param string $archivo_pdf Path completo al archivo PDF de entrada que contiene attachments 
	 * @param string $patron Patrón a extraer (formato preg_match), se asumen todas 
	 * @return array Paths completo a archivos extraidos. Es responsabilidad del que llama borrar la carpeta contenedora posterior a su uso
	 */
	static function pdf_get_attachments($archivo_pdf, $patron = null)
	{
		$carpeta_temp = toba::proyecto()->get_path_temp().'/'.md5(uniqid(time()));
		if (! file_exists($carpeta_temp)) {
			if (! mkdir($carpeta_temp)) {
				throw new toba_error("Error al intentar crear carpeta temporal $carpeta_temp . Verifique permisos");
			}
		}
		$stdout = null;
		$stderr = null;
		$comando = "pdftk $archivo_pdf unpack_files output $carpeta_temp";
		if (toba_manejador_archivos::ejecutar($comando, $stdout, $stderr) !== 0) {
			throw new toba_error("Error al ejecutar comando '$comando': ".$stderr);
		}
		$archivos = toba_manejador_archivos::get_archivos_directorio($carpeta_temp, $patron);
		if (empty($archivos)) {
			toba_manejador_archivos::eliminar_directorio($carpeta_temp);
		} 
		return $archivos;
	}
	
}

?>