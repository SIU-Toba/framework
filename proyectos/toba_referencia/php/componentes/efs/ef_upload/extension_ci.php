<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_ci extends toba_ci
{
	public $s__nombre_archivo;
	
	function conf__formulario()
	{
		if (isset($this->s__nombre_archivo)) {
			return array( 'archivo' => $this->s__nombre_archivo );
		}
	}
	
	function evt__formulario__modificacion($datos)
	{
		if (isset($datos['archivo'])) {
			$this->s__nombre_archivo = $datos['archivo']['name'];
			$img = toba::proyecto()->get_www_temp($this->s__nombre_archivo);
			// Mover los archivos subidos al servidor del directorio temporal PHP a uno propio.
			move_uploaded_file($datos['archivo']['tmp_name'], $img['path']);
		}
	}
	
}

?>