<?php
/**
 * Permite hacer validaciones de permisos globales particulares sobre el usuario actual
 * @package Seguridad
 */
class toba_derechos
{
	static private $instancia;
	protected $derechos;
	
	/**
	 * @return toba_derechos
	 */
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_derechos();	
		}
		return self::$instancia;	
	}	
	
	private function __construct()
	{
		$derechos = toba::proyecto()->get_lista_permisos();
		$this->derechos = array();
		foreach ($derechos as $perm) {
			$this->derechos[] = $perm['nombre'];
		}
		return $this->derechos;
	}
	
	/**
	 * Cambia la lista de permisos del usuario actual
	 * @param array $derechos Array de indices permitidos
	 */
	function set_derechos($derechos)
	{
		$this->derechos = $derechos;	
	}

	/**
	 * Valida que el usuario actual tenga un permiso particular
	 *
	 * @param string $derecho Indice del permiso a validar
	 * @param boolean $lanzar_excepcion Si el usuario no posee el permiso, se lanza una excepción, sino retorna falso
	 * @throws toba_error_permisos
	 */
	function validar($derecho, $lanzar_excepcion=true)
	{
		//El usuario tiene el permiso
		if ($this->chequear($derecho)) {
			return true;
		}
		//No tiene el permiso, tratar de ver si el permiso existe y cuales son sus datos
		$rs = toba::proyecto()->get_descripcion_permiso($derecho);
		if 	(empty($rs)) {
			throw new toba_error_def("El permiso '$derecho' no se encuentra definido en el sistema.");
		}
		if (! $lanzar_excepcion) {
			return false;
		} else {
			if (isset($rs['mensaje_particular'])) {
				throw new toba_error_permisos($rs['mensaje_particular']);
			} else {
				$usuario = toba::usuario()->get_id();
				$descripcion = isset($rs['descripcion']) ? $rs['descripcion'] : $derecho;
				throw new toba_error_permisos("El usuario $usuario no posee el derecho '$descripcion'");
			}
		}
	}
	
	/**
	 * Chequea si el usuario actual tiene acceso a un derecho especifico
	 *
	 * @param string $derecho Indice del permiso a validar
	 * @return boolean
	 */	
	function chequear($derecho)
	{
		return in_array($derecho, $this->derechos);
	}
	
}
?>