<?php

/**
 * Permite hacer validaciones de permisos particulares sobre el usuario actual
 */
class permisos
{
	static private $instancia;
	protected $permisos;
	
	private function __construct()
	{
	}
	
	function cargar($proyecto, $grupo)
	{
		$sql = " 
			SELECT 
				per.nombre
			FROM
				apex_permiso_grupo_acc per_grupo,
				apex_permiso per
			WHERE
				per_grupo.proyecto = '$proyecto'
			AND	per_grupo.usuario_grupo_acc = '$grupo'
			AND	per_grupo.permiso = per.permiso
			AND	per_grupo.proyecto = per.proyecto
		";
		$permisos = toba::get_db('instancia')->consultar($sql);
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
			self::$instancia = new permisos();	
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
		$proyecto = toba::get_hilo()->obtener_proyecto();
		$sql = " 
			SELECT
				per.descripcion,
				per.mensaje_particular
			FROM
				apex_permiso per
			WHERE
				per.proyecto = '$proyecto'
			AND	per.nombre = '$permiso'
		";
		$rs = toba::get_db('instancia')->consultar($sql);
		if 	(empty($rs)) {
			throw new excepcion_toba_def("El permiso '$permiso' no se encuentra definido en el sistema.");
		}
		if (! $lanzar_excepcion) {
			return false;
		} else {
			if (isset($rs[0]['mensaje_particular'])) {
				throw new excepcion_toba_permisos($rs[0]['mensaje_particular']);
			} else {
				$usuario = toba::get_hilo()->obtener_usuario();
				$descripcion = isset($rs[0]['descripcion']) ? $rs[0]['descripcion'] : $permiso;
				throw new excepcion_toba_permisos("El usuario $usuario no tiene permiso de $descripcion");
			}
		}
	}
	
}

?>