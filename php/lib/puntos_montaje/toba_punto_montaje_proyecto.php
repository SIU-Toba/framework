<?php
/**
 * Representa un punto de montaje de un proyecto toba
 */
class toba_punto_montaje_proyecto extends toba_punto_montaje_autoload
{
	protected $proyecto_ref_id;

	function set_proyecto_referenciado($proyecto_id)
	{
		$this->proyecto_ref_id = $proyecto_id;
	}

	function get_proyecto_referenciado()
	{
//		$proyectos = toba::instancia()->get_proyectos_accesibles();
//		if (!isset($this->proyecto_ref_id) && in_array($this->proyecto_ref_id, $proyectos)) {
//			throw new toba_error('Punto de montaje: el punto de montaje no referencia a un proyecto válido');
//		}
		return $this->proyecto_ref_id;
	}

	function get_tipo()
	{
		return toba_punto_montaje::tipo_proyecto;
	}
	
	protected function get_path_autoload()
	{
		// en los proyectos toba el autoload se asume en la raíz del punto de montaje
		$path_php		= $this->get_path_absoluto();
		$nombre_clase	= $this->get_clase_autoload();
		return $path_php."/$nombre_clase.php";
	}

	protected function get_clase_autoload()
	{
		return str_replace('%id_proyecto%', $this->get_proyecto_referenciado(),
							toba_modelo_proyecto::patron_nombre_autoload);
	}
	
	protected function get_metodo_autoload()
	{
		return 'cargar';
	}

	function get_path_absoluto()
	{
		$path_proyecto	= toba::instancia()->get_path_proyecto($this->get_proyecto_referenciado());
		return $path_proyecto.'/'.$this->get_path();
	}

	/**
	 * Verifica si el punto de montaje apunta al mismo proyecto que pertenece
	 * @return boolean
	 */
	function es_interno()
	{
		return $this->get_proyecto() == $this->get_proyecto_referenciado();
	}

	function to_array()
	{
		$res = parent::to_array();
		$res['proyecto_ref'] = $this->get_proyecto_referenciado();
		return $res;
	}
}
?>
