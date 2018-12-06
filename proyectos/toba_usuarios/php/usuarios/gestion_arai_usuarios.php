<?php	
/**
 * Clase para utilizar Arai-Usuarios
 * 
 */
class gestion_arai_usuarios
{
	
	static public function get_datos($datos) 
	{	
		if (toba::instalacion()->vincula_arai_usuarios()) {
			if (! isset($datos['cuenta']) && isset($datos['usuario'])) {
				$datos['cuenta'] = $datos['usuario'];	
			}
			if (! isset($datos['usuario_arai']) && isset($datos['cuenta']) && self::verifica_version_arai_cli()) {
				$datos['usuario_arai'] = rest_arai_usuarios::instancia()->get_identificador_x_aplicacion_cuenta(SIUToba\Framework\Arai\RegistryHooksProyectoToba::getAppUniqueId(), $datos['cuenta']);
			}
		}
		return $datos;
	}
	
	/**
	 * @deprecated desde version 3.1.0
	 * @param array $datos
	 * @param int $largo_clave
	 * @return array
	 */
	static public function set_datos($datos, $largo_clave) 
	{
		return self::completar_datos_usuario($datos, $largo_clave);
	}
	
	static public function completar_datos_usuario($datos, $largo_clave) 
	{	
		if (toba::instalacion()->vincula_arai_usuarios()) {
			if (!isset($datos['clave'])) {
				$datos['clave'] = self::get_clave_aleatoria($largo_clave);
			}
			if (!isset($datos['usuario']) && isset($datos['cuenta'])) {
				$datos['usuario'] = $datos['cuenta'];
			}
			if (isset($datos['usuario_arai'])) {
				$datos['usuario_arai'] = self::get_identificador_arai_usuarios($datos['usuario_arai']);
			}
			if (!isset($datos['nombre']) && isset($datos['usuario_arai'])) {
				$datos['nombre'] = rest_arai_usuarios::instancia()->get_nombre_apellido_usuario($datos['usuario_arai']);
			}
		}
		return $datos;
	}
	
	static public function sincronizar_datos($cuenta, $identificador) 
	{	
		$resultado = true;
		if (toba::instalacion()->vincula_arai_usuarios() && self::verifica_version_arai_cli()) {
			$appUniqueId = SIUToba\Framework\Arai\RegistryHooksProyectoToba::getAppUniqueId();
			$identificador_arai_usuarios = rest_arai_usuarios::instancia()->get_identificador_x_aplicacion_cuenta($appUniqueId, $cuenta);
			if (!isset($identificador_arai_usuarios)) {
				$datos_cuenta = array(
										'identificador_aplicacion' => $appUniqueId,
										'cuenta' => $cuenta,
										'identificador_usuario' => $identificador,
				);
				$resultado = rest_arai_usuarios::instancia()->agregar_cuenta($appUniqueId, $datos_cuenta);
			} elseif ($identificador != $identificador_arai_usuarios) {
				throw new toba_error('La cuenta se encuentra asociada a otro usuario de ARAI.');
			}
		}
		return $resultado;
	}
	
	static public function eliminar_datos($cuenta) 
	{	
		$resultado = true;
		if (toba::instalacion()->vincula_arai_usuarios() && self::verifica_version_arai_cli()) {
			$resultado = rest_arai_usuarios::instancia()->eliminar_cuenta(SIUToba\Framework\Arai\RegistryHooksProyectoToba::getAppUniqueId(), $cuenta);
		}
		return $resultado;
	}
	
	static function get_nombre_usuario_arai($identificador) 
	{
		return rest_arai_usuarios::instancia()->get_nombre_apellido_usuario($identificador);
	}
	
	static function get_identificador_arai_usuarios($clave) 
	{
		$datos = toba_ei_cuadro::recuperar_clave_fila('31000002', $clave);
		if (isset($datos) && !empty($datos) && isset($datos['identificador'])) {
			return $datos['identificador'];
		} else {
			return $clave;
		}
	}
	
	static public function get_usuarios_disponibles_aplicacion($filtro) 
	{
		$datos = array();
		if (toba::instalacion()->vincula_arai_usuarios() && self::verifica_version_arai_cli()) {
			$datos = rest_arai_usuarios::instancia()->get_usuarios($filtro, SIUToba\Framework\Arai\RegistryHooksProyectoToba::getAppUniqueId());
		}
		return $datos;
	}
	
	/*************************************************************************************************
		METODOS PRIVADOS
	*************************************************************************************************/
	
	static private function get_clave_aleatoria($largo_clave) 
	{
		 do {			
			try {
				$claveok = true;
				$clave_tmp = toba_usuario::generar_clave_aleatoria($largo_clave);
				toba_usuario::verificar_composicion_clave($clave_tmp, $largo_clave);
			} catch(toba_error_pwd_conformacion_invalida $e) {
				$claveok = false;
			} catch(toba_error_usuario $e) {
				$claveok = false;
			}
		} while(! $claveok);
		return $clave_tmp;
	}	
	
	static private function verifica_version_arai_cli()
	{
		if (! class_exists('SIU\AraiCli\AraiCli')) {
			throw new toba_error('No se encuentra instalado el paquete siu/arai-cli, revise los paquetes sugeridos en composer.');
		}
		//Agregar verificacion puntual de version compatible de arai-cli
		if (! SIUToba\Framework\Arai\RegistryHooksProyectoToba::checkVersionCompatible()) {
			throw new toba_error('La versi�n del paquete siu/arai-cli no es compatible, revise la documentaci�n del sistema.');
		}

		return true;
 	}
}
?>
