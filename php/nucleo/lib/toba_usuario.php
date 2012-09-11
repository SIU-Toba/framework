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
		$str = "ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789";
		for($cad="",$i=0;$i<$long;$i++) {
			$cad .= substr($str,rand(0,(strlen($str)-1)),1);
		}		
		return $cad;
	}

	function set_clave($clave_plana)
	{
		$this->set_clave_usuario($clave_plana, $this->get_id());
	}

	function set_clave_usuario ($clave_plana, $usuario)
	{
		$clave_enc = quote(encriptar_con_sal($clave_plana, 'sha256'));
		$sql = "UPDATE apex_usuario
					SET		clave = $clave_enc ,
					autentificacion = 'sha256'
					WHERE	usuario = ". quote($usuario);
		toba::instancia()->get_db()->ejecutar($sql);
	}
	
	function reemplazar_clave_vencida($clave_plana, $usuario, $dias_validez = null)
	{
		if (is_null($dias_validez)) {		//Anulo el vencimiento
			$accion = 'vencimiento = NULL';
		} else {								//Actualizo el vencimiento con la cantidad de dias restantes
			$accion = "vencimiento = (now() + '$dias_validez days')::date"; 
		}
		
		$sql = "UPDATE apex_usuario SET $accion  WHERE usuario = ". quote($usuario);
		toba::logger()->debug($sql);
		toba::instancia()->get_db()->abrir_transaccion();
		try {
			$this->set_clave_usuario($clave_plana, $usuario);
			toba::instancia()->get_db()->ejecutar($sql);
			toba::instancia()->get_db()->cerrar_transaccion();			
		} catch (toba_error_db $e) {
			toba::instancia()->get_db()->abortar_transaccion();
			throw new toba_error_usuario('No se pudo modificar la clave, contacte a un administrador del sistema');
		}
	}
	
	function verificar_clave_no_utilizada($clave_plana, $usuario) 
	{
		$claves = toba::instancia()->get_lista_claves_usadas($usuario);
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
	
	function verificar_clave_vencida($id_usuario)
	{
		$datos_usuario = toba::instancia()->get_info_autenticacion($id_usuario);
		if ( empty($datos_usuario) ) {
			return false;
		} else {
			return ($datos_usuario['clave_vencida'] === 1);
		}
	}
}
?>