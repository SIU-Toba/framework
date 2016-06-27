<?php
/**
 * Mapea un identificador a un path relativo en disco
 * @package Centrales
 * @subpackage Punto Montaje
 */
class toba_punto_montaje
{
	const tipo_proyecto		= 'proyecto_toba';
	const tipo_pers		= 'pers_proyecto_toba';
	const tipo_indefinido	= 'indefinido';
	
	protected $id;
	protected $etiqueta;
	protected $etiqueta_anterior;
	protected $proyecto_id;
	protected $descripcion;
	protected $path;
	protected $instancia_toba;
	
	/**
	 * Fija el identificador del PM
	 * @param integer $id
	 */
	function set_id($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Fija la etiqueta del PM
	 * @param string $etiqueta
	 */
	function set_etiqueta($etiqueta)
	{
		$this->etiqueta = $etiqueta;
	}

	/**
	 * Fija el identificador de proyecto del PM
	 * @param string $proyecto_id
	 */
	function set_proyecto($proyecto_id)
	{
		$this->proyecto_id = $proyecto_id;
	}

	/**
	 * Fija la descripcion del PM
	 * @param string $descripcion
	 */
	function set_descripcion($descripcion)
	{
		$this->descripcion = $descripcion;
	}

	/**
	 * Fija el path al que apunta el PM
	 * @param string $path
	 */
	function set_path($path)
	{
		$this->path = $path;
	}

	/**
	 * Fija el objeto que representa la instancia toba
	 * @param toba_instancia $obj_instancia
	 */
	function set_instancia_toba($obj_instancia)
	{
		$this->instancia_toba = $obj_instancia;
	}
	
	/**
	 * Devuelve un objeto representando la instancia
	 * @return toba_instancia
	 */
	function instancia_toba()
	{
		return (isset($this->instancia_toba))? $this->instancia_toba : toba::instancia();
	}		
	
	/**
	 * Retorna el id del PM
	 * @return integer
	 */
	function get_id()
	{
		return $this->id;
	}

	/**
	 * Retorna la etiqueta del PM
	 * @return string
	 * @throws toba_error
	 */
	function get_etiqueta()
	{
		if (!isset($this->etiqueta)) {
			throw new toba_error('Punto de montaje: el punto de montaje no
								  tiene una etiqueta válida');
		}
		return $this->etiqueta;
	}

	/**
	 * Fija una etiqueta anterior
	 * @param string $etiqueta
	 * @ignore
	 */
	function set_etiqueta_anterior($etiqueta)
	{
		$this->etiqueta_anterior = $etiqueta;
	}

	/**
	 * Devuelve la etiqueta anterior del PM
	 * @return string
	 * @ignore
	 */
	function get_etiqueta_anterior()
	{
		return $this->etiqueta_anterior;
	}

	/**
	 * Determina si el PM posee etiqueta anterior o no
	 * @return boolean
	 * @ignore
	 */
	function tiene_etiqueta_anterior()
	{
		return isset($this->etiqueta_anterior);
	}

	/**
	 * Devuelve el identificador de proyecto del PM
	 * @return string
	 * @throws toba_error
	 */
	function get_proyecto()
	{
		if (!isset($this->proyecto_id)) {
			throw new toba_error('Punto de montaje: el punto de montaje
								  no tiene un proyecto válido');
		}
		return $this->proyecto_id;
	}

	/**
	 * Devuelve la descripcion del PM
	 * @return string
	 */
	function get_descripcion()
	{
		return $this->descripcion;
	}

	/**
	 * Devuelve el path al que apunta el PM (recuperado de base o instancia.ini)
	 * @return string
	 * @throws toba_error
	 */
	function get_path()
	{
		if (empty($this->path)) {
			$path_instancia_ini = $this->instancia_toba()->get_path_ini();
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
	 * @return string
	 */
	function get_path_absoluto()
	{
		// los puntos de montaje externos tienen por defecto paths absolutos
		return $this->get_path();	
	}

	/**.
	 * Devuelve el tipo de punto de montaje
	 * @return string
	 */
	function get_tipo()
	{
		return toba_punto_montaje::tipo_indefinido;
	}
	
	/**
	 * Determina si el punto de montaje es del tipo proyecto o no
	 * @return boolean
	 */
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

	/**
	 * Determina si el PM es interno (always false)
	 * @return boolean
	 */
	function es_interno()
	{
		return false;	// Nunca puede ser interno un pm externo. por definición
	}
	
	/**
	 * Devuelve la info del PM como arreglo
	 * @return array
	 */
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
