<?php
/**
 * Encapsula al usuario actualmente logueado a la instancia
 *
 * Consumir usando toba::usuario()->
 * @package Seguridad
 */

class toba_usuario implements toba_interface_usuario
{
	function __construct($id_usuario)
	{
	}

	static function autenticar($id_usuario, $clave, $datos_inciales=null)
	{
		return false;
	}
	
	function get_id()
	{
		return null;
	}
	
	function get_nombre()
	{
		return null;
	}
	
	function get_perfiles_funcionales()
	{
		return array();	
	}

	//-------- Bloqueos --------------------------------------

	static function es_ip_rechazada($ip){}
	static function registrar_error_login($usuario, $ip, $texto){}
	static function bloquear_ip($ip){}
	static function get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal=null){}
	static function get_cantidad_intentos_usuario_en_ventana_temporal($usuario, $ventana_temporal=null){}
	static function bloquear_usuario($usuario){}
	static function es_usuario_bloqueado($usuario){}

	//-------- Restricciones de acceso ------------------------
	
	function get_restricciones_funcionales($perfiles = null)
	{
		return array();
	}

	function get_perfil_datos()
	{
		return null;
	}

	//------------------------ Generacion de claves ---------------------------
	static function generar_clave_aleatoria($long)
	{
		$str = 'ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789-._~';		//Se deberian agregar ciertos caracteres especiales segun SIGEN
		for($cad='',$i=0;$i<$long;$i++) {
			$cad .= substr($str,rand(0,(strlen($str)-1)),1);
		}		
		return $cad;
	}

	function set_clave($clave_plana)
	{
		$this->set_clave_usuario($clave_plana, $this->get_id());
	}

	static function set_clave_usuario ($clave_plana, $usuario)
	{
		$clave_enc = encriptar_con_sal($clave_plana, apex_pa_algoritmo_hash);
		$sql = 'UPDATE apex_usuario
					SET		clave = :clave ,
					autentificacion = :autenticacion ' 
			.'  WHERE  usuario = :usuario ;'; 
		
		$id = toba::instancia()->get_db()->sentencia_preparar($sql);
		toba::instancia()->get_db()->sentencia_ejecutar($id, array('clave' => $clave_enc,  'autenticacion' => apex_pa_algoritmo_hash, 'usuario'=>$usuario));
	}
	
	static function reemplazar_clave_vencida($clave_plana, $usuario, $dias_validez = null)
	{
		if (is_null($dias_validez)) {		//Anulo el vencimiento
			$accion = 'vencimiento = NULL';
		} else {								//Actualizo el vencimiento con la cantidad de dias restantes
			$accion = "vencimiento = (now() + '$dias_validez days')::date"; 
		}
		
		$sql = "UPDATE apex_usuario SET forzar_cambio_pwd = 0, $accion  WHERE usuario =  :usuario ;";
		toba::instancia()->get_db()->abrir_transaccion();
		try {
			self::set_clave_usuario($clave_plana, $usuario);
			$id = toba::instancia()->get_db()->sentencia_preparar($sql);
			toba::instancia()->get_db()->sentencia_ejecutar($id, array( 'usuario'=> $usuario));			
			toba::instancia()->get_db()->cerrar_transaccion();			
		} catch (toba_error_db $e) {
			toba::instancia()->get_db()->abortar_transaccion();
			throw new toba_error_usuario('No se pudo modificar la clave, contacte a un administrador del sistema');
		}
	}
	
	static function forzar_cambio_clave($usuario) 
	{
		$sql = 'UPDATE apex_usuario SET forzar_cambio_pwd = 1 WHERE usuario =  :usuario ;' ;
		try {
			$id = toba::instancia()->get_db()->sentencia_preparar($sql);
			toba::instancia()->get_db()->sentencia_ejecutar($id, array( 'usuario'=> $usuario));
		} catch(toba_error_db $e) {
			toba::logger()->debug('No se pudo forzar el cambio de contraseña en el usuario '. $usuario);
			throw new toba_error_usuario('Hubo problemas al modificar el usuario ');
		}
	}
	
	static function verificar_clave_no_utilizada($clave_plana, $usuario, $no_repetidas=null) 
	{		
		$claves = toba::instancia()->get_lista_claves_usadas($usuario, null, $no_repetidas);
		if (! empty($claves)) {
			foreach($claves as $clave) {
				$test_key = encriptar_con_sal($clave_plana, $clave['algoritmo'], $clave['clave']);			
				if ($clave['clave'] == $test_key) {
					toba::logger()->debug('El usuario selecciono una clave ya utilizada anteriormente');
					throw new toba_error_usuario('La clave fue utilizada anteriormente, por favor seleccione una nueva');
				}	
			}		
		}
	}
	
	static function verificar_clave_vencida($id_usuario)
	{
		$datos_usuario = toba::instancia()->get_info_autenticacion($id_usuario);
		if ( empty($datos_usuario) ) {
			return false;
		} else {
			return ($datos_usuario['clave_vencida'] === 1 || $datos_usuario['forzar_cambio_pwd'] === 1);
		}
	}
	
	static function verificar_periodo_minimo_cambio($usuario, $periodo)
	{
		$claves = toba::instancia()->get_lista_claves_usadas($usuario, $periodo);
		return empty($claves);
	}
		
	/**
	 *  Verifica la composicion y largo de una contraseña de usuario, lanza excepcion cuando falla la validacion,
	 *  de lo contrario retorna true.
	 * @param string $pwd
	 * @param int $largo_minimo
	 * @return boolean
	 * @throws toba_error_pwd_conformacion_invalida
	 */
	static function verificar_composicion_clave($pwd, $largo_minimo=null)
	{
		if (is_null($largo_minimo)) {
			$largo_minimo = apex_pa_pwd_largo_minimo;		//Ultimo recurso si es que no lo levantaron de la instancia o el proyecto.
		}		
		$expr = self::get_exp_reg_pwd($largo_minimo); 
		toba_logger::instancia()->debug("Expresion evaluacion: $expr");
		$largo_valido =  ($largo_minimo <= strlen($pwd));
		
		$valida = preg_match_all($expr, $pwd, $matches);
		if (! $largo_valido) {
			throw new toba_error_pwd_conformacion_invalida("La clave del usuario debe tener al menos $largo_minimo caracteres");
		} elseif ($valida === false || $valida == '0') {
			throw new toba_error_pwd_conformacion_invalida('La clave del usuario debe estar compuesta por caracteres, digitos y simbolos especiales, no se pueden repetir caracteres adyacentes');
		}
		return true;
	}
	
	/**
	 * @ignore
	 */
	static function get_exp_reg_pwd($largo_minimo)
	{
		return '/^(?!.*(.)\1{1})((?=.*[^\w\d\s])(?=.*[a-zA-Z])|(?=.*[0-9])(?=.*[a-zA-Z])).{'. $largo_minimo.',}$/';
	}
	
	static function existe_usuario($id_usuario)
	{
		$datos_usuario = toba::instancia()->get_info_autenticacion($id_usuario);
		return (isset($datos_usuario) && !is_null($datos_usuario));
	}
}
?>