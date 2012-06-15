<?php
/**
 * Esta clase representa los puntos de montaje del proyecto que está ejecutandose
 */

class toba_pms
{
	// Cosas estáticas -----------------------------------------------------

	static private $instancia;

	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_pms();
		}
		return self::$instancia;
	}
		
	// Cosas dinámicas -----------------------------------------------------

	protected $pms;

	function  __construct()
	{
		$this->pms = $this->convertir(toba::proyecto()->get_info_pms());
	}

	/**
	 * Convierte un arreglo de pms de la base a un arreglo de toba_punto_montaje
	 * @param array $pms
	 */
	protected function convertir($pms)
	{
		$rs = array();
		foreach ($pms as $registro) {
			$rs[] = toba_punto_montaje_factory::construir($registro);
		}

		return $rs;
	}

	/**
	 * Devuelve verdadero si el punto con etiqueta $etiqueta existe en el proyecto
	 * @param string $etiqueta
	 * @return boolean
	 */
	function existe($etiqueta)
	{
		foreach ($this->pms as $punto) {
			if ($punto->get_etiqueta() == $etiqueta) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Devuelve verdadero si el punto con id $id existe en el proyecto
	 * @param string $id
	 * @return boolean
	 */
	function existe_por_id($id)
	{
		foreach ($this->pms as $punto) {
			if ($punto->get_id() == $id) {
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Devuelve un punto de montaje del proyecto actual con etiqueta $etiqueta
	 * @param string $etiqueta
	 * @return toba_punto_montaje
	 */
	function get($etiqueta)
	{	
		foreach ($this->pms as $punto) {
			if ($punto->get_etiqueta() == $etiqueta) {
				return $punto;
			}
		}

		$proyecto = toba::proyecto()->get_id();
		throw new toba_error("PUNTOS DE MONTAJE: El punto de montaje con etiqueta '$etiqueta' no existe en el proyecto '$proyecto'");
	}

	/**
	 * Devuelve un punto de montaje del proyecto actual con id $id
	 * @param string $id
	 * @return toba_punto_montaje
	 */
	function get_por_id($id)
	{
		foreach ($this->pms as $punto) {
			if ($punto->get_id() == $id) {
				return $punto;
			}
		}
		$proyecto = toba::proyecto()->get_id();
		throw new toba_error("PUNTOS DE MONTAJE: El punto de montaje con id '$id' no existe en el proyecto '$proyecto'");
	}
	
	function get_instancia_pm_proyecto($proyecto, $id)
	{		
		$puntos = $this->convertir(toba::proyecto()->get_info_pms($proyecto));
		foreach ($puntos as $punto) {
			if ($punto->get_id() == $id) {
				return $punto;
			}
		}
	}

}
?>
