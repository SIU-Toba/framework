<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_ci extends toba_ci
{
	public $s__nombre_archivo;
	protected $s__id_recurso;
	
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
			//Mando el archivo subido al servidor derecho al ECM
			$this->enviar_repositorio($datos['archivo']['tmp_name']);
		}
	}
	
	function enviar_repositorio($img)
	{
		$srv = toba::rdi()->servicio(RDITipos::FOTO);
		$attr = array('tipoIdentificacion' => md5($img), 'numeroIdentificacion' => rand(100, 5000), 'alto' => 500, 'ancho' => 600, 'color' => true);
		if (file_exists($img)) {
			$cont = file_get_contents($img);
			$ecm_resource_id = $srv->crear($attr, $cont);
			toba::logger()->debug("El id del ECM es" . var_export($ecm_resource_id, true));
			if (! is_null($ecm_resource_id)) {
				$this->s__id_recurso = $ecm_resource_id;                
			}
		}
	}
	
	function recuperar_contenido()
	{
		$srv = toba::rdi()->servicio(RDITipos::FOTO);
		$attr = $srv->recuperarAtributos($this->s__id_recurso);
		$content = $srv->recuperarContenido($this->s__id_recurso);
		
		$nombre = toba::proyecto()->get_www_temp($attr['tipoIdentificacion']. $attr['numeroIdentificacion']);
		file_put_contents($nombre['path'], $content);
		return $nombre;
	}
	
}
?>