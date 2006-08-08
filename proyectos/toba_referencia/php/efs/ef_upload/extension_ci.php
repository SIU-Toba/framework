<?php
require_once('nucleo/componentes/interface/objeto_ci.php');
  
class extension_ci extends objeto_ci
{
	protected $nombre_archivo;
	
	function mantener_estado_sesion() 
	{ 
		$propiedades = parent::mantener_estado_sesion(); 
		$propiedades[] = "nombre_archivo";
		
		return $propiedades; 
	} 	
	
	function conf__formulario()
	{
		/*// No permitir cambiar el archivo en modo modificación
		if (isset($this->archivo_completo))
			$this->dependencias['formulario']->ejecutar_metodo_ef('archivo', 'establecer_solo_lectura');*/
		return array( 'archivo' => $this->nombre_archivo );
	}
	
	function evt__formulario__modificacion($datos)
	{
		if (isset($datos['archivo'])) {
			$this->nombre_archivo = $datos['archivo']['name'];
			
			// Mover los archivos subidos al servidor del directorio temporal PHP a uno propio.
			$archivo = toba::get_hilo()->obtener_proyecto_path() . '/temp/'. $datos['archivo']['name'];
			move_uploaded_file($datos['archivo']['tmp_name'], $archivo);
		}
	}
	
	function obtener_html_contenido__1()
	{
		echo ei_mensaje("	<div align='center'><strong>
								El archivo se ha subido al servidor.<br>Para cambiar de archivo presione Anterior.
							</strong></div>");
	}

}

?>