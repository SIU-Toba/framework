<?php

/**
 * Permite hacer validaciones de permisos particulares sobre el usuario actual
 */
class toba_permisos
{
	static private $instancia;
	protected $permisos;
	
	private function __construct()
	{
	}
	
	function cargar($proyecto, $grupo)
	{
		$permisos = toba_proyecto::get_lista_permisos($grupo);
		$this->permisos = array();
		foreach ($permisos as $perm) {
			$this->permisos[] = $perm['nombre'];
		}
		return $this->permisos;
	}
	
	function set_permisos($permisos)
	{
		$this->permisos = $permisos;	
	}
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_permisos();	
		}
		return self::$instancia;	
	}
	
	/**
	 * Valida que el usuario actual tenga un permiso particular
	 *
	 * @param string $permiso
	 * @param boolean $lanzar_excepcion Si el usuario no posee el permiso, se lanza una excepción, sino retorna falso
	 */
	function validar($permiso, $lanzar_excepcion=true)
	{
		//El usuario tiene el permiso
		if (in_array($permiso, $this->permisos)) {
			return true;
		}
		//No tiene el permiso, tratar de ver si el permiso existe y cuales son sus datos
		$rs = toba_proyecto::get_descripcion_permiso($permiso);
		if 	(empty($rs)) {
			throw new toba_error_def("El permiso '$permiso' no se encuentra definido en el sistema.");
		}
		if (! $lanzar_excepcion) {
			return false;
		} else {
			if (isset($rs[0]['mensaje_particular'])) {
				throw new toba_error_permisos($rs[0]['mensaje_particular']);
			} else {
				$usuario = toba::hilo()->get_usuario();
				$descripcion = isset($rs[0]['descripcion']) ? $rs[0]['descripcion'] : $permiso;
				throw new toba_error_permisos("El usuario $usuario no tiene permiso de $descripcion");
			}
		}
	}
	
}

?>