<?php

class toba_punto_montaje
{
	const tipo_proyecto		= 'proyecto_toba';
	const tipo_pers			= 'pers_proyecto_toba';
	const tipo_indefinido	= 'indefinido';
	
    protected $id;
	protected $etiqueta;
	protected $etiqueta_anterior;
	protected $proyecto_id;
	protected $descripcion;
	protected $path;

	function set_id($id)
	{
		$this->id = $id;
	}

	function set_etiqueta($etiqueta)
	{
		$this->etiqueta = $etiqueta;
	}

	function set_proyecto($proyecto_id)
	{
		$this->proyecto_id = $proyecto_id;
	}

	function set_descripcion($descripcion)
	{
		$this->descripcion = $descripcion;
	}

	function set_path($path)
	{
		$this->path = $path;
	}

	function get_id()
	{
		return $this->id;
	}

	function get_etiqueta()
	{
		if (!isset($this->etiqueta)) {
			throw new toba_error('Punto de montaje: el punto de montaje no
								  tiene una etiqueta válida');
		}
		return $this->etiqueta;
	}

	function set_etiqueta_anterior($etiqueta)
	{
		$this->etiqueta_anterior = $etiqueta;
	}

	function get_etiqueta_anterior()
	{
		return $this->etiqueta_anterior;
	}

	function tiene_etiqueta_anterior()
	{
		return isset($this->etiqueta_anterior);
	}

	function get_proyecto()
	{
		if (!isset($this->proyecto_id)) {
			throw new toba_error('Punto de montaje: el punto de montaje
								  no tiene un proyecto válido');
		}
		return $this->proyecto_id;
	}

	function get_descripcion()
	{
		return $this->descripcion;
	}

	function get_path()
	{
		if (empty($this->path)) {
			$path_instancia_ini = toba::instancia()->get_path_ini();
			$instancia_ini = new toba_ini($path_instancia_ini);
			$datos = $instancia_ini->get($this->get_proyecto());
			$nombre = toba_modelo_pms::prefijo_ini.$this->get_etiqueta();
			if (!isset($datos[$nombre])) {
				throw new toba_error("Punto de montaje: el punto de montaje
					'{$this->get_etiqueta()}' no existe en $path_instancia_ini.
					Debe agregar en instancia.ini la entrada
					'pm_{$this->get_etiqueta()} = path' donde path es la ubicación
					absoluta en el sistema de archivos. Esta entrada debe ir en
					la sección [{$this->get_proyecto()}]");
			}
			$this->path = $datos[$nombre];
		}
		return $this->path;
	}

	/**
	 * Devuelve un path absoluto al punto de montaje
	 */
	function get_path_absoluto()
	{
		// los puntos de montaje externos tienen por defecto paths absolutos
		return $this->get_path();	
	}

	function get_tipo()
	{
		return toba_punto_montaje::tipo_indefinido;
	}

	function es_de_proyecto()
	{
		return	($this->get_tipo() == toba_punto_montaje::tipo_proyecto) ||
				($this->get_tipo() == toba_punto_montaje::tipo_pers);
	}

	/**
	 * Devuelve verdadero si el punto no tiene seteado el id, esto quiere decir
	 * que el punto de montaje es nuevo en el sistema ya que no tiene un id
	 * asignado
	 * @return boolean
	 */
	function es_nuevo()
	{
		return !isset($this->id);
	}

	function es_interno()
	{
		return false;	// Nunca puede ser interno un pm externo. por definición
	}
	
	function to_array()
	{
		return array(
			'id' => $this->get_id(),
			'etiqueta' => $this->get_etiqueta(),
			'proyecto' => $this->get_proyecto(),
			'proyecto_ref' => ' - ',
			'descripcion' => $this->get_descripcion(),
			'path_pm' => $this->get_path(),
			'tipo' => $this->get_tipo()
		);
	}

}
?>
